<?php

declare(strict_types=1);

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\FunctionalTests\Core\Recipe\RecipeTestTrait;
use Drupal\canvas\JsonSchemaDefinitionsStreamwrapper;
use Drupal\node\NodeInterface;
use Drupal\views\Entity\View;

/**
 * Runs axe over the pages this site template actually installs.
 *
 * WHY THIS FILE EXISTS. Until it did, this project's accessibility result was
 * a statement about `agora_theme`'s own fixtures: seven synthetic routes in a
 * separate repository, scanned by that theme's blocking `nightwatch` job. That
 * green is true and it is about scaffolding. No fixture has a register with
 * real rows in it, a front page composed in Canvas, a breadcrumb trail, a
 * working pager or six footer menus - and those are the surfaces a citizen
 * meets. This class asks the same question about the product.
 *
 * WHY IT IS A PHPUNIT TEST AND NOT A NIGHTWATCH SUITE. Both were measured
 * before either was written; the record is D-053. Nightwatch collects its
 * tests by globbing from INSIDE the docroot, and a recipe package is installed
 * OUTSIDE it - as a sibling of `web/` - so a Nightwatch suite committed here
 * would materialise an eleventh CI job that collects zero tests. The `phpunit`
 * job, by contrast, already runs this package's tests, already receives
 * Drupal core's `node_modules` from the `composer` job, and already has a real
 * Chrome behind a Selenium service. So this adds a test, not a job: the
 * pipeline's job list does not move.
 *
 * WHERE AXE COMES FROM, AND WHY THE FIRST ASSERTION GUARDS IT. `axe-core` is
 * not named in Drupal core's `package.json`. It arrives three hops down -
 * core requires `nightwatch`, which requires `nightwatch-axe-verbose`, which
 * requires `axe-core` - and it lands in `web/core/node_modules` only because
 * the upstream `composer` job runs `yarn install` and publishes the result as
 * an artifact. Every link in that chain is somebody else's decision. So the
 * very first thing this test does is prove the file is readable, and fail with
 * a sentence naming the variable that controls it if it is not. A scan of
 * nothing must never be able to look like a pass (I-007, I-032).
 */
class AccessibilityTest extends WebDriverTestBase {

  use RecipeTestTrait;

  /**
   * {@inheritdoc}
   *
   * Deliberately NOT `agora_theme`. This property is applied AFTER the site is
   * installed, by `installDefaultThemeFromClassProperty()`, and the recipe is
   * applied later still - so the theme this template ships wins, exactly as it
   * does in ValidationTest. Naming `agora_theme` here would work too, but it
   * would prove that the class property can set a theme rather than that the
   * recipe does.
   */
  protected $defaultTheme = 'stark';

  /**
   * The main landmark this template's theme emits on every page.
   */
  private const MAIN = 'main.agora-page__main';

  /**
   * The four register routes whose path and heading are read from config.
   *
   * Nothing about these routes is typed here except the view ids. The path
   * comes from the `page_1` display and the heading from the `default`
   * display, so renaming a register in config cannot leave this file asserting
   * a stale string.
   */
  private const VIEW_PAGES = [
    'agora_base_contracts',
    'agora_base_publications',
    'agora_base_people',
    'agora_base_library',
  ];

  /**
   * The bundle whose first published node is scanned as an entity page.
   *
   * A node rendered through the entity path exercises `node.html.twig`,
   * `field.html.twig` and the breadcrumb, none of which any view route
   * reaches.
   */
  private const NODE_BUNDLE = 'agora_base_contract';

  /**
   * A path that must not resolve, so the not-found surface can be scanned.
   *
   * The 404 is the only page this site serves with no content entity behind
   * it, and it is where the theme's chrome has to hold on its own.
   */
  private const MISSING_PATH = '/agora-transparency-no-such-route';

  /**
   * The heading core renders on its own not-found page.
   */
  private const MISSING_HEADING = 'Page not found';

  /**
   * The floor, in characters of rendered text, below which a scan is empty.
   *
   * EVERY OTHER ASSERTION IN THIS FILE WOULD HOLD ON A BLANK PAGE. axe reports
   * no violations for a page with nothing on it - truthfully, and about
   * nothing (I-062). This is the assertion that says the scan was about
   * something. The number is low on purpose: it claims that content arrived,
   * not how much.
   */
  private const TEXT_FLOOR = 200;

  /**
   * The smallest number of footer menus a page of this portal may carry.
   *
   * The package ships six `system_menu_block` placements in the footer region
   * and all six render. The floor is set below that rather than at it, because
   * a menu with no links renders no list at all and the exact number is a
   * content question; the assertion that actually protects the footer is the
   * cross-page equality at the end of the run, which fails if any single page
   * loses a column the others keep.
   */
  private const FOOTER_MENU_FLOOR = 5;

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
   * Returns the absolute path of the axe-core bundle inside Drupal core.
   *
   * @return string
   *   The absolute path of `axe.min.js`.
   */
  protected static function getAxePath(): string {
    return DRUPAL_ROOT . '/core/node_modules/axe-core/axe.min.js';
  }

  /**
   * Scans the pages this template installs and reports what it covered.
   *
   * ONE TEST METHOD, NOT NINE. Every method of a BrowserTestBase class
   * installs its own Drupal, and this one also applies the recipe - so nine
   * methods would mean nine installs to scan nine pages. The totals are
   * therefore accumulated across the loop and asserted at the end, which is
   * also the only way the denominator can be checked at all: a run that
   * scanned fewer pages than it declared satisfies every per-page assertion
   * and says nothing about the pages it skipped.
   */
  public function testAccessibilityOfTheInstalledPages(): void {
    // -- (1) axe must be there, and its absence must be a sentence ----------
    // BEFORE THE SITE IS EVEN BUILT, because the failure this guards against
    // is not a defect in the product and should not cost a full install to
    // discover. The message names `_COMPOSER_YARN_INSTALL` because that is
    // the single variable that decides whether `node_modules` reaches this
    // job, and a reader who hits this line will not otherwise know it exists.
    $axe_path = self::getAxePath();
    $this->assertFileIsReadable($axe_path, sprintf(
      'axe-core must be readable at %s. It reaches this job only because the upstream `composer` job runs `yarn install` in core and publishes node_modules as an artifact, which is what _COMPOSER_YARN_INSTALL=1 (the default) turns on; `.phpunit-base` then declares needs: [composer]. If that variable is set to 0, or core stops depending on nightwatch (the package that pulls axe-core in three hops), this file disappears and no accessibility result is possible. A scan of nothing must not look like a pass.',
      $axe_path,
    ));
    $axe_source = (string) file_get_contents($axe_path);
    // A readable but truncated bundle would define no `axe` global and every
    // scan below would time out with no explanation. The real file is over
    // half a megabyte; the floor is an order of magnitude under that, so it
    // asserts "this is the bundle" without pinning a release.
    $this->assertGreaterThan(50000, strlen($axe_source), sprintf(
      'The axe-core bundle at %s is %d bytes, which is too small to be the real one.',
      $axe_path,
      strlen($axe_source),
    ));

    // -- (2) build the site this gate is about ------------------------------
    $this->applyRecipe(self::getRecipePath());

    // SCANNED ANONYMOUSLY, AND THAT IS A MEASURED CHOICE RATHER THAN A
    // DEFAULT. This method first logged in as a reader holding only `access
    // content`, on the reasoning that it removed any dependency on how the
    // anonymous role happens to be configured. The first real run refuted it:
    // that page came back with three `region` violations, and all three were
    // markup this template does not own - two from the `navigation` module's
    // top bar (the page title and its "Published" badge) and one from
    // `coffee`'s search form, none of them inside a landmark, none of them
    // fixable here, and none of them ever seen by a member of the public.
    //
    // A logged-in session is therefore the wrong surface for this question.
    // The pages this template installs are read by citizens, so the gate
    // reads them the same way - which is also how `agora_theme`'s own axe
    // suite scans. If the recipe ever stops granting anonymous users `access
    // content`, these routes stop rendering and the wait on the main landmark
    // fails by name on the first page.
    //
    // ASSERTED, NOT MERELY OMITTED. Anonymity here is a decision, and a
    // decision recorded only by the absence of a line is one a later edit
    // undoes without noticing. This is the line that notices.
    $this->assertFalse($this->loggedInUser, 'These pages must be scanned anonymously: a logged-in session is served admin chrome this template does not own, and a member of the public never sees it.');

    // -- (3) declare the pages, deriving every one of them from the package -
    $pages = $this->declarePages();
    // Six is the floor this row was written against; the package supplies
    // more. Asserted rather than assumed, because a helper that silently
    // returned two pages would make every total below meaningless.
    $this->assertGreaterThanOrEqual(6, count($pages), sprintf(
      'This gate must declare at least six real surfaces; it declared %d.',
      count($pages),
    ));

    // -- (4) scan them ------------------------------------------------------
    $scanned = [];
    $headings = [];
    $distinct = [];
    $footer_menus = [];
    $heading_order_ran = 0;
    $rules_min = 0;
    $rules_max = 0;
    $violations = 0;

    foreach ($pages as $page) {
      $result = $this->scanPage($page, $axe_source);
      $scanned[] = $page['name'];
      $headings[$page['name']] = $result['structure']['h1Text'];
      if (empty($page['sharesHeading'])) {
        $distinct[$page['name']] = $result['structure']['h1Text'];
      }
      $footer_menus[$page['name']] = $result['structure']['footerMenus'];
      if ($result['axe']['bucket'] !== 'absent') {
        $heading_order_ran++;
      }
      $rules_min = $rules_min === 0
        ? $result['axe']['rules']
        : min($rules_min, $result['axe']['rules']);
      $rules_max = max($rules_max, $result['axe']['rules']);
      $violations += count($result['axe']['violations']);
    }

    // -- (5) the denominators, which are the point of the whole file --------
    // THE ASSERTION MESSAGE IS THE REPORT. A test in this package cannot
    // print: PHPUnit turns any output a test emits into an exception, and
    // writing to STDERR does not dodge it - see RequirementsTest, which
    // records the pipeline that failed on exactly that. So every count this
    // gate produces is carried by an assertion message, where it reaches
    // junit.xml and the job's own summary instead of being lost.
    //
    // The two counts are locals rather than `count()` calls inside the
    // assertions: phpstan's `phpunit.assertCount` rule is blocking on this
    // project and rejects `assertSame($n, count($x))` by name.
    $scanned_count = count($scanned);
    $declared_count = count($pages);
    $this->assertSame(
      array_column($pages, 'name'),
      $scanned,
      sprintf(
        'agora_transparency axe gate: %d of %d declared pages scanned, %d-%d axe rules run per page, %d violations, heading-order reported on %d of %d pages.',
        $scanned_count,
        $declared_count,
        $rules_min,
        $rules_max,
        $violations,
        $heading_order_ran,
        $scanned_count,
      ),
    );

    // A rule that did not run cannot have passed. axe files a rule it could
    // not apply in a bucket that reads exactly like a pass, so the violation
    // count alone cannot tell "clean" from "never asked" (I-045). This
    // compares the number of pages on which `heading-order` was reported at
    // all - in any of the four buckets - against the number of pages scanned.
    $this->assertSame($scanned_count, $heading_order_ran, sprintf(
      'heading-order must be reported on every page scanned: it was reported on %d of %d.',
      $heading_order_ran,
      $scanned_count,
    ));

    // A page reporting far fewer rules than its neighbours is a page where
    // something did not load, and it reads exactly like a pass. The spread is
    // therefore stated; the floor is what fails.
    $this->assertGreaterThan(0, $rules_min, sprintf(
      'Every page must have had axe rules run against it; the thinnest reported %d (the richest reported %d).',
      $rules_min,
      $rules_max,
    ));

    $this->assertSame(0, $violations, sprintf(
      'Zero axe violations across the whole run; found %d over %d pages.',
      $violations,
      $scanned_count,
    ));

    // Every page must be a DIFFERENT page. Without this, a site serving the
    // same front page under nine paths would satisfy every assertion above.
    // The front page is excluded because it is the one surface that shares
    // its heading BY DESIGN - `page.front` points at a Canvas page that is
    // also scanned at its own alias - and its identity is pinned separately,
    // against the set of Canvas labels.
    $this->assertCount(
      count($distinct),
      array_unique($distinct),
      sprintf(
        'Every scanned page must carry a distinct heading, or one page is being served under several paths. Headings: %s',
        implode(' | ', $headings),
      ),
    );

    // The footer is the one surface a page can lose without any landmark
    // count changing: the region renders as long as it holds ANY block, so
    // the colophon alone would keep the contentinfo landmark while several
    // columns of register links quietly vanished from one page only.
    $this->assertCount(1, array_unique($footer_menus), sprintf(
      'Every page must carry the same number of footer menus; found %s.',
      implode(', ', array_map(
        static fn (string $name, int $count): string => "$name=$count",
        array_keys($footer_menus),
        $footer_menus,
      )),
    ));
  }

  /**
   * Builds the list of surfaces to scan, deriving each from the package.
   *
   * NOTHING HERE IS A TYPED PATH OR A TYPED TITLE except the one path that
   * must not resolve and the heading core puts on its own 404. Every other
   * entry is read from the config or the content this template ships, so a
   * renamed register or a moved alias changes what is scanned instead of
   * leaving this file asserting a string nothing produces any more.
   *
   * @return array<int, array<string, mixed>>
   *   The declared pages, each with a name, a path, an expected heading, the
   *   minimum number of table rows it must render, and why it is here.
   */
  protected function declarePages(): array {
    $pages = [];

    // The two Canvas pages, at their own aliases. A Canvas page is composed
    // of component blocks rather than rendered from a single entity view, and
    // no view route and no theme fixture reaches that render path.
    $canvas_storage = \Drupal::entityTypeManager()->getStorage('canvas_page');
    $canvas_pages = $canvas_storage->loadMultiple();
    $this->assertNotEmpty(
      $canvas_pages,
      'This template must ship Canvas pages, or the front page has nothing behind it.',
    );
    $canvas_labels = [];
    foreach ($canvas_pages as $entity) {
      $label = (string) $entity->label();
      $canvas_labels[] = $label;
      $pages[] = [
        'name' => 'canvas:' . $entity->id(),
        'path' => $entity->toUrl()->toString(),
        'heading' => $label,
        'minRows' => 3,
        'why' => 'a Canvas page, composed of component blocks',
      ];
    }

    // The front page as a visitor meets it. This is NOT the same render as
    // the alias above even though it is the same entity: core suppresses the
    // breadcrumb on the front-page route and shows it everywhere else, so the
    // two requests produce different chrome. The heading is not pinned to a
    // string - it is asserted to be one of the Canvas labels just collected,
    // which says "the front page is one of the pages this package ships"
    // without caring which one.
    //
    // It is also the ONE surface here that deliberately shares its heading
    // with another, because it deliberately is another: `page.front` points
    // at one of the Canvas pages above. That is flagged rather than left to
    // be discovered, because the uniqueness check at the end of the run
    // exists to catch a site serving one page under many paths, and this is
    // the single case where that is correct rather than a defect. It was
    // discovered by the check firing on a real run, not reasoned about in
    // advance.
    $pages[] = [
      'name' => 'front',
      'path' => '<front>',
      'heading' => $canvas_labels,
      'minRows' => 3,
      'sharesHeading' => TRUE,
      'why' => 'the page every visitor lands on, without a breadcrumb',
    ];

    // The four register routes. Each one renders a Views table, which is this
    // portal's core content type: it is how salaries, contracts and grants
    // are published, and a table is where accessibility is won or lost.
    foreach (self::VIEW_PAGES as $view_id) {
      $view = View::load($view_id);
      $this->assertInstanceOf(View::class, $view, sprintf(
        'The view %s must have been imported by the recipe, or this gate scans a route that does not exist.',
        $view_id,
      ));
      $path = $view->getDisplay('page_1')['display_options']['path'] ?? '';
      $title = $view->getDisplay('default')['display_options']['title'] ?? '';
      $this->assertNotSame('', $path, "$view_id must declare a page path.");
      $this->assertNotSame('', $title, "$view_id must declare a title, or the heading asserted for its route is the empty string and proves nothing.");
      $pages[] = [
        'name' => $view_id,
        'path' => '/' . $path,
        'heading' => (string) $title,
        'minRows' => 3,
        'why' => 'a register, rendered as a table with rows in it',
      ];
    }

    // One published node, reached at its alias. This is the entity render
    // path: node.html.twig, the field templates and the breadcrumb, none of
    // which a view route touches.
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $ids = $node_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('status', 1)
      ->condition('type', self::NODE_BUNDLE)
      ->sort('nid')
      ->range(0, 1)
      ->execute();
    $this->assertCount(1, $ids, sprintf(
      'This template must ship at least one published %s, or there is no entity page to scan.',
      self::NODE_BUNDLE,
    ));
    $node = $node_storage->load(reset($ids));
    $this->assertInstanceOf(NodeInterface::class, $node);
    $pages[] = [
      'name' => 'node:' . $node->id(),
      'path' => $node->toUrl()->toString(),
      'heading' => (string) $node->label(),
      'minRows' => 0,
      'why' => 'a published record, rendered through the entity path',
    ];

    // The not-found page. Nothing else in this list is served without a
    // content entity behind it.
    $pages[] = [
      'name' => 'not-found',
      'path' => self::MISSING_PATH,
      'heading' => self::MISSING_HEADING,
      'minRows' => 0,
      'why' => 'the one page served with no content behind it',
    ];

    return $pages;
  }

  /**
   * Scans one page: asserts its structure, then runs axe over it.
   *
   * THE ORDER IS LOAD-BEARING. The structural denominators are asserted
   * BEFORE axe is even injected, because a page that failed to render is a
   * page axe will report as clean. Views renders no table at all for an empty
   * result set, so a register with no rows produces no markup for a table
   * rule to fail on, and the run comes back green about nothing (I-062).
   *
   * @param array<string, mixed> $page
   *   One entry from ::declarePages().
   * @param string $axe_source
   *   The contents of core's axe-core bundle.
   *
   * @return array<string, array<string, mixed>>
   *   The structure counts and the axe summary for this page.
   */
  protected function scanPage(array $page, string $axe_source): array {
    $name = $page['name'];
    $this->drupalGet($page['path']);
    $this->assertNotNull(
      $this->assertSession()->waitForElement('css', self::MAIN, 15000),
      sprintf(
        '%s (%s): must be rendered by this template\'s theme - "%s" was never present at %s.',
        $name,
        $page['why'],
        self::MAIN,
        $page['path'],
      ),
    );

    // -- the structure, measured in one round trip -------------------------
    // `__MAIN__` is substituted rather than typed four times. The selector
    // already appears in the wait above, and a selector written in two places
    // is a selector that goes stale in one of them first - the same defect
    // this project keeps finding in numbers written twice.
    $structure = $this->getSession()->evaluateScript(str_replace('__MAIN__', self::MAIN, <<<'JS'
      (function () {
        var main = document.querySelectorAll('__MAIN__');
        var h1 = document.querySelectorAll('h1');
        return {
          h1: h1.length,
          h1Text: h1.length ? (h1[0].textContent || '').trim() : '',
          header: document.querySelectorAll('header.agora-page__header').length,
          footer: document.querySelectorAll('footer.agora-page__footer').length,
          main: main.length,
          skipLink: document.querySelectorAll('__MAIN__ > a#main-content[tabindex="-1"]').length,
          footerMenus: document.querySelectorAll('footer.agora-page__footer ul.agora-footer-menu').length,
          rows: document.querySelectorAll('__MAIN__ table tbody tr').length,
          text: (document.body.innerText || '').trim().length
        };
      })()
      JS));
    $this->assertIsArray($structure, "$name: the page returned no structure at all.");

    // Exactly one, never "at least one". Zero and two are both defects and
    // only an equality catches both: the theme's own fixtures carried two
    // <h1> elements for months - one hand-written, one from the page title
    // block - and a `>= 1` assertion called that green.
    $this->assertSame(1, $structure['h1'], sprintf(
      '%s: exactly one <h1> (found %d: "%s").',
      $name,
      $structure['h1'],
      $structure['h1Text'],
    ));
    // The heading is what says this route served its own page and not some
    // other one. It is an array for the front page, whose identity is "one of
    // the Canvas pages" rather than a particular title.
    if (is_array($page['heading'])) {
      $this->assertContains($structure['h1Text'], $page['heading'], sprintf(
        '%s: the heading "%s" must be one of the pages this template ships (%s).',
        $name,
        $structure['h1Text'],
        implode(' | ', $page['heading']),
      ));
    }
    else {
      $this->assertSame($page['heading'], $structure['h1Text'], sprintf(
        '%s: served the page it is for - expected the heading "%s" and found "%s".',
        $name,
        $page['heading'],
        $structure['h1Text'],
      ));
    }
    $this->assertSame(1, $structure['main'], sprintf(
      '%s: one main landmark (found %d).',
      $name,
      $structure['main'],
    ));
    $this->assertSame(1, $structure['header'], sprintf(
      '%s: one banner landmark (found %d).',
      $name,
      $structure['header'],
    ));
    $this->assertSame(1, $structure['footer'], sprintf(
      '%s: one contentinfo landmark (found %d).',
      $name,
      $structure['footer'],
    ));
    // WCAG 2.2 SC 2.4.1. The skip link is how a keyboard user reaches the
    // content without walking the whole masthead, and it is inert unless
    // something on the page carries the id it points at.
    $this->assertSame(1, $structure['skipLink'], sprintf(
      '%s: the skip link has a target to skip to (found %d).',
      $name,
      $structure['skipLink'],
    ));
    $this->assertGreaterThanOrEqual(
      self::FOOTER_MENU_FLOOR,
      $structure['footerMenus'],
      sprintf(
        '%s: the footer carries its register menus (found %d, floor %d).',
        $name,
        $structure['footerMenus'],
        self::FOOTER_MENU_FLOOR,
      ),
    );
    $this->assertGreaterThan(self::TEXT_FLOOR, $structure['text'], sprintf(
      '%s: the page has content to be accessible about (%d characters of rendered text, floor %d).',
      $name,
      $structure['text'],
      self::TEXT_FLOOR,
    ));
    if ($page['minRows'] > 0) {
      $this->assertGreaterThanOrEqual(
        $page['minRows'],
        $structure['rows'],
        sprintf(
          '%s: the table on this page has rows before axe is asked about it (found %d, floor %d).',
          $name,
          $structure['rows'],
          $page['minRows'],
        ),
      );
    }

    // -- axe, injected from core's node_modules ----------------------------
    // The bundle is executed in the page rather than loaded over HTTP: it
    // lives outside the docroot, so no URL serves it.
    $session = $this->getSession();
    $session->executeScript($axe_source);
    $this->assertTrue(
      (bool) $session->evaluateScript('typeof window.axe === "object"'),
      "$name: the axe-core bundle was executed but defined no `axe` global.",
    );

    // axe.run() is a promise, and evaluateScript() is synchronous, so the
    // result is parked on the window and waited for. The whole result is NOT
    // returned across the wire - it is reduced in the page to the four counts
    // and the one bucket this gate asserts on, because a full axe report over
    // a register page is megabytes of nodes nothing here reads.
    $session->executeScript(<<<'JS'
      window.agoraAxeResult = null;
      window.axe.run(document).then(function (r) {
        var buckets = ['passes', 'violations', 'incomplete', 'inapplicable'];
        var found = [];
        buckets.forEach(function (b) {
          var hit = r[b].some(function (x) { return x.id === 'heading-order'; });
          if (hit) { found.push(b); }
        });
        window.agoraAxeResult = {
          rules: r.passes.length + r.violations.length + r.incomplete.length + r.inapplicable.length,
          violations: r.violations.map(function (v) {
            var where = v.nodes.slice(0, 3).map(function (n) {
              return n.target.join(',') + ' :: ' + n.html.slice(0, 120);
            });
            return v.id + ' x' + v.nodes.length + ' @ ' + where.join(' | ');
          }),
          bucket: found.length ? found.join('+') : 'absent'
        };
      }).catch(function (e) {
        window.agoraAxeResult = {
          rules: 0,
          violations: ['axe.run() threw: ' + String(e)],
          bucket: 'absent'
        };
      });
      JS);
    $this->assertJsCondition(
      'window.agoraAxeResult !== null',
      60000,
      "$name: axe.run() never settled within 60 seconds.",
    );
    $axe = $session->evaluateScript('window.agoraAxeResult');
    $this->assertIsArray($axe, "$name: axe returned no result object.");

    // THE VIOLATION LIST IS SPELLED INTO THE MESSAGE, WITH THE MARKUP THAT
    // CAUSED IT. A failure that says "expected 0, got 3" names nothing a
    // reader can fix, and this file's first real run is the argument: the
    // rule id alone said `region x3`, which could have been three defects in
    // this template's own templates. The selectors said `.toolbar-badge` and
    // `.coffee-form-wrapper`, which is a different problem with a different
    // owner, and told the difference in one line instead of an afternoon.
    $this->assertCount(0, $axe['violations'], sprintf(
      '%s (%s): %d axe rules run, violations: %s.',
      $name,
      $page['why'],
      $axe['rules'],
      $axe['violations'] ? implode(', ', $axe['violations']) : 'none',
    ));
    $this->assertNotSame('absent', $axe['bucket'], sprintf(
      '%s: axe must have reported the heading-order rule; it landed in no bucket at all, which is not the same as passing.',
      $name,
    ));

    return ['structure' => $structure, 'axe' => $axe];
  }

  /**
   * {@inheritdoc}
   *
   * Applying a recipe happens in a separate process, so the stream wrapper
   * Canvas registers on install has not been registered in THIS one, and the
   * rebuild fails without it. Identical to ValidationTest's override, for the
   * identical reason.
   *
   * @see canvas_module_preinstall()
   */
  protected function rebuildAll(): void {
    \Drupal::service('stream_wrapper_manager')->registerWrapper(
      'json-schema-definitions',
      JsonSchemaDefinitionsStreamwrapper::class,
      JsonSchemaDefinitionsStreamwrapper::getType(),
    );
    parent::rebuildAll();
  }

}
