<?php

declare(strict_types=1);

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
