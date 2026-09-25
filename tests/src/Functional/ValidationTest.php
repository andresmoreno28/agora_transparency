<?php

declare(strict_types=1);

use Drupal\Component\Serialization\Yaml;
use Drupal\Component\Utility\Html;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\canvas\Entity\ComponentTreeEntityInterface;
use Drupal\canvas\JsonSchemaDefinitionsStreamwrapper;
use Drupal\field\Entity\FieldConfig;
use Drupal\file\Entity\File;
use Drupal\FunctionalTests\Core\Recipe\RecipeTestTrait;
use Drupal\media\Entity\Media;
use Drupal\media\Entity\MediaType;
use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\BrowserTestBase;
use Drupal\views\Entity\View;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

require_once __DIR__ . '/../Traits/NoUsageReportingTrait.php';

/**
 * Tests that this site template can be applied without errors.
 *
 * All deprecation notices triggered by the recipe's dependencies will be
 * displayed. To suppress them, add the
 * \PHPUnit\Framework\Attributes\IgnoreDeprecations attribute to this class.
 */
#[RunTestsInSeparateProcesses]
class ValidationTest extends BrowserTestBase {

  use NoUsageReportingTrait;
  use RecipeTestTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Returns the absolute path of the recipe this test is for.
   *
   * @return string
   *   The absolute path of the recipe.
   */
  protected static function getRecipePath(): string {
    return dirname(__FILE__, 4);
  }

  /**
   * Tests that the site template can be applied without errors.
   *
   * At the very least, this test ensures that this site template can be applied
   * against an empty site with the `drupal recipe` command-line tool. You
   * should customize this test to also confirm that the site template sets up
   * everything as you expect.
   *
   * If you need to test JavaScript interactions, you can convert this test to
   * a functional JavaScript test instead.
   *
   * Documentation on how to write functional (non-JavaScript) tests can be
   * found at https://www.drupal.org/docs/develop/automated-testing/phpunit-in-drupal/creating-functional-tests-simulated-browser.
   *
   * Documentation on how to write functional JavaScript tests can be found at
   * https://www.drupal.org/docs/develop/automated-testing/phpunit-in-drupal/creating-functionaljavascript-tests-real-browser.
   *
   * Further documentation on writing PHPUnit tests for Drupal can be found at
   * https://www.drupal.org/docs/develop/automated-testing/phpunit-in-drupal.
   */
  public function testApply(): void {
    $this->applyRecipe(self::getRecipePath());

    // If this site template uses Canvas, it is a best practice for it to ship
    // `canvas.component.*.yml` files for every component that is actually using
    // in content templates, page regions, patterns, landing pages, etc. This
    // method checks for that.
    $this->assertCanvasComponentsAreIncluded();
  }

  /**
   * The demonstration masthead photograph arrives, and the theme reads it.
   *
   * TWO CLAIMS THAT LOOK UNRELATED AND ARE THE SAME STATEMENT. The theme
   * stopped shipping a photograph in September 2026 - `images/hero-wide.webp`
   * deleted, the hard-coded `background-image` replaced by
   * `var(--agora-hero-image, none)`, and a `hero_image_path` setting added - so
   * a council installing the theme alone gets a flat navy band, which is
   * correct. The demonstration picture therefore has to travel with the
   * DEMONSTRATION, and this package is the demonstration.
   *
   * ⚠️ THE LAST ASSERTION WAS THE EXACT OPPOSITE UNTIL THIS COMMIT, AND THAT
   * IS WORTH KNOWING BEFORE ANYONE TOUCHES IT. `recipe.yml` deliberately did
   * NOT set `agora_theme.settings:hero_image_path`, because the config object
   * holding it existed in no published theme release, and core's
   * `SimpleConfigUpdate::apply()` throws on a config object that is absent.
   * (A `?` prefix would have skipped it silently instead; `recipe.yml` says
   * why this action carries none.) This test asserted
   * the WITHHOLDING, so that landing the action would fail here rather than on
   * somebody's clean install. It was watched failing with the action in place.
   *
   * WHAT DISCHARGED IT, AND WHICH HALF IS LOAD-BEARING. `agora_theme` 1.1.0,
   * published 2026-09-05 22:21 UTC - 00:21 on the 6th at this repository's
   * own +0200, which is why two dates for one release are both right - ships
   * `config/install/agora_theme.settings.yml` and
   * `config/schema/agora_theme.schema.yml`. `composer.json` moved from `^1.0`
   * to `^1.1` in the same change, and THAT is the half holding this up: at
   * `^1.0` a resolver may still legitimately install 1.0.7, which carries no
   * settings object, and the action would throw on exactly the installs the
   * withholding existed to protect. If this assertion is ever reverted, the
   * constraint goes back with it or neither moves.
   */
  public function testDemonstrationMastheadImageArrives(): void {
    $this->applyRecipe(self::getRecipePath());

    $uri = 'public://hero-wide.webp';
    $files = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->loadByProperties(['uri' => $uri]);
    $this->assertCount(1, $files, "Exactly one file entity must live at $uri.");
    $file = reset($files);
    $this->assertTrue($file->isPermanent(), 'The masthead image must be a permanent file: nothing references it as an entity, so a temporary one would be swept away.');
    $this->assertSame('image/webp', $file->getMimeType());

    // The size is compared against the PACKAGED BYTES, never against a number
    // typed here. A constant in this file would agree with itself for ever.
    $source = self::getRecipePath() . '/content/file/hero-wide.webp';
    $this->assertFileExists($source, 'The package must ship the file the entity names; the content importer copies it by basename.');
    $expected = file_get_contents($source);
    $this->assertSame(strlen($expected), (int) $file->getSize(), 'The file entity\'s filesize must match the packaged bytes.');

    // And the bytes that actually landed, not merely the record of them.
    $destination = \Drupal::service('file_system')->realpath($uri);
    $this->assertIsString($destination);
    $this->assertSame(
      hash('sha256', $expected),
      hash_file('sha256', $destination),
      'The file copied into public:// must be byte-identical to the packaged one.'
    );

    // Reachable anonymously, because a masthead image behind a 403 is a flat
    // band with extra steps.
    $this->drupalGet(\Drupal::service('file_url_generator')->generateString($uri));
    $this->assertSession()->statusCodeEquals(200);

    // ⚠️ The formerly withheld action, now asserted PRESENT. See the docblock.
    // Compared against $uri rather than against a literal typed here, so the
    // setting and the file entity cannot drift apart: the entity's copy is
    // already checked against the packaged bytes above, so pinning the setting
    // to it chains the whole claim back to the shipped file.
    $setting = \Drupal::config('agora_theme.settings')->get('hero_image_path');
    $this->assertSame($uri, $setting, 'recipe.yml must point agora_theme.settings:hero_image_path at the demonstration photograph this package ships. A theme release without the settings object answers NULL here, and the recipe would have thrown before reaching this line.');

    // The setting has to name something that is REALLY THERE, and this walks
    // the theme's own route to it - `_agora_theme_setting_url()` hands this
    // value to the file URL generator - rather than the route the assertions
    // above already walked with $uri. A setting pointing at a plausible file
    // that does not exist renders a flat band and no error, which is the
    // failure this pair of assertions is shaped to catch.
    $from_setting = \Drupal::service('file_system')->realpath($setting);
    $this->assertIsString($from_setting, 'hero_image_path must resolve to a real filesystem path.');
    $this->assertFileExists($from_setting, 'The file named by hero_image_path must exist on disk.');
    $this->drupalGet(\Drupal::service('file_url_generator')->generateString($setting));
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * The six table views (T-615), and the bundle each of them lists.
   *
   * The COLUMN COUNT is deliberately absent: it is read from the view that was
   * actually imported, so this test cannot pass by agreeing with a number typed
   * beside it. `ContentModelTest::testTableViews()` is what pins that number to
   * the bundle's own field set.
   */
  private const TABLE_VIEWS = [
    'agora_base_documents' => 'agora_base_document',
    'agora_base_people' => 'agora_base_person',
    'agora_base_contracts' => 'agora_base_contract',
    'agora_base_agreements' => 'agora_base_agreement',
    'agora_base_grants' => 'agora_base_grant',
    'agora_base_datasets' => 'agora_base_dataset',
  ];

  /**
   * The container Views wraps every rendered view in.
   *
   * Selected on the `js-view-dom-id-` class, which core's own
   * `views-view.html.twig` writes on every view in every theme, rather than on
   * a `view-id-*` class that this Drupal version does not emit at all. Checked
   * against the rendered markup rather than recalled - the assertions below are
   * worth nothing if they are scoped to a selector that never matches.
   */
  private const VIEW_CONTAINER = 'div[class*="js-view-dom-id-"]';

  /**
   * The view that IS the page, as opposed to any view this template PLACES.
   *
   * ⚠️ `VIEW_CONTAINER` ALONE WAS ONLY EVER UNAMBIGUOUS BY ACCIDENT, and
   * T-1310 is what revealed it. Views writes `js-view-dom-id-…` on every view
   * it renders, so the bare selector means "the view on this page" only while
   * a page carries exactly one. `/publications` now carries TWO - the
   * register, and the service-area cards placed beside it - and Mink's text
   * assertions take the FIRST match, which after a `weight: -10` placement is
   * the cards.
   *
   * IT WAS WATCHED FAILING RATHER THAN REASONED ABOUT. Pipeline `969078`,
   * commit `7836bc3`: `ElementTextException: The text "Nothing has been
   * published yet." was not found in the text of the element matching css
   * "div[class*=js-view-dom-id-]"`, at `::assertEmptyState()`, with the other
   * eight jobs green and both phpunit jobs red on the same assertion. That is
   * NOT the MySQL-gone-away shape (which reds one database and not the
   * other); it was this package's own defect, found where it should be.
   *
   * The anchor is the main content block because that is a statement rather
   * than a workaround: the view that IS the page renders inside it, and a
   * view this template places beside the page does not. `recipe.yml` imports
   * `block.block.agora_theme_content` by name, so the id exists on every
   * clean install and not merely on a developer's rig.
   *
   * ⚠️ IT SCOPES, IT DOES NOT COUNT, and the difference was measured rather
   * than reasoned about. Asserting exactly one match reds on `/contracts`
   * (pipeline `969085`): `agora_base_contracts` has an `attachment_1`
   * display, and an attachment nests a second view container INSIDE the
   * page's own, which is correct. What this constant guarantees is that a
   * block this template PLACES - always a sibling of the main content block -
   * is never one of the matches.
   */
  private const PAGE_VIEW = '#block-agora-theme-content ' . self::VIEW_CONTAINER;

  /**
   * The class the art. 8.1.a) breakdown declares on its own table (T-1103).
   *
   * It is a DECLARATION, not a style hook, in the same sense as the theme's
   * `agora-row-header-first`: no stylesheet selects it, and it exists so that
   * the one page carrying two tables can say which of them is which.
   */
  private const STATISTIC_TABLE_CLASS = 'agora-contract-procedures';

  /**
   * Selects a register's OWN table on a page that now carries two of them.
   *
   * WHY THIS IS NOT SIMPLY `table`, and it stopped being simply `table` on
   * one page only. `/contracts` renders the register and, attached beneath
   * it, the breakdown of those contracts by procurement procedure - a second
   * `<table>`, inside the same view container, because Views renders an
   * attachment display INSIDE the wrapper of the display it attaches to. So
   * every count in this class that said "this page has exactly one table"
   * became a count of two on that page and of one everywhere else, which is
   * the shape of an assertion that fails for a correct product.
   *
   * The exclusion is applied EVERYWHERE rather than only to `/contracts`,
   * and that is deliberate: a selector that means "the register's table"
   * should mean the same thing on all six registers, and the alternative -
   * passing a different selector for one of them - puts the knowledge in the
   * six call sites instead of in one constant. On the five pages that carry
   * no statistic table it selects exactly what `table` selected before.
   *
   * ⚠️ It is a `:not()`, so it keeps the ABSENCE assertions honest as well:
   * `::assertEmptyState()` still requires that no register table is rendered
   * for an empty result set (I-062), and it no longer passes or fails on the
   * presence of a table that is not the register's.
   */
  private const REGISTER_TABLE = 'table:not(.' . self::STATISTIC_TABLE_CLASS . ')';

  /**
   * Tests the six table views on a real site, populated and then emptied.
   *
   * THE TWO LAYERS THAT NEED A RUNNING SITE, and one of them can only be
   * tested from this unit.
   *
   * LAYER (d) - THE SHIPPED STATE, AND IT USED TO ASSERT THE OPPOSITE. While
   * the package shipped no demo content an installed Ágora WAS the empty
   * state, so this layer asserted each view's empty text and the absence of a
   * `<table>`. That was true and worth guarding then. The package now ships a
   * published demo corpus, so on a clean install these views are NEVER empty
   * and the old assertion was asserting the absence of the product. What
   * replaces it is strictly stronger, and it is the promise a site template
   * actually makes - install this and the portal ALREADY PUBLISHES SOMETHING:
   * every register renders a table whose row count equals the published
   * records of its bundle, across every page of its pager, with a cell for
   * every column and its empty text nowhere on the page.
   *
   * NO CORPUS SIZE IS TYPED HERE. Every expected count is read from the site
   * with an entity query over the bundle, so adding a demo node changes the
   * denominator instead of breaking a test that somebody then edits downward.
   * The DENOMINATOR IS ASSERTED BEFORE THE PROPERTY: a bundle that shipped
   * zero published nodes fails by name, so none of the counts below can hold
   * vacuously over an import that silently did nothing.
   *
   * THE EMPTY-STATE GUARD IS KEPT, not dropped, and is reached two ways: an
   * out-of-range page, which a stale bookmark or a crawler reaches on a live
   * site, and one bundle whose nodes are all unpublished, which is the
   * genuine "nothing published yet" state an administrator sees on day one.
   * The defect guarded is unchanged - headers with no rows under them, which
   * tells a screen-reader user there is a table and then leaves them nothing
   * in it. Both paths assert the empty text AND that NO `<table>` is emitted,
   * and that second half is load-bearing: Views emits no table at all for an
   * empty result set (I-062), so a check that only looked for the empty text
   * would pass against a page with nothing on it.
   *
   * LAYER (b) - ONE FIXTURE NODE PER BUNDLE, EVERY FIELD POPULATED. The demo
   * corpus cannot carry this claim, because a real record may legitimately
   * leave an optional field blank; only a node built from the FIELD
   * DEFINITIONS can prove that a fully populated record renders text in every
   * column. So the fixture stays, and the assertion sharpens: its row is
   * located by title among the rendered rows and must carry no empty cell,
   * which fails if a column is wired to a field the bundle does not have.
   * THE FIXTURE LIVES IN THIS CLASS and exists only in the test database. It
   * therefore never goes near `drush site:export`, which is what satisfies
   * the NO-list's narrow demo-content exception BY CONSTRUCTION rather than
   * by anyone remembering.
   */
  public function testTableViews(): void {
    $this->applyRecipe(self::getRecipePath());

    // A reader, not an editor. These tables exist to be read by whoever can
    // read content, which on a transparency portal is everybody.
    $this->drupalLogin($this->drupalCreateUser(['access content']));

    $assert = $this->assertSession();
    $columns = [];
    $empty_text = [];
    $paths = [];
    $per_page = [];
    $shipped = [];

    // -- The denominators, read from the site BEFORE anything is created ----
    foreach (self::TABLE_VIEWS as $view_id => $bundle) {
      $view = View::load($view_id);
      $this->assertNotNull($view, "$view_id must have been imported by the recipe.");
      $display = $view->getDisplay('default')['display_options'];

      $columns[$view_id] = count($display['fields']);
      $this->assertGreaterThan(0, $columns[$view_id], "$view_id must declare columns, or every count below holds vacuously.");

      $per_page[$view_id] = (int) $display['pager']['options']['items_per_page'];
      $this->assertGreaterThan(0, $per_page[$view_id], "$view_id must declare a page size, or the page walk below has no stride.");

      $empty_text[$view_id] = reset($display['empty'])['content'];
      $paths[$view_id] = $view->getDisplay('page_1')['display_options']['path'];

      // The shipped corpus, measured before this test adds anything to it.
      $shipped[$view_id] = $this->publishedCount([$bundle]);
      $this->assertGreaterThan(0, $shipped[$view_id], "This package must ship at least one published $bundle. A register with nothing in it is not a site template that publishes something, and every row count below would hold over an empty table.");
    }

    // -- The fixture: one node per bundle, every field populated -------------
    $titles = [];
    foreach (self::TABLE_VIEWS as $view_id => $bundle) {
      $values = ['type' => $bundle, 'title' => 'Fixture ' . $bundle, 'status' => 1];
      $definitions = \Drupal::service('entity_field.manager')
        ->getFieldDefinitions('node', $bundle);
      $populated = 0;
      foreach ($definitions as $field_name => $definition) {
        if ($definition instanceof FieldConfig) {
          $values[$field_name] = $this->fixtureValue($definition);
          $populated++;
        }
      }
      // Every column except the title comes from one of these fields, so a
      // bundle whose fields were not all populated would leave a blank cell
      // on the fixture row and the layer-(b) assertion would be measuring the
      // fixture again - the very failure the T-615 row was rewritten to avoid.
      $this->assertSame($columns[$view_id] - 1, $populated, "Every field on $bundle must be populated, or the empty-cell check below tests the fixture instead of the model.");
      $this->drupalCreateNode($values);
      $titles[$view_id] = $values['title'];
    }

    // -- LAYER (d), INVERTED: every register renders what the site holds ----
    $rendered = 0;
    foreach (self::TABLE_VIEWS as $view_id => $bundle) {
      $count = $columns[$view_id];
      $expected = $shipped[$view_id] + 1;
      // The fixture is the only node this test added, so the expected row
      // count stays tied to the SHIPPED corpus rather than drifting with it.
      $this->assertSame($expected, $this->publishedCount([$bundle]), "The published $bundle records must be the shipped corpus plus this test's one fixture node.");

      [$headers, $rows] = $this->assertPagedTable($paths[$view_id], $count, $expected, $per_page[$view_id], $empty_text[$view_id]);
      $rendered += count($rows);

      // -- LAYER (b): the one row whose every field was populated -----------
      $fixture_row = array_values(array_filter(
        $rows,
        static fn (array $row): bool => in_array($titles[$view_id], $row, TRUE),
      ));
      $this->assertCount(1, $fixture_row, "$view_id must list the fixture node exactly once; a register that cannot show a record of its own bundle is not a register.");
      $this->assertSame([], $this->blankCells($headers, $fixture_row[0]), "$view_id renders an empty cell on a row whose every field was populated, so one of its columns is not reaching its field.");
    }

    // The six registers together must account for the whole corpus of their
    // six bundles: nothing missing from its register, nothing listed twice.
    // Not a tautology - the left side is an entity query and the right side
    // is what eighteen-odd rendered pages actually contained.
    $this->assertSame(
      $this->publishedCount(array_values(self::TABLE_VIEWS)),
      $rendered,
      'The six registers together must render exactly the published records of their six bundles.',
    );

    // -- The empty state, kept, and reached without deleting the corpus -----
    foreach (self::TABLE_VIEWS as $view_id => $bundle) {
      // A page index one past the last. MEASURED on a populated install on
      // 2026-08-26 rather than assumed: 200, the view's own empty text, and
      // zero `<table>` - the same rendering path an empty result set takes,
      // and a state a stale bookmark reaches on a live site.
      $beyond = intdiv($shipped[$view_id], $per_page[$view_id]) + 1;
      $this->assertEmptyState($paths[$view_id], ['query' => ['page' => $beyond]], $empty_text[$view_id]);
    }

    // And the genuine "nothing published yet" state, on the bundle with the
    // fewest records so that this stays cheap however far the corpus grows.
    $smallest = (string) array_search(min($shipped), $shipped, TRUE);
    $this->assertArrayHasKey($smallest, self::TABLE_VIEWS, 'The bundle with the fewest shipped records must be one of the six registers.');
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nodes = $storage->loadByProperties(['type' => self::TABLE_VIEWS[$smallest]]);
    $this->assertNotEmpty($nodes, 'The bundle chosen for the empty-state check must actually have nodes to unpublish.');
    foreach ($nodes as $node) {
      $node->setUnpublished()->save();
    }
    $this->assertSame(0, $this->publishedCount([self::TABLE_VIEWS[$smallest]]), 'Every node of the chosen bundle must now be unpublished, or the empty state below is not the state being tested.');
    $this->assertEmptyState($paths[$smallest], [], $empty_text[$smallest]);

    // Restored, and the table comes back. The transition is what says the two
    // states are genuinely different rather than one of them being rendered
    // all the time - neither state alone can say that.
    foreach ($nodes as $node) {
      $node->setPublished()->save();
    }
    $this->drupalGet($paths[$smallest]);
    $assert->statusCodeEquals(200);
    $assert->elementsCount('css', self::VIEW_CONTAINER . ' ' . self::REGISTER_TABLE, 1);
    $assert->pageTextNotContains($empty_text[$smallest]);
  }

  /**
   * The view id and display id of the art. 8.1.a) breakdown (T-1103).
   *
   * Read here rather than typed at each use, and everything else this method
   * needs - the caption, the column labels, the empty text - is taken off
   * the display itself, so a renamed column changes what is asserted instead
   * of leaving this file agreeing with a string nothing renders any more.
   */
  private const STATISTIC_DISPLAY = ['agora_base_contracts', 'attachment_1'];

  /**
   * The bundle the art. 8.1.a) statistic is about.
   */
  private const STATISTIC_BUNDLE = 'agora_base_contract';

  /**
   * The vocabulary the statistic groups by.
   */
  private const STATISTIC_VOCABULARY = 'agora_base_procedure_type';

  /**
   * The field that carries it.
   */
  private const STATISTIC_FIELD = 'field_agora_base_procedure_type';

  /**
   * Tests the art. 8.1.a) breakdown of contracts by procedure (T-1103).
   *
   * WHAT THE STATUTE ASKS FOR, AND WHAT WAS THERE BEFORE. Ley 19/2013
   * art. 8.1.a) obliges a public body to publish the distribution of its
   * contracts by procurement procedure.
   * `specs/002-base-and-theme/plan.md:52-57` records it as a legal
   * requirement and hands it to unit 003. Until this display existed
   * nothing on the site grouped contracts by procedure at all: the only
   * aggregating view in `config/` was the publications one, and the string
   * `procedure` appeared in no `views.view.*.yml`.
   *
   * TWO ROUTES TO THE SAME NUMBERS, SHARING NO CODE. The row's criterion is
   * that the statistic is asserted against a count the test computes
   * INDEPENDENTLY, and the weakest reading of that - asking the same view
   * twice - would pass over a view that grouped on the wrong field. So the
   * expected breakdown is read from the files this package SHIPS:
   * `content/node/*.yml` for the contracts and `content/taxonomy_term/*.yml`
   * to resolve the procedure each one references, parsed off disk with no
   * database, no view and no Views query involved. The rendered table is
   * then required to equal it.
   *
   * A third route sits between them and is what says the import worked: the
   * entity query's published count of the bundle must equal the total the
   * files describe. Disk, database and rendered page therefore have to agree
   * with one another, and a disagreement names which pair fell out rather
   * than reporting a bare mismatch.
   *
   * THE ROWS ARE COMPARED AS A MAP, NOT AS A LIST. The display sorts by
   * count descending and three of the four demo procedures hold two
   * contracts each, so the order among equal counts is not decided by
   * anything the configuration says and an ordered comparison would be
   * asserting the database's tie-breaking. What IS asserted about the order
   * is the property the sort actually promises: no row carries a larger
   * count than the row above it.
   *
   * THE PERCENTAGE IS COMPUTED HERE AND IS NOT ON THE PAGE, and that is
   * stated rather than left to be discovered. Views cannot divide. A field
   * rewrite is handed the token set `FieldPluginBase::getRenderTokens()`
   * builds, and that set carries the current row's own fields and nothing
   * from any other row, so there is no denominator to divide by; and
   * `$view->total_rows` on an aggregating display is the number of GROUPS -
   * four here - not the number of records. Both were measured in core
   * 11.4.5 and `agora_theme` records the same finding from its own side.
   * The shares below are therefore asserted as a property of the two
   * columns the page does render, which is what a reader can compute from
   * them; the percentage COLUMN is theme work this repository cannot do.
   *
   * WHAT THE FILTERS AND THE PAGER MUST NOT DO TO IT. A legal figure that
   * changes when a reader types in a filter box is a wrong figure, so the
   * display inherits neither the exposed filters nor the pager - and both
   * are exercised here rather than trusted. The register is filtered down
   * to fewer rows and paged past its own end, and the breakdown has to come
   * back identical through both.
   *
   * AND THE EMPTY CASE, WHERE THIS KIND OF TABLE USUALLY LIES. Views emits
   * no table at all for an empty result set, so axe finds nothing wrong
   * with it, truthfully and about nothing (I-062). Every markup assertion
   * below therefore runs only after the row count has been asserted, and
   * the genuinely empty state is reached at the end by unpublishing the
   * corpus - where the requirement is the empty text AND the absence of the
   * table, never one of the two.
   */
  public function testContractsByProcedureType(): void {
    $this->applyRecipe(self::getRecipePath());

    [$view_id, $display_id] = self::STATISTIC_DISPLAY;
    $view = View::load($view_id);
    $this->assertInstanceOf(View::class, $view, "$view_id must have been imported by the recipe.");
    $display = $view->getDisplay($display_id);
    $this->assertIsArray($display, "$view_id must ship the $display_id display, which is where the art. 8.1.a) statistic is computed.");
    $options = $display['display_options'];

    // -- (1) The display says what it is, in config -------------------------
    // Asserted before anything is rendered, because these are the properties
    // that make the rendered numbers mean what the statute asks for. A
    // display that lost its aggregation would still render a table.
    $this->assertTrue($options['group_by'], "$display_id must aggregate, or it lists contracts instead of counting them by procedure.");
    $this->assertSame('count', $options['fields']['nid']['group_type'], 'The breakdown must COUNT rows. D-040 forbids sum, avg and stddev_pop over a Field API field on every database Drupal supports; count is the aggregate measured safe on all three.');
    $this->assertSame('group', $options['fields'][self::STATISTIC_FIELD]['group_type'], 'The procedure must be the group key, not an aggregate.');
    $this->assertFalse($options['inherit_exposed_filters'], 'The statute asks about the whole register, so the breakdown must not follow the exposed filters.');
    $this->assertFalse($options['inherit_pager'], 'A statistic computed over one page of a pager is a statistic about a page.');
    $this->assertSame('after', $options['attachment_position'], 'The breakdown is a summary of the register, so it belongs beneath it.');
    $this->assertSame(['page_1' => 'page_1'], array_filter($options['displays']), "The breakdown must be attached to $view_id's page display, or it renders nowhere.");

    $caption = (string) $options['style']['options']['caption'];
    $this->assertNotSame('', trim($caption), 'The breakdown must declare a caption: it is the accessible name of the table and the only place that says what the numbers count.');
    $empty_state = reset($options['empty']);
    $empty_text = (string) $empty_state['content'];
    $this->assertNotSame('', trim($empty_text), 'The breakdown must declare empty text, or an install with no contracts shows a reader nothing at all.');
    $labels = [
      (string) $options['fields'][self::STATISTIC_FIELD]['label'],
      (string) $options['fields']['nid']['label'],
    ];
    foreach ($labels as $label) {
      $this->assertNotSame('', trim($label), 'Every column of the breakdown must be labelled, or its header cell has no text for a headers attribute to point at.');
    }

    // -- (2) Route one: the corpus this package ships, read off disk --------
    $on_disk = $this->shippedProcedureCounts();
    $total_on_disk = array_sum($on_disk);
    $this->assertGreaterThan(0, $total_on_disk, 'This package must ship published contracts, or every count below holds vacuously over an empty corpus (I-007).');
    // Three distinct procedures is T-1005's non-degeneracy floor, and it is
    // what makes the statistic carry information rather than restate the row
    // count: a single-procedure corpus is arithmetically true and empty.
    $procedures = count($on_disk);
    $this->assertGreaterThanOrEqual(3, $procedures, sprintf(
      'The shipped corpus must use at least three distinct procedures for the breakdown to say anything; it uses %d.',
      $procedures,
    ));

    // -- (3) Route two: the site the recipe just built ----------------------
    $published = $this->publishedCount([self::STATISTIC_BUNDLE]);
    $this->assertSame($total_on_disk, $published, sprintf(
      'The published nodes on the installed site (%d) must be exactly the ones content/node/*.yml describes (%d), or the import and the package have fallen out of step and no number below can be trusted.',
      $published,
      $total_on_disk,
    ));

    // -- (4) Route three: what a reader is actually served ------------------
    $this->drupalLogin($this->drupalCreateUser(['access content']));
    $path = '/' . $view->getDisplay('page_1')['display_options']['path'];

    $rendered = $this->assertStatisticTable($path, $caption, $labels, $empty_text);
    $observed = $rendered;
    ksort($observed);
    $this->assertSame($on_disk, $observed, sprintf(
      'The rendered breakdown must equal the one computed from content/node/*.yml. Rendered: %s. On disk: %s.',
      $this->describeCounts($rendered),
      $this->describeCounts($on_disk),
    ));

    // -- (5) The statistic itself, and the share each procedure holds -------
    // The percentages are DERIVED from the two rendered columns and never
    // stored: this is the art. 8.1.a) figure, and the assertion message is
    // where the gate reports it, because a test in this package cannot
    // print - PHPUnit turns any output a test emits into an error.
    $total_rendered = array_sum($rendered);
    $this->assertSame($published, $total_rendered, sprintf(
      'The breakdown must account for every published contract: it counts %d of %d.',
      $total_rendered,
      $published,
    ));
    $shares = [];
    foreach ($rendered as $procedure => $count) {
      $shares[$procedure] = round($count * 100 / $total_rendered, 1);
    }
    $this->assertEqualsWithDelta(100.0, array_sum($shares), 0.2, sprintf(
      'The art. 8.1.a) shares must account for the whole register: %s, over %d published contracts in %d procedures.',
      implode(' | ', array_map(
        static fn (string $name, float $share): string => $name . ' ' . $share . '%',
        array_keys($shares),
        $shares,
      )),
      $total_rendered,
      $procedures,
    ));

    // -- (6) The order the display promises ---------------------------------
    // Not the exact sequence - three procedures hold two contracts each and
    // nothing in the configuration decides which of them comes first - but
    // the property the sort is there for.
    $counts = array_values($rendered);
    $sorted = $counts;
    rsort($sorted);
    $this->assertSame($sorted, $counts, sprintf(
      'The breakdown is sorted by count, largest first; it rendered %s.',
      implode(', ', $counts),
    ));

    // -- (7) The filters must not move it -----------------------------------
    // A real filter that really narrows the register: the area of some of
    // the shipped contracts, chosen from the site rather than typed, and
    // asserted to remove rows BEFORE the breakdown is asked to have ignored
    // it. A filter that filtered nothing would make this check vacuous.
    $narrowed = $this->narrowingAreaFilter();
    $this->drupalGet($path, ['query' => ['area' => $narrowed['tid']]]);
    $this->assertSession()->statusCodeEquals(200);
    $register_rows = count($this->getSession()->getPage()->findAll(
      'css',
      self::VIEW_CONTAINER . ' ' . self::REGISTER_TABLE . ' tbody tr',
    ));
    $this->assertSame($narrowed['rows'], $register_rows, sprintf(
      'Filtering the register by area %d must leave %d of its %d rows.',
      $narrowed['tid'],
      $narrowed['rows'],
      $published,
    ));
    $this->assertLessThan($published, $register_rows, 'The area chosen for this check must genuinely narrow the register, or the breakdown has nothing to have ignored.');
    $filtered = $this->readStatisticTable();
    $this->assertSame($rendered, $filtered, 'The art. 8.1.a) breakdown must be unchanged by an exposed filter: it is a statement about the whole register, and a legal figure that moves when a reader types in a filter box is a wrong figure.');

    // -- (8) And neither must the pager -------------------------------------
    $per_page = (int) $view->getDisplay('default')['display_options']['pager']['options']['items_per_page'];
    $this->assertGreaterThan(0, $per_page, 'The register must declare a page size, or the page index below has no stride.');
    $beyond = intdiv($published, $per_page) + 1;
    $this->drupalGet($path, ['query' => ['page' => $beyond]]);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->elementNotExists('css', self::VIEW_CONTAINER . ' ' . self::REGISTER_TABLE);
    $paged = $this->readStatisticTable();
    $this->assertSame($rendered, $paged, 'The breakdown must survive a page index past the end of the register: it carries its own pager precisely so that it counts the register and not a page of it.');

    // -- (9) The genuinely empty state --------------------------------------
    // BOTH HALVES REQUIRED. Views emits no table for an empty result set, so
    // the absent table alone would be satisfied by a page that rendered
    // nothing and said nothing about why (I-062).
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nodes = $storage->loadByProperties(['type' => self::STATISTIC_BUNDLE]);
    $this->assertNotEmpty($nodes, 'There must be contracts to unpublish, or the empty state below is not the state being tested.');
    foreach ($nodes as $node) {
      $node->setUnpublished()->save();
    }
    $this->assertSame(0, $this->publishedCount([self::STATISTIC_BUNDLE]), 'Every contract must now be unpublished.');
    $this->drupalGet($path);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains($empty_text);
    $this->assertSession()->elementNotExists('css', 'table.' . self::STATISTIC_TABLE_CLASS);

    // -- (10) And back, because neither state alone says they differ --------
    foreach ($nodes as $node) {
      $node->setPublished()->save();
    }
    $restored = $this->assertStatisticTable($path, $caption, $labels, $empty_text);
    $this->assertSame($rendered, $restored, 'The breakdown must come back exactly as it was: a table that renders in both states is not rendering either of them.');
  }

  /**
   * Counts the shipped contracts by procedure, from `content/` alone.
   *
   * NO DATABASE, NO VIEW AND NO QUERY. This is the independent route the
   * T-1103 row asks for, so it may not share a mechanism with the thing it
   * checks: it opens the YAML this package ships, resolves each contract's
   * procedure reference through the term files by uuid, and counts. A view
   * that grouped on the wrong field, or an import that dropped a record,
   * disagrees with it.
   *
   * Only PUBLISHED contracts are counted, because that is what the register
   * lists and therefore what the statistic is about.
   *
   * @return array<string, int>
   *   How many published contracts each procedure name holds, keyed by name
   *   and sorted by name so that comparisons are order-independent.
   */
  protected function shippedProcedureCounts(): array {
    $root = self::getRecipePath();

    $terms = [];
    $term_files = glob($root . '/content/taxonomy_term/*.yml') ?: [];
    $this->assertNotEmpty($term_files, 'This package must ship taxonomy terms in content/, or the procedure references below resolve to nothing.');
    foreach ($term_files as $file) {
      $data = Yaml::decode((string) file_get_contents($file));
      if (($data['_meta']['bundle'] ?? '') !== self::STATISTIC_VOCABULARY) {
        continue;
      }
      $uuid = (string) $data['_meta']['uuid'];
      $terms[$uuid] = (string) $data['default']['name'][0]['value'];
    }
    $this->assertNotEmpty($terms, 'This package must ship procedure-type terms, or no contract can name its procedure.');

    $counts = [];
    $node_files = glob($root . '/content/node/*.yml') ?: [];
    $this->assertNotEmpty($node_files, 'This package must ship nodes in content/.');
    foreach ($node_files as $file) {
      $data = Yaml::decode((string) file_get_contents($file));
      if (($data['_meta']['bundle'] ?? '') !== self::STATISTIC_BUNDLE) {
        continue;
      }
      if (($data['default']['status'][0]['value'] ?? FALSE) !== TRUE) {
        continue;
      }
      $reference = $data['default'][self::STATISTIC_FIELD][0]['entity'] ?? NULL;
      $this->assertNotNull($reference, sprintf(
        '%s names no procedure, so the breakdown cannot account for it.',
        basename($file),
      ));
      $this->assertArrayHasKey($reference, $terms, sprintf(
        '%s references a term this package does not ship.',
        basename($file),
      ));
      $name = $terms[$reference];
      $counts[$name] = ($counts[$name] ?? 0) + 1;
    }

    ksort($counts);
    return $counts;
  }

  /**
   * Asserts the breakdown's markup, then reads its rows.
   *
   * THE ORDER IS THE POINT. The row count is asserted before any structural
   * claim, because Views renders no table at all for an empty result set and
   * every markup assertion below would hold over a page with nothing on it
   * (I-062).
   *
   * @param string $path
   *   The register's path.
   * @param string $caption
   *   The caption the display declares.
   * @param array $labels
   *   The two column labels the display declares, in column order.
   * @param string $empty_text
   *   The empty text, which must appear nowhere on a populated page.
   *
   * @return array<string, int>
   *   The rendered breakdown, procedure name to count, in rendered order.
   */
  protected function assertStatisticTable(string $path, string $caption, array $labels, string $empty_text): array {
    $assert = $this->assertSession();
    $this->drupalGet($path);
    $assert->statusCodeEquals(200);

    // Scoped inside the main landmark: a statistic rendered outside the
    // region the skip link leads to is not part of the page's content.
    $table = 'main.agora-page__main table.' . self::STATISTIC_TABLE_CLASS;
    $assert->elementsCount('css', $table, 1);

    $rows = $this->getSession()->getPage()->findAll('css', $table . ' tbody tr');
    $this->assertNotEmpty($rows, 'The breakdown must render rows before its markup is worth asserting: an empty result set renders no table and no violations, truthfully and about nothing (I-062).');
    $row_count = count($rows);

    // WCAG 2.2 AA, 1.3.1. The caption is the table's accessible name, and it
    // is the view's own string rather than one typed here.
    $assert->elementsCount('css', $table . ' > caption', 1);
    $assert->elementTextContains('css', $table . ' > caption', $caption);
    // Both column headers, and every one of them scoped. A `th` without a
    // scope in a table that also has scoped ones slips past a count that
    // only looks at the scoped set.
    $assert->elementsCount('css', $table . ' thead th', 2);
    $assert->elementsCount('css', $table . ' thead th[scope="col"]', 2);
    foreach ($labels as $index => $label) {
      $assert->elementTextContains(
        'css',
        $table . ' thead th:nth-child(' . ($index + 1) . ')',
        $label,
      );
    }
    // The procedure is what each row is ABOUT, so it is the row's header and
    // not a data cell. The theme promotes the first column to a row header
    // when the view declares `agora-row-header-first`, which this display
    // does; asserting it here is what makes that declaration load-bearing
    // rather than an inert class nobody notices the loss of.
    $assert->elementsCount('css', $table . ' tbody th[scope="row"]', $row_count);
    $assert->pageTextNotContains($empty_text);

    return $this->readStatisticTable();
  }

  /**
   * Reads the breakdown off the page currently loaded.
   *
   * @return array<string, int>
   *   Procedure name to count, in the order the page rendered them.
   */
  protected function readStatisticTable(): array {
    $table = 'table.' . self::STATISTIC_TABLE_CLASS;
    $rows = $this->getSession()->getPage()->findAll('css', $table . ' tbody tr');
    $this->assertNotEmpty($rows, 'The breakdown must be on this page for its rows to be read.');

    $counts = [];
    foreach ($rows as $row) {
      $header = $row->find('css', 'th');
      $this->assertNotNull($header, 'Every row of the breakdown carries the procedure as its row header.');
      $cell = $row->find('css', 'td');
      $this->assertNotNull($cell, 'Every row of the breakdown carries its count.');
      $name = trim($header->getText());
      $value = trim($cell->getText());
      $this->assertMatchesRegularExpression('/^\d+$/', $value, "The count beside \"$name\" must be a whole number; it rendered \"$value\".");
      $counts[$name] = (int) $value;
    }
    return $counts;
  }

  /**
   * Picks an area filter value that genuinely narrows the register.
   *
   * Read off the site rather than typed, so that changing the demo corpus
   * moves what this test filters by instead of breaking it.
   *
   * @return array{tid: int, rows: int}
   *   The term id to filter by, and how many contracts carry it.
   */
  protected function narrowingAreaFilter(): array {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $ids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', self::STATISTIC_BUNDLE)
      ->condition('status', 1)
      ->execute();
    $this->assertNotEmpty($ids, 'There must be published contracts for an area filter to narrow.');

    $by_area = [];
    foreach ($storage->loadMultiple($ids) as $node) {
      $tid = (int) $node->get('field_agora_base_area')->target_id;
      $by_area[$tid] = ($by_area[$tid] ?? 0) + 1;
    }
    // The largest group that is still smaller than the whole: it has to
    // narrow, or the check that follows proves nothing.
    $total = count($ids);
    $candidates = array_filter($by_area, static fn (int $n): bool => $n < $total);
    $this->assertNotEmpty($candidates, 'Every contract shares one service area, so no area filter can narrow the register and this check cannot be made.');
    arsort($candidates);
    $tid = (int) array_key_first($candidates);
    return ['tid' => $tid, 'rows' => $candidates[$tid]];
  }

  /**
   * Renders a breakdown for a failure message.
   *
   * @param array<string, int> $counts
   *   Procedure name to count.
   *
   * @return string
   *   One readable line.
   */
  protected function describeCounts(array $counts): string {
    return implode(' | ', array_map(
      static fn (string $name, int $count): string => "$name=$count",
      array_keys($counts),
      $counts,
    ));
  }

  /**
   * The seven money field instances (D-047), by bundle and by field name.
   *
   * TYPED ON PURPOSE, and it is the guard rather than the subject. Every other
   * count in the method below is read from the installed site, but this list
   * is what makes the sweep fail on the one mistake D-047 names by name: a
   * bulk edit over "every field instance with a prefix key" would put a
   * currency unit on `field_agora_base_bidder_count`, which is an integer
   * labelled "Number of bidders". The site is asked which instances carry a
   * unit and the answer is compared against this list, so BOTH directions
   * fail - a money field that lost its unit, and a non-money field that
   * gained one. A test that only looked for the unit could not tell those
   * apart, and only one of them is visible in a screenshot.
   */
  private const MONEY_FIELDS = [
    'agora_base_agreement' => [
      'field_agora_base_amount',
      'field_agora_base_obligations',
    ],
    'agora_base_contract' => [
      'field_agora_base_amount',
      'field_agora_base_tender_amount',
    ],
    'agora_base_grant' => [
      'field_agora_base_amount',
    ],
    'agora_base_person' => [
      'field_agora_base_remuneration',
      'field_agora_base_severance',
    ],
  ];

  /**
   * The field instance that carries the same keys and must stay bare.
   *
   * Named here rather than left to be inferred from a missing entry above,
   * because "it is not in the list" is a fact about the list and not an
   * assertion about the site.
   */
  private const COUNT_FIELD = [
    'agora_base_contract',
    'field_agora_base_bidder_count',
  ];

  /**
   * Tests that money renders with its unit and that counts render without one.
   *
   * T-1308, and the defect it closes is INCONSISTENCY rather than silence.
   * Before this change one office-holder's remuneration appeared three ways in
   * a single install: the register table rendered `21,300.00`, the shipped
   * declaration PDF beside it read `21300.00 EUR`, and the front page printed
   * its total bare. A transparency portal whose headline number does not say
   * what unit it is in is ambiguous about the one thing it exists to publish.
   *
   * THE UNIT IS NEVER TYPED IN THIS METHOD. Every assertion reads the prefix
   * from the field instance the site actually installed and then looks for
   * THAT string in the markup, so what is tested is the MECHANISM D-047 chose
   * - the unit lives in configuration, a site owner in another jurisdiction
   * edits seven fields in the UI and the whole register set follows - rather
   * than the euro sign this package happens to ship. Hard-code the symbol here
   * and the test would go green on a build that had stopped reading the
   * configuration at all, which is exactly the property that matters.
   *
   * THE DENOMINATORS COME FIRST, all three of them. The number of field
   * instances inspected, the number carrying `prefix`/`suffix` keys at all,
   * and the number carrying a NON-EMPTY prefix are each asserted before any
   * rendering is looked at: a sweep that opened nothing reports "no findings"
   * in exactly the same words as a clean one (I-028), and every count below
   * would hold vacuously over a site whose fields failed to import.
   *
   * FOURTEEN RENDERING SITES, COUNTED ON THE RENDERED PAGES. Seven field
   * handlers in four register views and seven formatters in four node record
   * sheets. Counting them in the config instead would only re-read the file
   * that was just edited; counting them in the markup is what says the
   * `prefix_suffix: true` on each of those fourteen is actually honoured.
   *
   * AND THE TWO NEGATIVES, which are half the row. `bidder_count` renders on
   * a contract record sheet as bare digits, and the record COUNTS on the front
   * page render with no unit either. Both are things a bulk edit would break
   * silently, and neither would be caught by any assertion about money.
   */
  public function testMoneyFieldsShowTheirConfiguredUnit(): void {
    $this->applyRecipe(self::getRecipePath());

    // A reader, not an editor: these figures exist to be read by the public.
    $this->drupalLogin($this->drupalCreateUser(['access content']));
    $assert = $this->assertSession();
    $storage = \Drupal::entityTypeManager()->getStorage('node');

    // -- The sweep, over EVERY field instance the site installed ------------
    // Read from the site rather than from a list, so a field added to the
    // model joins this denominator on its own instead of being missed by a
    // test that agrees with a number typed beside it.
    $inspected = 0;
    $carrying = [];
    $with_unit = [];
    $field_manager = \Drupal::service('entity_field.manager');
    $bundles = \Drupal::service('entity_type.bundle.info')->getBundleInfo('node');
    foreach (array_keys($bundles) as $bundle) {
      foreach ($field_manager->getFieldDefinitions('node', $bundle) as $name => $definition) {
        if (!$definition instanceof FieldConfig) {
          continue;
        }
        $inspected++;
        $settings = $definition->getSettings();
        if (!array_key_exists('prefix', $settings)) {
          continue;
        }
        $carrying[] = $bundle . '.' . $name;
        if ($settings['prefix'] !== '') {
          $with_unit[$bundle . '.' . $name] = $settings['prefix'];
        }
      }
    }

    $expected = [];
    foreach (self::MONEY_FIELDS as $bundle => $names) {
      foreach ($names as $name) {
        $expected[] = $bundle . '.' . $name;
      }
    }
    sort($expected);

    // Denominators, in the order that makes each one meaningful.
    $this->assertGreaterThan(0, $inspected, 'This site installed no configurable node fields at all, so every assertion below would hold over nothing.');
    $this->assertGreaterThan(count($expected), count($carrying), 'More field instances must carry prefix/suffix keys than are money, or the discrimination this test exists for is untestable on this site.');

    $found = array_keys($with_unit);
    sort($found);
    $this->assertSame($expected, $found, 'Exactly the seven money field instances of D-047 must carry a currency unit - no fewer, and no others.');

    // Money is decimal and counts are integers; that is what separates the
    // seven from the eighth, and it is a property of the model rather than of
    // the list above.
    foreach ($found as $id) {
      [$bundle, $name] = explode('.', $id, 2);
      $definition = $field_manager->getFieldDefinitions('node', $bundle)[$name];
      $this->assertSame('decimal', $definition->getType(), "$id carries a currency unit but is not a decimal field, so something that is not money has been given one.");
    }

    // -- The eighth instance, named because it is the trap ------------------
    [$count_bundle, $count_name] = self::COUNT_FIELD;
    $count_id = $count_bundle . '.' . $count_name;
    $count_definition = $field_manager->getFieldDefinitions('node', $count_bundle)[$count_name] ?? NULL;
    $this->assertInstanceOf(FieldConfig::class, $count_definition, "$count_id must exist, or this test is protecting a field that is no longer there.");
    $this->assertContains($count_id, $carrying, "$count_id must still carry the prefix/suffix keys, or it is no longer the field this test guards.");
    $this->assertNotContains($count_id, $found, "$count_id is a count of bidders and must never carry a currency unit.");
    $this->assertSame('integer', $count_definition->getType(), "$count_id must be an integer; a decimal count of bidders would make the rule above ambiguous.");

    $units = array_values(array_unique(array_values($with_unit)));

    // -- SEVEN RENDERING SITES: the register views --------------------------
    $register_sites = 0;
    foreach (self::MONEY_FIELDS as $bundle => $names) {
      $view_id = (string) array_search($bundle, self::TABLE_VIEWS, TRUE);
      $this->assertArrayHasKey($view_id, self::TABLE_VIEWS, "$bundle must be listed by one of the registers, or its columns render nowhere.");
      $view = View::load($view_id);
      $this->assertNotNull($view, "$view_id must have been imported by the recipe.");
      $this->drupalGet($view->getDisplay('page_1')['display_options']['path']);
      $assert->statusCodeEquals(200);

      foreach ($names as $name) {
        $unit = $with_unit[$bundle . '.' . $name];
        $selector = self::VIEW_CONTAINER . ' table tbody td.views-field-' . Html::cleanCssIdentifier($name);
        $cells = $this->getSession()->getPage()->findAll('css', $selector);
        // A selector that matches nothing would let every assertion in the
        // loop below pass without one of them running (I-045).
        $this->assertNotEmpty($cells, "No cell on $view_id matched `$selector`, so this column is either absent or wearing a different class.");
        foreach ($cells as $cell) {
          $this->assertMatchesRegularExpression(
            '/^' . preg_quote($unit, '/') . '[\d,]+\.\d{2}$/u',
            trim($cell->getText()),
            "Every $name cell on $view_id must render the configured unit immediately before the formatted amount.",
          );
        }
        $register_sites++;
      }
    }
    $this->assertSame(count($expected), $register_sites, 'Each of the seven money fields must be shown carrying its unit in its register.');

    // -- SEVEN MORE: the node record sheets ---------------------------------
    // Rendered from the SHIPPED corpus rather than from a fixture: a record
    // sheet is the page a visitor reaches from a register row, and the demo
    // records are what they will find on it.
    //
    // LOCATED BY LABEL, NOT BY CLASS, and that was learned the hard way. The
    // obvious selector is core's `.field--name-<field>`, which this suite ran
    // against and found ZERO of: the recipe installs `agora_theme`, whose
    // `field.html.twig` override emits `agora-field` and drops the field-name
    // class entirely, so a class-based check here would have been a check on
    // which theme happens to be default. The label's parent element is the
    // field wrapper in BOTH templates, so that is what is walked - and if the
    // label ever matches two elements, `assertCount(1)` says so rather than
    // silently reading the first.
    $sheet_sites = 0;
    foreach (self::MONEY_FIELDS as $bundle => $names) {
      $display = EntityViewDisplay::load("node.$bundle.default");
      $this->assertNotNull($display, "node.$bundle.default must have been imported, or these fields render through no display at all.");

      foreach ($names as $name) {
        $unit = $with_unit[$bundle . '.' . $name];
        $component = $display->getComponent($name);
        $this->assertNotNull($component, "$name must be a visible component of node.$bundle.default, or its record sheet shows no amount to put a unit on.");
        $this->assertTrue((bool) $component['settings']['prefix_suffix'], "node.$bundle.default must honour prefix/suffix on $name, or the configured unit is read and thrown away.");

        $nids = $storage->getQuery()
          ->accessCheck(FALSE)
          ->condition('type', $bundle)
          ->condition('status', 1)
          ->exists($name)
          ->range(0, 1)
          ->execute();
        $this->assertNotEmpty($nids, "No published $bundle ships a value in $name, so its record sheet would prove nothing about the unit.");
        $node = $storage->load(reset($nids));
        $this->assertNotNull($node, "The $bundle the query just returned must load.");
        $this->drupalGet($node->toUrl());
        $assert->statusCodeEquals(200);

        // The whole string, built from the value the site stores and the
        // separators the display declares - not merely "it starts with the
        // unit". A formatter that emitted the unit and then mangled the
        // number would pass the weaker check.
        $formatted = $unit . number_format(
          (float) $node->get($name)->value,
          (int) $component['settings']['scale'],
          $component['settings']['decimal_separator'],
          $component['settings']['thousand_separator'],
        );
        $value = $this->fieldValueOnPage((string) $field_manager->getFieldDefinitions('node', $bundle)[$name]->getLabel(), $node->label());
        $this->assertSame($formatted, $value, "The record sheet of \"{$node->label()}\" must render $name as the configured unit followed by the formatted amount; a register that shows the unit beside a record sheet that does not is the inconsistency D-047 was opened over.");
        $sheet_sites++;
      }
    }
    $this->assertSame(count($expected), $sheet_sites, 'Each of the seven money fields must be shown carrying its unit on a record sheet.');

    // -- NEGATIVE 1: a count of bidders is not money ------------------------
    $nids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', $count_bundle)
      ->condition('status', 1)
      ->exists($count_name)
      ->range(0, 1)
      ->execute();
    $this->assertNotEmpty($nids, "No published $count_bundle ships a value in $count_name, so the assertion below would hold over an unrendered field.");
    $node = $storage->load(reset($nids));
    $this->assertNotNull($node, "The $count_bundle the query just returned must load.");
    $this->drupalGet($node->toUrl());
    $assert->statusCodeEquals(200);
    // Bare digits and NOTHING else. Asserted as a whole string rather than as
    // "it does not contain a euro sign", because the euro sign is not the only
    // way this could go wrong and a substring check on this page would trip
    // over the contract's own award amount a few lines above it.
    $bidders = $this->fieldValueOnPage((string) $count_definition->getLabel(), $node->label());
    $this->assertMatchesRegularExpression('/^\d+$/u', $bidders, "$count_name must render as bare digits: it counts bidders, and a currency unit on it would be shipped nonsense.");
    $this->assertSame((string) $node->get($count_name)->value, $bidders, "$count_name must render the value the site stores, so the check above cannot pass over some other number on the page.");

    // -- NEGATIVE 2: the record counts on the front page --------------------
    $this->drupalGet('<front>');
    $assert->statusCodeEquals(200);
    $counts = $this->getSession()->getPage()->findAll('css', self::VIEW_CONTAINER . ' .views-field-nid');
    $this->assertNotEmpty($counts, 'The front page must render at least one record-count field, or the assertion below holds over nothing.');
    $inspected_counts = 0;
    $figures = 0;
    foreach ($counts as $figure) {
      $text = trim($figure->getText());
      foreach ($units as $unit) {
        $this->assertStringNotContainsString($unit, $text, "A record count on the front page carries the currency unit `$unit`; it counts records, not money.");
      }
      $inspected_counts++;
      $figures += (int) (preg_match('/\d/u', $text) === 1);
    }
    // Two denominators rather than one, because they answer different
    // questions. The first says every matched element was inspected. The
    // second says at least one of them was a FIGURE: the same class is worn by
    // block_4's column heading, which reads "Awards" and carries no digits at
    // all, so a per-element digit requirement would fail on a heading that is
    // perfectly correct - while a check with no digit requirement anywhere
    // would pass over a front page whose counts had all stopped rendering.
    $this->assertSame(count($counts), $inspected_counts, 'Every record-count field on the front page must have been inspected.');
    $this->assertGreaterThan(0, $figures, 'None of the front page record-count fields rendered a digit, so the unit check above ran over headings only.');
  }

  /**
   * The two T-603 surfaces, and the bundles each of them is meant to list.
   *
   * TRANSCRIBED, NEVER READ FROM THE VIEW, and that is the whole point of the
   * pair. Read from each view's own bundle filter, the expected row counts
   * would agree with whatever the view lists, and a library that quietly
   * started listing all six bundles would pass. Transcribed, the count is
   * DERIVED from the site - an entity query over these bundles - so the
   * numbers track the demo corpus while the SET stays a decision somebody has
   * to change on purpose.
   *
   * This replaces a pair of hard-coded row counts (6 and 2) that were true
   * only while the sole content on the site was this test's own six-node
   * fixture. The package now ships a demo corpus, both surfaces paginate, and
   * two constants clamped to the page size would have proved nothing.
   */
  private const SURFACE_BUNDLES = [
    'agora_base_publications' => [
      'agora_base_document',
      'agora_base_person',
      'agora_base_contract',
      'agora_base_agreement',
      'agora_base_grant',
      'agora_base_dataset',
    ],
    'agora_base_library' => [
      'agora_base_document',
      'agora_base_dataset',
    ],
  ];

  /**
   * The nine routes the main menu must link, in menu order.
   *
   * Transcribed rather than derived, on purpose: derived from the views, this
   * would assert that the menu links whatever it links.
   *
   * ⚠️ EIGHT OF THE NINE ARE VIEWS PAGE DISPLAYS; the ninth is not, and that
   * is why it is last. `/institution` is a Canvas page, so no view can carry
   * it and it is the one `menu_link_content` entity in `main` (T-1309). The
   * eight above it are plugin derivatives that only exist once the menu link
   * manager has rebuilt - which is the reason the test rebuilds before
   * counting, and the reason a duplicate check over BOTH sources is worth
   * running at all (T-1008 shipped 8 entities that duplicated the 8
   * derivatives and rendered 16 links).
   */
  private const MENU_ROUTES = [
    '/publications' => 'All publications',
    '/documents' => 'Documents',
    '/people' => 'People',
    '/contracts' => 'Contracts',
    '/agreements' => 'Agreements',
    '/grants' => 'Grants',
    '/datasets' => 'Datasets',
    '/library' => 'Document library',
    '/institution' => 'The institution',
  ];

  /**
   * The four links the legal bottom bar must carry, in menu order.
   *
   * Transcribed for the reason MENU_ROUTES is. ⚠️ These four are the reason
   * the row exists: T-1215 deliberately shipped NO privacy, accessibility or
   * contact link because none of the three had a destination that answered,
   * and `/privacy-policy` - the page `drupal_cms_privacy_basic` ships
   * unpublished - answered 404. A link that goes nowhere is the defect that
   * decision refused to ship, so the test asserts the destinations ANSWER
   * rather than that the links exist (T-1311, T-1312, T-1105).
   */
  private const LEGAL_LINKS = [
    '/accessibility-statement' => 'Accessibility statement',
    '/legal-notice' => 'Legal notice',
    '/privacy-notice' => 'Privacy notice',
    '/cookies' => 'Cookies',
  ];

  /**
   * The four links the `Follow us` footer menu must carry, in menu order.
   *
   * Transcribed for the reason MENU_ROUTES is: derived from the shipped
   * content, this would assert that the menu links whatever it links. Every
   * key is a network's ROOT URL and never an account - the demo municipality
   * is fictional and owns no account anywhere (T-1216, D-046) - and the test
   * asserts href EQUALITY, so the demo cannot quietly acquire one.
   */
  private const SOCIAL_LINKS = [
    'https://www.facebook.com/' => 'Facebook',
    'https://x.com/' => 'X',
    'https://www.instagram.com/' => 'Instagram',
    'https://www.youtube.com/' => 'YouTube',
  ];

  /**
   * Tests the library, the cross-type listing, the search box and the menu.
   *
   * FOUR THINGS THAT NEED A RUNNING SITE. The kernel test reads `config/` and
   * can prove what the two surfaces DECLARE; none of the four below is visible
   * there.
   *
   * (1) THE POPULATED STATE, WHICH USED TO BE THE EMPTY ONE. Unit 002 shipped
   * no content, so this asserted that both routes said why they were empty.
   * The package now ships a published demo corpus, so both routes are never
   * empty on a clean install and that assertion had inverted into a claim
   * that the product is absent. What is asserted instead is the stronger
   * half - both surfaces render every published record they list, across
   * every page of the pager - and the empty state is kept, reached through a
   * search that matches nothing, which is a state a real reader reaches and
   * which needs no content to be deleted to get there.
   *
   * (2) THE RENDERED TABLE. One `<table>`, one `<caption>`, a
   * `<th scope="col">` for every column, and a `<td>` for every column in
   * every row. The T-603 row allowed "a `<table>` with `<th scope>` on every
   * header cell, OR an equivalent semantic list". That disjunction has no
   * definition a test can evaluate, so the structure is CHOSEN - a table, on
   * both surfaces - and the table is what is asserted.
   *
   * (3) NO CELL IS EMPTY, and here that assertion means something it would not
   * mean on a per-bundle table. These two surfaces show the INTERSECTION of
   * their bundles' fields; that is their entire design, and this is what makes
   * it falsifiable. Add a column only some of the listed bundles carry - a
   * financial year on the six-bundle listing, say - and rows of the other
   * bundles render a structurally empty cell, which is exactly the union-type
   * defect D-026 refused. Verified BY FALSIFICATION before being trusted: with
   * `field_agora_base_financial_year` added to the cross-type listing this
   * assertion fails on four of the six rows.
   *
   * ⚠️ IT NOW RUNS OVER THE DEMO CORPUS TOO, NOT ONLY OVER THE FIXTURE, AND
   * THAT IS DELIBERATE. A cell left blank because a record has no value is
   * indistinguishable, to a screen-reader user, from a cell blank because the
   * column does not belong on the surface; WCAG 2.2 AA 1.3.1 does not care
   * which cause produced it. So the failure message names the COLUMN and the
   * RECORD, and either fix is legitimate: give the record a value, or drop
   * the column from a surface where it cannot always be filled.
   *
   * (4) THE SEARCH BOX ACTUALLY FILTERS. A `<input name="search">` in the
   * exposed form proves a box exists; only a request with a value in it proves
   * the box is wired to the query. Both are asserted, and so is the no-match
   * case, which must fall back to the empty state rather than an empty table.
   *
   * AND THE MENU, which is the carried debt this row absorbed: six accessible
   * tables nobody can reach is not a delivered feature. The links are declared
   * as views page-display menu options, so what has to be proved on a live
   * site is that core's deriver turns them into real menu links with real
   * hrefs - the config could be perfect and the deriver still produce nothing,
   * because a menu link whose parent does not resolve is silently moved to
   * the root, and a flat menu still looks like a menu.
   */
  public function testBaseSurfacesAndMenu(): void {
    $this->applyRecipe(self::getRecipePath());
    $this->drupalLogin($this->drupalCreateUser(['access content']));

    $assert = $this->assertSession();
    $columns = [];
    $empty_text = [];
    $paths = [];
    $per_page = [];
    $shipped = [];

    // -- (1) The denominators, read before this test creates anything -------
    foreach (self::SURFACE_BUNDLES as $view_id => $bundles) {
      $view = View::load($view_id);
      $this->assertNotNull($view, "$view_id must have been imported by the recipe.");
      $display = $view->getDisplay('default')['display_options'];

      $columns[$view_id] = count($display['fields']);
      $this->assertGreaterThan(0, $columns[$view_id], "$view_id must declare columns, or every count below holds vacuously.");

      $per_page[$view_id] = (int) $display['pager']['options']['items_per_page'];
      $this->assertGreaterThan(0, $per_page[$view_id], "$view_id must declare a page size, or the page walk below has no stride.");

      $empty_text[$view_id] = reset($display['empty'])['content'];
      $paths[$view_id] = $view->getDisplay('page_1')['display_options']['path'];

      $shipped[$view_id] = $this->publishedCount($bundles);
      $this->assertGreaterThan(0, $shipped[$view_id], "This package must ship published records for $view_id to list, or every row count below holds over an empty table.");

      $this->drupalGet($paths[$view_id]);
      $assert->statusCodeEquals(200);
      // (4a) The box is present on the listing itself, which is where a
      // reader reaches for it.
      $assert->elementExists('css', self::VIEW_CONTAINER . ' input[name="search"]');
      // WCAG 2.2 AA 3.3.2: the input carries a label, not just a placeholder.
      $assert->elementExists('css', self::VIEW_CONTAINER . ' label[for]');
    }

    // The pair is only discriminating if the two numbers differ. Asserted on
    // the DERIVED counts rather than on two constants: the library lists the
    // two bundles whose payload is a published file, so it must be a strict
    // subset of the listing that shows every type. A library that quietly
    // started listing everything fails here, by name, before any markup is
    // looked at.
    $this->assertLessThan(
      $shipped['agora_base_publications'],
      $shipped['agora_base_library'],
      'The library must list strictly fewer records than the cross-type listing, or the two surfaces are the same surface twice.',
    );

    // -- The fixture: one node per bundle, every field populated -------------
    // Identical in kind to testTableViews()'s fixture and for the same reason:
    // it lives in this class, uses the test database, and therefore never goes
    // near `drush site:export`, so nothing it creates can reach `content/` BY
    // CONSTRUCTION rather than by anyone remembering. It is still needed now
    // that the package ships a corpus: a real record may leave an optional
    // field blank, so only a node built from the field definitions gives the
    // search below a title that is unique and certain to exist.
    $titles = [];
    foreach (self::TABLE_VIEWS as $bundle_view => $bundle) {
      $values = ['type' => $bundle, 'title' => 'Fixture ' . $bundle, 'status' => 1];
      $definitions = \Drupal::service('entity_field.manager')
        ->getFieldDefinitions('node', $bundle);
      foreach ($definitions as $field_name => $definition) {
        if ($definition instanceof FieldConfig) {
          $values[$field_name] = $this->fixtureValue($definition);
        }
      }
      $this->drupalCreateNode($values);
      $titles[$bundle] = $values['title'];
    }
    $this->assertCount(6, $titles, 'One node per bundle, or the row counts below are measuring the fixture.');

    // -- (2) and (3): the rendered tables, every page of them ----------------
    $observed = [];
    foreach (self::SURFACE_BUNDLES as $view_id => $bundles) {
      $count = $columns[$view_id];
      // The shipped corpus plus the fixture nodes of the bundles THIS surface
      // lists - two of the six for the library, all six for the listing.
      // Bound to a variable rather than written inline: phpstan's
      // phpunit.assertCount rule is blocking here and rejects a `count()`
      // sitting directly in an assertion's arguments.
      $fixtures_here = count($bundles);
      $expected_rows = $this->publishedCount($bundles);
      $this->assertSame($shipped[$view_id] + $fixtures_here, $expected_rows, "The records $view_id lists must be the shipped corpus plus one fixture node per bundle it lists.");

      [$headers, $rows] = $this->assertPagedTable($paths[$view_id], $count, $expected_rows, $per_page[$view_id], $empty_text[$view_id]);
      $observed[$view_id] = $rows;

      // (3) Not one empty cell, on a surface whose columns are the
      // intersection precisely so that there cannot be one.
      $blank = [];
      foreach ($rows as $row) {
        $blank = array_merge($blank, $this->blankCells($headers, $row));
      }
      $this->assertSame([], $blank, "$view_id renders a cell a screen-reader user cannot tell from missing data. Either the record needs a value, or the column is not one every listed bundle can always fill and does not belong on an intersection surface.");
    }

    // The same claim the two hard-coded row counts used to make, now made on
    // what was actually rendered: the library is a strict subset. This is the
    // assertion a surface that started listing everything fails.
    $listed = count($observed['agora_base_publications']);
    $shelved = count($observed['agora_base_library']);
    $this->assertLessThan(
      $listed,
      $shelved,
      'The library must render strictly fewer rows than the cross-type listing.',
    );

    // -- (4b) The search box is wired to the query ---------------------------
    // The needle is the fixture grant's title, which is unique on the site
    // and belongs to a bundle the library does NOT list - so one row on the
    // cross-type listing is a claim about filtering, and it now has to be
    // one row out of a corpus rather than one row out of six.
    $needle = $titles['agora_base_grant'];
    $this->drupalGet($paths['agora_base_publications'], ['query' => ['search' => $needle]]);
    $assert->statusCodeEquals(200);
    $assert->elementsCount('css', self::VIEW_CONTAINER . ' table tbody tr', 1);
    $assert->elementTextContains('css', self::VIEW_CONTAINER . ' table tbody', $needle);

    // -- (1) The empty state, reached without deleting anything --------------
    // A search that matches nothing must reach the empty state, not an empty
    // table - the same accessibility defect, arrived at from the other side,
    // and now the cheapest way to reach a state the shipped corpus no longer
    // puts these surfaces in. Asserted on BOTH surfaces, where it used to be
    // asserted on publications alone.
    //
    // 🔴 THE WORDING HERE IS OWNED BY `agora_theme`, NOT BY THIS PACKAGE, and
    // that is why the exact string is NOT asserted at this one call site.
    // `agora_theme_preprocess_views_view()` (theme commit b02e0de, released in
    // 1.2.0) replaces the view's configured empty text whenever an exposed
    // filter is carrying user input, because "Nothing has been published yet."
    // in answer to a search that merely matched nothing is a FALSE COMPLIANCE
    // CLAIM about a public body. The theme is right and the register's own
    // config cannot make that distinction: to a view, both are zero rows.
    //
    // ⚠️ AND THIS PACKAGE PINS NO THEME VERSION. `composer.json` requires
    // `drupal/agora_theme: ^1.1`, nothing here is locked, and what a clean
    // install actually receives is decided by packages.drupal.org AT INSTALL
    // TIME. So a theme release - a repository with its own cadence, that no
    // commit here participates in - would turn this gate red with nothing in
    // this repository to blame. Asserting the theme's NEW string would be the
    // same defect mirrored: green only against 1.2.0 and red against 1.1.0.
    // What is asserted is what this package owns and what holds across both:
    // no table, and an empty region that carries text.
    //
    // ⚠️ THE STRING ITSELF IS STILL COVERED, where it is true: the two call
    // sites in ::testTableViews() reach the empty state WITHOUT exposed input
    // - one past the last pager page, one with the bundle unpublished - so
    // the theme's replacement never fires there and both still assert the
    // configured text verbatim. Measured stable across both theme versions.
    //
    // ⚠️ SECOND TIME `agora_theme`'s RELEASE CADENCE HAS REACHED INTO THIS
    // PACKAGE'S GATE; the four `canvas.component.sdc.agora_theme.*` prefixes
    // in `recipe.yml` were the first. Recorded here rather than in an
    // invariant because the coupling is a fact a reader of this test needs at
    // the moment they read it, and a script cannot tell a string this package
    // owns from one it merely renders.
    foreach (array_keys(self::SURFACE_BUNDLES) as $view_id) {
      $this->assertEmptyState($paths[$view_id], ['query' => ['search' => 'zzzz-no-such-record']], empty_text: NULL);
    }

    // -- The menu, on a live site --------------------------------------------
    // Rebuilt explicitly: the designed links are plugin DERIVATIVES of the
    // views, so they exist only once the menu link manager has looked at the
    // views the recipe imported. ⚠️ The rebuild is also what makes the
    // duplicate check below meaningful: `menu_link_content` entities imported
    // from `content/` land in the same menu from the other direction, and
    // only a menu with BOTH sources resolved shows whether the two collide.
    \Drupal::service('plugin.manager.menu.link')->rebuild();
    $block = $this->drupalPlaceBlock('system_menu_block:main', ['region' => 'content']);
    $selector = '#block-' . str_replace('_', '-', $block->id());

    $this->drupalGet('<front>');
    $assert->statusCodeEquals(200);
    $assert->elementExists('css', $selector);

    // Drupal prefixes every internal href with the site's BASE PATH, which is
    // empty only when the docroot IS the server root. The drupalci runner
    // serves the docroot at http://localhost/web, so there the menu emits
    // `/web/publications`; a DDEV rig serves it at the root and emits
    // `/publications`. Hard-coding the second is what turned this gate red
    // while every local run stayed green - the rig could not see the bug
    // because the rig is the environment the bug is invisible in.
    //
    // NOT a loosened selector: this stays an exact `href="..."` match on a
    // whole path. `a[href$="/publications"]` would pass here too and would be
    // the forbidden move, because it also matches a link to
    // /anything-else/publications. Only the expected VALUE is corrected.
    //
    // `base_path()` is the same call `testFrontPageRoundTrip()` below already
    // relies on, and that test passed on the runner in the very pipeline this
    // one failed in - so this is a mechanism observed working there, not a
    // second guess at how the runner is laid out. It returns a trailing
    // slash ('/' or '/web/') and MENU_ROUTES carries a leading one.
    $base_path = rtrim(base_path(), '/');

    foreach (self::MENU_ROUTES as $route => $title) {
      $link = $assert->elementExists('css', $selector . ' a[href="' . $base_path . $route . '"]');
      $this->assertSame($title, trim($link->getText()), "The main menu's link to $route must carry the title T-603 gives it.");
    }
    // Exactly eight, so a ninth link appearing from somewhere is a change
    // somebody has to make on purpose.
    //
    // ⚠️ AND THE HREFS ARE LISTED, not merely counted, because a bare count
    // says "16, expected 8" and leaves whoever reads it to find out why. The
    // failure this catches is a SECOND set of links to the same eight routes
    // - the exact shape a menu acquires when links are declared twice, once
    // as views page-display menu options and once as `menu_link_content`
    // entities in `content/`. A duplicated main navigation is a WCAG 2.2
    // problem before it is a tidiness one: a keyboard user tabs the whole
    // navigation twice, and a screen-reader user hears every destination
    // announced twice with no way to tell the two apart.
    $hrefs = array_map(
      static fn ($link): string => (string) $link->getAttribute('href'),
      $this->getSession()->getPage()->findAll('css', $selector . ' a'),
    );
    $duplicated = array_values(array_unique(array_diff_assoc($hrefs, array_unique($hrefs))));
    $this->assertSame([], $duplicated, 'The main menu links the same route more than once. Each of these destinations is declared twice, so every reader meets the whole navigation twice over.');
    $assert->elementsCount('css', $selector . ' a', count(self::MENU_ROUTES));

    // The six tables hang UNDER the cross-type listing rather than beside it,
    // which is the difference between a menu and a list of eight things.
    $assert->elementsCount('css', $selector . ' li li a', count(self::TABLE_VIEWS));
    $assert->elementExists('css', $selector . ' li li a[href="' . $base_path . '/contracts"]');

    // And every route the menu offers actually answers.
    foreach (array_keys(self::MENU_ROUTES) as $route) {
      $this->drupalGet(ltrim($route, '/'));
      $assert->statusCodeEquals(200);
    }

    // -- The footer, on the same live site -----------------------------------
    // Six navigation landmarks inside the theme's <footer>, each named by
    // its visible <h2>: the four columns T-1215 ships, the social row
    // T-1216 adds and the legal bottom bar T-1311 adds. Counted INSIDE the
    // footer element on purpose - the page also carries the main menu and the
    // quick-access component as <nav>, so a page-wide count of six would go
    // green on the wrong six.
    $this->drupalGet('<front>');
    $assert->statusCodeEquals(200);
    $footer = 'footer.agora-page__footer';
    $assert->elementsCount('css', $footer . ' nav', 6);

    // The social row: exactly four links, every href EXACTLY one network's
    // root URL and every text exactly the brand name. A `starts with
    // https://` check alone would pass an invented account URL; equality
    // does not, and the prefix is still asserted so the failure message
    // names the cheaper defect first.
    $social = $footer . ' #block-agora-base-footer-social';
    $assert->elementExists('css', $social);
    $assert->elementTextContains('css', $social . ' h2', 'Follow us');
    $social_links = $this->getSession()->getPage()->findAll('css', $social . ' a');
    $this->assertCount(count(self::SOCIAL_LINKS), $social_links, 'The social menu must carry exactly four links.');
    $expected = self::SOCIAL_LINKS;
    foreach ($social_links as $link) {
      $href = (string) $link->getAttribute('href');
      $this->assertStringStartsWith('https://', $href, 'Every social link must be absolute and HTTPS.');
      $this->assertArrayHasKey($href, $expected, "The social menu links $href, which is not one of the four root URLs T-1216 ships - or links it twice.");
      $this->assertSame($expected[$href], trim($link->getText()), "The social link to $href must read as the network's name.");
      unset($expected[$href]);
    }
    $this->assertSame([], $expected, 'Every one of the four networks must be linked exactly once.');

    // The legal bottom bar: four links, and - the part that matters - four
    // destinations that ANSWER.
    //
    // ⚠️ THE HREFS ARE NOT ASSERTED FROM THE SHIPPED ALIAS ALONE, because the
    // shipped alias is not what decides them. `pathauto` owns the alias of a
    // `page` node (pattern `/[node:title]`), and the menu links are stored as
    // entity references (`target_uuid`) precisely so that Drupal resolves the
    // href from the node rather than from a string somebody typed. So the
    // test reads the href the site actually emitted, asserts it is the alias
    // this package intends, and THEN follows it. Asserting the alias without
    // following it would pass on a link to a 404; following it without
    // asserting the alias would pass on a link that silently moved.
    $legal = $footer . ' #block-agora-base-footer-legal';
    $assert->elementExists('css', $legal);
    $assert->elementTextContains('css', $legal . ' h2', 'Legal and accessibility');
    $legal_links = $this->getSession()->getPage()->findAll('css', $legal . ' a');
    $this->assertCount(count(self::LEGAL_LINKS), $legal_links, 'The legal bar must carry exactly four links.');
    $found = [];
    foreach ($legal_links as $link) {
      $href = (string) $link->getAttribute('href');
      $path = '/' . ltrim(substr($href, strlen($base_path)), '/');
      $this->assertArrayHasKey($path, self::LEGAL_LINKS, "The legal bar links $href, which is not one of the four destinations T-1311 ships - or links it twice.");
      $this->assertSame(self::LEGAL_LINKS[$path], trim($link->getText()), "The legal link to $path must read as the name of the statement it opens.");
      $found[$path] = TRUE;
    }
    $this->assertSame(array_keys(self::LEGAL_LINKS), array_keys($found), 'Every one of the four legal destinations must be linked exactly once.');

    // And every one of them answers. This is the criterion the row exists for:
    // four dead links is the defect T-1215 refused to ship, at four times the
    // size.
    foreach (array_keys(self::LEGAL_LINKS) as $route) {
      $this->drupalGet(ltrim($route, '/'));
      $assert->statusCodeEquals(200);
      // A page that answers 200 with no heading is a page that answers about
      // nothing (I-062, in prose form): the statement has to BE there.
      $assert->elementsCount('css', 'h1', 1);
      $assert->elementTextContains('css', 'h1', self::LEGAL_LINKS[$route]);
    }

    // ⚠️ THE UPSTREAM PRIVACY STUB IS ASSERTED STILL ABSENT, not merely left
    // alone. `drupal_cms_privacy_basic` ships node 1, `Privacy policy`,
    // UNPUBLISHED, its body reading "This content needs to be edited before
    // publishing"; `RecipeRunner` imports content with `Existing::Skip` and
    // that recipe runs first, so nothing this package ships can amend it.
    // Publishing it would ship a page that says it needs to be edited. If it
    // ever starts answering 200, somebody has published that text on every
    // installed site and this test is where it shows up.
    $this->drupalGet('privacy-policy');
    $assert->statusCodeEquals(404);
  }

  /**
   * Counts the published nodes the site holds in the given bundles.
   *
   * THE DENOMINATOR, and it is read from the site rather than typed. Every
   * expected row count in this class derives from this method, so adding a
   * demo node moves the number instead of breaking a test that somebody then
   * edits downward. It is deliberately NOT read from the view being tested:
   * a count taken from the view's own bundle filter would assert that the
   * view lists whatever it lists.
   *
   * @param string[] $bundles
   *   The node bundles to count across.
   *
   * @return int
   *   How many published nodes of those bundles exist.
   */
  protected function publishedCount(array $bundles): int {
    return (int) \Drupal::entityTypeManager()
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', $bundles, 'IN')
      ->condition('status', 1)
      ->count()
      ->execute();
  }

  /**
   * Walks every page of a rendered table view and asserts its structure.
   *
   * WHY EVERY PAGE AND NOT ONLY THE FIRST. The package ships more records
   * than a page holds, so a check confined to page one would assert the page
   * size and nothing else - and page two, the page that carries the
   * remainder, is where an off-by-one in a pager actually shows up. Walking
   * the pager also ties the rendered rows to the entity count: a register
   * that dropped records, or listed one twice, changes the total and fails
   * here rather than passing on a full first page.
   *
   * @param string $path
   *   The view page's path.
   * @param int $columns
   *   How many columns the view declares.
   * @param int $expected_rows
   *   How many data rows the site's content should produce, in total.
   * @param int $per_page
   *   The pager's page size.
   * @param string $empty_text
   *   The view's empty text, which must appear on none of these pages.
   *
   * @return array
   *   A two-element list: the header labels, and one entry per data row
   *   holding that row's cell texts in column order.
   */
  protected function assertPagedTable(string $path, int $columns, int $expected_rows, int $per_page, string $empty_text): array {
    $this->assertGreaterThan(0, $expected_rows, "$path is expected to render no rows at all, so every assertion below would hold vacuously.");

    $assert = $this->assertSession();
    $container = self::VIEW_CONTAINER;
    $pages = intdiv($expected_rows - 1, $per_page) + 1;
    $headers = [];
    $rows = [];

    for ($page = 0; $page < $pages; $page++) {
      $this->drupalGet($path, $page === 0 ? [] : ['query' => ['page' => $page]]);
      $assert->statusCodeEquals(200);
      $assert->elementsCount('css', $container . ' ' . self::REGISTER_TABLE, 1);
      // WCAG 2.2 AA, 1.3.1: the table says what it is, and every header cell
      // declares what it heads. This portal's core content IS tables.
      $assert->elementsCount('css', $container . ' ' . self::REGISTER_TABLE . ' > caption', 1);
      $assert->elementsCount('css', $container . ' ' . self::REGISTER_TABLE . ' thead th[scope="col"]', $columns);
      // Every header cell, not merely as many as there are columns: a `<th>`
      // without a scope in a table that also has scoped ones would slip past
      // a count that only looked at the scoped set.
      $assert->elementsCount('css', $container . ' ' . self::REGISTER_TABLE . ' thead th', $columns);
      // The empty text on a page that has rows would mean both states are
      // rendered all the time, which would make every empty-state assertion
      // in this class meaningless.
      $assert->pageTextNotContains($empty_text);

      $on_this_page = min($per_page, $expected_rows - count($rows));
      $assert->elementsCount('css', $container . ' ' . self::REGISTER_TABLE . ' tbody tr', $on_this_page);
      $assert->elementsCount('css', $container . ' ' . self::REGISTER_TABLE . ' tbody tr td', $on_this_page * $columns);

      if ($headers === []) {
        foreach ($this->getSession()->getPage()->findAll('css', $container . ' ' . self::REGISTER_TABLE . ' thead th') as $header) {
          // The label only; a sortable column's cell also carries the text
          // of its sort link, which is noise in a failure message.
          $headers[] = trim(explode("\n", trim($header->getText()))[0]);
        }
        $this->assertCount($columns, $headers, "$path must expose one header label per column.");
      }

      foreach ($this->getSession()->getPage()->findAll('css', $container . ' ' . self::REGISTER_TABLE . ' tbody tr') as $row) {
        $cells = array_map(
          static fn ($cell): string => trim($cell->getText()),
          $row->findAll('css', 'td'),
        );
        $this->assertCount($columns, $cells, "Every row on $path must carry one cell per column.");
        $rows[] = $cells;
      }
    }

    $this->assertCount($expected_rows, $rows, "$path must render every published record it lists, across all of its pages.");
    return [$headers, $rows];
  }

  /**
   * Asserts that a view with an empty result set renders its empty state.
   *
   * BOTH HALVES ARE LOAD-BEARING. Views emits NO `<table>` at all for an
   * empty result set (I-062), so a check that only looked for the empty text
   * would pass against a page with nothing on it; and a check that only
   * looked for the absent table would pass against a page that never told
   * the reader why it is blank. The defect guarded by the second is a table
   * carrying headers with no rows under them, which announces a table to a
   * screen-reader user and then leaves them nothing in it.
   *
   * ⚠️ THE WORDING IS NOT ALWAYS OURS TO ASSERT, which is what `$empty_text`
   * being nullable is for. See the block comment at the only call site that
   * passes NULL: `agora_theme` replaces the configured empty text whenever an
   * exposed filter carries user input, so on a filtered route the sentence a
   * reader sees belongs to a different Drupal project on a different release
   * cadence. Every call site that reaches the empty state WITHOUT exposed
   * input passes the configured string and still asserts it exactly.
   *
   * @param string $path
   *   The view page's path.
   * @param array $options
   *   Options for drupalGet(), carrying whatever makes the result set empty.
   * @param string|null $empty_text
   *   The empty text the view declares, asserted verbatim - or NULL to assert
   *   only that the empty region carries SOME text, for a route whose wording
   *   this package does not own.
   */
  protected function assertEmptyState(string $path, array $options, ?string $empty_text): void {
    $assert = $this->assertSession();
    $this->drupalGet($path, $options);
    $assert->statusCodeEquals(200);
    // ⚠️ A COUNT OF ONE WAS TRIED HERE AND IS WRONG, and it is recorded
    // rather than quietly dropped. `elementsCount(PAGE_VIEW, 1)` reds on
    // `/contracts` with `2 elements ... found on the page, but should be 1`
    // (pipeline 969085): `agora_base_contracts` carries an `attachment_1`
    // display, and an attachment renders a second view container NESTED
    // inside the page's own. Two containers there is correct by design, so
    // "exactly one" is false of a page nobody has broken.
    //
    // The durable guarantee is the SCOPE, not a count: a block this template
    // places is a SIBLING of the main content block and can never be inside
    // it, while an attachment can only ever be inside the page's own view -
    // so the first match under PAGE_VIEW is the page's view in both cases,
    // and its text subtree contains the attachment's anyway.
    $view = $assert->elementExists('css', self::PAGE_VIEW);

    if ($empty_text !== NULL) {
      $assert->elementTextContains('css', self::PAGE_VIEW, $empty_text);
    }
    else {
      // THE SHAPE, NOT THE SENTENCE. The reader-facing guarantee is that a
      // register which matched nothing says SOMETHING about why it is blank,
      // and that guarantee is version-independent; the sentence itself is
      // not ours (see the call site). So the form's own text is subtracted
      // from the container's, and what is left must not be empty.
      //
      // WHY THAT REMAINDER IS THE EMPTY REGION AND NOT SOMETHING ELSE, in
      // terms a reader can check rather than a measurement they cannot
      // reproduce: core's `views-view.html.twig` renders the rows and the
      // empty area as the two mutually exclusive branches of one `if`, and
      // wraps the empty area in no element of its own. These registers put
      // nothing else inside that container - no view title, and no pager to
      // render once the result set is empty. So with the table excluded
      // below, what is left beside the exposed form is the empty area.
      $form = $view->find('css', 'form');
      $this->assertNotNull($form, "$path must render an exposed form, or this is no longer the FILTERED empty state and the call site has stopped testing what it says.");
      $form_text = trim($form->getText());
      $whole = trim($view->getText());
      // ⚠️ THE TWO GUARDS BELOW ARE WHAT KEEP THIS FROM PASSING VACUOUSLY. A
      // form with no text, or a form whose text is not found in the
      // container's, makes the subtraction a no-op - and a no-op subtraction
      // leaves the whole container behind, so the check would hold over a
      // page carrying nothing but the search box (I-028).
      $this->assertNotSame('', $form_text, "$path must render a form that carries text, or the subtraction below removes nothing.");
      $this->assertStringContainsString($form_text, $whole, "$path must contain its own form's text, or the subtraction below removes nothing.");
      $this->assertNotSame(
        '',
        trim(str_replace($form_text, '', $whole)),
        "$path matched nothing and then told the reader nothing: the view container carries the exposed form and no other text at all. Whatever the wording, and whoever owns it, a filter that matched nothing must say so.",
      );
    }

    $assert->elementNotExists('css', self::PAGE_VIEW . ' ' . self::REGISTER_TABLE);
  }

  /**
   * Reads one field's rendered value off the page currently loaded.
   *
   * ANCHORED ON THE LABEL, WHICH IS THE ONLY ANCHOR BOTH TEMPLATES SHARE.
   * Core's `field.html.twig` writes a `field--name-<field>` class on the
   * wrapper; `agora_theme`'s override writes `agora-field` and no field name
   * at all. What survives both is the shape: a label element whose PARENT is
   * the field wrapper, with the value alongside it. So the label is located by
   * its exact text, its parent is taken, and the label is removed from that
   * parent's text - what is left is the value, and nothing has been assumed
   * about which theme rendered it.
   *
   * @param string $label
   *   The field's configured label, as it is rendered.
   * @param string $where
   *   What is being read, for the failure message.
   *
   * @return string
   *   The field's rendered value, with surrounding whitespace collapsed.
   */
  protected function fieldValueOnPage(string $label, string $where): string {
    $this->assertStringNotContainsString('"', $label, "The label \"$label\" would need escaping in the XPath below.");
    $wrappers = $this->getSession()->getPage()->findAll('xpath', '//*[normalize-space(text())="' . $label . '"]/..');
    // Exactly one: zero means the field did not render, and more than one
    // means the label is ambiguous and the first match would be a guess.
    $this->assertCount(1, $wrappers, "\"$where\" must render exactly one field labelled \"$label\".");
    $text = preg_replace('/\s+/u', ' ', $wrappers[0]->getText());
    return trim(str_replace($label, '', $text));
  }

  /**
   * Names the blank cells in one rendered row, by column and by record.
   *
   * A failure message that says "cell 63 is empty" costs whoever reads it a
   * page of counting; one that names the column and the record is a fix.
   *
   * @param string[] $headers
   *   The table's header labels, in column order.
   * @param string[] $row
   *   One row's cell texts, in column order.
   *
   * @return string[]
   *   One entry per blank cell, empty when the row is complete.
   */
  protected function blankCells(array $headers, array $row): array {
    $blank = [];
    foreach ($row as $index => $cell) {
      if ($cell === '') {
        $column = $headers[$index] ?? ('column ' . $index);
        $blank[] = sprintf('%s on "%s"', $column, $row[0] ?? '(untitled row)');
      }
    }
    return $blank;
  }

  /**
   * Builds a value that fills one field, whatever kind of field it is.
   *
   * Driven by the field DEFINITION rather than by a table of field names, so
   * the fixture stays correct when the model changes and cannot quietly stop
   * populating a field that was renamed.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface $definition
   *   The field to build a value for.
   *
   * @return mixed
   *   A value the field will accept.
   */
  protected function fixtureValue(FieldDefinitionInterface $definition): mixed {
    $type = $definition->getType();

    switch ($type) {
      case 'string':
      case 'string_long':
        return 'Fixture value';

      case 'text':
      case 'text_long':
      case 'text_with_summary':
        return ['value' => 'Fixture text.', 'format' => 'plain_text'];

      case 'decimal':
      case 'float':
        return '1234.56';

      case 'integer':
        return 3;

      case 'daterange':
        // The stored format follows the storage's own datetime_type, so an
        // `allday` or `datetime` range would not silently fail to save.
        $format = $definition->getFieldStorageDefinition()->getSetting('datetime_type') === 'date'
          ? 'Y-m-d'
          : 'Y-m-d\TH:i:s';
        return [
          'value' => date($format, mktime(0, 0, 0, 1, 1, 2024)),
          'end_value' => date($format, mktime(0, 0, 0, 12, 31, 2024)),
        ];

      case 'list_string':
        $allowed = $definition->getFieldStorageDefinition()->getSetting('allowed_values');
        return array_key_first($allowed);

      case 'file':
        return ['target_id' => $this->fixtureFile($definition->getSetting('file_extensions'))->id()];

      case 'entity_reference':
        $target = $definition->getSetting('target_type');
        $bundles = array_keys($definition->getSetting('handler_settings')['target_bundles'] ?? []);
        $this->assertNotEmpty($bundles, $definition->getName() . ' must be restricted to at least one bundle.');

        if ($target === 'taxonomy_term') {
          $term = Term::create(['vid' => reset($bundles), 'name' => 'Fixture term']);
          $term->save();
          return ['target_id' => $term->id()];
        }
        if ($target === 'media') {
          return ['target_id' => $this->fixtureMedia(reset($bundles))->id()];
        }
    }

    // A field type nobody planned for is a FAILURE, never a skipped field: a
    // silently unpopulated field is one empty cell, and one empty cell is the
    // thing this whole test exists to detect.
    $this->fail(sprintf('No fixture value is defined for field %s of type %s.', $definition->getName(), $type));
  }

  /**
   * Creates a file whose extension the field being filled actually accepts.
   *
   * @param string $extensions
   *   The space-separated extension list from the field's settings.
   *
   * @return \Drupal\file\FileInterface
   *   The saved file.
   */
  protected function fixtureFile(string $extensions): File {
    $extension = strtok(trim($extensions), ' ');
    $this->assertNotEmpty($extension, 'A file field must accept at least one extension.');

    $uri = 'public://agora-fixture-' . $extension . '-' . uniqid() . '.' . $extension;
    file_put_contents($uri, 'fixture');
    $file = File::create(['uri' => $uri]);
    $file->setPermanent();
    $file->save();
    return $file;
  }

  /**
   * Creates a media entity of the bundle the field being filled accepts.
   *
   * @param string $bundle
   *   The media type to create.
   *
   * @return \Drupal\media\MediaInterface
   *   The saved media entity.
   */
  protected function fixtureMedia(string $bundle): Media {
    $media_type = MediaType::load($bundle);
    $this->assertNotNull($media_type, "The $bundle media type must exist; a Document cannot reference one otherwise.");

    $source_field = $media_type->getSource()->getConfiguration()['source_field'];
    $source = FieldConfig::loadByName('media', $bundle, $source_field);
    $file = $this->fixtureFile($source->getSetting('file_extensions'));

    $media = Media::create([
      'bundle' => $bundle,
      'name' => 'Fixture media',
      $source_field => ['target_id' => $file->id()],
    ]);
    $media->save();
    return $media;
  }

  /**
   * The inherited `^administer ` grants T-602 records as a dated exception.
   *
   * MEASURED 2026-08-24 on a clean install, RE-MEASURED 2026-08-25, and named
   * with provenance rather than merely tolerated:
   *  - `administer menu` and `administer url aliases` come from
   *    `drupal_cms_content_type_base/recipe.yml:109-110`, which reaches Ágora
   *    TRANSITIVELY through `drupal_cms_privacy_basic`;
   *  - `administer redirects` comes from `drupal_cms_seo_basic/recipe.yml:46`.
   * Core's own `content_editor_role` recipe grants none of the three, so this
   * is not Drupal being lax — it is two recipes this template lists on purpose.
   *
   * The list is ASSERTED rather than written in a comment because an exception
   * nobody checks is an exception that rots. If upstream fixes one, adds a
   * fourth, or a DIFFERENT role starts holding one, this fails and the
   * exception gets re-measured instead of re-quoted.
   */
  private const INHERITED_ADMINISTER_EXCEPTIONS = [
    'content_editor' => [
      'administer menu',
      'administer redirects',
      'administer url aliases',
    ],
  ];

  /**
   * Tests the roles on an installed site, and the exception list (T-602).
   *
   * Three things a kernel test reading `config/` cannot see, and this can.
   *
   * (1) THE INHERITED OFFENDERS ARE EXACTLY THE THREE RECORDED. The kernel test
   * scopes its criterion to the roles this recipe CREATES, which is the honest
   * scoping; this is the other half of that bargain.
   *
   * (2) THE `is_admin` TRAP, on live role entities. `administrator` reports
   * zero permissions because `is_admin` roles store none, not because it is
   * clean. Keyed on `isAdmin()`, never on the ID.
   *
   * (3) THE TWO ROLES ARE DISTINGUISHABLE IN BOTH DIRECTIONS. This is the part
   * that justifies creating two rather than one: a role whose grants are a
   * subset of another's is a role that confuses a clerk. Asserted as BEHAVIOUR
   * against real entities rather than as permission strings, because a
   * permission that grants nothing — the failure mode of a typo, and of
   * `view any unpublished content` if these bundles had turned out to be
   * outside its reach — still LOOKS correct in a YAML file.
   */
  public function testRolesOnAnInstalledSite(): void {
    $this->applyRecipe(self::getRecipePath());

    $roles = \Drupal::entityTypeManager()->getStorage('user_role')->loadMultiple();
    $this->assertNotEmpty($roles, 'Zero roles inspected is a failure, not a pass.');

    // -- (1) and (2): who holds an `^administer ` permission, and why ---------
    $offenders = [];
    $admin_roles = 0;
    foreach ($roles as $id => $role) {
      if ($role->isAdmin()) {
        // The exemption keys on the FLAG. An `is_admin` role is allowed
        // everything implicitly, so its permission list proves nothing — and
        // asserting that the list is empty is what makes the exemption safe
        // rather than a hole a future `is_admin` role could slip through.
        $this->assertSame([], $role->getPermissions(), "$id declares is_admin, so it must enumerate no permissions.");
        $admin_roles++;
        continue;
      }
      $held = array_values(array_filter(
        $role->getPermissions(),
        static fn (string $p): bool => str_starts_with($p, 'administer '),
      ));
      if ($held !== []) {
        sort($held);
        $offenders[$id] = $held;
      }
    }
    $this->assertGreaterThan(0, $admin_roles, 'At least one role must be is_admin, or the exemption above was never exercised and proves nothing.');

    ksort($offenders);
    $expected = self::INHERITED_ADMINISTER_EXCEPTIONS;
    ksort($expected);
    $this->assertSame($expected, $offenders, 'The `^administer ` grants on this site must be exactly the inherited exceptions T-602 recorded, on exactly the roles it named.');

    // The roles this recipe creates are, by construction, not among them.
    //
    // ⚠️ `agora_governance_auditor` (T-0511) is here for a second reason, and
    // it is the more important one. The kernel test reads the YAML on disk,
    // which proves the file is right and NOT that Drupal ever created the
    // entity. A `user.role.*` is a config ENTITY, and a recipe that fails to
    // bring one in fails silently — I-086, and the same defect that left this
    // theme with no page furniture for a fortnight. This line is what turns
    // that silence into a red job.
    foreach (['agora_base_editor', 'agora_base_reviewer', 'agora_governance_auditor'] as $id) {
      $this->assertArrayHasKey($id, $roles, "$id must have been imported by the recipe.");
      $this->assertArrayNotHasKey($id, $offenders, "$id is a role this recipe creates and must hold no ^administer permission.");
    }

    // -- (3) The two roles differ in both directions --------------------------
    $editor = $this->drupalCreateUser([], 'probe_editor');
    $editor->addRole('agora_base_editor')->save();
    $reviewer = $this->drupalCreateUser([], 'probe_reviewer');
    $reviewer->addRole('agora_base_reviewer')->save();

    $bundle = 'agora_base_contract';
    $unpublished = $this->drupalCreateNode(['type' => $bundle, 'status' => 0]);

    // The reviewer's direction: sees what is not yet public, changes nothing.
    $this->assertTrue($unpublished->access('view', $reviewer), 'A reviewer must be able to read an unpublished record; that is the whole job.');
    $this->assertFalse($unpublished->access('update', $reviewer), 'A reviewer must not be able to edit.');
    $this->assertFalse($unpublished->access('delete', $reviewer), 'A reviewer must not be able to delete.');

    // The editor's direction: creates and edits, and is NOT an auditor.
    $access = \Drupal::entityTypeManager()->getAccessControlHandler('node');
    $this->assertTrue($access->createAccess($bundle, $editor), 'An editor must be able to create a record.');
    $this->assertTrue($unpublished->access('update', $editor), 'An editor must be able to edit a record.');
    $this->assertFalse($unpublished->access('view', $editor), "An editor must NOT see another author's unpublished record, or the reviewer role grants nothing distinguishable.");
    $this->assertFalse($unpublished->access('delete', $editor), 'No role this recipe creates may delete a record.');

    // And the reason there is no third role: neither can touch `status`.
    // `NodeAccessControlHandler::checkFieldAccess()` gates it on
    // `administer node published status` OR `administer nodes` — both
    // `^administer `, and the first is site-wide and `restrict access: true`.
    foreach (['agora_base_editor' => $editor, 'agora_base_reviewer' => $reviewer] as $id => $account) {
      $this->assertFalse(
        $unpublished->get('status')->access('edit', $account),
        "$id must not be able to change a record's published status; expressing publication is content moderation's job and unit 004's row.",
      );
    }
  }

  /**
   * Tests the Canvas component review, and that no `?` is hiding a failure.
   *
   * T-604's original criterion — "0 unresolved `?`-optional keys that were
   * expected to exist" — CANNOT BE EVALUATED, and that is a property of the
   * mechanism rather than of the wording. A `?` prefix means "apply this action
   * if the config is present, and SILENTLY SKIP IT IF NOT". No API reports what
   * was skipped. Silence is the feature, so there is nothing to count.
   *
   * WHAT THIS ASSERTS INSTEAD, which is the restated criterion made executable:
   * every component named in `recipe.yml`'s `config.actions` block resolves to
   * config that EXISTS after the recipe is applied, and every one of them is
   * actually disabled. All 16 formerly `?`-prefixed names were resolved against
   * a clean install carrying this template's exact dependency closure — 16
   * exist, 0 absent — so all 16 prefixes were dropped, and this test is what
   * turns the silent skip they used to permit into a loud failure. If an
   * upstream module stops shipping one of these components, the recipe now
   * fails at apply time and this test says which name stopped resolving,
   * instead of a hidden component quietly reappearing in the editor's palette
   * with nothing to announce it.
   *
   * ⚠️ AMENDED BY T-1404, AND THE AMENDMENT NARROWS THE RULE RATHER THAN
   * RELAXING IT. Four names now carry a `?` again - the layout components
   * provided by `agora_theme` - and they carry one because the same
   * measurement that removed the other sixteen came back the other way for
   * these: the theme is a separate Drupal.org project (D-014), its newest
   * published release contains no `components/` directory, and a bare name
   * therefore aborts `site:install` with `Entity … does not exist`. The rule
   * this method enforces is no longer "no name carries a `?`" but "no name
   * carries a `?` except these four, they may only carry `enable`, and each of
   * them must be enabled the moment it exists" - which is strictly more to
   * check, not less.
   *
   * The count inspected is asserted here and PRINTED by
   * `tests/bin/config-inventory`; a test cannot print (pipeline 934619).
   */
  public function testCanvasComponentReview(): void {
    $this->applyRecipe(self::getRecipePath());

    $recipe = Yaml::decode(file_get_contents(self::getRecipePath() . '/recipe.yml'));
    $actions = $recipe['config']['actions'];

    $named = 0;
    $wildcards = 0;
    $disabled = 0;
    $enabled = 0;
    $optional_absent = 0;
    $optional_present = 0;
    foreach ($actions as $name => $action) {
      // ⚠️ THE `?` IS STRIPPED BEFORE THE FILTER, NOT AFTER, AND THAT ORDERING
      // IS THE WHOLE POINT. A recipe key carrying the prefix reads
      // `?canvas.component.…`, which does NOT start with `canvas.component.`
      // — so filtering on the raw name would `continue` straight past exactly
      // the case this method exists to catch, leaving the assertion below as
      // dead code that could never fire. Found by asking how this loop fails
      // rather than by watching it pass.
      $bare = ltrim((string) $name, '?');
      if (!str_starts_with($bare, 'canvas.component.')) {
        continue;
      }

      // ⚠️ EXACTLY ONE FAMILY OF NAMES MAY CARRY A `?`, AND IT IS NAMED HERE
      // RATHER THAN PATTERN-MATCHED LOOSELY (T-1404). Everything else must not:
      // the audit that dropped sixteen prefixes is only durable if re-adding
      // one is a failure, and "add a `?` until it passes" is precisely the
      // tempting wrong fix when a component goes missing.
      //
      // The exception is the four SDC components provided by `agora_theme`,
      // which is a SEPARATE Drupal.org project on its own release cadence
      // (D-014). The policy in recipe.yml is not "never a `?`" - it is that a
      // `?` which is NEVER NEEDED is dropped, and these four were resolved the
      // same way the sixteen were, against a clean install carrying this
      // template's exact dependency closure. The measurement came back the
      // other way: the newest published theme release carries no `components/`
      // directory at all, so all four are absent and a bare name would abort
      // `site:install` with `Entity … does not exist`.
      $may_be_optional = str_starts_with($bare, 'canvas.component.sdc.agora_theme.');
      if (!$may_be_optional) {
        $this->assertSame($bare, (string) $name, "$name must not be `?`-optional: every name here was resolved against a clean install, and a `?` that is never needed is a permanent blind spot.");
      }
      else {
        // ⚠️ THIS BRANCH IS VACUOUS TODAY AND SAYS SO, WHICH IS THE ONLY
        // HONEST WAY TO SHIP IT (I-028, I-045). While the theme ships no
        // components every one of these is absent, so the `assertTrue` below
        // never runs. What stops that from being a green over nothing is the
        // denominator: the count of names examined is pinned at four at the
        // end of this method, and the absent/present split is asserted to sum
        // to it. The day a theme release carries the components, `present`
        // becomes non-zero by itself and the enabled assertion starts firing -
        // no edit here required, which is the point.
        $this->assertSame(['enable' => []], $action, "$bare is `?`-optional, so the only verb this review accepts on it is `enable`: an optional `disable` would be a silent skip with nothing to count.");
        $config = \Drupal::config($bare);
        if ($config->isNew()) {
          $optional_absent++;
          continue;
        }
        // Present. Canvas mints an SDC component ENABLED - and never
        // re-enables one it has auto-disabled - so a component that exists and
        // is disabled here is the permanent, silent absence from the palette
        // that this action exists to repair.
        $this->assertTrue($config->get('status'), "$bare exists and is disabled after the recipe was applied: the `enable` action did not take effect. Canvas never re-enables a component it auto-disabled, so this state is permanent and invisible - the component is simply missing from the palette.");
        $optional_present++;
        continue;
      }

      if (str_contains($bare, '*')) {
        // A wildcard is resolved by the recipe engine against whatever exists,
        // so "does this config exist" is not a question that can be asked of
        // it. It is counted separately rather than skipped silently.
        $wildcards++;
        continue;
      }

      // ⚠️ THE ACTION'S OWN VERB DECIDES WHAT TO ASSERT, and this loop used to
      // assume every one of them was `disable`. It was written when that was
      // true, and it stayed true until a component had to be ENABLED: Canvas
      // mints a menu component disabled, so the quick-access cards' component
      // needs an `enable` action or it is missing from the palette and a site
      // owner can never place those cards on a second page.
      //
      // The old assertion then failed on the recipe doing exactly what it
      // intends, with a message accusing the action of not taking effect. The
      // fix is NOT to exempt that component - it is to assert the intent that
      // is written beside it. A test that checks the verb is stricter than one
      // that assumes it: an `enable` that leaves a component disabled now fails
      // too, and that case was previously unreachable.
      $this->assertCount(1, $action, "$bare carries " . count($action) . ' actions, and this review reads only the first. Asserted rather than assumed: `array_key_first` would silently ignore the second.');
      $verb = array_key_first($action);
      $this->assertContains($verb, ['enable', 'disable'], "$bare uses `$verb`, and this review only knows how to check `enable` and `disable`. A third verb needs its own assertion rather than passing unexamined.");

      $config = \Drupal::config($bare);
      $this->assertFalse($config->isNew(), "$bare is named in recipe.yml without a `?`, so it must exist after the recipe is applied.");
      if ($verb === 'disable') {
        $this->assertFalse($config->get('status'), "$bare is named with `disable` and is enabled: the action did not take effect.");
        $disabled++;
      }
      else {
        $this->assertTrue($config->get('status'), "$bare is named with `enable` and is disabled: the action did not take effect. Canvas creates menu components disabled and `config.strict: false` means an already-created object wins, so the shipped file alone is not enough - this is the case that action exists for.");
        $enabled++;
      }
      $named++;
    }

    // The denominators. 16 of these were `?`-prefixed before this row ran, and
    // a count that silently fell to zero would leave every assertion above
    // passing over nothing (I-045). The split is stated as well as the total,
    // because a component quietly moving from one list to the other is exactly
    // the change this method exists to notice.
    $this->assertSame(21, $named, 'The review covers twenty-one individually named Canvas components.');
    $this->assertSame(20, $disabled, 'Twenty are named with `disable`.');
    $this->assertSame(1, $enabled, 'One is named with `enable`: the quick-access menu component, which Canvas would otherwise leave out of the palette.');
    $this->assertSame(1, $wildcards, 'Exactly one entry is a wildcard: the project browser blocks.');
    // The `?`-optional denominator, pinned so the branch above cannot pass
    // over an empty set. Four names, and every one of them accounted for as
    // either absent or present-and-enabled - a name that vanished from
    // recipe.yml would fail here rather than quietly stop being checked.
    $this->assertSame(4, $optional_absent + $optional_present, 'The review covers four `?`-optional Canvas components: the theme layout kit.');
  }

  /**
   * Tests the `page.front` round trip (T-605).
   *
   * THE GAP, AND WHY IT IS NOT A BUG. `recipe.yml` declares `/home`; an
   * installed site reports `/page/1`. That is not drift — it is a ROUND-TRIP
   * PAIR working as designed. `drupal_cms_helper`'s
   * `RecipeSubscriber::onRecipeApplied()` deliberately converts the declared
   * alias into its system path when the recipe is applied, and
   * `GenericConfigurationListener` rewrites it back to the alias on export,
   * gated on a flag only `SiteExporter` switches on. The alias survives an
   * export because it rides inside the landing page's own `path` field rather
   * than as a standalone `path_alias` entity.
   *
   * So the ruling is to keep `/home` and assert the ROUND TRIP, rather than
   * declaring `/page/1`: an entity ID is not stable, and `site:export` would
   * rewrite it back to the alias on every export, which means declaring it
   * would be a decision to fight the tool forever. The assertion below is
   * written against the PAIR — it reads the alias's own target rather than
   * comparing to a hard-coded `/page/1`, so it stays true when the node ID
   * changes and still fails if the conversion stops happening.
   *
   * ⚠️ WHICH STATUS THIS ASSERTS, AND WHY — and the answer changed once this
   * was actually run, which is the whole reason it is written down.
   *
   * T-605's criterion was corrected before implementation to say that `/home`
   * does not return 200 but 301 to `/`, so `statusCodeEquals(200)` would fail.
   * That correction is RIGHT on a DDEV-served install, where it was measured:
   * `/home` -> 301 -> `/` -> 200, and `/page/1` behaves the same way. IT IS
   * WRONG HERE. Under BrowserTestBase the same recipe on a clean site answers
   * `/home` with a plain 200, and asserting 301 FAILED — measured 2026-08-25,
   * not predicted.
   *
   * THE CAUSE, read at source rather than guessed. The 301 is not Drupal
   * normalising a front-page path and it is not this template: it is the
   * contributed `redirect` module's `RouteNormalizerRequestSubscriber`, which
   * fires only when `route_normalizer_enabled` is set AND
   * `RedirectChecker::canRedirect()` passes — and that method bails on
   * conditions belonging to the ENVIRONMENT rather than to the site, among
   * them the running script not being `index.php`, the request not being
   * GET/HEAD, maintenance mode, and a `destination` query parameter. So the
   * redirect status is a property of how the site is being served, and a test
   * that pins it is testing the harness.
   *
   * SO THIS ASSERTS THE FINAL STATUS AFTER FOLLOWING REDIRECTS, which is the
   * half that belongs to this template — while keeping the chain's sharpness
   * rather than trading it away. A bare "follow it and expect 200" would pass
   * for a `/home` that started resolving somewhere else entirely, so the
   * un-followed status is still inspected: it must be one of the two outcomes
   * that have an explanation, a 301 must come WITH its cause (the normalizer
   * switched on) and must point at the site root, and the followed request
   * must land on the front page rather than merely on something that renders.
   */
  public function testFrontPageRoundTrip(): void {
    $this->applyRecipe(self::getRecipePath());

    $recipe = Yaml::decode(file_get_contents(self::getRecipePath() . '/recipe.yml'));
    $declared = $recipe['config']['actions']['system.site']['simpleConfigUpdate']['page.front'];
    $this->assertSame('/home', $declared, 'This template declares the ALIAS as its front page, not a system path.');

    // -- The round trip, read from the pair rather than from a constant ------
    $aliases = \Drupal::entityTypeManager()
      ->getStorage('path_alias')
      ->loadByProperties(['alias' => $declared]);
    $this->assertCount(1, $aliases, "The declared alias $declared must exist on a clean install; if it does not, the landing page was exported without its path field.");
    $alias = reset($aliases);

    $installed = \Drupal::config('system.site')->get('page.front');
    $this->assertSame(
      $alias->getPath(),
      $installed,
      'system.site page.front must hold the SYSTEM PATH the declared alias points at — that is the recipe-side half of the round trip.',
    );
    $this->assertNotSame(
      $declared,
      $installed,
      'The conversion must actually have happened; page.front still holding the alias would mean RecipeSubscriber did not run.',
    );

    // -- The chain a visitor sees --------------------------------------------
    // Whichever status the un-followed request carries, it must be one this
    // test understands, and the branch it takes must match its cause. A bare
    // `assertSame(200, $followed)` would collapse "served directly",
    // "redirected to the front page" and "redirected somewhere else that
    // happened to render" into one green; this keeps them apart.
    $client = $this->getSession()->getDriver()->getClient();
    $client->followRedirects(FALSE);
    try {
      $this->drupalGet($declared);
      $status = $this->getSession()->getStatusCode();
      $this->assertContains($status, [200, 301], "$declared answered $status, which is neither of the two outcomes this test knows how to explain.");

      if ($status === 301) {
        // The redirect branch. It exists only because `redirect`'s route
        // normalizer is switched on, so assert the cause alongside the effect
        // — a 301 appearing with the normalizer off would be a different bug
        // wearing the same status code.
        $this->assertTrue(
          (bool) \Drupal::config('redirect.settings')->get('route_normalizer_enabled'),
          "$declared returned 301, so redirect's route normalizer must be what produced it.",
        );
        $location = $this->getSession()->getResponseHeader('Location');
        $this->assertNotNull($location, "$declared must send a Location header with its 301.");
        $this->assertSame(
          base_path(),
          parse_url($location, PHP_URL_PATH),
          "$declared must redirect to the site root, because it IS the front page.",
        );
      }
    }
    finally {
      $client->followRedirects(TRUE);
    }

    // The invariant that IS this template's to keep, in both branches: the
    // declared alias resolves, renders, and lands on the front page.
    $this->drupalGet($declared);
    $this->assertSession()->statusCodeEquals(200);
    $landed = parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH);
    $this->assertContains(
      $landed,
      [base_path(), base_path() . ltrim($declared, '/')],
      "Following $declared must end on the front page, at the site root or at the alias itself.",
    );

    // And the front route itself renders, which is what makes the line above a
    // statement about the front page rather than about any 200 at all.
    $this->drupalGet('<front>');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * The nine key routes, each asserted for status AND for an Ágora marker.
   *
   * T-801, carried from unit 001 as T-402. THE SECOND CLAUSE IS THE WHOLE ROW.
   * A 200 proves a page exists; it does not prove that ÁGORA'S page exists.
   * Unit 001 deferred this rather than write it weak, and it was right to: with
   * no content model and no theme, every route the site served came from the
   * Drupal CMS base recipes, so a status-only assertion would have passed with
   * Ágora entirely absent - a test that cannot fail for the reason it claims to
   * test (I-032). That is no longer true, so the row is payable.
   *
   * WHAT COUNTS AS A KEY ROUTE, and what deliberately does not. The nine are
   * the eight routes this template's own `views.view.agora_base_*` config
   * creates - the six per-bundle registers, the cross-type listing and the
   * library - plus the front page, which is the one route every visitor reaches
   * without being sent there. Administrative paths are NOT key routes: they are
   * rendered by the admin theme, they belong to upstream recipes rather than to
   * this template, and asserting them would measure Gin.
   *
   * THE MARKER PER ROUTE, and why each cannot appear by accident:
   *
   *   (a) `main.agora-page__main`, and the skip-link anchor inside it. Emitted
   *       by `agora_theme`'s own `page.html.twig` and by nothing else: core's
   *       page template writes no such class, and the `<a id="main-content"
   *       tabindex="-1">` immediately inside <main> is this theme's answer to
   *       WCAG 2.2 SC 2.4.1 rather than anything core emits. The pair is
   *       therefore a statement that ÁGORA'S THEME RENDERED THIS PAGE - which
   *       is what the wave-7 swap actually claims, and which no assertion in
   *       this class checked before today.
   *
   *   (b) The table's `<caption>`, asserted for its EXACT TEXT, read from the
   *       view that was imported rather than typed here. The text is written by
   *       this template's own `views.view.agora_base_*` config and by nothing
   *       else; core emits a caption only when the view declares one; and the
   *       eight strings are DISTINCT, which is what makes this a marker for the
   *       ROUTE rather than for the project. A route serving some other view's
   *       table fails it, and so does a route whose caption was lost - the
   *       defect a config-level check cannot see, and which this unit has
   *       already met once, in the shape of a view column that passed its
   *       config check and never rendered.
   *
   *       ⚠️ THIS SELECTOR WAS WRITTEN WRONG FIRST, AND THE WRONG VERSION IS
   *       WHAT FOUND THE DEFECT BELOW. It began as
   *       `table.agora-table > caption.agora-table__caption`, on the reasoning
   *       that the theme's `table.html.twig` renders the portal's tables. It
   *       does not. Views renders through `views-view-table.html.twig`, a
   *       different theme hook that this theme does not override, so the served
   *       markup is `<table class="cols-8">` with a bare `<caption>` and the
   *       string `agora-table` appears ZERO times on the page. The class-based
   *       selector matched nothing, and the assertion failed by name rather
   *       than passing on a coincidence.
   *
   *       ⚠️ EVERYTHING THAT FOLLOWED FROM THAT WAS FIXED, AND THIS COMMENT
   *       WENT ON DESCRIBING THE DEFECT FOR ANOTHER TWO WEEKS. Corrected
   *       2026-09-12 against a measurement, not against a memory. The
   *       superseded text said: the theme's table template renders no table
   *       this portal actually serves, so `agora-table__scroll[tabindex="0"]`
   *       - its whole answer to horizontal overflow and to axe's
   *       `scrollable-region-focusable` - is absent from every register page.
   *
   *       THE THEME NOW SHIPS `templates/views-view-table.html.twig`, with the
   *       wrapper at its line 210, and the portal serves it. Measured on the
   *       smoke rig against `agora_theme` at `71de28e`, the same file by
   *       sha1 as the sibling checkout, default theme `agora_theme`:
   *       `/contracts` returns 200 and carries 2 `<table>`, 2
   *       `agora-table__scroll`, 2 `tabindex="0"`, 2 `<caption>` and 13
   *       `scope="col"`.
   *
   *       WHAT THIS DOES NOT SAY. It does not say the markers below changed:
   *       marker (b) is still core's bare `<caption>` asserted for its exact
   *       TEXT, because the caption's string comes from this template's view
   *       config and is the marker for the ROUTE. And the `scope="col"` the
   *       sibling methods assert is still CORE's, from the views table
   *       preprocess, not this theme's - the override does not author it, so
   *       an assertion on it would keep passing with the theme absent.
   *
   *       ⚠️ NOTHING IN THIS CLASS ASSERTS THE WRAPPER, and that gap is named
   *       rather than quietly filled. A comment corrected in the same commit
   *       as a new assertion hides which of the two was the finding; adding
   *       coverage is a change with its own row, not a docblock edit.
   *
   * MARKERS CONSIDERED AND REJECTED, because a rejected marker is the part of
   * this row that is easiest to get wrong:
   *   - the site name: emitted by core's `html.html.twig` on every page of
   *     every Drupal site, and settable by anyone. It says nothing about Ágora.
   *   - `<title>Home</title>` on the front page: the word is generic, the
   *     landing page is deliberately blank, and any site with a page called
   *     Home would pass.
   *   - `path-frontpage`, `page-node-type-*` and the other body classes: core.
   *   - the theme's stylesheet URL: CSS AGGREGATION IS ON, so grepping the
   *     served HTML for `agora_theme/css/tokens.css` returns nothing WITH THE
   *     THEME PERFECTLY ACTIVE. T-705 worked around it by reading the
   *     aggregate; asserting the DOM the template emits is better still,
   *     because a library can be attached to a page the theme never rendered.
   *
   * THE FRONT PAGE'S MARKER IS THEME-LEVEL ONLY, AND THAT IS A GAP, STATED.
   * The package now ships a demo corpus, but its landing page is still a
   * deliberately blank Canvas page - RE-MEASURED on a populated install on
   * 2026-08-26, where `/` serves no table and no register content - so the
   * front page still has no Ágora-specific TEXT to assert. Comparing its
   * document against the one served at `/home` was considered and rejected:
   * under the harnesses where `redirect`'s route normalizer fires, `/home`
   * 301s to `/` and the comparison is a tautology. The front page's route
   * identity is asserted by `testFrontPageRoundTrip()`; what this method adds
   * there is the theme.
   *
   * NO LITERAL PATH IS ASSERTED. `drupalGet()` resolves a relative path against
   * the harness's own base path, and the markers below are DOM, not hrefs - so
   * nothing here can go green on a `/`-rooted DDEV and red on a `/web`-rooted
   * drupalci, which is what I-056 cost this project two pipelines to learn.
   *
   * THE COUNT IS ASSERTED HERE AND PRINTED BY `tests/bin/config-inventory`.
   * A test cannot print: PHPUnit turns any output a test emits, STDOUT and
   * STDERR alike, into an exception (pipeline 934619).
   */
  public function testKeyRoutes(): void {
    $this->applyRecipe(self::getRecipePath());

    // A reader, because these routes exist to be read.
    $this->drupalLogin($this->drupalCreateUser(['access content']));
    $assert = $this->assertSession();

    // -- The route set, named AND checked against the site -------------------
    // Named, so that a route silently disappearing fails; checked against what
    // was actually imported, so that a NINTH view landing with no assertions
    // fails too. Either direction alone would let this method quietly stop
    // covering the thing it is named after.
    $named = array_merge(array_keys(self::TABLE_VIEWS), array_keys(self::SURFACE_BUNDLES));
    sort($named);
    $imported = array_filter(
      array_keys(\Drupal::entityTypeManager()->getStorage('view')->loadMultiple()),
      static fn (string $id): bool => str_starts_with($id, 'agora_base_'),
    );
    $imported = array_values($imported);
    sort($imported);
    $this->assertSame($named, $imported, 'The views this template ships and the views this method asserts must be the same set, in both directions.');
    $this->assertCount(8, $named, 'Eight view routes, which with the front page is the nine this row counts.');

    // -- The rows these routes need now come from the package ----------------
    // THE FIXTURE THAT USED TO STAND HERE IS GONE, and dropping it makes this
    // method stronger rather than cheaper. Marker (b) is a `<caption>`, and
    // Views renders no `<table>` - so no caption - for an empty result set
    // (I-062); the fixture existed only to give these eight routes rows to
    // render. The package now ships published records for every bundle, so
    // the rows are the product's, and a content import that silently did
    // nothing fails HERE instead of being papered over by six nodes this test
    // created for itself. Asserted before it is relied on, so the denominator
    // can never be zero unnoticed.
    foreach (self::TABLE_VIEWS as $view_id => $bundle) {
      $this->assertGreaterThan(0, $this->publishedCount([$bundle]), "This package must ship at least one published $bundle, or $view_id renders no table, marker (b) has no caption to match, and the route's assertion holds vacuously.");
    }

    // -- The eight view routes ----------------------------------------------
    $main = 'main.agora-page__main';
    // The register's own caption, never the breakdown's: `/contracts` carries
    // two captioned tables since T-1103, and marker (b) is a statement about
    // which ROUTE was served, so it has to read the caption of the register
    // and not of the statistic attached beneath it.
    $caption_selector = $main . ' ' . self::VIEW_CONTAINER . ' ' . self::REGISTER_TABLE . ' > caption';
    $routes = 0;
    $captions = [];

    foreach ($named as $view_id) {
      $view = View::load($view_id);
      $this->assertNotNull($view, "$view_id must have been imported by the recipe.");
      $default = $view->getDisplay('default')['display_options'];
      $caption = (string) ($default['style']['options']['caption'] ?? '');
      $this->assertNotSame('', trim($caption), "$view_id must declare a caption, or the marker asserted below is the empty string and proves nothing.");
      $captions[$view_id] = $caption;
      $path = $view->getDisplay('page_1')['display_options']['path'];

      $this->drupalGet($path);

      // Clause one: the status.
      $assert->statusCodeEquals(200);

      // Clause two, marker (a): Ágora's theme rendered this page.
      $assert->elementsCount('css', $main, 1);
      $assert->elementExists('css', $main . ' > a#main-content[tabindex="-1"]');

      // Clause two, marker (b): and the page it rendered is THIS route's.
      // Scoped inside the main landmark on purpose - a caption rendered
      // outside the region the skip link leads to would satisfy neither the
      // theme claim nor the accessibility one.
      $assert->elementsCount('css', $caption_selector, 1);
      $assert->elementTextEquals('css', $caption_selector, $caption);

      $routes++;
    }

    // The eight markers must be eight DIFFERENT strings, or marker (b)
    // identifies the project and not the route, and any one of these routes
    // could be serving any other one's table.
    // assertCount, never assertSame(count(), count()): phpstan's
    // phpunit.assertCount rule is blocking here and rejected the
    // latter on 2026-08-25.
    $this->assertCount(
      count($captions),
      array_unique($captions),
      'Every route marker must be unique, or a route serving the wrong view would pass.',
    );

    // -- The ninth route: the front page -------------------------------------
    $this->drupalGet('<front>');
    $assert->statusCodeEquals(200);
    $assert->elementsCount('css', $main, 1);
    $assert->elementExists('css', $main . ' > a#main-content[tabindex="-1"]');
    $routes++;

    // The number this row asks for, asserted because it cannot be printed.
    // It is not a constant typed beside the loop: eight of it is pinned by the
    // set-equality above, so a ninth view arriving fails there, by name, before
    // it can silently change this total.
    $this->assertSame(9, $routes, 'Nine key routes: the eight views this template creates, and the front page.');
  }

  /**
   * The service-area cards sit beside the register, not on the landing page.
   *
   * T-1310. `block_6` was the fifth of SEVEN components on the Canvas front
   * page. Every one of its six cards links to `/publications?area=<tid>`, so
   * on the landing page each card was a link away from the thing it narrows;
   * beside the register it is a facet.
   *
   * WHY THIS IS ASSERTED IN MARKUP RATHER THAN IN CONFIG, which is the row's
   * own criterion. Config says what should happen; markup says what did. A
   * block placed in a region the theme does not declare, or hidden behind a
   * visibility condition that never matches, is correct config that renders
   * nothing - and a config-level assertion passes in both cases.
   *
   * THE PLACEMENT IS A BLOCK, NOT AN ATTACHMENT DISPLAY, and a measurement
   * decided that rather than a preference. `agora_theme` 1.1.0 declares
   * three regions - `header`, `content`, `footer` - so `content` is the only
   * body region there is; its own blocks in that region are weighted
   * messages -30, breadcrumb -25, page title -20 and main content 0, so -10
   * lands these cards under the `<h1>` and above the register. Read from the
   * published release, which is what a clean install resolves. Keeping it a
   * BLOCK display also keeps `block_6` the name this row's criterion uses,
   * keeps its Canvas component valid, and keeps the heading, which is
   * supplied by the placement and not by the view.
   *
   * THE THIRD GROUP OF ASSERTIONS LOOKS UNRELATED AND IS THE REASON THIS
   * METHOD EXISTS. `block_2` carries D-041's frame sentence in its `header`
   * area, and that sentence is the only place the served site declares
   * itself fictional. Moving a neighbouring block is exactly how a sentence
   * nobody re-reads disappears. It is read out of config and asserted on the
   * rendered page, so deleting the sentence fails the read and deleting the
   * block fails the page.
   */
  public function testServiceAreaCardsMoveToTheRegister(): void {
    $this->applyRecipe(self::getRecipePath());
    $this->drupalLogin($this->drupalCreateUser(['access content']));
    $assert = $this->assertSession();

    $view = View::load('agora_base_publications');
    $this->assertNotNull($view, 'The publications view must have been imported by the recipe.');

    // Read, never typed: the path, the page size, the two marker classes and
    // the sentence all come out of the config this change edits, so a rename
    // there fails here instead of quietly un-asserting the page.
    $path = $view->getDisplay('page_1')['display_options']['path'];
    $cards = $view->getDisplay('block_6')['display_options'];
    $registers = $view->getDisplay('block_2')['display_options'];

    $per_page = (int) $cards['pager']['options']['items_per_page'];
    $this->assertGreaterThan(0, $per_page, 'block_6 must declare a page size, or the card count below holds vacuously.');
    $this->assertNotSame('', (string) ($cards['css_class'] ?? ''), 'block_6 must carry a css_class, or the selector below matches every view on the page.');
    $this->assertNotSame('', (string) ($registers['css_class'] ?? ''), 'block_2 must carry a css_class, or the front-page control below matches everything.');

    $block = '#block-agora-base-service-areas';
    $display = '.' . $cards['css_class'];
    $control = '.' . $registers['css_class'];

    // -- (1) It renders on the register ------------------------------------
    $this->drupalGet($path);
    $assert->statusCodeEquals(200);

    // Two selectors, because they answer two questions: the first says the
    // PLACEMENT exists, the second says what it placed is block_6's own
    // display and not some other view that happens to sit there.
    $assert->elementsCount('css', $block, 1);
    $assert->elementsCount('css', $block . ' ' . $display, 1);

    // The heading comes from the placement, not from the view - so it is the
    // thing a move like this loses silently, and it is asserted by value.
    $assert->elementTextEquals('css', $block . ' h2', 'Every record, by service area');

    // Not "at least one row": a view whose result set is empty renders no
    // rows at all, and every assertion above stays true about nothing
    // (I-062). Six areas have records and the display shows six.
    $assert->elementsCount('css', $block . ' .views-row', $per_page);

    // -- (2) It is gone from the landing page -------------------------------
    $this->drupalGet('<front>');
    $assert->statusCodeEquals(200);
    $assert->elementNotExists('css', $block);
    $assert->elementNotExists('css', $display);

    // -- (3) The landing page still renders, and still says it is fiction ---
    // The register cards are the positive control: without them, (2) passes
    // just as well on a front page that rendered nothing at all.
    $assert->elementsCount('css', $control, 1);

    $frame = trim(strip_tags((string) ($registers['header']['area_text_custom']['content'] ?? '')));
    $this->assertNotSame('', $frame, "block_2 must still carry D-041's frame sentence in its header area: it is the only place the served site declares itself fictional.");
    $assert->pageTextContains($frame);
  }

  /**
   * Checks that the site template includes all Canvas components that it uses.
   */
  protected function assertCanvasComponentsAreIncluded(): void {
    // Examine all entities that implement
    // \Drupal\canvas\Entity\ComponentTreeEntityInterface.
    $entity_types = array_filter(
      \Drupal::entityTypeManager()->getDefinitions(),
      fn ($entity_type): bool => $entity_type->entityClassImplements(ComponentTreeEntityInterface::class),
    );

    $included_components = (new FileStorage(self::getRecipePath() . '/config'))
      ->listAll('canvas.component.');

    foreach ($entity_types as $entity_type) {
      $entities = \Drupal::entityTypeManager()
        ->getStorage($entity_type->id())
        ->loadMultiple();

      foreach ($entities as $entity) {
        $this->assertInstanceOf(ComponentTreeEntityInterface::class, $entity);
        /** @var \Drupal\canvas\Plugin\Field\FieldType\ComponentTreeItem $item */
        foreach ($entity->getComponentTree() as $item) {
          $component = $item->getComponent()?->getConfigDependencyName();
          if ($component) {
            $this->assertContains($component, $included_components, 'The site template should include this component in its configuration.');
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function rebuildAll(): void {
    // The rebuild won't succeed without the `json-schema-definitions` stream
    // wrapper. This would normally happen automatically whenever a module is
    // installed, but in this case, all of that has taken place in a separate
    // process, so we need to refresh *this* process manually.
    // @see canvas_module_preinstall()
    \Drupal::service('stream_wrapper_manager')->registerWrapper(
      'json-schema-definitions',
      JsonSchemaDefinitionsStreamwrapper::class,
      JsonSchemaDefinitionsStreamwrapper::getType(),
    );
    parent::rebuildAll();
  }

}
