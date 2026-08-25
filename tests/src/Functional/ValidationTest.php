<?php

declare(strict_types=1);

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Config\FileStorage;
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

/**
 * Tests that this site template can be applied without errors.
 *
 * All deprecation notices triggered by the recipe's dependencies will be
 * displayed. To suppress them, add the
 * \PHPUnit\Framework\Attributes\IgnoreDeprecations attribute to this class.
 */
#[RunTestsInSeparateProcesses]
class ValidationTest extends BrowserTestBase {

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
   * Tests the six table views on a real site, empty and then populated (T-615).
   *
   * THE TWO LAYERS THAT NEED A RUNNING SITE, and one of them can only be tested
   * from this unit.
   *
   * LAYER (d) - THE EMPTY STATE. Unit 002 ships no demo content, so an
   * installed Ágora IS the empty state; it is a real shipping state and not an
   * edge case. Each route must return 200, must say why it is empty, and must
   * NOT emit a table carrying `<th>`s and no rows - an empty table with headers
   * is an accessibility defect, because a screen-reader user navigating by
   * table is told there is a table and then finds nothing in it. UNIT 003
   * CANNOT MAKE THIS ASSERTION: by then there is demo content, and every one of
   * these views has rows.
   *
   * LAYER (b) - ONE NODE PER BUNDLE, EVERY FIELD POPULATED. The number of
   * `<td>` in a row must equal the view's column count, which is the rendered
   * half of the set-equality that `ContentModelTest::testTableViews()` asserts
   * in config. THE FIXTURE LIVES IN THIS CLASS AND IS BUILT FROM THE FIELD
   * DEFINITIONS, never from a hand-written list, and it exists only in the test
   * database. It therefore never goes near `drush site:export`, which is what
   * satisfies the NO-list's narrow demo-content exception BY CONSTRUCTION
   * rather than by anyone remembering: `content/` cannot acquire a file from a
   * fixture that was never in the export rig at all.
   */
  public function testTableViews(): void {
    $this->applyRecipe(self::getRecipePath());

    // A reader, not an editor. These tables exist to be read by whoever can
    // read content, which on a transparency portal is everybody.
    $this->drupalLogin($this->drupalCreateUser(['access content']));

    $assert = $this->assertSession();
    $columns = [];
    $empty_text = [];

    // -- LAYER (d): the empty state, before a single node exists -------------
    foreach (self::TABLE_VIEWS as $view_id => $bundle) {
      $view = View::load($view_id);
      $this->assertNotNull($view, "$view_id must have been imported by the recipe.");
      $display = $view->getDisplay('default')['display_options'];

      $columns[$view_id] = count($display['fields']);
      $this->assertGreaterThan(0, $columns[$view_id], "$view_id must declare columns, or every count below holds vacuously.");

      $empty_text[$view_id] = reset($display['empty'])['content'];
      $path = $view->getDisplay('page_1')['display_options']['path'];

      $this->drupalGet($path);
      $assert->statusCodeEquals(200);
      $assert->elementExists('css', self::VIEW_CONTAINER);
      $assert->elementTextContains('css', self::VIEW_CONTAINER, $empty_text[$view_id]);
      // The defect being guarded: headers with no rows under them.
      $assert->elementNotExists('css', self::VIEW_CONTAINER . ' table');
    }

    // -- The fixture: one node per bundle, every field populated -------------
    $paths = [];
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
      // bundle whose fields were not all populated would under-count cells and
      // the layer-(b) assertion would be measuring the fixture again - the very
      // failure the T-615 row was rewritten to avoid.
      $this->assertSame($columns[$view_id] - 1, $populated, "Every field on $bundle must be populated, or the cell count below tests the fixture instead of the model.");
      $this->drupalCreateNode($values);

      $paths[$view_id] = View::load($view_id)
        ->getDisplay('page_1')['display_options']['path'];
    }

    // -- LAYER (b): 200, one row, and a cell for every column ----------------
    $cells = 0;
    foreach (self::TABLE_VIEWS as $view_id => $bundle) {
      $count = $columns[$view_id];
      $this->drupalGet($paths[$view_id]);
      $assert->statusCodeEquals(200);
      $assert->elementsCount('css', self::VIEW_CONTAINER . ' table', 1);
      // WCAG 2.2 AA, 1.3.1: the table says what it is, and every header cell
      // declares what it heads. This portal's core content IS tables.
      $assert->elementsCount('css', self::VIEW_CONTAINER . ' table > caption', 1);
      $assert->elementsCount('css', self::VIEW_CONTAINER . ' table thead th[scope="col"]', $count);
      $assert->elementsCount('css', self::VIEW_CONTAINER . ' table tbody tr', 1);
      $assert->elementsCount('css', self::VIEW_CONTAINER . ' table tbody tr td', $count);
      // And the empty text is gone, which is what says the two states are
      // genuinely different rather than both being rendered all the time.
      $assert->pageTextNotContains($empty_text[$view_id]);
      $cells += $count;
    }

    // The total, asserted rather than printed: a functional test cannot print
    // either (pipeline 934619). Derived from the imported views, so it grows
    // with the model instead of having to be relaxed.
    $this->assertSame(array_sum($columns), $cells, 'Every one of the six routes must have been counted.');
  }

  /**
   * The two T-603 surfaces, and how many rows each shows over the fixture set.
   *
   * The row count is the point of the pair: the cross-type listing shows one
   * row per bundle, the library shows only the two bundles whose payload is a
   * published file. A surface that quietly started listing everything would
   * pass every column assertion and fail here.
   */
  private const SURFACE_ROWS = [
    'agora_base_publications' => 6,
    'agora_base_library' => 2,
  ];

  /**
   * The eight routes the main menu must link, in menu order.
   *
   * Transcribed rather than derived, on purpose: derived from the views, this
   * would assert that the menu links whatever it links.
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
  ];

  /**
   * Tests the library, the cross-type listing, the search box and the menu.
   *
   * FOUR THINGS THAT NEED A RUNNING SITE. The kernel test reads `config/` and
   * can prove what the two surfaces DECLARE; none of the four below is visible
   * there.
   *
   * (1) THE EMPTY STATE, which unit 002 IS. Both routes return 200, say why
   * they are empty, and do not emit a table carrying headers and no rows.
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

    // -- (1) The empty state, before a single node exists --------------------
    foreach (self::SURFACE_ROWS as $view_id => $expected_rows) {
      $view = View::load($view_id);
      $this->assertNotNull($view, "$view_id must have been imported by the recipe.");
      $display = $view->getDisplay('default')['display_options'];

      $columns[$view_id] = count($display['fields']);
      $this->assertGreaterThan(0, $columns[$view_id], "$view_id must declare columns, or every count below holds vacuously.");

      $empty_text[$view_id] = reset($display['empty'])['content'];
      $paths[$view_id] = $view->getDisplay('page_1')['display_options']['path'];

      $this->drupalGet($paths[$view_id]);
      $assert->statusCodeEquals(200);
      $assert->elementExists('css', self::VIEW_CONTAINER);
      $assert->elementTextContains('css', self::VIEW_CONTAINER, $empty_text[$view_id]);
      $assert->elementNotExists('css', self::VIEW_CONTAINER . ' table');

      // (4a) The box is present even with nothing to search, which is when a
      // reader is most likely to reach for it.
      $assert->elementExists('css', self::VIEW_CONTAINER . ' input[name="search"]');
      // WCAG 2.2 AA 3.3.2: the input carries a label, not just a placeholder.
      $assert->elementExists('css', self::VIEW_CONTAINER . ' label[for]');
    }

    // -- The fixture: one node per bundle, every field populated -------------
    // Identical in kind to testTableViews()'s fixture and for the same reason:
    // it lives in this class, uses the test database, and therefore never goes
    // near `drush site:export`, which is what keeps `content/` at one file BY
    // CONSTRUCTION rather than by anyone remembering.
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

    // -- (2) and (3): the rendered tables ------------------------------------
    $cells = 0;
    foreach (self::SURFACE_ROWS as $view_id => $expected_rows) {
      $count = $columns[$view_id];
      $this->drupalGet($paths[$view_id]);
      $assert->statusCodeEquals(200);
      $assert->elementsCount('css', self::VIEW_CONTAINER . ' table', 1);
      $assert->elementsCount('css', self::VIEW_CONTAINER . ' table > caption', 1);
      $assert->elementsCount('css', self::VIEW_CONTAINER . ' table thead th[scope="col"]', $count);
      // Every header cell, not merely as many as there are columns: a `<th>`
      // without a scope in a table that also has scoped ones would slip past a
      // count that only looked at the scoped set.
      $assert->elementsCount('css', self::VIEW_CONTAINER . ' table thead th', $count);
      $assert->elementsCount('css', self::VIEW_CONTAINER . ' table tbody tr', $expected_rows);
      $assert->elementsCount('css', self::VIEW_CONTAINER . ' table tbody tr td', $count * $expected_rows);
      $assert->pageTextNotContains($empty_text[$view_id]);

      // (3) Not one structurally empty cell, on a surface whose columns are
      // the intersection precisely so that there cannot be one.
      $tds = $this->getSession()->getPage()
        ->findAll('css', self::VIEW_CONTAINER . ' table tbody td');
      $this->assertCount($count * $expected_rows, $tds, "$view_id must render every cell it declares.");
      $empty_cells = [];
      foreach ($tds as $index => $td) {
        if (trim($td->getText()) === '') {
          $empty_cells[] = $index;
        }
      }
      $this->assertSame([], $empty_cells, "$view_id renders a cell that is empty BY DESIGN, which a screen-reader user cannot tell from missing data. A column here must be one every listed bundle carries.");

      $cells += $count * $expected_rows;
    }
    $this->assertSame(32, $cells, 'The two surfaces together render 4 columns x 6 rows plus 4 columns x 2 rows.');

    // -- (4b) The search box is wired to the query ---------------------------
    $needle = $titles['agora_base_grant'];
    $this->drupalGet($paths['agora_base_publications'], ['query' => ['search' => $needle]]);
    $assert->statusCodeEquals(200);
    $assert->elementsCount('css', self::VIEW_CONTAINER . ' table tbody tr', 1);
    $assert->elementTextContains('css', self::VIEW_CONTAINER . ' table tbody', $needle);

    // A search that matches nothing must reach the empty state, not an empty
    // table - the same accessibility defect, arrived at from the other side.
    $this->drupalGet($paths['agora_base_publications'], ['query' => ['search' => 'zzzz-no-such-record']]);
    $assert->statusCodeEquals(200);
    $assert->elementNotExists('css', self::VIEW_CONTAINER . ' table');
    $assert->elementTextContains('css', self::VIEW_CONTAINER, $empty_text['agora_base_publications']);

    // -- The menu, on a live site --------------------------------------------
    // Rebuilt explicitly: the links are plugin DERIVATIVES of the views, so
    // they exist only once the menu link manager has looked at the views the
    // recipe imported.
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
    foreach (['agora_base_editor', 'agora_base_reviewer'] as $id) {
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
   * The count inspected is asserted here and PRINTED by
   * `tests/bin/config-inventory`; a test cannot print (pipeline 934619).
   */
  public function testCanvasComponentReview(): void {
    $this->applyRecipe(self::getRecipePath());

    $recipe = Yaml::decode(file_get_contents(self::getRecipePath() . '/recipe.yml'));
    $actions = $recipe['config']['actions'];

    $named = 0;
    $wildcards = 0;
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

      // No name may still carry a `?`. Asserted rather than assumed: the audit
      // that dropped them is only durable if re-adding one is a failure, and
      // "add a `?` until it passes" is precisely the tempting wrong fix when a
      // component goes missing.
      $this->assertSame($bare, (string) $name, "$name must not be `?`-optional: every name here was resolved against a clean install, and a `?` that is never needed is a permanent blind spot.");

      if (str_contains($bare, '*')) {
        // A wildcard is resolved by the recipe engine against whatever exists,
        // so "does this config exist" is not a question that can be asked of
        // it. It is counted separately rather than skipped silently.
        $wildcards++;
        continue;
      }

      $config = \Drupal::config($bare);
      $this->assertFalse($config->isNew(), "$bare is named in recipe.yml without a `?`, so it must exist after the recipe is applied.");
      $this->assertFalse($config->get('status'), "$bare must actually be disabled; naming it in the disable list and finding it enabled means the action did not take effect.");
      $named++;
    }

    // The denominator. 16 of these were `?`-prefixed before this row ran, and a
    // count that silently fell to zero would leave every assertion above
    // passing over nothing (I-045).
    $this->assertSame(20, $named, 'The review covers twenty individually named Canvas components.');
    $this->assertSame(1, $wildcards, 'Exactly one entry is a wildcard: the project browser blocks.');
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
   *       than passing on a coincidence. What follows from that is stated in
   *       the closure report and owned outside this row: the theme's table
   *       template - its own README calls it the most important one it ships -
   *       renders no table this portal actually serves, so `agora-table__scroll
   *       [tabindex="0"]`, the theme's whole answer to horizontal overflow and
   *       to axe's `scrollable-region-focusable`, is absent from every register
   *       page. The `scope="col"` that the sibling methods assert is CORE's,
   *       from the views table preprocess, not this theme's.
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
   * `content/` holds exactly one file, a deliberately blank Canvas landing
   * page, so the front page has no Ágora-specific TEXT to assert - there is
   * none to render. Comparing its document against the one served at `/home`
   * was considered and rejected: under the harnesses where `redirect`'s route
   * normalizer fires, `/home` 301s to `/` and the comparison is a tautology.
   * The front page's route identity is asserted by `testFrontPageRoundTrip()`;
   * what this method adds there is the theme. Demo content is unit 003.
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
    $named = array_merge(array_keys(self::TABLE_VIEWS), array_keys(self::SURFACE_ROWS));
    sort($named);
    $imported = array_filter(
      array_keys(\Drupal::entityTypeManager()->getStorage('view')->loadMultiple()),
      static fn (string $id): bool => str_starts_with($id, 'agora_base_'),
    );
    $imported = array_values($imported);
    sort($imported);
    $this->assertSame($named, $imported, 'The views this template ships and the views this method asserts must be the same set, in both directions.');
    $this->assertCount(8, $named, 'Eight view routes, which with the front page is the nine this row counts.');

    // -- The fixture: one node per bundle, so every table has rows -----------
    // Built from the field definitions and living only in the test database,
    // exactly as the sibling methods do. `content/` holds one file and a test
    // asserts it; nothing here can reach an export.
    foreach (self::TABLE_VIEWS as $bundle) {
      $values = ['type' => $bundle, 'title' => 'Fixture ' . $bundle, 'status' => 1];
      $definitions = \Drupal::service('entity_field.manager')
        ->getFieldDefinitions('node', $bundle);
      foreach ($definitions as $field_name => $definition) {
        if ($definition instanceof FieldConfig) {
          $values[$field_name] = $this->fixtureValue($definition);
        }
      }
      $this->drupalCreateNode($values);
    }

    // -- The eight view routes ----------------------------------------------
    $main = 'main.agora-page__main';
    $caption_selector = $main . ' ' . self::VIEW_CONTAINER . ' table > caption';
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
    // assertCount, not assertSame(count(), count()): phpstan's phpunit.assertCount
    // rule is blocking here and rejected the latter on 2026-08-25.
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
