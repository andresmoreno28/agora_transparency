<?php

declare(strict_types=1);

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Config\FileStorage;
use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests the shared spine of the content model (T-601, D-026).
 *
 * SCOPE, and it is deliberately narrow. T-601 ships the five taxonomy
 * vocabularies and the six reusable field storages that the three financial
 * regimes (Contract, Agreement, Grant) attach in common. It ships ZERO node
 * types: the six bundles belong to T-612, T-613 and T-614, which assert them.
 * A field storage exists independently of its instances, so nothing here is
 * attached to a bundle yet, and nothing here carries a human-readable label —
 * field labels live on `field.field.node.<bundle>.<name>`, which this task does
 * not create.
 *
 * NAMING CONVENTION, asserted rather than merely described:
 *  - machine names are ENGLISH (CLAUDE.md rule 6 / D-017: identifiers are
 *    English; only demo content is bilingual),
 *  - the Spanish the law uses is carried by the LABELS, which is why the five
 *    vocabularies have Spanish `name` and `description` values and the six
 *    field storages have none to carry,
 *  - every identifier Ágora owns is prefixed with its functional area
 *    (recipe.yml's seam convention, D-011 rider b). The content model is area
 *    `base`, hence `agora_base_*`; fields additionally keep Drupal's `field_`
 *    prefix, hence `field_agora_base_*`, which also keeps `estado` from
 *    colliding with the `status` base field of a node.
 *
 * The Spanish the law names, mapped to the machine name that carries it:
 *   objeto      -> field_agora_base_subject
 *   importe     -> field_agora_base_amount
 *   periodo     -> field_agora_base_period
 *   contraparte -> field_agora_base_counterparty
 *   área        -> field_agora_base_area
 *   estado      -> field_agora_base_status
 *
 * This test reads the shipped YAML rather than an installed site, which is what
 * a kernel test in a package containing no code can honestly do. That the
 * config actually applies is ValidationTest's and the install smoke's job.
 */
#[RunTestsInSeparateProcesses]
final class ContentModelTest extends KernelTestBase {

  /**
   * The vocabularies T-601 ships, by machine name.
   *
   * D-026 names five and only five: `area`, `document type`, `financial year`,
   * `status`, `procedure type`. One facet spine (área · año · estado) serves
   * all six bundles.
   */
  private const VOCABULARIES = [
    'agora_base_area',
    'agora_base_document_type',
    'agora_base_financial_year',
    'agora_base_procedure_type',
    'agora_base_status',
  ];

  /**
   * The reusable field storages T-601 ships, machine name => field type.
   *
   * The type is asserted, not just the name: D-026's whole argument is that a
   * table must be able to sort and filter on these, so `importe` being
   * `decimal` rather than `string` is the load-bearing part.
   */
  private const FIELD_STORAGES = [
    'field_agora_base_amount' => 'decimal',
    'field_agora_base_area' => 'entity_reference',
    'field_agora_base_counterparty' => 'string',
    'field_agora_base_period' => 'daterange',
    'field_agora_base_status' => 'entity_reference',
    'field_agora_base_subject' => 'string',
  ];

  /**
   * Tests that the shared spine is present, complete and no larger than stated.
   */
  public function testSharedSpine(): void {
    $path = dirname(__FILE__, 4);
    $storage = new FileStorage($path . '/config');
    $assertions = 0;

    // -- The five vocabularies -----------------------------------------------
    foreach (self::VOCABULARIES as $vid) {
      $name = 'taxonomy.vocabulary.' . $vid;
      $data = $storage->read($name);
      $this->assertIsArray($data, "$name must be shipped in config/.");
      $assertions++;
      $this->assertSame($vid, $data['vid'], "$name must declare its own machine name.");
      $assertions++;
      // The label is what carries the Spanish; an empty one would mean the
      // vocabulary shipped with nothing a Spanish clerk can read.
      $this->assertNotEmpty($data['name'], "$name must carry a label.");
      $assertions++;
    }

    // -- The six field storages ----------------------------------------------
    foreach (self::FIELD_STORAGES as $field_name => $type) {
      $name = 'field.storage.node.' . $field_name;
      $data = $storage->read($name);
      $this->assertIsArray($data, "$name must be shipped in config/.");
      $assertions++;
      $this->assertSame($field_name, $data['field_name'], "$name must declare its own machine name.");
      $assertions++;
      $this->assertSame('node', $data['entity_type'], "$name must be a node field storage.");
      $assertions++;
      $this->assertSame($type, $data['type'], "$name must be of type $type.");
      $assertions++;
      // Single-valued: a facet spine and a sortable table column both stop
      // working the moment a field can hold an unbounded list.
      $this->assertSame(1, $data['cardinality'], "$name must be single-valued.");
      $assertions++;
    }

    // The two taxonomy references must actually reference taxonomy terms; the
    // vocabulary they are restricted to is an instance-level setting, so it is
    // T-612/T-613/T-614's to assert, not this row's.
    foreach (['field_agora_base_area', 'field_agora_base_status'] as $field_name) {
      $data = $storage->read('field.storage.node.' . $field_name);
      $this->assertSame('taxonomy_term', $data['settings']['target_type'], "$field_name must reference taxonomy terms.");
      $assertions++;
    }

    // -- Set equality, both directions ---------------------------------------
    // "Every vocabulary and every field storage" is a claim in both directions:
    // it fails if one is missing AND if an unannounced one has accreted. Node
    // types are deliberately not asserted here — T-612 onward add them
    // legitimately, and a test that had to be relaxed to let them in would be
    // the exact shape of a weakened gate.
    $vocabularies = $storage->listAll('taxonomy.vocabulary.');
    sort($vocabularies);
    $expected = array_map(
      static fn (string $vid): string => 'taxonomy.vocabulary.' . $vid,
      self::VOCABULARIES,
    );
    sort($expected);
    $this->assertSame($expected, $vocabularies, 'config/ must ship exactly the vocabularies D-026 names.');
    $assertions++;

    $storages = $storage->listAll('field.storage.node.');
    sort($storages);
    $expected = array_map(
      static fn (string $field_name): string => 'field.storage.node.' . $field_name,
      array_keys(self::FIELD_STORAGES),
    );
    sort($expected);
    $this->assertSame($expected, $storages, 'config/ must ship exactly the shared field storages T-601 names.');
    $assertions++;

    // -- The one thing that does NOT live in config/ -------------------------
    // D-032 step 4b: `SiteExporter::isAction()` routes core/System/User default
    // config into the export's regenerated `recipe.yml`, never into `config/`.
    // Installing `datetime_range` is exactly such a change, and it is what makes
    // `field_agora_base_period`'s `daterange` type resolvable on a clean
    // install. Copying `config/` alone would have dropped it with no error, so
    // the transplant gets an assertion rather than a promise.
    $recipe = Yaml::decode(file_get_contents($path . '/recipe.yml'));
    $this->assertContains('datetime_range', $recipe['install'], 'recipe.yml must install datetime_range, or field_agora_base_period cannot import.');
    $assertions++;

    // I-045: a green test is a statement about the set it opened, so print the
    // set. STDERR, because core's phpunit.xml sets beStrictAboutOutputDuringTests.
    fwrite(STDERR, sprintf(
      "\nContentModelTest: %d vocabularies, %d field storages, %d assertions performed.\n",
      count(self::VOCABULARIES),
      count(self::FIELD_STORAGES),
      $assertions,
    ));
  }

}
