<?php

declare(strict_types=1);

use Composer\InstalledVersions;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Recipe\Recipe;
use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Finder\Finder;

/**
 * Tests that the site template conforms to basic requirements.
 *
 * You can customize this test, but generally shouldn't unless you have a
 * specific reason to do so. The requirements for site templates are documented
 * in GET-STARTED.md.
 */
#[RunTestsInSeparateProcesses]
final class RequirementsTest extends KernelTestBase {

  /**
   * Tests that the site template conforms to basic requirements.
   */
  public function testSiteTemplateRequirements(): void {
    $path = dirname(__FILE__, 4);

    // The site template cannot include any code (i.e., modules or themes).
    $finder = Finder::create()->in($path)->files()->name('*.info.yml');
    $this->assertCount(0, $finder, "Recipes cannot include any code (modules or themes) of their own; they must list them as dependencies in `composer.json`.");

    // Ensure the recipe's type is correct.
    $this->assertSame('Site', Recipe::createFromDirectory($path)->type, 'The recipe type must be "Site".');

    // Read `composer.json` and ensure it's syntactically valid.
    $file = $path . '/composer.json';
    $this->assertFileExists($file);
    $data = file_get_contents($file);
    $data = Json::decode($data);
    $this->assertIsArray($data);

    // To avoid confusion about what packages are part of Drupal CMS, site
    // templates should never be prefixed with "drupal_cms_" or "drupal-cms-".
    // The only exception is the starter kit.
    [, $name] = explode('/', $data['name'], 2);
    if ($name !== 'drupal_cms_site_template_base') {
      $this->assertStringStartsNotWith('drupal_cms_', $name, 'Site templates should not use the drupal_cms_ prefix in their name.');
      $this->assertStringStartsNotWith('drupal-cms-', $name, 'Site templates should not use the drupal-cms- prefix in their name.');
    }

    // In CI, respect a list of dependencies which are required as dev branches.
    // For example, this is useful for testing the site template against the
    // latest commit of a bespoke theme to which it is strongly coupled.
    $allow_dev = getenv('CI_ALLOW_DEV');
    if (getenv('CI') && $allow_dev) {
      $allow_dev = array_map('trim', explode(',', $allow_dev));
    }
    else {
      $allow_dev = [];
    }

    $install_profiles = InstalledVersions::getInstalledPackagesByType('drupal-profile');
    foreach ($data['require'] ?? [] as $name => $constraint) {
      // Site templates aren't allowed to depend on install profiles.
      $this->assertNotContains($name, $install_profiles, "The site template cannot depend on $name because it is an install profile.");
      // Site templates may not patch dependencies in any way, which includes
      // depending on the cweagans/composer-patches plugin.
      $this->assertNotSame('cweagans/composer-patches', $name, "The site template cannot depend on $name because site templates must not patch dependencies.");

      if (in_array($name, $allow_dev, TRUE)) {
        continue;
      }
      // Use a basic heuristic to detect pinned dependencies, which are never
      // allowed in a site template.
      $this->assertDoesNotMatchRegularExpression('/^v?[0-9]+\./i', $constraint, "The site template cannot pin a specific version of $name.");
    }
    $this->assertArrayNotHasKey('patches', $data['extra'] ?? [], 'Site templates cannot supply or specify patches for dependencies.');

    // The site template must identify itself as a recipe.
    $this->assertSame(Recipe::COMPOSER_PROJECT_TYPE, $data['type'], sprintf('The project type must be "%s".', Recipe::COMPOSER_PROJECT_TYPE));

    // Although not a hard technical requirement, it's an extremely good idea
    // for a site template to specify a license.
    $this->assertNotEmpty($data['license'], 'The site template should declare a license.');

    // Ensure that all config shipped by this site template doesn't have the
    // `_core` or (except in certain situations) `uuid` keys.
    $storage = new FileStorage($path . '/config');
    $config_names = $storage->listAll();
    // CUSTOMISED DELIBERATELY (T-601), and this is the "specific reason" the
    // docblock above asks for. As shipped, the loop below was a TAUTOLOGY for
    // this repository: `config/` did not exist, `listAll()` returned an empty
    // array, and the `_core`/`uuid` block passed with nothing built. It could
    // not distinguish a good export from no export at all — and every task row
    // from T-601 onward cites "RequirementsTest still green" as its criterion,
    // so all of them inherited a check that could not fail.
    //
    // The denominator is therefore ASSERTED, and deliberately not printed: a
    // test cannot print. PHPUnit turns any output a test emits into a
    // `PHPUnit\Framework\Exception`, and writing to STDERR to dodge
    // `beStrictAboutOutputDuringTests` does not work - it is what failed
    // pipeline 934619 here, with every assertion in this method passing. The
    // count itself is printed by tests/bin/config-inventory, in the
    // agora-invariants job, where output is free and this project already
    // states its denominators (I-045).
    //
    // Two assertions replace the one print. The first is that config/
    // enumerates at all. The second BINDS that enumeration to the directory
    // this file names: `listAll()` reads whatever `getCollectionDirectory()`
    // returns, and if that ever stops being the `config/` counted on the line
    // below - a changed path, a collection, a storage built over somewhere
    // else - the loop would go quietly back to being a tautology over a
    // shorter list. Read at source in core rather than assumed: `listAll()`
    // is a `scandir()` filtered by `/.*\.yml$/` with dotfiles dropped, which
    // is the same set `glob('*.yml')` returns for a real directory, so the two
    // counts agree by construction and disagree only when they are no longer
    // looking at the same place. What it does NOT catch is a config object
    // parked in a SUBDIRECTORY: neither side recurses, so both miss it
    // equally. That one belongs to tests/bin/config-inventory, which does
    // recurse, and which is the only reason it recurses.
    //
    // No expected TOTAL is written here: T-612 through T-615 grow config/
    // legitimately, and a number that must be relaxed on every growth is a
    // gate that teaches people to relax gates.
    $this->assertNotEmpty($config_names, 'The site template must ship configuration in config/; an empty config/ makes the checks below vacuous.');
    $config_files = glob($path . '/config/*.yml') ?: [];
    $this->assertCount(count($config_files), $config_names, 'FileStorage must enumerate every config object shipped in config/.');
    foreach ($config_names as $name) {
      $data = $storage->read($name);
      // In general, the config shipped by a site template should not have a
      // UUID key. The exception is certain entity types, Canvas folders being
      // the main example, that use their UUID as their main identifier. In such
      // cases, we would expect to see the UUID in the config's name.
      if (isset($data['uuid'])) {
        $this->assertStringContainsString($data['uuid'], $name, "The $name config should contain its UUID in its name.");
      }
      $this->assertArrayNotHasKey('_core', $data, "The $name config should not include a `_core` key.");
    }
  }

}
