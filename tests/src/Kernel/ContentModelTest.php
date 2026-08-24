<?php

declare(strict_types=1);

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Config\FileStorage;
use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests the content model (T-601 and T-612, under D-026).
 *
 * SCOPE, in two methods that own two different rows.
 *
 * `testSharedSpine()` — T-601. The five taxonomy vocabularies and the six
 * reusable field storages that the three financial regimes (Contract,
 * Agreement, Grant) attach in common. T-601 ships ZERO node types. A field
 * storage exists independently of its instances, so nothing it ships is
 * attached to a bundle, and nothing it ships carries a human-readable label —
 * field labels live on `field.field.node.<bundle>.<name>`.
 *
 * `testDocumentAndPersonBundles()` — T-612. The first two of D-026's six
 * bundles, `Document` and `Person`, with their field instances, the eight new
 * field storages those instances needed, and the display components that make
 * each field editable and readable. `Contract`, `Agreement` and `Grant` are
 * T-613; `Dataset` is T-614; the six table views are T-615.
 *
 * WHY THE SET-EQUALITY CONSTANTS GROW RATHER THAN THE ASSERTIONS RELAXING.
 * Both methods assert set equality in BOTH directions — a missing object fails
 * and an unannounced one fails too. T-612 legitimately added eight field
 * storages, which is exactly the growth T-601's row warned would tempt someone
 * to weaken the check. It was not weakened: the DECLARED LIST grew and the
 * assertion stayed identical, so an accretion nobody declared still fails.
 * T-613 and T-614 add four more bundles the same way. What is forbidden is a
 * hard-coded TOTAL count of config objects, which would have to be edited on
 * every legitimate growth and would teach exactly the wrong habit; the
 * denominator is printed by tests/bin/config-inventory instead.
 *
 * NAMING CONVENTION, asserted rather than merely described:
 *  - machine names are ENGLISH (CLAUDE.md rule 6 / D-017: identifiers are
 *    English; only demo content is bilingual),
 *  - labels and descriptions are ENGLISH too (D-033, which OVERTURNED the
 *    earlier ruling this docblock used to state: that the labels carry the
 *    Spanish). English is not a preference here - core compares a config
 *    object's langcode to 'en' before it will translate its strings at all,
 *    so Spanish text under `langcode: en` was both false and untranslatable.
 *    The Spanish reaches a site as a TRANSLATION, from localize.drupal.org,
 *    never from this repository,
 *  - every identifier Ágora owns is prefixed with its functional area
 *    (recipe.yml's seam convention, D-011 rider b). The content model is area
 *    `base`, hence `agora_base_*`; fields additionally keep Drupal's `field_`
 *    prefix, hence `field_agora_base_*`, which also keeps `estado` from
 *    colliding with the `status` base field of a node.
 *
 * The Spanish term the law uses, mapped to the machine name that implements it.
 * This is a glossary for whoever reads the statute next to the code - it is NOT
 * a statement about what any shipped string says (D-033):
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
   * The field storages T-612 had to create, machine name => field type.
   *
   * REUSE BEFORE CREATION, stated so the choice is auditable rather than
   * implied. `field_agora_base_area` is NOT here: Document and Person both
   * attach T-601's existing storage. Nor are the `document type` and
   * `financial year` VOCABULARIES, which T-601 already ships — only the
   * reference fields pointing at them are new, because T-601 created no
   * storage for either. Every entry below exists because no shipped storage
   * fitted:
   *  - `document_type` / `financial_year` — T-601 created entity_reference
   *    storages for `área` and `estado` only,
   *  - `document_file` — the first reference to a media entity in the model,
   *  - `summary` — the first long-text field; deliberately ONE storage shared
   *    by both bundles (Document's "Summary", Person's "Profile and career"),
   *    since both hold the same shape of prose,
   *  - `position` — a Person's post; reusing `counterparty` would have put the
   *    word "counterparty" in the machine name of an organisation-chart entry,
   *  - `remuneration` / `severance` — art. 8.1.f) names TWO money values on one
   *    entity, and one storage cannot be attached twice to the same bundle, so
   *    `field_agora_base_amount` could serve at most one of them. Reusing it
   *    for one and inventing the other would also have put the three financial
   *    regimes' `importe` on a Person, which is precisely the union-typing
   *    D-026 refused,
   *  - `declaration` — the first file field.
   *
   * Both money storages are `decimal`, not `string`: the same load-bearing
   * point as `importe`, because a table has to sort them.
   */
  private const T612_FIELD_STORAGES = [
    'field_agora_base_declaration' => 'file',
    'field_agora_base_document_file' => 'entity_reference',
    'field_agora_base_document_type' => 'entity_reference',
    'field_agora_base_financial_year' => 'entity_reference',
    'field_agora_base_position' => 'string',
    'field_agora_base_remuneration' => 'decimal',
    'field_agora_base_severance' => 'decimal',
    'field_agora_base_summary' => 'text_long',
  ];

  /**
   * The two bundles T-612 ships, with every field attached to each.
   *
   * The field list is asserted as a SET, both directions, against what
   * `config/` actually holds — so a field silently dropped fails, and a field
   * that accreted without being declared here fails too. T-615 will assert the
   * same set against each view's columns, which is what makes D-026's
   * "six types, not one union type" argument falsifiable at config level.
   *
   * The node TITLE is not listed: it is a base field, not a `field.field.*`
   * config object. Person overrides its label to "Full name" through
   * `core.base_field_override.node.agora_base_person.title`, asserted below.
   */
  private const BUNDLES = [
    'agora_base_document' => [
      'name' => 'Document',
      'fields' => [
        'field_agora_base_document_type' => 'entity_reference',
        'field_agora_base_summary' => 'text_long',
        'field_agora_base_document_file' => 'entity_reference',
        'field_agora_base_area' => 'entity_reference',
        'field_agora_base_financial_year' => 'entity_reference',
      ],
    ],
    'agora_base_person' => [
      'name' => 'Person',
      'fields' => [
        'field_agora_base_position' => 'string',
        'field_agora_base_area' => 'entity_reference',
        'field_agora_base_summary' => 'text_long',
        'field_agora_base_remuneration' => 'decimal',
        'field_agora_base_severance' => 'decimal',
        'field_agora_base_declaration' => 'file',
      ],
    ],
  ];

  /**
   * The two money fields of art. 8.1.f), which must be numeric, never text.
   *
   * `retribución anual` and `indemnización`. This is the same point
   * `field_agora_base_amount` carries for the three financial regimes: a
   * transparency portal publishes these so they can be COMPARED, and a table
   * cannot sort a string. Asserted positively (the type is numeric) AND
   * negatively (the type is not one of the textual types), because "is
   * decimal" alone would still pass if someone later widened the positive list.
   */
  private const MONEY_FIELDS = [
    'field_agora_base_remuneration',
    'field_agora_base_severance',
  ];

  /**
   * Field types that a table can sort as a number.
   */
  private const NUMERIC_FIELD_TYPES = ['decimal', 'float', 'integer'];

  /**
   * Field types that a table sorts lexically - the failure being guarded.
   */
  private const TEXTUAL_FIELD_TYPES = [
    'list_string',
    'string',
    'string_long',
    'text',
    'text_long',
    'text_with_summary',
  ];

  /**
   * Taxonomy references, bundle => field name => the vocabulary it is tied to.
   *
   * A term reference with no `target_bundles` accepts terms from EVERY
   * vocabulary, which would let a `financial year` term be chosen as a
   * `document type`. The restriction is the whole point of having five
   * vocabularies rather than one, so it is asserted rather than assumed.
   */
  private const TERM_REFERENCES = [
    'agora_base_document' => [
      'field_agora_base_document_type' => 'agora_base_document_type',
      'field_agora_base_area' => 'agora_base_area',
      'field_agora_base_financial_year' => 'agora_base_financial_year',
    ],
    'agora_base_person' => [
      'field_agora_base_area' => 'agora_base_area',
    ],
  ];

  /**
   * Tests that the shared spine is present, complete and no larger than stated.
   */
  public function testSharedSpine(): void {
    $path = dirname(__FILE__, 4);
    $storage = new FileStorage($path . '/config');
    $assertions = 0;

    // -- The census, asserted rather than printed ----------------------------
    // D-026 fixes these two numbers, and they are the denominator every loop
    // below runs over: a constant silently shortened to one entry would leave
    // every one of those loops passing over almost nothing. Asserting the size
    // is what stops "0 failures" from also meaning "0 subjects" (I-045).
    // They are asserted rather than printed because a test cannot print here
    // at all - see the closing block of this method.
    $this->assertCount(5, self::VOCABULARIES, 'D-026 names five vocabularies and only five.');
    $assertions++;
    $this->assertCount(6, self::FIELD_STORAGES, 'T-601 ships six shared field storages.');
    $assertions++;

    // -- The five vocabularies -----------------------------------------------
    foreach (self::VOCABULARIES as $vid) {
      $name = 'taxonomy.vocabulary.' . $vid;
      $data = $storage->read($name);
      $this->assertIsArray($data, "$name must be shipped in config/.");
      $assertions++;
      $this->assertSame($vid, $data['vid'], "$name must declare its own machine name.");
      $assertions++;
      // D-033: the label is ENGLISH, and an empty one would mean the vocabulary
      // shipped with nothing at all to read. This comment used to say the label
      // "carries the Spanish", which was the ruling D-033 overturned - a stale
      // comment left beside a corrected file is the same failure as a stale
      // justification left in the word list, one file over.
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

    // The expected list is the UNION of every row's declared storages, not
    // T-601's six. T-612 added eight, and the honest way to admit them was to
    // grow the declared list — the assertion itself is byte-for-byte what it
    // was, still failing in both directions. Relaxing it to a `>=` or dropping
    // it would have been the weakened gate T-601's row named in advance.
    $storages = $storage->listAll('field.storage.node.');
    sort($storages);
    $expected = array_map(
      static fn (string $field_name): string => 'field.storage.node.' . $field_name,
      array_keys(array_merge(self::FIELD_STORAGES, self::T612_FIELD_STORAGES)),
    );
    sort($expected);
    $this->assertSame($expected, $storages, 'config/ must ship exactly the field storages the content-model rows declare.');
    $assertions++;

    // -- The one thing that does NOT live in config/ -------------------------
    // D-032 step 4b: `SiteExporter::isAction()` routes core/System/User default
    // config into the export's regenerated `recipe.yml`, never into `config/`.
    // Installing `datetime_range` is exactly such a change, and it is what
    // makes `field_agora_base_period`'s `daterange` type resolvable on a clean
    // install. Copying `config/` alone would have dropped it with no error, so
    // the transplant gets an assertion rather than a promise.
    $recipe = Yaml::decode(file_get_contents($path . '/recipe.yml'));
    $this->assertContains('datetime_range', $recipe['install'], 'recipe.yml must install datetime_range, or field_agora_base_period cannot import.');
    $assertions++;

    // -- The assertion count, asserted rather than printed -------------------
    // T-601 asks this test to state its assertion count, and a test cannot
    // state anything by printing it: PHPUnit turns ANY output a test emits
    // into a `PHPUnit\Framework\Exception`. Writing to STDERR does not dodge
    // `beStrictAboutOutputDuringTests` - the first version of this file did
    // exactly that and failed pipeline 934619 with all 50 of its assertions
    // passing. The number is asserted here instead, reaches the CI log through
    // PHPUnit's own "OK (N tests, N assertions)" line, and the config
    // denominators are printed by tests/bin/config-inventory, in the
    // agora-invariants job, where output is free.
    //
    // Derived from the two constants above rather than written as a literal,
    // so that growing the model updates it: three assertions per vocabulary
    // (shipped, own vid, non-empty label), five per field storage (shipped,
    // own name, node, type, cardinality), plus the two census assertions, the
    // two `target_type` checks, the two set-equality checks and the one
    // `recipe.yml` check. What it still catches is the failure this whole file
    // guards against - a loop that ran over nothing at all.
    $expected_assertions = 2
      + (3 * count(self::VOCABULARIES))
      + (5 * count(self::FIELD_STORAGES))
      + 2 + 2 + 1;
    $this->assertSame($expected_assertions, $assertions, 'Every assertion loop in this test must have run to completion.');
  }

  /**
   * Tests the Document and Person bundles and every field on them (T-612).
   */
  public function testDocumentAndPersonBundles(): void {
    $path = dirname(__FILE__, 4);
    $storage = new FileStorage($path . '/config');
    $assertions = 0;

    // -- The census, for the same reason as in testSharedSpine() -------------
    // These three constants are the denominators every loop below runs over. A
    // constant silently emptied would leave each loop passing over nothing,
    // which is the shape of a green gate that proves nothing (I-045).
    $this->assertCount(2, self::BUNDLES, 'T-612 ships two of D-026\'s six bundles.');
    $assertions++;
    $this->assertCount(8, self::T612_FIELD_STORAGES, 'T-612 creates eight new field storages.');
    $assertions++;
    $this->assertCount(2, self::MONEY_FIELDS, 'Art. 8.1.f) names two money fields on a Person.');
    $assertions++;

    // -- Exactly these node types, both directions ---------------------------
    // D-026 fixed the count at six and the row that produced this test named
    // the failure it is guarding against: the declaración de bienes modelled
    // as a Person SUBTYPE would have been a seventh bundle by the back door.
    // It is an unconditional optional field instead, and this assertion is
    // what makes that a fact about config/ rather than an intention.
    // T-613 and T-614 admit their four bundles by GROWING self::BUNDLES.
    $node_types = $storage->listAll('node.type.');
    sort($node_types);
    $expected_types = array_map(
      static fn (string $bundle): string => 'node.type.' . $bundle,
      array_keys(self::BUNDLES),
    );
    sort($expected_types);
    $this->assertSame($expected_types, $node_types, 'config/ must ship exactly the node types the content-model rows declare.');
    $assertions++;

    foreach (self::BUNDLES as $bundle => $definition) {
      // -- The bundle itself -------------------------------------------------
      $name = 'node.type.' . $bundle;
      $data = $storage->read($name);
      $this->assertIsArray($data, "$name must be shipped in config/.");
      $assertions++;
      $this->assertSame($bundle, $data['type'], "$name must declare its own machine name.");
      $assertions++;
      $this->assertSame($definition['name'], $data['name'], "$name must carry its English label (D-033).");
      $assertions++;
      $this->assertNotEmpty($data['description'], "$name must explain what belongs in it; the description is the only help a clerk gets on the node/add screen.");
      $assertions++;

      // -- Exactly these fields, both directions -----------------------------
      $prefix = 'field.field.node.' . $bundle . '.';
      $attached = $storage->listAll($prefix);
      sort($attached);
      $expected_fields = array_map(
        static fn (string $field_name): string => $prefix . $field_name,
        array_keys($definition['fields']),
      );
      sort($expected_fields);
      $this->assertSame($expected_fields, $attached, "$bundle must have exactly the fields T-612 declares - no more, and no fewer.");
      $assertions++;

      // -- The displays ------------------------------------------------------
      // A field absent from the form display cannot be filled in, and a field
      // absent from the view display is never published. Both are silent
      // failures: the config imports, the site installs, and the data is
      // simply unreachable.
      $form_display = $storage->read('core.entity_form_display.node.' . $bundle . '.default');
      $this->assertIsArray($form_display, "$bundle must ship a default form display.");
      $assertions++;
      $view_display = $storage->read('core.entity_view_display.node.' . $bundle . '.default');
      $this->assertIsArray($view_display, "$bundle must ship a default view display.");
      $assertions++;

      // -- Every field on the bundle ----------------------------------------
      foreach ($definition['fields'] as $field_name => $type) {
        $name = $prefix . $field_name;
        $field = $storage->read($name);
        $this->assertIsArray($field, "$name must be shipped in config/.");
        $assertions++;
        $this->assertSame($field_name, $field['field_name'], "$name must declare its own machine name.");
        $assertions++;
        $this->assertSame($bundle, $field['bundle'], "$name must be attached to $bundle.");
        $assertions++;
        $this->assertSame($type, $field['field_type'], "$name must be of type $type.");
        $assertions++;
        // D-033: English, and present at all. An empty label renders a form
        // element and a table column with nothing to read.
        $this->assertNotEmpty($field['label'], "$name must carry a label.");
        $assertions++;

        $this->assertArrayHasKey($field_name, $form_display['content'], "$field_name must be editable on $bundle's form.");
        $assertions++;
        $this->assertArrayHasKey($field_name, $view_display['content'], "$field_name must be rendered on $bundle.");
        $assertions++;
        // WCAG 2.2 AA, 1.3.1. This portal publishes salaries and contracts:
        // a value rendered with its label hidden is a number with no name,
        // and a screen-reader user cannot tell 45000 the salary from 45000
        // the severance payment.
        $this->assertSame('above', $view_display['content'][$field_name]['label'], "$field_name must render its label visibly on $bundle.");
        $assertions++;
      }
    }

    // -- The eight new field storages ----------------------------------------
    foreach (self::T612_FIELD_STORAGES as $field_name => $type) {
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
      $this->assertSame(1, $data['cardinality'], "$name must be single-valued.");
      $assertions++;
    }

    // -- The two money fields are NUMERIC, not text --------------------------
    // The success criterion of the row, and the same point as `importe`.
    foreach (self::MONEY_FIELDS as $field_name) {
      $field = $storage->read('field.field.node.agora_base_person.' . $field_name);
      $this->assertContains($field['field_type'], self::NUMERIC_FIELD_TYPES, "$field_name must be numeric: a table has to sort it.");
      $assertions++;
      $this->assertNotContains($field['field_type'], self::TEXTUAL_FIELD_TYPES, "$field_name must not be a text field.");
      $assertions++;
      $storage_data = $storage->read('field.storage.node.' . $field_name);
      $this->assertSame('decimal', $storage_data['type'], "$field_name must be stored as decimal.");
      $assertions++;
      // Money has cents. An integer or a scale of 0 silently truncates every
      // amount it stores, and nothing in the UI would say so.
      $this->assertSame(2, $storage_data['settings']['scale'], "$field_name must keep two decimal places.");
      $assertions++;
    }

    // -- Every term reference is tied to ONE vocabulary ----------------------
    foreach (self::TERM_REFERENCES as $bundle => $map) {
      foreach ($map as $field_name => $vid) {
        $field = $storage->read('field.field.node.' . $bundle . '.' . $field_name);
        $this->assertSame('default:taxonomy_term', $field['settings']['handler'], "$field_name on $bundle must reference taxonomy terms.");
        $assertions++;
        $this->assertSame([$vid], array_keys($field['settings']['handler_settings']['target_bundles']), "$field_name on $bundle must be restricted to the $vid vocabulary.");
        $assertions++;
      }
    }

    // -- The document file is a media reference, restricted to documents -----
    // `media.type.document` is not ours: it comes from core's
    // `document_media_type` recipe, applied by `drupal_cms_media`, which
    // recipe.yml lists under `recipes:`. That is why the reference resolves on
    // a clean install without this repository shipping a media type.
    $media_storage = $storage->read('field.storage.node.field_agora_base_document_file');
    $this->assertSame('media', $media_storage['settings']['target_type'], 'The document file must reference a media entity.');
    $assertions++;
    $media_field = $storage->read('field.field.node.agora_base_document.field_agora_base_document_file');
    $this->assertSame('default:media', $media_field['settings']['handler'], 'The document file must use the media selection handler.');
    $assertions++;
    $this->assertSame(['document'], array_keys($media_field['settings']['handler_settings']['target_bundles']), 'The document file must be restricted to the document media type.');
    $assertions++;

    // -- The declaración de bienes is a file, and it is OPTIONAL -------------
    // Art. 8.1.h) scopes it to elected local representatives. The ruling on the
    // row: an unconditional optional field, never a Person subtype.
    $declaration = $storage->read('field.field.node.agora_base_person.field_agora_base_declaration');
    $this->assertSame('file', $declaration['field_type'], 'The asset declaration must be an attached file.');
    $assertions++;
    $this->assertFalse($declaration['required'], 'The asset declaration must be optional: it is required of elected representatives only, and a required field would stop every other Person from being saved at all.');
    $assertions++;
    $this->assertSame('pdf', $declaration['settings']['file_extensions'], 'The asset declaration is published as a PDF.');
    $assertions++;

    // -- Person's title is a name, not a "Title" -----------------------------
    $override = $storage->read('core.base_field_override.node.agora_base_person.title');
    $this->assertIsArray($override, 'Person must override the node title label.');
    $assertions++;
    $this->assertSame('Full name', $override['label'], 'A person in an organisation chart has a name, not a title.');
    $assertions++;

    // -- The assertion count, asserted rather than printed -------------------
    // Same mechanic and same reason as testSharedSpine(): PHPUnit turns ANY
    // output a test emits into an exception, so the count is asserted here and
    // reaches the CI log through PHPUnit's own "OK (N tests, N assertions)"
    // line. Derived from the constants so that growing the model updates it.
    $field_instances = 0;
    foreach (self::BUNDLES as $definition) {
      $field_instances += count($definition['fields']);
    }
    $term_references = 0;
    foreach (self::TERM_REFERENCES as $map) {
      $term_references += count($map);
    }
    $expected_assertions = 3
      + 1
      + (7 * count(self::BUNDLES))
      + (8 * $field_instances)
      + (5 * count(self::T612_FIELD_STORAGES))
      + (4 * count(self::MONEY_FIELDS))
      + (2 * $term_references)
      + 3
      + 3
      + 2;
    $this->assertSame($expected_assertions, $assertions, 'Every assertion loop in this test must have run to completion.');
  }

}
