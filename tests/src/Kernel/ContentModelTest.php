<?php

declare(strict_types=1);

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Config\FileStorage;
use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests the content model (T-601, T-612 and T-613, under D-026).
 *
 * SCOPE, in three methods that own three different rows.
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
 * `testFinancialRegimeBundles()` — T-613. The three legal regimes, which are
 * three bundles and not one bundle with a `regime` dropdown because Spanish
 * administrative law makes them three: a convenio is defined by its exclusion
 * from public-procurement law, and a subvención has its own statute and its own
 * national register (D-026, option D refuted). Each attaches T-601's shared
 * six-field pattern and then ONLY what its own statute names — Contract four
 * more, Agreement one, Grant none. A bundle that adds nothing is the correct
 * outcome, not an oversight.
 *
 * `testDatasetBundle()` — T-614. The sixth and LAST bundle, and the only one
 * that comes from neither the transparency act nor a financial statute: the
 * public sector information reuse act, as amended by the Open Data Directive,
 * plus an EU implementing regulation on high value datasets that binds
 * Spanish local bodies directly, without transposition. Dataset is where the
 * machine-readable budget execution table is published, which is why there is
 * no seventh bundle: budget is a Document plus a Dataset, and the Dataset's
 * rendered table is the accessible source of truth any chart is built on.
 * Three of its seven fields are ATTACHED rather than created, and this method
 * ASSERTS that reuse rather than describing it.
 *
 * WHAT MAKES D-026 FALSIFIABLE, and what it deliberately is NOT. The row asked
 * for an assertion that "no bundle carries a field its own regime does not
 * name". A test cannot assert against a statute: the oracle exists only as
 * prose in D-026 and in the research file. So what runs here is a LITERAL
 * expected field set per bundle, transcribed from D-026, asserted for SET
 * EQUALITY IN BOTH DIRECTIONS — a missing field fails, and a field that
 * accreted without being written into that literal set fails too. That is the
 * union typing D-026 refuses, caught at config level where it is cheap and
 * exact. The set SIZES are asserted rather than printed, for the reason the
 * closing block of every method here repeats; `tests/bin/config-inventory`
 * prints the per-bundle denominators, in a job whose whole contract is
 * printing counts.
 *
 * WHY THE SET-EQUALITY CONSTANTS GROW RATHER THAN THE ASSERTIONS RELAXING.
 * Both methods assert set equality in BOTH directions — a missing object fails
 * and an unannounced one fails too. T-612 legitimately added eight field
 * storages, which is exactly the growth T-601's row warned would tempt someone
 * to weaken the check. It was not weakened: the DECLARED LIST grew and the
 * assertion stayed identical, so an accretion nobody declared still fails.
 * T-613 added three bundles and five storages the same way, and T-614 the
 * sixth bundle and four more. The model is now COMPLETE at six bundles, so
 * the next growth this file sees should be none. What is forbidden is a
 * hard-coded TOTAL count of config objects, which would have to be edited on
 * every legitimate growth and would teach exactly the wrong habit; the
 * denominator is printed by tests/bin/config-inventory instead. The one
 * number that IS hard-coded is six — the bundle count D-026 signed — because
 * a number a decision fixes is not a denominator that grows.
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
   * The field storages T-613 had to create, machine name => field type.
   *
   * REUSE BEFORE CREATION, again. All six of T-601's shared storages are
   * ATTACHED by the three regimes and none is recreated, which is the whole
   * point of D-026 calling them "created once and attached three times".
   * `agora_base_procedure_type` is a VOCABULARY T-601 already ships — only the
   * reference field pointing at it is new, because T-601 created
   * entity_reference storages for `área` and `estado` only, exactly as T-612
   * found for `document type` and `financial year`.
   *
   * Every entry exists because no shipped storage fitted:
   *  - `procedure_type` — see above; art. 8.1.a) names the procurement
   *    procedure and the law then requires a statistic aggregated BY it, which
   *    is why it is a term reference and not free text,
   *  - `tender_amount` — art. 8.1.a) names TWO amounts on one contract
   *    (licitación and adjudicación) and one storage cannot be attached twice
   *    to the same bundle, so `field_agora_base_amount` could serve at most
   *    one. The shared storage carries the award amount, this one the tender,
   *  - `bidder_count` — the first integer in the model; a count, not a sum,
   *  - `modifications` — prose about what changed after award; `summary` is
   *    already attached to Document and Person for a different purpose and
   *    reusing it would put "summary" in the machine name of a contract's
   *    modification history,
   *  - `obligations` — art. 8.1.b)'s `obligaciones económicas convenidas`, the
   *    ONE field Agreement adds. Decimal, not prose: D-026's own rule counts
   *    it as the numeric field that lets convenios clear the bar for a bundle
   *    of their own, and a table has to sort it.
   *
   * FIELD-NAME CEILING, stated because Drupal's is 32 characters and
   * `field_agora_base_` spends 17 of them, leaving 15. The longest name here
   * is `procedure_type` at 14, so `field_agora_base_procedure_type` is 31 —
   * tying `field_agora_base_financial_year`, the tightest name in the model.
   * The others are 30, 30, 29 and 28. Nothing is near the ceiling by accident.
   */
  private const T613_FIELD_STORAGES = [
    'field_agora_base_bidder_count' => 'integer',
    'field_agora_base_modifications' => 'text_long',
    'field_agora_base_obligations' => 'decimal',
    'field_agora_base_procedure_type' => 'entity_reference',
    'field_agora_base_tender_amount' => 'decimal',
  ];

  /**
   * The field storages T-614 had to create, machine name => field type.
   *
   * REUSE BEFORE CREATION, for the last time in this model. Three of Dataset's
   * seven fields are ATTACHED and are therefore absent here —
   * `field_agora_base_summary` (T-612) carries the description,
   * `field_agora_base_area` (T-601) and `field_agora_base_financial_year`
   * (T-612) are attached unchanged. DATASET_REUSED_STORAGES below asserts that
   * absence rather than leaving it as a claim in a comment.
   *
   * Every entry exists because no shipped storage fitted:
   *  - `distribution` — the model's second file field. Reusing
   *    `field_agora_base_declaration` would have put "declaration" in the
   *    machine name of a dataset distribution, and its instance is locked to
   *    PDF. Reusing `field_agora_base_document_file` was worse: that one
   *    references a MEDIA entity restricted to core's `document` media type,
   *    whose extension list holds no CSV, no JSON and no XML, so a dataset
   *    could not have been attached to it at all,
   *  - `format` — stated as a value rather than inferred from the file name,
   *    because a catalogue can filter on a value and cannot filter on a
   *    suffix. A list, so the value set is closed,
   *  - `licence` — a dataset with no licence stated is not reusable in
   *    practice, whatever the statute says,
   *  - `frequency` — how often the file is republished; the one thing
   *    somebody reusing the data needs that the file itself cannot say.
   *
   * FIELD-NAME CEILING. `field_agora_base_` spends 17 of Drupal's 32, leaving
   * 15. These four names are 12, 6, 7 and 9 characters, so the longest is
   * `field_agora_base_distribution` at 29 — two short of the model's tightest
   * pair (`field_agora_base_financial_year` and
   * `field_agora_base_procedure_type`, both 31). `update_frequency` would have
   * been 16 and would NOT have fitted: the field is `frequency` and its LABEL
   * is "Update frequency".
   */
  private const T614_FIELD_STORAGES = [
    'field_agora_base_distribution' => 'file',
    'field_agora_base_format' => 'list_string',
    'field_agora_base_frequency' => 'list_string',
    'field_agora_base_licence' => 'list_string',
  ];

  /**
   * The sixth and last bundle, with every field attached to it.
   *
   * Seven fields, transcribed from the T-614 row: title (a base field, so not
   * listed), description, distribution file, format, licence, área, financial
   * year and update frequency. Asserted for set equality in both directions
   * against `config/`, exactly as the other five bundles are.
   */
  private const DATASET_BUNDLE = [
    'agora_base_dataset' => [
      'name' => 'Dataset',
      'fields' => [
        'field_agora_base_summary' => 'text_long',
        'field_agora_base_distribution' => 'file',
        'field_agora_base_format' => 'list_string',
        'field_agora_base_licence' => 'list_string',
        'field_agora_base_frequency' => 'list_string',
        'field_agora_base_area' => 'entity_reference',
        'field_agora_base_financial_year' => 'entity_reference',
      ],
    ],
  ];

  /**
   * The storages Dataset ATTACHES rather than creates.
   *
   * "Reuse before creation" is the instruction every content-model row carries,
   * and until now it has been honoured in prose. Here it is a test: each of
   * these must be absent from T614_FIELD_STORAGES AND already attached to some
   * other bundle. A storage this row had quietly duplicated would fail both.
   */
  private const DATASET_REUSED_STORAGES = [
    'field_agora_base_area',
    'field_agora_base_financial_year',
    'field_agora_base_summary',
  ];

  /**
   * Dataset's three list fields, machine name => how many values it allows.
   *
   * The size is asserted because an `allowed_values` list silently emptied
   * leaves a select element with nothing in it, and the field then imports,
   * renders and can never be filled in — a failure with no error anywhere.
   */
  private const DATASET_LIST_FIELDS = [
    'field_agora_base_format' => 5,
    'field_agora_base_frequency' => 7,
    'field_agora_base_licence' => 4,
  ];

  /**
   * Taxonomy references on Dataset, field name => the vocabulary it is tied to.
   *
   * Same reason as T-612's and T-613's: a term reference with no
   * `target_bundles` accepts terms from EVERY vocabulary, so without this an
   * area term could be chosen as the financial year of a budget dataset.
   */
  private const DATASET_TERM_REFERENCES = [
    'agora_base_dataset' => [
      'field_agora_base_area' => 'agora_base_area',
      'field_agora_base_financial_year' => 'agora_base_financial_year',
    ],
  ];

  /**
   * The number of node types D-026 signed, and the reason six is hard-coded.
   *
   * Every other total in this file is forbidden, because the model grows and a
   * gate that must be relaxed on every growth teaches people to relax gates.
   * This one is different in kind: D-026 FIXED the bundle count at six and
   * named the failure mode — a seventh bundle arriving by the back door, as a
   * Person subtype or as a Budget type. The model is complete, so this number
   * is a decision rather than a denominator.
   */
  private const BUNDLE_COUNT = 6;

  /**
   * T-601's shared pattern, by machine name: the six every regime attaches.
   *
   * `objeto · importe · periodo · contraparte · área · estado`. Asserted
   * against `config/` for each of the three bundles, never against the
   * constants below — asserting a constant against a constant would pass with
   * nothing built, which is the tautology T-601's row was corrected for.
   */
  private const SHARED_PATTERN = [
    'field_agora_base_subject',
    'field_agora_base_amount',
    'field_agora_base_period',
    'field_agora_base_counterparty',
    'field_agora_base_area',
    'field_agora_base_status',
  ];

  /**
   * The four fields art. 8.1.a) names that ONLY Contract carries.
   *
   * Asserted twice over: present on Contract, and absent from Agreement and
   * Grant. Set equality already implies the absence, but the union-typing
   * failure is the thing D-026 exists to prevent, so it is named explicitly
   * rather than left to be inferred from a set comparison.
   */
  private const CONTRACT_EXTRAS = [
    'field_agora_base_procedure_type',
    'field_agora_base_tender_amount',
    'field_agora_base_bidder_count',
    'field_agora_base_modifications',
  ];

  /**
   * The three regime bundles T-613 ships, with every field attached to each.
   *
   * This is the literal set transcribed from D-026, and it is the oracle the
   * set-equality assertion compares `config/` against in both directions.
   * Contract 10 fields, Agreement 7, Grant 6 — the shared six plus four, plus
   * one, plus nothing.
   */
  private const REGIME_BUNDLES = [
    'agora_base_contract' => [
      'name' => 'Contract',
      'fields' => [
        'field_agora_base_subject' => 'string',
        'field_agora_base_counterparty' => 'string',
        'field_agora_base_procedure_type' => 'entity_reference',
        'field_agora_base_tender_amount' => 'decimal',
        'field_agora_base_amount' => 'decimal',
        'field_agora_base_bidder_count' => 'integer',
        'field_agora_base_period' => 'daterange',
        'field_agora_base_modifications' => 'text_long',
        'field_agora_base_area' => 'entity_reference',
        'field_agora_base_status' => 'entity_reference',
      ],
    ],
    'agora_base_agreement' => [
      'name' => 'Agreement',
      'fields' => [
        'field_agora_base_subject' => 'string',
        'field_agora_base_counterparty' => 'string',
        'field_agora_base_amount' => 'decimal',
        'field_agora_base_obligations' => 'decimal',
        'field_agora_base_period' => 'daterange',
        'field_agora_base_area' => 'entity_reference',
        'field_agora_base_status' => 'entity_reference',
      ],
    ],
    'agora_base_grant' => [
      'name' => 'Grant',
      'fields' => [
        'field_agora_base_subject' => 'string',
        'field_agora_base_counterparty' => 'string',
        'field_agora_base_amount' => 'decimal',
        'field_agora_base_period' => 'daterange',
        'field_agora_base_area' => 'entity_reference',
        'field_agora_base_status' => 'entity_reference',
      ],
    ],
  ];

  /**
   * Every money field on the three regimes, bundle => field names.
   *
   * Same load-bearing point as `retribución` and `indemnización` on a Person:
   * a transparency portal publishes these so they can be COMPARED, and a table
   * cannot sort a string. Five in all — Contract's tender and award amounts,
   * Agreement's total and its agreed financial obligations, Grant's amount.
   */
  private const REGIME_MONEY_FIELDS = [
    'agora_base_contract' => [
      'field_agora_base_amount',
      'field_agora_base_tender_amount',
    ],
    'agora_base_agreement' => [
      'field_agora_base_amount',
      'field_agora_base_obligations',
    ],
    'agora_base_grant' => [
      'field_agora_base_amount',
    ],
  ];

  /**
   * Taxonomy references on the regimes, bundle => field name => vocabulary.
   *
   * Same reason as T-612's: a term reference with no `target_bundles` accepts
   * terms from EVERY vocabulary, so without this a financial-year term could be
   * chosen as a contract's procurement procedure — and the statistic art.
   * 8.1.a) requires is aggregated by exactly that field.
   */
  private const REGIME_TERM_REFERENCES = [
    'agora_base_contract' => [
      'field_agora_base_procedure_type' => 'agora_base_procedure_type',
      'field_agora_base_area' => 'agora_base_area',
      'field_agora_base_status' => 'agora_base_status',
    ],
    'agora_base_agreement' => [
      'field_agora_base_area' => 'agora_base_area',
      'field_agora_base_status' => 'agora_base_status',
    ],
    'agora_base_grant' => [
      'field_agora_base_area' => 'agora_base_area',
      'field_agora_base_status' => 'agora_base_status',
    ],
  ];

  /**
   * The three legal citations D-033 permits, and the only Spanish in config/.
   *
   * D-033 ruled every shipped label and description ENGLISH, with exactly three
   * exceptions — terms where the English is a correct translation and still the
   * wrong word. Each names its Spanish term ONCE, in a description, as a legal
   * citation. That is a citation, not bilingual UI.
   *
   * Config object name => the Spanish term its `description` must contain.
   * A FOURTH is a D-033 amendment, not an implementer's call: T-612 wanted
   * `LCSP` and correctly declined. The bound is enforced below by scanning
   * every shipped file for a byte above 0x7F, so a fourth term cannot arrive
   * unnoticed — and note that only two of the three carry an accent, which is
   * why `convenio` needs its own containment assertion.
   */
  private const LEGAL_CITATIONS = [
    'field.field.node.agora_base_contract.field_agora_base_amount' => 'importe de adjudicación',
    'node.type.agora_base_agreement' => 'convenio',
    'node.type.agora_base_grant' => 'subvención',
  ];

  /**
   * The only shipped config objects that may contain a byte above 0x7F.
   *
   * `convenio` is pure ASCII, so it is absent here by arithmetic rather than by
   * omission. Audited with a byte range and never with a `[^\x00-\x7F]` bracket
   * class, which GNU grep does not read the way it looks.
   */
  private const NON_ASCII_OBJECTS = [
    'field.field.node.agora_base_contract.field_agora_base_amount',
    'node.type.agora_base_grant',
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
   * The six table views (T-615), one per bundle, with the path each answers on.
   *
   * WHAT THE COLUMNS ARE IS NOT WRITTEN HERE, and that is the point. The
   * expected column set is DERIVED from what `config/` attaches to the bundle,
   * and asserted for set equality in BOTH directions against what the view
   * declares. Transcribing the columns into this file would let two
   * hand-written lists agree with each other while both disagreed with the
   * model, which is exactly the failure a set-equality assertion exists to
   * catch.
   *
   * The `facets` entry IS transcribed, from the measurement recorded in the
   * T-615 row, and is then cross-checked against the derived intersection of
   * the spine with the bundle's own fields. Two independent sources have to
   * agree: a view that exposes a filter its bundle does not carry fails, and so
   * does a bundle that grows a spine field its view does not expose.
   */
  private const TABLE_VIEWS = [
    'agora_base_documents' => [
      'bundle' => 'agora_base_document',
      'label' => 'Documents',
      'path' => 'documents',
      'facets' => [
        'field_agora_base_area',
        'field_agora_base_financial_year',
      ],
    ],
    'agora_base_people' => [
      'bundle' => 'agora_base_person',
      'label' => 'People',
      'path' => 'people',
      'facets' => [
        'field_agora_base_area',
      ],
    ],
    'agora_base_contracts' => [
      'bundle' => 'agora_base_contract',
      'label' => 'Contracts',
      'path' => 'contracts',
      'facets' => [
        'field_agora_base_area',
        'field_agora_base_status',
      ],
    ],
    'agora_base_agreements' => [
      'bundle' => 'agora_base_agreement',
      'label' => 'Agreements',
      'path' => 'agreements',
      'facets' => [
        'field_agora_base_area',
        'field_agora_base_status',
      ],
    ],
    'agora_base_grants' => [
      'bundle' => 'agora_base_grant',
      'label' => 'Grants',
      'path' => 'grants',
      'facets' => [
        'field_agora_base_area',
        'field_agora_base_status',
      ],
    ],
    'agora_base_datasets' => [
      'bundle' => 'agora_base_dataset',
      'label' => 'Datasets',
      'path' => 'datasets',
      'facets' => [
        'field_agora_base_area',
        'field_agora_base_financial_year',
      ],
    ],
  ];

  /**
   * The BASE fields a view column may show on top of the bundle's own fields.
   *
   * Exactly one: the node title, which is the row's identifier and the link to
   * the record. Everything else must be a `field.field.node.<bundle>.*` object,
   * so `created`, `uid`, `nid` or a field belonging to another regime all fail
   * the set-equality assertion below.
   *
   * The T-615 row phrases the criterion as "the set of field columns equals the
   * set of fields attached to that bundle". Read strictly that forbids a title
   * column, which would leave a table of contracts with no contract name and no
   * link to the record - an accessibility defect, not a purer assertion. The
   * exception is therefore ONE named base field, declared here so it is part of
   * what the test asserts rather than a hole in it: the allowlist's own size is
   * asserted, so widening it is a visible change to this file.
   */
  private const VIEW_BASE_FIELD_COLUMNS = ['title'];

  /**
   * The facet spine (T-615, from T-603): field name => its vocabulary.
   *
   * `area`, `financial year`, `status` - one spine for the whole model, and
   * [andres] ruled the mechanism: CORE VIEWS EXPOSED FILTERS. No Search API, no
   * Facets, no new dependency; three exposed selects is what a spine is at this
   * size and it adds nothing a marketplace reviewer has to justify.
   *
   * IT DOES NOT APPLY UNIFORMLY, and nothing here tries to make it. Measured in
   * T-612 and re-measured against `config/` by the assertions below: Document
   * and Dataset carry `area` and `financial year` but no `status`; Person
   * carries `area` alone; and the three financial regimes carry `area` and
   * `status` but NOT `financial year` - their temporal field is `period`, a
   * date RANGE naming the term of the contract, which is not a taxonomy facet
   * and is not pretended to be one. Adding a financial year to them to square
   * the spine would be attaching a field the regime's own statute does not
   * name, which is precisely the accretion D-026 forbids and precisely what
   * `testFinancialRegimeBundles()` fails on.
   */
  private const FACET_SPINE = [
    'field_agora_base_area' => 'agora_base_area',
    'field_agora_base_financial_year' => 'agora_base_financial_year',
    'field_agora_base_status' => 'agora_base_status',
  ];

  /**
   * The two surfaces above the six per-bundle tables (T-603).
   *
   * `field_columns` are the shipped columns that are `field.field.node.*`
   * objects; `dropped` are fields the bundles all carry that this surface
   * deliberately does not show. Their union must equal the COMPUTED
   * intersection of the listed bundles' fields - see `testBaseSurfaces()`.
   *
   * The paths are short, English and at the site root, because they are what
   * a citizen is handed in a link.
   */
  private const BASE_SURFACES = [
    'agora_base_publications' => [
      'label' => 'All publications',
      'path' => 'publications',
      'bundles' => [
        'agora_base_document',
        'agora_base_person',
        'agora_base_contract',
        'agora_base_agreement',
        'agora_base_grant',
        'agora_base_dataset',
      ],
      'columns' => ['title', 'type', 'field_agora_base_area', 'changed'],
      'field_columns' => ['field_agora_base_area'],
      'dropped' => [],
      'facets' => ['field_agora_base_area'],
      'expose_bundle' => TRUE,
      'menu' => [
        'title' => 'All publications',
        'weight' => -50,
        'parent' => '',
      ],
    ],
    'agora_base_library' => [
      'label' => 'Document library',
      'path' => 'library',
      'bundles' => [
        'agora_base_document',
        'agora_base_dataset',
      ],
      'columns' => [
        'title',
        'type',
        'field_agora_base_area',
        'field_agora_base_financial_year',
      ],
      'field_columns' => [
        'field_agora_base_area',
        'field_agora_base_financial_year',
      ],
      'dropped' => ['field_agora_base_summary'],
      'facets' => [
        'field_agora_base_area',
        'field_agora_base_financial_year',
      ],
      'expose_bundle' => FALSE,
      'menu' => [
        'title' => 'Document library',
        'weight' => -49,
        'parent' => '',
      ],
    ],
  ];

  /**
   * The node BASE fields a T-603 surface may show as a column.
   *
   * Three, and the reason each is safe is the same: it is populated on every
   * node that exists at all, whatever its bundle. A `field.field.node.*`
   * object is not safe that way, which is what the intersection assertion in
   * `testBaseSurfaces()` exists to enforce.
   */
  private const SURFACE_BASE_FIELD_COLUMNS = ['title', 'type', 'changed'];

  /**
   * The view whose menu link the six per-bundle tables hang from.
   */
  private const MENU_PARENT_VIEW = 'agora_base_publications';

  /**
   * The menu links on the six per-bundle views (T-603's carried debt).
   *
   * Order is D-026's own order for the six bundles, which is the order the
   * weights encode.
   */
  private const SURFACE_MENU_CHILDREN = [
    'agora_base_documents' => [
      'title' => 'Documents',
      'weight' => 0,
      'parent' => 'views_view:views.agora_base_publications.page_1',
    ],
    'agora_base_people' => [
      'title' => 'People',
      'weight' => 1,
      'parent' => 'views_view:views.agora_base_publications.page_1',
    ],
    'agora_base_contracts' => [
      'title' => 'Contracts',
      'weight' => 2,
      'parent' => 'views_view:views.agora_base_publications.page_1',
    ],
    'agora_base_agreements' => [
      'title' => 'Agreements',
      'weight' => 3,
      'parent' => 'views_view:views.agora_base_publications.page_1',
    ],
    'agora_base_grants' => [
      'title' => 'Grants',
      'weight' => 4,
      'parent' => 'views_view:views.agora_base_publications.page_1',
    ],
    'agora_base_datasets' => [
      'title' => 'Datasets',
      'weight' => 5,
      'parent' => 'views_view:views.agora_base_publications.page_1',
    ],
  ];

  /**
   * The nine keys of a page display's `menu` mapping, in core's own order.
   *
   * Asserted as an ordered list rather than a set because that is what the
   * exporter writes, and a divergence in order means the mapping was written
   * by something other than the export loop D-032 mandates.
   */
  private const MENU_KEYS = [
    'type',
    'title',
    'description',
    'weight',
    'enabled',
    'expanded',
    'menu_name',
    'parent',
    'context',
  ];

  /**
   * The number of assertions `assertMenuLink()` makes on one menu mapping.
   */
  private const MENU_ASSERTIONS = 9;

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
    // T-601's six. T-612 added eight, T-613 five and T-614 four, and the
    // honest way to admit each was to grow the declared list — the assertion
    // itself is byte-for-byte what it was, still failing in both directions.
    // Relaxing it to a `>=` or dropping it would have been the weakened gate
    // T-601's row named in advance.
    $storages = $storage->listAll('field.storage.node.');
    sort($storages);
    $expected = array_map(
      static fn (string $field_name): string => 'field.storage.node.' . $field_name,
      array_keys(array_merge(
        self::FIELD_STORAGES,
        self::T612_FIELD_STORAGES,
        self::T613_FIELD_STORAGES,
        self::T614_FIELD_STORAGES,
      )),
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
    //
    // This is the ONE place that asserts which node types ship at all, so it
    // spans every content-model row rather than only T-612's two: a census
    // asserted in two places is a census that can disagree with itself. T-613
    // admitted its three bundles by GROWING the declared list — the assertion
    // is byte-for-byte what it was, still failing in both directions. T-614
    // added `Dataset` the same way, and with it the set is CLOSED: D-026 fixes
    // the final count at six. testDatasetBundle() asserts that number, which
    // is a different claim from this one — this is the SET, that is the COUNT
    // a decision signed — and the two cannot silently disagree, because a set
    // of any other size fails both.
    $node_types = $storage->listAll('node.type.');
    sort($node_types);
    $expected_types = array_map(
      static fn (string $bundle): string => 'node.type.' . $bundle,
      array_keys(array_merge(
        self::BUNDLES,
        self::REGIME_BUNDLES,
        self::DATASET_BUNDLE,
      )),
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

  /**
   * Tests Contract, Agreement and Grant, and only what each names (T-613).
   */
  public function testFinancialRegimeBundles(): void {
    $path = dirname(__FILE__, 4);
    $storage = new FileStorage($path . '/config');
    $assertions = 0;

    // -- The census, for the same reason as in the two methods above ---------
    // Every loop below runs over one of these four constants. A constant
    // silently emptied would leave each loop passing over nothing, which is a
    // green gate that proves nothing (I-045).
    $this->assertCount(3, self::REGIME_BUNDLES, 'D-026 makes the financial regimes THREE bundles, because Spanish administrative law makes them three.');
    $assertions++;
    $this->assertCount(5, self::T613_FIELD_STORAGES, 'T-613 creates five new field storages.');
    $assertions++;
    $this->assertCount(6, self::SHARED_PATTERN, 'T-601 ships a six-field shared pattern.');
    $assertions++;
    $this->assertCount(4, self::CONTRACT_EXTRAS, 'Art. 8.1.a) gives Contract four fields the other two regimes do not have.');
    $assertions++;

    foreach (self::REGIME_BUNDLES as $bundle => $definition) {
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

      // -- SET EQUALITY, BOTH DIRECTIONS -------------------------------------
      // This is the assertion that makes D-026 falsifiable. The expected side
      // is the literal set transcribed from D-026; the actual side is whatever
      // config/ ships. A field the regime's statute does not name fails here,
      // which is the union typing D-026 refused when it rejected one bundle
      // with a `regime` dropdown.
      $prefix = 'field.field.node.' . $bundle . '.';
      $attached = $storage->listAll($prefix);
      sort($attached);
      $expected_fields = array_map(
        static fn (string $field_name): string => $prefix . $field_name,
        array_keys($definition['fields']),
      );
      sort($expected_fields);
      $this->assertSame($expected_fields, $attached, "$bundle must carry exactly the fields its own regime names - no more, and no fewer.");
      $assertions++;
      // The set size, asserted because it cannot be printed from here. The
      // three sizes are 10, 7 and 6; tests/bin/config-inventory prints them.
      $this->assertCount(count($definition['fields']), $attached, "$bundle's field set must be the size D-026 gives it.");
      $assertions++;

      // -- All six of the shared pattern, asserted against config/ -----------
      // Against the SHIPPED set, never against $definition['fields'] - a
      // constant checked against a constant passes with nothing built.
      foreach (self::SHARED_PATTERN as $shared) {
        $this->assertContains($prefix . $shared, $attached, "$bundle must attach the shared pattern's $shared: all three regimes reuse it, created once by T-601.");
        $assertions++;
      }

      // -- The displays ------------------------------------------------------
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
        $this->assertNotEmpty($field['label'], "$name must carry a label.");
        $assertions++;
        $this->assertArrayHasKey($field_name, $form_display['content'], "$field_name must be editable on $bundle's form.");
        $assertions++;
        $this->assertArrayHasKey($field_name, $view_display['content'], "$field_name must be rendered on $bundle.");
        $assertions++;
        // WCAG 2.2 AA, 1.3.1. A contract page carries a tender amount and an
        // award amount side by side; rendered with their labels hidden they are
        // two bare numbers, and a screen-reader user cannot tell them apart.
        $this->assertSame('above', $view_display['content'][$field_name]['label'], "$field_name must render its label visibly on $bundle.");
        $assertions++;
      }
    }

    // -- Contract's four extras, present and NOT anywhere else ---------------
    $contract_fields = $storage->listAll('field.field.node.agora_base_contract.');
    foreach (self::CONTRACT_EXTRAS as $field_name) {
      $this->assertContains('field.field.node.agora_base_contract.' . $field_name, $contract_fields, "Art. 8.1.a) names $field_name and Contract must carry it.");
      $assertions++;
      // Asserted against the SHIPPED set rather than against a `read()` of a
      // name expected to be absent: FileStorage::read() returns FALSE for a
      // missing object, not NULL, so `assertNull` here passed nothing and
      // failed a correct model. Comparing sets has no such convention to get
      // wrong, and it is the same comparison the equality assertion makes.
      foreach (['agora_base_agreement', 'agora_base_grant'] as $other) {
        $this->assertNotContains(
          'field.field.node.' . $other . '.' . $field_name,
          $storage->listAll('field.field.node.' . $other . '.'),
          "$field_name belongs to art. 8.1.a) alone; putting it on $other is the union typing D-026 rejected."
        );
        $assertions++;
      }
    }

    // -- The five new field storages -----------------------------------------
    foreach (self::T613_FIELD_STORAGES as $field_name => $type) {
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

    // -- Every money field is NUMERIC, not text ------------------------------
    $money_fields = 0;
    foreach (self::REGIME_MONEY_FIELDS as $bundle => $field_names) {
      foreach ($field_names as $field_name) {
        $money_fields++;
        $field = $storage->read('field.field.node.' . $bundle . '.' . $field_name);
        $this->assertContains($field['field_type'], self::NUMERIC_FIELD_TYPES, "$field_name on $bundle must be numeric: a table has to sort it.");
        $assertions++;
        $this->assertNotContains($field['field_type'], self::TEXTUAL_FIELD_TYPES, "$field_name on $bundle must not be a text field.");
        $assertions++;
        $storage_data = $storage->read('field.storage.node.' . $field_name);
        $this->assertSame('decimal', $storage_data['type'], "$field_name must be stored as decimal.");
        $assertions++;
        // Money has cents. A scale of 0 silently truncates every amount it
        // stores, and nothing in the UI would say so.
        $this->assertSame(2, $storage_data['settings']['scale'], "$field_name must keep two decimal places.");
        $assertions++;
      }
    }

    // -- Every term reference is tied to ONE vocabulary ----------------------
    $term_references = 0;
    foreach (self::REGIME_TERM_REFERENCES as $bundle => $map) {
      foreach ($map as $field_name => $vid) {
        $term_references++;
        $field = $storage->read('field.field.node.' . $bundle . '.' . $field_name);
        $this->assertSame('default:taxonomy_term', $field['settings']['handler'], "$field_name on $bundle must reference taxonomy terms.");
        $assertions++;
        $this->assertSame([$vid], array_keys($field['settings']['handler_settings']['target_bundles']), "$field_name on $bundle must be restricted to the $vid vocabulary.");
        $assertions++;
      }
    }

    // -- D-033: three legal citations, and not a fourth ----------------------
    // Each of the three names its Spanish term once, in a description. Two of
    // them carry an accent and one does not, so the containment check and the
    // byte scan are both needed: neither alone bounds the set at three.
    foreach (self::LEGAL_CITATIONS as $object => $term) {
      $data = $storage->read($object);
      $this->assertStringContainsString($term, $data['description'], "$object must name its Spanish legal term once, in its description (D-033).");
      $assertions++;
    }

    // The bound. Every shipped config object is read as raw bytes and tested
    // for a byte above 0x7F - the same range the audit in tests/bin uses, and
    // deliberately NOT a `[^\x00-\x7F]` character class, which GNU grep does
    // not read the way it looks. Before T-613, config/ contained zero such
    // bytes; after it, exactly two objects do, and a fourth Spanish term
    // cannot arrive without failing here.
    $non_ascii = [];
    foreach ($storage->listAll() as $object) {
      $raw = file_get_contents($path . '/config/' . $object . '.yml');
      if (preg_match('/[\x80-\xFF]/', (string) $raw) === 1) {
        $non_ascii[] = $object;
      }
    }
    sort($non_ascii);
    $expected_non_ascii = self::NON_ASCII_OBJECTS;
    sort($expected_non_ascii);
    $this->assertSame($expected_non_ascii, $non_ascii, 'Only the accented legal citations D-033 permits may put a non-ASCII byte into config/; a fourth Spanish term is a D-033 amendment, not an implementer\'s call.');
    $assertions++;

    // -- The assertion count, asserted rather than printed -------------------
    // Same mechanic and same reason as the two methods above: PHPUnit turns
    // ANY output a test emits into an exception, so the count is asserted here
    // and reaches the CI log through PHPUnit's own "OK (N tests, N assertions)"
    // line. Derived from the constants so that growing the model updates it.
    $field_instances = 0;
    foreach (self::REGIME_BUNDLES as $definition) {
      $field_instances += count($definition['fields']);
    }
    $expected_assertions = 4
      + (14 * count(self::REGIME_BUNDLES))
      + (8 * $field_instances)
      + (3 * count(self::CONTRACT_EXTRAS))
      + (5 * count(self::T613_FIELD_STORAGES))
      + (4 * $money_fields)
      + (2 * $term_references)
      + count(self::LEGAL_CITATIONS)
      + 1;
    $this->assertSame($expected_assertions, $assertions, 'Every assertion loop in this test must have run to completion.');
  }

  /**
   * Tests the Dataset bundle and every field on it (T-614).
   */
  public function testDatasetBundle(): void {
    $path = dirname(__FILE__, 4);
    $storage = new FileStorage($path . '/config');
    $assertions = 0;

    // -- The census, for the same reason as in the three methods above -------
    // Every loop below runs over one of these four constants. A constant
    // silently emptied would leave each loop passing over nothing, which is a
    // green gate that proves nothing (I-045).
    $this->assertCount(1, self::DATASET_BUNDLE, 'T-614 ships the sixth and last of D-026\'s six bundles.');
    $assertions++;
    $this->assertCount(4, self::T614_FIELD_STORAGES, 'T-614 creates four new field storages.');
    $assertions++;
    $this->assertCount(3, self::DATASET_LIST_FIELDS, 'Dataset carries three list fields: format, licence and update frequency.');
    $assertions++;
    $this->assertCount(3, self::DATASET_REUSED_STORAGES, 'Three of Dataset\'s seven fields are attached, not created.');
    $assertions++;

    // -- Six bundles, and there is no seventh --------------------------------
    // D-026 fixed the count and named the two ways a seventh would arrive: a
    // Person subtype for the declaración de bienes (refused in T-612) and a
    // Budget type (refused here — budget is a Document plus this Dataset).
    // Asserted against what config/ SHIPS, never against the constants, which
    // would pass with nothing built.
    $this->assertCount(self::BUNDLE_COUNT, $storage->listAll('node.type.'), 'D-026 fixes the model at six node types; a seventh is a decision, not an implementation detail.');
    $assertions++;

    foreach (self::DATASET_BUNDLE as $bundle => $definition) {
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

      // -- SET EQUALITY, BOTH DIRECTIONS -------------------------------------
      // A field the T-614 row does not name fails here, and so does one it
      // names that never shipped.
      $prefix = 'field.field.node.' . $bundle . '.';
      $attached = $storage->listAll($prefix);
      sort($attached);
      $expected_fields = array_map(
        static fn (string $field_name): string => $prefix . $field_name,
        array_keys($definition['fields']),
      );
      sort($expected_fields);
      $this->assertSame($expected_fields, $attached, "$bundle must carry exactly the fields the T-614 row names - no more, and no fewer.");
      $assertions++;
      // The set size, asserted because it cannot be printed from here.
      // tests/bin/config-inventory prints it, in a job where output is free.
      $this->assertCount(count($definition['fields']), $attached, "$bundle's field set must be the size the T-614 row gives it.");
      $assertions++;

      // -- The displays ------------------------------------------------------
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
        $this->assertNotEmpty($field['label'], "$name must carry a label.");
        $assertions++;
        $this->assertArrayHasKey($field_name, $form_display['content'], "$field_name must be editable on $bundle's form.");
        $assertions++;
        $this->assertArrayHasKey($field_name, $view_display['content'], "$field_name must be rendered on $bundle.");
        $assertions++;
        // WCAG 2.2 AA, 1.3.1. A format and a licence rendered with their
        // labels hidden are two bare codes side by side, and nothing tells a
        // screen-reader user which of them is which.
        $this->assertSame('above', $view_display['content'][$field_name]['label'], "$field_name must render its label visibly on $bundle.");
        $assertions++;
      }
    }

    // -- The four new field storages -----------------------------------------
    foreach (self::T614_FIELD_STORAGES as $field_name => $type) {
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
      // Cardinality 1, like every other field in the model. DCAT lets a
      // dataset carry several distributions; a facet and a sortable table
      // column both stop working the moment a field holds an unbounded list,
      // so a second format is a second Dataset node. A real narrowing, and it
      // is asserted rather than left as an intention.
      $this->assertSame(1, $data['cardinality'], "$name must be single-valued.");
      $assertions++;
    }

    // -- The three list fields actually offer something to choose ------------
    // An `allowed_values` list silently emptied leaves a select element with
    // no options: the config imports, the site installs, the form renders and
    // the field can never be filled in. Nothing anywhere reports it.
    $list_options = 0;
    foreach (self::DATASET_LIST_FIELDS as $field_name => $expected_count) {
      $data = $storage->read('field.storage.node.' . $field_name);
      $this->assertCount($expected_count, $data['settings']['allowed_values'], "$field_name must offer exactly the values T-614 declares for it.");
      $assertions++;
      foreach ($data['settings']['allowed_values'] as $option) {
        $list_options++;
        $this->assertArrayHasKey('value', $option, "Every allowed value of $field_name must carry the value that is stored.");
        $assertions++;
        // D-033: English, and present at all. An option with an empty label
        // renders as a blank line in a select element.
        $this->assertNotEmpty($option['label'], "Every allowed value of $field_name must carry a label.");
        $assertions++;
      }
    }

    // -- REUSE BEFORE CREATION, asserted rather than claimed -----------------
    // Every content-model row carries this instruction and until now it lived
    // in prose. Each reused storage must be absent from what this row created
    // AND already attached to some other bundle; a storage quietly duplicated
    // for Dataset fails both halves.
    $all_instances = $storage->listAll('field.field.node.');
    foreach (self::DATASET_REUSED_STORAGES as $field_name) {
      $this->assertArrayNotHasKey($field_name, self::T614_FIELD_STORAGES, "$field_name is attached by T-614, not created by it: declaring it as new would be the duplication every row is told to avoid.");
      $assertions++;
      $elsewhere = array_filter(
        $all_instances,
        static fn (string $object): bool => str_ends_with($object, '.' . $field_name)
          && !str_contains($object, '.agora_base_dataset.'),
      );
      $this->assertNotEmpty($elsewhere, "$field_name must already be attached to another bundle - that is what makes attaching it to Dataset reuse rather than creation.");
      $assertions++;
    }

    // -- Every term reference is tied to ONE vocabulary ----------------------
    $term_references = 0;
    foreach (self::DATASET_TERM_REFERENCES as $bundle => $map) {
      foreach ($map as $field_name => $vid) {
        $term_references++;
        $field = $storage->read('field.field.node.' . $bundle . '.' . $field_name);
        $this->assertSame('default:taxonomy_term', $field['settings']['handler'], "$field_name on $bundle must reference taxonomy terms.");
        $assertions++;
        $this->assertSame([$vid], array_keys($field['settings']['handler_settings']['target_bundles']), "$field_name on $bundle must be restricted to the $vid vocabulary.");
        $assertions++;
      }
    }

    // -- The distribution is a plain file, and it accepts what it advertises --
    // NOT a media reference: core's `document` media type, which Document
    // uses, lists no CSV, no JSON and no XML, so a dataset could not be
    // attached to it at all. And the extensions the field accepts must be
    // exactly the formats the `format` field can state, in both directions -
    // otherwise the two drift into a dataset whose declared format is one no
    // file on it could ever have, which is a broken record that imports and
    // validates and says nothing.
    $distribution = $storage->read('field.field.node.agora_base_dataset.field_agora_base_distribution');
    $this->assertSame('file', $distribution['field_type'], 'The distribution must be an attached file: a dataset is published as a file a machine can read.');
    $assertions++;
    $extensions = explode(' ', $distribution['settings']['file_extensions']);
    sort($extensions);
    $formats = array_column(
      $storage->read('field.storage.node.field_agora_base_format')['settings']['allowed_values'],
      'value',
    );
    sort($formats);
    $this->assertSame($formats, $extensions, 'The extensions the distribution accepts must be exactly the formats the model can state, or a dataset can declare a format no file on it could carry.');
    $assertions++;

    // -- The assertion count, asserted rather than printed -------------------
    // Same mechanic and same reason as the three methods above: PHPUnit turns
    // ANY output a test emits, STDOUT and STDERR alike, into an exception
    // (pipeline 934619), so the count is asserted here and reaches the CI log
    // through PHPUnit's own "OK (N tests, N assertions)" line. Derived from
    // the constants rather than written as a literal, so that a change to the
    // model updates it instead of being papered over.
    $field_instances = 0;
    foreach (self::DATASET_BUNDLE as $definition) {
      $field_instances += count($definition['fields']);
    }
    $expected_assertions = 4
      + 1
      + (8 * count(self::DATASET_BUNDLE))
      + (8 * $field_instances)
      + (5 * count(self::T614_FIELD_STORAGES))
      + count(self::DATASET_LIST_FIELDS)
      + (2 * $list_options)
      + (2 * count(self::DATASET_REUSED_STORAGES))
      + (2 * $term_references)
      + 2;
    $this->assertSame($expected_assertions, $assertions, 'Every assertion loop in this test must have run to completion.');
  }

  /**
   * Tests the six table views at config level (T-615, layer (a)).
   *
   * THIS IS THE LAYER THAT ACTUALLY TESTS D-026'S CLAIM. The T-615 row
   * originally asked for a functional assertion that no rendered `<td>` is
   * structurally empty; that was unsatisfiable three ways, and the third is the
   * one this method answers: the claim is about the MODEL, and the model can be
   * read straight out of `config/` with no site, no content and no fixtures.
   * For each of the six views, the set of columns it declares must equal the
   * set of fields attached to its bundle - asserted in BOTH directions, so a
   * column belonging to another regime fails and a field with no column fails
   * too. A union type could not pass this: `Grant` would have to declare
   * `bidder_count` as a column to render one, and declaring it fails here.
   *
   * The set sizes are asserted rather than printed, for the reason every method
   * in this file repeats: PHPUnit turns any output a test emits into an
   * exception (pipeline 934619). `tests/bin/config-inventory` prints them.
   *
   * LAYER (d) HAS A HALF HERE TOO. `empty_table: false` is what makes Views
   * render the empty area INSTEAD of a table when there are no rows; with it
   * true, an empty view emits a `<table>` carrying `<th>`s and not one row,
   * which is an accessibility defect rather than an empty state. The other half
   * - that the rendered page really does behave that way - is asserted in
   * `ValidationTest::testTableViews()`, which is the only place it can be.
   */
  public function testTableViews(): void {
    $path = dirname(__FILE__, 4);
    $storage = new FileStorage($path . '/config');
    $assertions = 0;

    // -- The census, for the same reason as in the four methods above --------
    $this->assertCount(6, self::TABLE_VIEWS, 'T-615 ships one table view per bundle.');
    $assertions++;
    $this->assertCount(self::BUNDLE_COUNT, self::TABLE_VIEWS, 'One view per bundle, and D-026 fixes the bundles at six.');
    $assertions++;
    $this->assertCount(3, self::FACET_SPINE, 'The spine is three facets: area, financial year, status.');
    $assertions++;
    $this->assertCount(1, self::VIEW_BASE_FIELD_COLUMNS, 'Exactly one base field may be a column. Widening this allowlist is a change to the model\'s falsifiability, not a detail.');
    $assertions++;

    // -- The shipped set of views equals the six, both directions ------------
    // A seventh view shipped without a row fails here, and so does one of the
    // six that never made it out of the rig.
    // T-603's two surfaces are named here too, and deliberately so: this is
    // the one assertion in the file that would otherwise have had to be
    // RELAXED to let a legitimate new view through. Widening it by name keeps
    // it a set equality instead of turning it into a floor.
    $shipped = $storage->listAll('views.view.');
    sort($shipped);
    $expected_views = array_map(
      static fn (string $view_id): string => 'views.view.' . $view_id,
      array_merge(array_keys(self::TABLE_VIEWS), array_keys(self::BASE_SURFACES)),
    );
    sort($expected_views);
    $this->assertSame($expected_views, $shipped, 'config/ must ship exactly the six table views T-615 declares plus the two surfaces T-603 declares.');
    $assertions++;

    $paths = [];
    $columns_total = 0;
    $facets_total = 0;

    foreach (self::TABLE_VIEWS as $view_id => $spec) {
      $bundle = $spec['bundle'];
      $name = 'views.view.' . $view_id;
      $data = $storage->read($name);

      $this->assertIsArray($data, "$name must be shipped in config/.");
      $assertions++;
      $this->assertSame($view_id, $data['id'], "$name must declare its own machine name.");
      $assertions++;
      $this->assertSame($spec['label'], $data['label'], "$name must carry its English label (D-033).");
      $assertions++;
      $this->assertNotEmpty($data['description'], "$name must say what it lists; the description is what a site builder reads in the Views UI.");
      $assertions++;
      $this->assertSame('node_field_data', $data['base_table'], "$name must list nodes.");
      $assertions++;
      $this->assertSame('en', $data['langcode'], "$name must declare English, which is the ONE field core consults before it will translate a string at all (D-033).");
      $assertions++;
      $this->assertTrue($data['status'], "$name must be enabled; a disabled view ships as dead weight.");
      $assertions++;

      $options = $data['display']['default']['display_options'];

      // -- LAYER (a): SET EQUALITY, BOTH DIRECTIONS --------------------------
      // The expected set is DERIVED from config/, never transcribed: two
      // hand-written lists can agree with each other while both disagree with
      // the model, and that is the failure this assertion exists to catch.
      $prefix = 'field.field.node.' . $bundle . '.';
      $attached = array_map(
        static fn (string $object): string => substr($object, strlen($prefix)),
        $storage->listAll($prefix),
      );
      $this->assertNotEmpty($attached, "$bundle must have fields attached, or the set equality below would hold vacuously (I-028).");
      $assertions++;

      $columns = array_keys($options['fields']);
      sort($columns);
      $columns_total += count($columns);
      $expected_columns = array_merge(self::VIEW_BASE_FIELD_COLUMNS, $attached);
      sort($expected_columns);
      $this->assertSame($expected_columns, $columns, "$view_id must show exactly $bundle's own fields plus the node title - a column from another regime is the union typing D-026 refuses, and a field with no column is a field the table silently hides.");
      $assertions++;
      $this->assertCount(count($attached) + count(self::VIEW_BASE_FIELD_COLUMNS), $columns, "$view_id's column set must be the size $bundle's field set gives it.");
      $assertions++;

      // -- The table, and the half of layer (d) that lives in config ---------
      $style = $options['style'];
      $this->assertSame('table', $style['type'], "$view_id must render a table: this portal publishes salaries and contracts, and a table is the accessible shape for them.");
      $assertions++;
      $declared = array_keys($style['options']['columns']);
      sort($declared);
      $this->assertSame($columns, $declared, "$view_id must give every field a column of its own and declare no column without a field.");
      $assertions++;
      // WCAG 2.2 AA, 1.3.1: the table says what it is, inside the table.
      $this->assertNotEmpty($style['options']['caption'], "$view_id's table must carry a caption.");
      $assertions++;
      // Layer (d), at config level. With this TRUE, an empty view renders a
      // table of headers and no rows, which is a defect and not an empty state.
      $this->assertFalse($style['options']['empty_table'], "$view_id must render its empty text INSTEAD of the table, never a table with headers and no rows.");
      $assertions++;

      foreach ($columns as $column) {
        // A column that disappears when every row happens to be empty makes
        // the number of cells in a row depend on the DATA rather than on the
        // model - which is exactly what ValidationTest asserts it does not.
        $this->assertFalse($style['options']['info'][$column]['empty_column'], "$column must keep its column on $view_id even when every value is empty.");
        $assertions++;
        // A column header with no label is an unlabelled `<th>`.
        $this->assertNotEmpty($options['fields'][$column]['label'], "$column must carry a column header on $view_id.");
        $assertions++;
        // The hole this closes was found by falsifying the layer above rather
        // than by reading the schema: a field marked `exclude` is DECLARED as a
        // column and never RENDERED as one, so the set equality above passes
        // while the table quietly shows one column fewer. Proven, not argued -
        // excluding one field made ValidationTest report 5 header cells where 6
        // were expected while every assertion in this method still passed.
        $this->assertFalse((bool) ($options['fields'][$column]['exclude'] ?? FALSE), "$column must actually render on $view_id; an excluded column is declared and then hidden.");
        $assertions++;
      }

      // -- The two filters that are never exposed ----------------------------
      // The view IS the bundle, and it is published content only. Exposing
      // either would let a reader ask a listing of contracts for documents, or
      // for unpublished work.
      $filters = $options['filters'];
      $this->assertSame([$bundle => $bundle], $filters['type']['value'], "$view_id must list $bundle and nothing else.");
      $assertions++;
      $this->assertFalse($filters['type']['exposed'], "$view_id's bundle filter must not be exposed.");
      $assertions++;
      $this->assertSame('1', $filters['status']['value'], "$view_id must list published content only.");
      $assertions++;
      $this->assertFalse($filters['status']['exposed'], "$view_id's published filter must not be exposed.");
      $assertions++;

      // -- THE FACET SPINE, and the fact that it is NOT uniform --------------
      // Two independent sources have to agree: what the T-615 row measured
      // (self::TABLE_VIEWS) and what the bundle's own field set yields today.
      $exposed = array_filter(
        $filters,
        static fn (array $filter): bool => !empty($filter['exposed']),
      );
      $exposed_fields = [];
      foreach (array_keys($exposed) as $filter_id) {
        $this->assertStringEndsWith('_target_id', $filter_id, "$filter_id on $view_id must be a taxonomy term reference filter.");
        $assertions++;
        $exposed_fields[] = substr($filter_id, 0, -strlen('_target_id'));
      }
      sort($exposed_fields);

      $declared_facets = $spec['facets'];
      sort($declared_facets);
      $this->assertSame($declared_facets, $exposed_fields, "$view_id must expose exactly the facets the T-615 row measured for $bundle.");
      $assertions++;

      $derived_facets = array_values(array_intersect(array_keys(self::FACET_SPINE), $attached));
      sort($derived_facets);
      $this->assertSame($derived_facets, $exposed_fields, "$view_id must expose the spine fields $bundle ACTUALLY carries - no more, which would need a field the regime does not name, and no fewer, which would hide a facet the bundle has.");
      $assertions++;

      foreach ($exposed as $filter_id => $filter) {
        $facets_total++;
        $field_name = substr($filter_id, 0, -strlen('_target_id'));
        $this->assertSame('taxonomy_index_tid', $filter['plugin_id'], "$filter_id must be a core Views exposed filter: [andres] ruled the mechanism, and no Search API and no Facets module enters the SBOM for a spine of three selects.");
        $assertions++;
        $this->assertSame(self::FACET_SPINE[$field_name], $filter['vid'], "$filter_id must offer terms from one vocabulary only.");
        $assertions++;
        $this->assertNotEmpty($filter['expose']['label'], "$filter_id must label its select element; an unlabelled select is a WCAG 2.2 AA failure and an English label is D-033.");
        $assertions++;
        $this->assertNotEmpty($filter['expose']['identifier'], "$filter_id must name the query parameter it answers to.");
        $assertions++;
        $this->assertContains($field_name, $attached, "$filter_id filters on a field $bundle does not carry.");
        $assertions++;
      }

      // -- The empty text exists at all --------------------------------------
      // With `empty_table: false` and no empty area, an empty view renders
      // NOTHING: a 200 with a page that says nothing at all about why.
      $empty = $options['empty'];
      $this->assertNotEmpty($empty, "$view_id must say something when it has no rows.");
      $assertions++;
      $area = reset($empty);
      $this->assertTrue($area['empty'], "$view_id's empty area must be marked as such.");
      $assertions++;
      $this->assertNotEmpty($area['content'], "$view_id's empty text must not be blank.");
      $assertions++;

      // -- The page display --------------------------------------------------
      $page = $data['display']['page_1'];
      $this->assertSame('page', $page['display_plugin'], "$view_id must answer on a route of its own.");
      $assertions++;
      $this->assertSame($spec['path'], $page['display_options']['path'], "$view_id must answer on the path the T-615 row declares.");
      $assertions++;
      $paths[] = $page['display_options']['path'];

      // -- Access -------------------------------------------------------------
      // A transparency portal's tables are readable by whoever can read
      // content, which for an anonymous visitor is the whole point.
      $this->assertSame('perm', $options['access']['type'], "$view_id must gate on a permission.");
      $assertions++;
      $this->assertSame('access content', $options['access']['options']['perm'], "$view_id must be readable by anyone who can read content.");
      $assertions++;
    }

    // -- No two views may answer on the same path ----------------------------
    // Two views on one path is a race Drupal resolves by weight, and the loser
    // is unreachable while every test above still passes.
    $this->assertCount(count(self::TABLE_VIEWS), array_unique($paths), 'Each of the six views must answer on a path of its own.');
    $assertions++;

    // -- The assertion count, asserted rather than printed -------------------
    // Derived from the loops' own accumulators, so a legitimate growth of the
    // model updates it instead of forcing this file to be relaxed.
    $expected_assertions = 4
      + 1
      + (27 * count(self::TABLE_VIEWS))
      + (3 * $columns_total)
      + (6 * $facets_total)
      + 1;
    $this->assertSame($expected_assertions, $assertions, 'Every assertion loop in this test must have run to completion.');
  }

  /**
   * Tests the two surfaces above the six tables, and the menu (T-603).
   *
   * WHAT THE TWO SURFACES ARE, and why the column sets are the whole design.
   *
   * D-026 rejected a single union content type on ONE argument: a `<td>` that
   * is empty BY DESIGN is indistinguishable, to a screen-reader user, from
   * missing data. A cross-type listing is a union type by construction - it
   * puts rows of six different bundles in one table - so the argument applies
   * to it with full force, and the answer is that its columns are the
   * INTERSECTION of what every bundle it lists carries, never the union.
   *
   * That intersection is COMPUTED here from `config/field.field.node.*` and
   * compared with the column list transcribed below. Two independent sources
   * have to agree: a column that is not universal fails, and a bundle that
   * loses a field the surface shows fails too. This is the same shape of
   * assertion as `testTableViews()`, aimed at the opposite property - that
   * one asserts each table shows its bundle's WHOLE field set, this one
   * asserts each surface shows only the SHARED part of several.
   *
   * A column may be DROPPED from the intersection, by name, and the drop is
   * asserted rather than merely permitted: `dropped` fields must still be
   * carried by every listed bundle, so a field that quietly disappeared from
   * the model cannot hide in the drop list. The library drops one - the
   * summary - because Document labels that field `Summary` while Dataset
   * labels it `Description`. Same storage, two per-bundle labels, both
   * correct for their bundle, and no honest single heading for a shared
   * column. Found by the modelling script's own guard on its first run.
   *
   * THE SEARCH BOX is a core Views exposed string filter on the node title,
   * `contains`, under the query identifier `search`. [andres] ruled the
   * mechanism for the facet spine - core Views, no Search API, no Facets, no
   * new dependency - and it governs here. Asserted by plugin and operator, not
   * by the presence of a filter called something search-like.
   *
   * THE MENU, which is the carried debt this row absorbed. Nothing linked to
   * the six routes T-615 built. The links are declared as page-display `menu`
   * options, which core derives into `views_view:*` menu link PLUGINS. That
   * choice is load-bearing for this repository: `menu_link_content` entities
   * are CONTENT, they would land in `content/`, and `content/` holds
   * exactly one file which `ValidationTest` asserts. Declared this way the
   * links are config, they ride inside the view they point at, and
   * `system.menu.main` - core's own default config - is never modified. That
   * last part is what kept D-032's step 4b silent on this row: a change to
   * core, System or User default config is captured by the export in its
   * `recipe.yml` and nowhere else, and the export's `recipe.yml` came back
   * byte-identical to the baseline's.
   *
   * All nine keys of the `menu` mapping are asserted PRESENT rather than only
   * correct. `PathPluginBase::getMenuLinks()` reads `title`, `description`,
   * `parent`, `enabled` and `expanded` with direct array access and no null
   * coalescing, so a mapping missing one raises a PHP warning during menu
   * rebuild - on a code path that only runs once a menu link exists, which is
   * to say on nobody's machine until it is on everybody's.
   */
  public function testBaseSurfaces(): void {
    $path = dirname(__FILE__, 4);
    $storage = new FileStorage($path . '/config');
    $assertions = 0;

    // -- The census, for the same reason as every method above ---------------
    $this->assertCount(2, self::BASE_SURFACES, 'T-603 ships two surfaces: the document library and the cross-type listing.');
    $assertions++;
    $this->assertCount(6, self::SURFACE_MENU_CHILDREN, 'Every one of the six per-bundle routes must be linked, or the carried debt is not paid.');
    $assertions++;
    $this->assertCount(9, self::MENU_KEYS, 'The menu mapping core reads has nine keys, five of them without a null check.');
    $assertions++;
    $this->assertCount(3, self::SURFACE_BASE_FIELD_COLUMNS, 'Three node base fields may be a column here; widening this is a change to what the assertion below can catch.');
    $assertions++;

    $surface_paths = [];
    $field_columns_total = 0;
    $facets_total = 0;
    $bundles_total = 0;

    foreach (self::BASE_SURFACES as $view_id => $spec) {
      $name = 'views.view.' . $view_id;
      $data = $storage->read($name);

      $this->assertIsArray($data, "$name must be shipped in config/.");
      $assertions++;
      $this->assertSame($view_id, $data['id'], "$name must declare its own machine name.");
      $assertions++;
      $this->assertSame($spec['label'], $data['label'], "$name must carry its English label (D-033).");
      $assertions++;
      $this->assertNotEmpty($data['description'], "$name must say what it lists.");
      $assertions++;
      $this->assertSame('node_field_data', $data['base_table'], "$name must list nodes.");
      $assertions++;
      $this->assertSame('en', $data['langcode'], "$name must declare English (D-033).");
      $assertions++;
      $this->assertTrue($data['status'], "$name must be enabled, or its route does not exist.");
      $assertions++;

      $options = $data['display']['default']['display_options'];

      // -- Access, and it is the whole point of a transparency portal --------
      $this->assertSame('perm', $options['access']['type'], "$name must gate on a permission.");
      $assertions++;
      $this->assertSame('access content', $options['access']['options']['perm'], "$name must be readable by anyone who can read content.");
      $assertions++;

      // -- The table, and the two accessibility flags ------------------------
      $this->assertSame('table', $options['style']['type'], "$name must render a table. The T-603 row allowed 'or an equivalent semantic list'; a disjunction a test cannot decide is not a criterion, so the structure is CHOSEN here and it is a table.");
      $assertions++;
      $this->assertNotEmpty($options['style']['options']['caption'], "$name must caption its table (WCAG 2.2 AA, 1.3.1).");
      $assertions++;
      $this->assertFalse($options['style']['options']['empty_table'], "$name must not emit headers with no rows under them; that is an accessibility defect, not an empty state.");
      $assertions++;
      $this->assertNotEmpty(reset($options['empty'])['content'], "$name must say why it is empty, because an empty portal is a real shipping state.");
      $assertions++;

      // -- The columns, both directions --------------------------------------
      $this->assertSame($spec['columns'], array_keys($options['fields']), "$name must declare exactly the columns T-603 records, in order.");
      $assertions++;
      $this->assertSame($spec['columns'], array_keys($options['style']['options']['columns']), "$name's table style must lay out exactly the fields it declares.");
      $assertions++;

      foreach ($options['style']['options']['info'] as $column => $info) {
        // The flag that made a table render with no cells at all in T-615.
        $this->assertFalse($info['empty_column'], "$name column $column must not vanish on empty data; the number of cells in a row must depend on the model, not on the data.");
        $assertions++;
      }

      // -- The intersection, computed rather than transcribed ----------------
      $intersection = NULL;
      foreach ($spec['bundles'] as $bundle) {
        $prefix = 'field.field.node.' . $bundle . '.';
        $attached = array_map(
          static fn (string $config_name): string => substr($config_name, strlen($prefix)),
          $storage->listAll($prefix),
        );
        $this->assertNotEmpty($attached, "$bundle must carry fields, or the intersection below is empty for the wrong reason.");
        $assertions++;
        $bundles_total++;
        $intersection = $intersection === NULL
          ? $attached
          : array_values(array_intersect($intersection, $attached));
      }
      sort($intersection);

      $claimed = array_merge($spec['field_columns'], $spec['dropped']);
      sort($claimed);
      $this->assertSame($intersection, $claimed, "$name must show the intersection of its bundles' fields, or a subset named in its drop list. A column half the rows cannot fill is the union type D-026 refused.");
      $assertions++;

      // A dropped column must be a CHOICE, never an absence.
      foreach ($spec['dropped'] as $dropped) {
        $this->assertContains($dropped, $intersection, "$name drops $dropped, so every bundle it lists must actually carry it.");
        $assertions++;
        $this->assertNotContains($dropped, $spec['columns'], "$name cannot both drop $dropped and show it.");
        $assertions++;
      }

      // Every column is either one of the three allowed base fields or one of
      // the intersected fields. Nothing else may become a column.
      foreach ($spec['columns'] as $column) {
        $allowed = in_array($column, self::SURFACE_BASE_FIELD_COLUMNS, TRUE)
          || in_array($column, $spec['field_columns'], TRUE);
        $this->assertTrue($allowed, "$name column $column is neither an allowed base field nor an intersected field.");
        $assertions++;
      }
      $field_columns_total += count($spec['field_columns']);

      // -- The bundles this surface lists ------------------------------------
      $bundle_filter = $options['filters']['type'];
      $this->assertSame('bundle', $bundle_filter['plugin_id'], "$name must restrict itself by bundle.");
      $assertions++;
      $this->assertSame($spec['bundles'], array_values($bundle_filter['value']), "$name must list exactly the bundles T-603 records.");
      $assertions++;
      $this->assertSame($spec['expose_bundle'], !empty($bundle_filter['exposed']), "$name must expose the type filter only where mixing types makes it meaningful.");
      $assertions++;
      if ($spec['expose_bundle']) {
        $this->assertTrue($bundle_filter['expose']['reduce'], "$name must offer only the bundles it lists in its type select, not every node type on the site.");
        $assertions++;
      }

      // -- Published only ----------------------------------------------------
      $this->assertSame('1', $options['filters']['status']['value'], "$name must list published records only.");
      $assertions++;
      $this->assertFalse($options['filters']['status']['exposed'], "$name must not let a reader ask for unpublished records.");
      $assertions++;

      // -- THE SEARCH BOX ----------------------------------------------------
      $search = $options['filters']['title'];
      $this->assertSame('string', $search['plugin_id'], "$name's search box must be a core Views string filter. No Search API, no Facets, no new dependency.");
      $assertions++;
      $this->assertSame('contains', $search['operator'], "$name's search box must match words inside the title, not the whole title.");
      $assertions++;
      $this->assertTrue($search['exposed'], "$name's search box must be exposed, or there is no box.");
      $assertions++;
      $this->assertSame('search', $search['expose']['identifier'], "$name's search box must answer on ?search=, which is what any link into it will use.");
      $assertions++;
      $this->assertNotEmpty($search['expose']['label'], "$name's search box must be labelled; an unlabelled input fails WCAG 2.2 AA 3.3.2.");
      $assertions++;
      $this->assertFalse($search['expose']['required'], "$name's search box must not be required, or the surface cannot be browsed at all.");
      $assertions++;

      // -- The facets, reused from T-615's spine rather than reinvented ------
      foreach ($spec['facets'] as $facet_field) {
        $filter_id = $facet_field . '_target_id';
        $this->assertArrayHasKey($filter_id, $options['filters'], "$name must expose the $facet_field facet.");
        $assertions++;
        $filter = $options['filters'][$filter_id];
        $this->assertSame('taxonomy_index_tid', $filter['plugin_id'], "$name's $facet_field facet must be the same core filter the six tables use.");
        $assertions++;
        $this->assertTrue($filter['exposed'], "$name's $facet_field facet must be exposed.");
        $assertions++;
        $this->assertSame(self::FACET_SPINE[$facet_field], $filter['vid'], "$name's $facet_field facet must read the spine's own vocabulary.");
        $assertions++;
        // A facet on a field only some rows carry silently deletes the rest.
        $this->assertContains($facet_field, $spec['field_columns'], "$name may only facet on a field every bundle it lists carries.");
        $assertions++;
        $facets_total++;
      }

      // -- The page display and its menu link --------------------------------
      $page = $data['display']['page_1']['display_options'];
      $this->assertSame($spec['path'], $page['path'], "$name must answer on the path T-603 records.");
      $assertions++;
      $surface_paths[] = $page['path'];

      $assertions += $this->assertMenuLink($page['menu'] ?? [], $name, $spec['menu']);
    }

    // -- The six per-bundle views are LINKED, which is the carried debt ------
    // Six accessible tables nobody can reach is not a delivered feature.
    $child_paths = [];
    foreach (self::SURFACE_MENU_CHILDREN as $view_id => $link) {
      $name = 'views.view.' . $view_id;
      $data = $storage->read($name);
      $this->assertIsArray($data, "$name must be shipped in config/.");
      $assertions++;
      $page = $data['display']['page_1']['display_options'];
      $assertions += $this->assertMenuLink($page['menu'] ?? [], $name, $link);
      $child_paths[] = $page['path'];

      // The parent must be a view this template actually ships. A menu link
      // whose parent does not exist is a link core silently moves to the
      // root, and the menu still looks fine while the hierarchy is gone.
      $parent_view = self::MENU_PARENT_VIEW;
      $this->assertSame('views_view:views.' . $parent_view . '.page_1', $link['parent'], "$name must hang from the cross-type listing.");
      $assertions++;
      $this->assertArrayHasKey($parent_view, self::BASE_SURFACES, 'The menu parent must be a view T-603 ships.');
      $assertions++;
    }

    // -- No two routes may collide -------------------------------------------
    $all_paths = array_merge($surface_paths, $child_paths);
    $this->assertCount(count($all_paths), array_unique($all_paths), 'Two views on one path is a race Drupal resolves by weight, and the loser is unreachable while every assertion above still passes.');
    $assertions++;
    $this->assertCount(8, $all_paths, 'Eight routes are linked: two surfaces and the six tables.');
    $assertions++;

    // -- The assertion count, asserted rather than printed -------------------
    $columns_total = 0;
    foreach (self::BASE_SURFACES as $spec) {
      $columns_total += count($spec['columns']);
    }
    $dropped_total = 0;
    foreach (self::BASE_SURFACES as $spec) {
      $dropped_total += count($spec['dropped']);
    }
    $exposed_bundle_total = 0;
    foreach (self::BASE_SURFACES as $spec) {
      $exposed_bundle_total += $spec['expose_bundle'] ? 1 : 0;
    }
    $expected_assertions = 4
      + (28 * count(self::BASE_SURFACES))
      + (2 * $columns_total)
      + (2 * $dropped_total)
      + $exposed_bundle_total
      + $bundles_total
      + (5 * $facets_total)
      + (self::MENU_ASSERTIONS * (count(self::BASE_SURFACES) + count(self::SURFACE_MENU_CHILDREN)))
      + (3 * count(self::SURFACE_MENU_CHILDREN))
      + 2;
    $this->assertSame($expected_assertions, $assertions, 'Every assertion loop in this test must have run to completion.');
  }

  /**
   * Asserts one page display's menu mapping, and returns the count.
   *
   * @param array $menu
   *   The `menu` mapping read from the page display, or [] if there is none.
   * @param string $name
   *   The config object name, for the failure messages.
   * @param array $expected
   *   The expected title, weight and parent.
   *
   * @return int
   *   The number of assertions made, so the caller can keep its own count.
   */
  protected function assertMenuLink(array $menu, string $name, array $expected): int {
    $this->assertNotEmpty($menu, "$name must declare a menu link. Nothing linking to a route is the debt T-603 carried.");
    $this->assertSame(self::MENU_KEYS, array_keys($menu), "$name's menu mapping must carry all nine keys; core reads five of them without a null check.");
    $this->assertSame('normal', $menu['type'], "$name must be a normal menu entry, not a tab.");
    $this->assertSame('main', $menu['menu_name'], "$name must land in the main menu.");
    $this->assertTrue($menu['enabled'], "$name's link must be enabled, or it is not a link.");
    $this->assertSame($expected['title'], $menu['title'], "$name's link must carry its English title (D-033).");
    $this->assertNotEmpty($menu['description'], "$name's link must describe itself.");
    $this->assertSame($expected['weight'], $menu['weight'], "$name's link must sit where T-603 puts it.");
    $this->assertSame($expected['parent'], $menu['parent'], "$name's link must hang where T-603 puts it.");

    return self::MENU_ASSERTIONS;
  }

}
