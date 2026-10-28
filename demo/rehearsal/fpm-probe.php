<?php

/**
 * @file
 * Asks PHP-FPM itself - not the command-line PHP - for its settings.
 *
 * Run it inside the php container, fed on standard input:
 *
 *   docker compose exec -T php php < rehearsal/fpm-probe.php
 *
 * It writes a tiny script to /tmp, sends one FastCGI request for it to the
 * pool on 127.0.0.1:9000 (the same pool nginx uses), prints what the pool
 * answered, and deletes the script. Nothing is installed and nothing in the
 * site changes.
 */

// cspell:ignore Cversion nlength Cpadding Creserved
$script = '/tmp/agora-fpm-check-' . bin2hex(random_bytes(4)) . '.php';
file_put_contents($script, <<<'PHP'
<?php
header('Content-Type: text/plain');
$wanted = ['exec', 'shell_exec', 'system', 'passthru', 'proc_open', 'popen', 'pcntl_exec'];
$callable = array_values(array_filter($wanted, 'function_exists'));
$status = function_exists('opcache_get_status') ? @opcache_get_status(FALSE) : FALSE;
echo 'sapi=', PHP_SAPI, "\n";
echo 'php_version=', PHP_VERSION, "\n";
echo 'disable_functions=', ini_get('disable_functions'), "\n";
echo 'still_callable=', $callable ? implode(',', $callable) : 'none', "\n";
echo 'expose_php=', ini_get('expose_php') ? 'on' : 'off', "\n";
echo 'session.upload_progress.enabled=', ini_get('session.upload_progress.enabled') ? 'on' : 'off', "\n";
echo 'opcache_enabled=', (is_array($status) && $status['opcache_enabled']) ? 'yes' : 'no', "\n";
echo 'memory_limit=', ini_get('memory_limit'), "\n";
echo 'env_names=', implode(',', array_keys(getenv())), "\n";
PHP);

/**
 * Builds one FastCGI record: its 8-byte header, then its content.
 *
 * @param int $type
 *   The record type: 1 BEGIN_REQUEST, 4 PARAMS or 5 STDIN.
 * @param string $content
 *   The record's content.
 *
 * @return string
 *   The record, ready to write to the socket.
 */
function fcgi_record(int $type, string $content): string {
  return pack('CCnnCx', 1, $type, 1, strlen($content), 0) . $content;
}

/**
 * Encodes one FastCGI name-value pair, each length in one or four bytes.
 *
 * @param string $name
 *   The parameter's name.
 * @param string $value
 *   The parameter's value.
 *
 * @return string
 *   The encoded pair.
 */
function fcgi_pair(string $name, string $value): string {
  $len = static fn (int $n): string => $n < 128 ? chr($n) : pack('N', $n | 0x80000000);
  return $len(strlen($name)) . $len(strlen($value)) . $name . $value;
}

/**
 * Reads exactly $n bytes from the socket, or fewer if it closes first.
 *
 * @param resource $socket
 *   The open socket to PHP-FPM.
 * @param int $n
 *   The number of bytes wanted.
 *
 * @return string
 *   The bytes read.
 */
function read_exactly($socket, int $n): string {
  $data = '';
  while (strlen($data) < $n && !feof($socket)) {
    $chunk = fread($socket, $n - strlen($data));
    if ($chunk === FALSE || $chunk === '') {
      break;
    }
    $data .= $chunk;
  }
  return $data;
}

$params = '';
foreach ([
  'GATEWAY_INTERFACE' => 'CGI/1.1',
  'REQUEST_METHOD' => 'GET',
  'SCRIPT_FILENAME' => $script,
  'SCRIPT_NAME' => '/' . basename($script),
  'REQUEST_URI' => '/' . basename($script),
  'QUERY_STRING' => '',
  'DOCUMENT_ROOT' => '/tmp',
  'SERVER_PROTOCOL' => 'HTTP/1.1',
  'SERVER_SOFTWARE' => 'agora-fpm-probe',
  'SERVER_NAME' => 'localhost',
  'SERVER_PORT' => '80',
  'REMOTE_ADDR' => '127.0.0.1',
] as $name => $value) {
  $params .= fcgi_pair($name, $value);
}

$socket = @fsockopen('127.0.0.1', 9000, $errno, $errstr, 5);
if (!$socket) {
  unlink($script);
  fwrite(STDERR, "cannot connect to PHP-FPM on 127.0.0.1:9000: $errstr\n");
  exit(1);
}
// BEGIN_REQUEST (role: responder), PARAMS, empty PARAMS, empty STDIN.
fwrite($socket, fcgi_record(1, pack('nCx5', 1, 0)) . fcgi_record(4, $params) . fcgi_record(4, '') . fcgi_record(5, ''));

$stdout = '';
while (!feof($socket)) {
  $header = read_exactly($socket, 8);
  if (strlen($header) < 8) {
    break;
  }
  $record = unpack('Cversion/Ctype/nid/nlength/Cpadding/Creserved', $header);
  $content = $record['length'] ? read_exactly($socket, $record['length']) : '';
  if ($record['padding']) {
    read_exactly($socket, $record['padding']);
  }
  if ($record['type'] === 6) {
    $stdout .= $content;
  }
  elseif ($record['type'] === 7) {
    fwrite(STDERR, $content);
  }
  elseif ($record['type'] === 3) {
    break;
  }
}
fclose($socket);
unlink($script);

// Drop the response headers; print the body.
$parts = preg_split("/\r?\n\r?\n/", $stdout, 2);
echo $parts[1] ?? $stdout;
