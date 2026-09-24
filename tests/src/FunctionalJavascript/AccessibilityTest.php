<?php

declare(strict_types=1);

use Drupal\Core\Url;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\FunctionalTests\Core\Recipe\RecipeTestTrait;
use Drupal\canvas\JsonSchemaDefinitionsStreamwrapper;
use Drupal\node\NodeInterface;
use Drupal\user\RoleInterface;
use Drupal\user\UserInterface;
use Drupal\views\Entity\View;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

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
 *
 * AND ONE LOGGED-IN PAGE, DECLARED SEPARATELY (D-061, option B). Config
 * Guardian's dashboard is the one administrative surface this package chose,
 * installs and configures itself, and it ships a role whose whole purpose is
 * to read it. That page is scanned AFTER the nine, logged in as that role,
 * against an expectation set of its own - because on it almost every
 * violation sits in markup this package installed and did not write. The
 * criterion is zero violations in markup this package owns, with every other
 * violation matched by rule, selector and owner against a declared list, so
 * that a new one fails. ::scanTheGovernanceDashboard() carries the reasoning
 * and the measurement; the nine are untouched by it.
 *
 * AND ONE RULE AXE SHIPS SWITCHED OFF, SWITCHED ON BY NAME (T-0634). WCAG
 * 2.2's criterion 2.5.8, Target Size (Minimum), is level AA, and the only
 * axe-core rule that measures it is disabled in the bundle this job loads.
 * Both scans ask for it by name, so every page this gate reads is measured
 * for target size too. AXE_RULE_ENABLED_BY_NAME carries the reasoning, and
 * says what that measurement does not settle.
 *
 * WHY THE CLASS CARRIES #[RunTestsInSeparateProcesses] (T-0628). Core raises
 * E_USER_DEPRECATED, from BrowserTestBase::setUp(), for any Functional or
 * FunctionalJavascript class that omits the attribute - "deprecated in
 * drupal:11.3.0 and is throwing an exception in drupal:12.0.0", read at
 * source on 2026-09-21 at core/tests/Drupal/Tests/BrowserTestBase.php:320-321
 * on the 11.x branch. This was the ONLY test class in this package without
 * it; the other five have carried it since they were written, so the omission
 * was an oversight rather than a decision, and it would have become a fatal
 * one on the first Drupal 12 run. Neither BrowserTestBase nor
 * WebDriverTestBase declares it for us: PHP attributes are not inherited.
 *
 * IT IS NOT COSMETIC, AND THE HOOK BELOW IS WHY IT IS SAFE ANYWAY. The
 * attribute decides whether tearDownAfterClass() is invoked once or twice -
 * PHPUnit calls it from TestCase when inIsolation, and again from
 * TestSuite::invokeMethodsAfterLastTest(). The guard in that hook keys on
 * ob_get_level() rather than on the isolation mode for exactly this reason,
 * and that was a measured choice, not a lucky one: 1 in the isolated child
 * with the per-test output buffer still open (must not print), 0 in the
 * parent after the last test (prints). So the summary prints once whichever
 * way this class is scheduled. Read that docblock before touching either.
 */
#[RunTestsInSeparateProcesses]
class AccessibilityTest extends WebDriverTestBase {

  use RecipeTestTrait;

  /**
   * The one-line report this gate produces, carried out of the test method.
   *
   * It is the assertion message, and it is ALSO written to ::summaryPath()
   * so that the reporting hook can print it from the parent process - see
   * that hook's docblock, which is where the mechanism is explained. NULL
   * means the run never reached the point where the totals are computed,
   * which is a different thing from "the totals were zero".
   */
  private static ?string $summary = NULL;

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
   * The one axe-core rule this gate switches on BY NAME (T-0634).
   *
   * WCAG 2.2's success criterion 2.5.8, Target Size (Minimum), is level AA.
   * axe-core maps exactly one rule to it, `target-size`, and ships that rule
   * DISABLED: `enabled:!1` in the 4.10.3 bundle that core's yarn.lock
   * resolves, which is the file ::getAxePath() reads. A default
   * `axe.run(document)` therefore never asked the question, so the "rules run
   * per page" this gate printed held no 2.5.8 check at all, while README.md
   * told a reader 2.5.8 is a criterion axe cannot decide. Found as F3 in
   * specs/006-hardening/research/2026-09-23-keyboard-measurability.md.
   *
   * NAMED, NEVER A TAG SET. `runOnly: {type: 'tag', values: ['wcag22aa']}`
   * would switch the rule on too, and would REPLACE the default run with a
   * filtered one in the same stroke. `rules: {<id>: {enabled: true}}` adds
   * exactly one rule to what the default run already does, so the change is
   * one criterion wide and this constant says which. The other rules a
   * default run leaves out stay out: seven more ship disabled, every one AAA
   * or tagged deprecated or obsolete by axe itself, and seven are tagged
   * experimental, five of them mapped to level A or AA criteria (1.3.1,
   * 1.3.4, 2.5.3). Read in the bundle on 2026-09-24: 103 rules in all.
   *
   * WHAT IT SETTLES AND WHAT IT DOES NOT. The rule measures each target's
   * box against the 24 by 24 CSS pixel minimum and applies the criterion's
   * spacing exception. Where it cannot decide - a target partly covered by
   * another element, for one - it files the node as `incomplete`, which is a
   * person's to judge; both counts are printed on every green run. And it
   * measures at ONE window size, the one the summary prints: a layout that
   * shrinks its targets only on a narrower screen is not measured here.
   */
  private const AXE_RULE_ENABLED_BY_NAME = 'target-size';

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
   * How many surfaces this gate scans. An EQUALITY, not a floor (T-0612).
   *
   * WHY THE FLOOR WAS WRONG, stated as the defect it was rather than as a
   * preference. The line below read `assertGreaterThanOrEqual(6, ...)` until
   * today, against a package that supplies nine and a shipped accessibility
   * statement that tells a citizen nine pages are scanned. Those two numbers
   * were never compared by anything. A Canvas page could stop shipping, this
   * gate would scan eight, every per-page assertion would hold, the run would
   * be green, and the statement would go on saying nine. A floor cannot fail
   * in the direction the product actually moves.
   *
   * WHERE THE NINE COMES FROM. It is not a preference either; it is the sum
   * of what ::declarePages() derives from the package, and each term is a
   * different render path:
   *
   *   2  Canvas pages, one file each in content/canvas_page/
   *   1  the front page, which is one of those two WITHOUT a breadcrumb
   *   4  the register routes named in VIEW_PAGES
   *   1  a published node, reached through the entity path
   *   1  the not-found page
   *
   * THE CONSTANT IS READ FROM OUTSIDE THIS FILE, which is the other half of
   * why it is a named constant and not a literal. tests/bin/packaged-claims
   * re-derives those five terms from the package - it counts the files in
   * content/canvas_page/, parses VIEW_PAGES, counts the single-surface
   * entries - and compares the total against this constant AND against the
   * count the shipped statement quotes. So the number is bound three ways: to
   * the package offline, to the prose offline, and to the pages actually
   * scanned at runtime by the assertion below. Change any one of the three
   * and two checks fail rather than none.
   */
  private const DECLARED_PAGES = 9;

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
   * How many LOGGED-IN surfaces this gate scans. An equality, like the nine.
   *
   * SEPARATE FROM DECLARED_PAGES ON PURPOSE (D-061, option B). That constant
   * counts the pages a member of the public reads, tests/bin/packaged-claims
   * compares it against the count the shipped accessibility statement quotes,
   * and that statement is about the public site. This one counts the single
   * administrative surface this package chose and installed itself. Folding
   * the two into one number would make the statement's count wrong in the
   * direction nobody checks.
   */
  private const DECLARED_LOGGED_IN_PAGES = 1;

  /**
   * The role the logged-in page is read as: this package's own, and only it.
   *
   * NOT AN ADMINISTRATOR, AND THAT IS THE POINT RATHER THAN A SHORTCUT.
   * config/user.role.agora_governance_auditor.yml exists so that somebody who
   * is NOT the administrator can witness the configuration audit. It holds
   * exactly two permissions, and `view config snapshots` is the one this route
   * requires. What that person is served is the page this package chose to give
   * them. Measured 2026-09-23 on the same route: this role and an administrator
   * are served the SAME seven nodes of Config Guardian's own markup; the
   * administrator is additionally served two foreign nodes this role is not
   * (the `dashboard` module's empty sidebar heading and Gin's top-bar actions).
   * So the choice changes which foreign chrome is present and hides nothing
   * Config Guardian renders.
   */
  private const LOGGED_IN_ROLE = 'agora_governance_auditor';

  /**
   * The route of the one logged-in page. Its path and title are read here.
   */
  private const DASHBOARD_ROUTE = 'config_guardian.dashboard';

  /**
   * The root element of Config Guardian's dashboard template.
   *
   * `<div class="config-guardian-dashboard">`, line 16 of
   * templates/config-guardian-dashboard.html.twig in config_guardian 1.0.3.
   * It is the wait target, part of the page-identity check and the ownership
   * anchor for that module's markup, so it is written once.
   */
  private const DASHBOARD_WRAPPER = '.config-guardian-dashboard';

  /**
   * Where each installed project's markup begins, each verified at source.
   *
   * A violation is attributed to a project only if its node sits INSIDE that
   * project's anchor, and that is measured in the page, per node, on every
   * run - not inferred from how a selector reads. A node inside no anchor is
   * counted as this package's own. That is the conservative direction on
   * purpose: foreign ownership is the claim that needs the evidence.
   *
   *   config_guardian  the dashboard template's own root, above.
   *   gin              the primary-tabs block Gin places from its own
   *                    config/optional. Gin's page.html.twig renders it in a
   *                    div.content-header OUTSIDE <main>, which is what puts
   *                    the tab list's heading outside every landmark; Claro,
   *                    Gin's base theme, wraps the same region in a <header>
   *                    inside <main>.
   *   coffee           the wrapper coffee.js builds and appends to <body> in
   *                    its behaviour's attach, outside every landmark by
   *                    construction.
   *
   * `navigation` IS NOT HERE, AND THE HANDED FOREIGN LIST NAMED IT. The empty
   * heading attributed to it, <h4 id="menu--dashboard">, is written EMPTY in
   * the `dashboard` module's menu-region--dashboard.html.twig; navigation only
   * supplies the sidebar that heading lands in. Walking a node's ancestors
   * names the container's owner, not the template's author. It is absent from
   * this page anyway: this role is not served it, measured.
   */
  private const OWNERSHIP_ANCHORS = [
    'config_guardian' => self::DASHBOARD_WRAPPER,
    'gin' => '#block-gin-primary-local-tasks',
    'coffee' => '.coffee-form-wrapper',
  ];

  /**
   * Violations on the logged-in page in markup this package did not write.
   *
   * Each is rule, axe selector and owner, asserted EXACTLY (D-061, option B).
   *
   * MEASURED 2026-09-23 on a runner built the way the pipeline builds this
   * job: core 11.4.7, core's own axe-core 4.10.3, selenium/standalone-chrome
   * 127.0, config_guardian 1.0.3, gin 5.0.15, coffee 2.0.1, logged in as
   * LOGGED_IN_ROLE. Nine nodes over two rules, the same nine on every scan.
   *
   * A SET AND NOT A TOTAL, because a total holds when one violation vanishes
   * and another arrives. A new entry fails; so does a declared entry that is
   * no longer there, because a list that outlives its measurement is a claim
   * about a page nobody looked at.
   *
   * config_guardian, color-contrast [serious]: #6c757d on #f5f7fa, 4.36:1
   * against the 4.5:1 AA floor - the module's own --cg-text-muted on its own
   * --cg-bg, css/config-guardian.css lines 17 and 14 - on the "(sync ->
   * active)" and "(active -> sync)" labels of the two comparison cards and the
   * five header cells of the recent-snapshots table. THE TABLE EXISTS ONLY ONCE
   * THE SITE HAS A SNAPSHOT, and the first one is taken by the site's first
   * cron run; see ::scanTheGovernanceDashboard() for why the scan waits for it.
   *
   * THIS IS NOT A COMMITMENT TO FIX ANY OF THEM. This project does not
   * maintain Config Guardian, Gin or Coffee. It is a commitment to have
   * measured what this package chose, and to say so where a reader looks.
   */
  private const INSTALLED_MARKUP_VIOLATIONS = [
    'config-guardian-dashboard' => [
      ['color-contrast', '.cg-comparison-card:nth-child(1) > .cg-comparison-header > h4 > small', 'config_guardian'],
      ['color-contrast', '.cg-comparison-card:nth-child(2) > .cg-comparison-header > h4 > small', 'config_guardian'],
      ['color-contrast', 'th:nth-child(1)', 'config_guardian'],
      ['color-contrast', 'th:nth-child(2)', 'config_guardian'],
      ['color-contrast', 'th:nth-child(3)', 'config_guardian'],
      ['color-contrast', 'th:nth-child(4)', 'config_guardian'],
      ['color-contrast', 'th:nth-child(5)', 'config_guardian'],
      ['region', '#primary-tabs-title', 'gin'],
      ['region', '.coffee-form-wrapper', 'coffee'],
    ],
  ];

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
   * Returns the options BOTH axe runs pass: one rule switched on by name.
   *
   * Written once and spliced into both scripts, so the anonymous scan and the
   * logged-in one cannot drift apart on which rules they run.
   *
   * @return string
   *   A JSON object literal, spliced into the page's script as it is.
   */
  private static function axeOptions(): string {
    return (string) json_encode([
      'rules' => [self::AXE_RULE_ENABLED_BY_NAME => ['enabled' => TRUE]],
    ]);
  }

  /**
   * Where the summary travels from the process that measures it to this one.
   *
   * A FILE, BECAUSE A STATIC PROPERTY DOES NOT CROSS A PROCESS BOUNDARY, and
   * there is one here. Drupal runs functional tests in PROCESS ISOLATION: the
   * test method executes in a child process, and ::tearDownAfterClass() is
   * then called TWICE - once in that child, from `TestCase::run()`, and once
   * in the parent, from `TestSuite::invokeMethodsAfterLastTest()`. The parent
   * is where the printing has to happen, and the parent never saw the totals.
   * So they are written to a file the child can write and the parent can
   * read; it is removed again as soon as it has been read.
   *
   * @return string
   *   The absolute path of the file carrying this run's summary line.
   */
  private static function summaryPath(): string {
    return sys_get_temp_dir() . '/agora-transparency-axe-gate.txt';
  }

  /**
   * {@inheritdoc}
   *
   * REMOVES ANY SUMMARY LEFT BEHIND BY AN EARLIER RUN. Without this, a run
   * that dies before it measures anything would print the PREVIOUS run's
   * figures as if they were its own - a stale number wearing a fresh date,
   * which is the exact defect this whole unit exists to remove. Watched: with
   * a summary file planted by hand and the test made to fail early, the
   * reporting hook prints NO SUMMARY rather than the planted line.
   */
  public static function setUpBeforeClass(): void {
    parent::setUpBeforeClass();
    if (file_exists(self::summaryPath())) {
      @unlink(self::summaryPath());
    }
  }

  /**
   * {@inheritdoc}
   *
   * PRINTS THE GATE'S OWN RESULT, ON A GREEN RUN (T-0613). Until this method
   * existed, this package's accessibility figure was checkable from nowhere a
   * stranger could reach: the assertion message is emitted only on failure,
   * `tests/` is `export-ignore`d so the tarball does not contain the test
   * either, and the shipped statement therefore quoted a number with no
   * public source. The theme's equivalent figure has always been public in
   * its `nightwatch` job. A reviewer could check one claim and not the other.
   *
   * ONE LINE PER SURFACE (T-0615). The file carries the nine's line and,
   * appended after it by ::scanTheGovernanceDashboard(), the logged-in
   * page's. Both are printed, in that order, and neither restates the
   * other: they count different pages, with the same definitions of rules
   * run, of heading-order and of the targets AXE_RULE_ENABLED_BY_NAME
   * measured. The logged-in line also counts violation NODES, because its
   * criterion is a set of nodes.
   *
   * THE GUARD ON THE FIRST LINE IS THE WHOLE MECHANISM, and it is there
   * because the obvious version of this method TURNED THE GATE RED. Pipeline
   * 970421 reported `Risky: 1` - "Test code or tested code printed unexpected
   * output" - with every assertion passing, and printed the summary twice:
   * once with the figures and once saying there were none.
   *
   * WHY: THE HOOK IS CALLED TWICE, and only one of the two calls may print.
   * PHPUnit 11 has exactly two call sites for it - `TestCase::run()`, guarded
   * by `inIsolation`, and `TestSuite::invokeMethodsAfterLastTest()` - and the
   * trace shows both firing. The first runs while the per-test output buffer
   * PHPUnit opened in `runBare()` IS STILL OPEN, so what it prints is
   * attributed to the test; core's `phpunit.xml.dist` sets
   * `beStrictAboutOutputDuringTests` with `failOnRisky`, and the run fails.
   * The second runs after the class is finished, with no buffer open, and
   * that is the call that may print.
   *
   * ⚠️ THE PIPELINE'S COMMAND LINE CARRIES NO `--process-isolation`, so the
   * isolation is decided by PHPUnit's per-class metadata rather than by a
   * flag, and this comment deliberately does not claim to have located it.
   * IT DOES NOT MATTER, and that is the point of keying the guard on the
   * BUFFER rather than on the mode: `ob_get_level()` is right whichever way
   * the second call arrives, and it is right in a single process too.
   *
   * `ob_get_level()` separates the two EXACTLY, and the numbers were measured
   * against PHPUnit 11.5.56 rather than assumed:
   *   child, inside the test buffer ......... 1  (must not print)
   *   parent, after the last test ........... 0  (prints)
   *   single process, no isolation .......... 0  (prints, once)
   *
   * ⚠️ AND THE SAME PROBE EXPLAINS THE RECORD `RequirementsTest` CITES.
   * Writing to STDERR from the isolated child does not dodge the buffer - it
   * raises `PHPUnit\Framework\Exception` outright, which is pipeline 934619's
   * failure reproduced on a laptop in one command.
   *
   * ⚠️ THE FIRST ATTEMPT WAS "FALSIFIED" AGAINST A CONTROL THAT DID NOT
   * REPRODUCE THE ENVIRONMENT, which is the lesson worth more than the fix: a
   * plain `TestCase` with no isolation passed happily, twice, and said
   * nothing about the only environment this code runs in. The probe that
   * matters is the one that fails the same way production does.
   *
   * WHAT IT PRINTS WHEN THERE IS NOTHING TO PRINT. A sentence saying so. This
   * hook runs even when the test errored on its first line, and a summary
   * line that quietly reported `0 of 0 pages` would be the exact shape of
   * green-about-nothing the rest of this file exists to refuse (I-007).
   */
  public static function tearDownAfterClass(): void {
    parent::tearDownAfterClass();
    // The isolated child, with the per-test output buffer still open. Printing
    // here is what makes the test risky; the parent prints instead.
    if (ob_get_level() !== 0) {
      return;
    }
    $line = '';
    if (is_readable(self::summaryPath())) {
      $line = trim((string) file_get_contents(self::summaryPath()));
      @unlink(self::summaryPath());
    }
    if ($line === '') {
      $line = 'agora_transparency axe gate: NO SUMMARY - the run did not reach the point where the totals are computed, so this pipeline has no page count, no rule count and no violation count. That is not a clean result; it is an absent one.';
    }
    print "\n" . $line . "\n";
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
    // AN EQUALITY, AND IT FAILS IN BOTH DIRECTIONS ON PURPOSE (T-0612). Too
    // few means a surface stopped shipping and this gate would otherwise scan
    // what is left and stay green; too many means a surface arrived that
    // nobody declared, and the shipped statement's count is then wrong in the
    // other direction. The superseded line was a floor of six against a
    // package that supplies nine, so it could not fail either way.
    //
    // The count is a local rather than a `count()` call inside the assertion:
    // phpstan's `phpunit.assertCount` rule is blocking on this project and
    // rejects `assertSame($n, count($x))` by name.
    $declared = count($pages);
    $this->assertSame(self::DECLARED_PAGES, $declared, sprintf(
      'This gate declares %d surfaces and DECLARED_PAGES says %d. One of the two moved without the other: either a page stopped being shipped, or one arrived and the constant - which tests/bin/packaged-claims compares against the package and against the count the shipped accessibility statement quotes - was not moved with it.',
      $declared,
      self::DECLARED_PAGES,
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
    $target_pages = 0;
    $targets_measured = 0;
    $targets_incomplete = 0;
    $viewports = [];

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
      if ($result['axe']['targets']['measured'] > 0) {
        $target_pages++;
      }
      $targets_measured += $result['axe']['targets']['measured'];
      $targets_incomplete += $result['axe']['targets']['incomplete'];
      $viewports[$result['structure']['viewport']] = TRUE;
    }

    // -- (5) the denominators, which are the point of the whole file --------
    // THE SUMMARY IS BUILT ONCE AND USED TWICE (T-0613). It is the assertion
    // message below, so a failure carries it; and it is handed to
    // ::tearDownAfterClass(), which prints it, so a PASS carries it too.
    //
    // ⚠️ THE COMMENT THAT STOOD HERE WAS FALSE AND WAS MEASURED FALSE. It
    // said the message "reaches junit.xml and the job's own summary instead
    // of being lost". PHPUnit emits an assertion message ONLY when the
    // assertion fails. On pipeline 969787, commit d06b9b6, job 12330735 -
    // green - `grep -c "pages scanned"` over the trace returns 0, and the
    // junit.xml artefact of that same job carries
    // `<testcase name="testAccessibilityOfTheInstalledPages" ... />`,
    // self-closing, with no message anywhere. The comment was right about the
    // failure path and wrong about the only path a green gate ever takes, so
    // this package's shipped accessibility statement quoted a figure that
    // existed in no log a reviewer could open - while the theme's equivalent
    // figure is public in its nightwatch job.
    //
    // The two counts are locals rather than `count()` calls inside the
    // assertions: phpstan's `phpunit.assertCount` rule is blocking on this
    // project and rejects `assertSame($n, count($x))` by name.
    $scanned_count = count($scanned);
    $declared_count = count($pages);
    // The target-size clause comes AFTER everything tests/bin/packaged-claims
    // reads out of this line under --online, so that reader's pattern, which
    // stops at the violation count, is untouched by it.
    self::$summary = sprintf(
      'agora_transparency axe gate: %d of %d declared pages scanned, %d-%d axe rules run per page, %d violations, heading-order reported on %d of %d pages; %s (WCAG 2.5.8), switched on by name, measured %d targets on %d of %d pages at a %s viewport, %d of them left incomplete for a person to judge.',
      $scanned_count,
      $declared_count,
      $rules_min,
      $rules_max,
      $violations,
      $heading_order_ran,
      $scanned_count,
      self::AXE_RULE_ENABLED_BY_NAME,
      $targets_measured,
      $target_pages,
      $scanned_count,
      implode(', ', array_keys($viewports)),
      $targets_incomplete,
    );
    // Handed to the reporting hook through a file, for the reason its own
    // docblock gives: this method runs in a child process and that hook prints
    // from the parent. Writing a file emits no output, so it cannot make this
    // test risky.
    @file_put_contents(self::summaryPath(), self::$summary);
    $this->assertSame(array_column($pages, 'name'), $scanned, self::$summary);

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

    // -- (6) the one logged-in page, declared separately (D-061, option B) --
    // AFTER THE NINE, NEVER INTERLEAVED WITH THEM. Every assertion above was
    // made anonymously, and the login inside this call changes what every
    // later request is served.
    $this->scanTheGovernanceDashboard($axe_source);
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
          viewport: window.innerWidth + 'x' + window.innerHeight,
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
    // returned across the wire - it is reduced in the page to the counts and
    // the buckets this gate asserts on, because a full axe report over a
    // register page is megabytes of nodes nothing here reads.
    //
    // THE OPTIONS ARE THE ONLY DIFFERENCE FROM A DEFAULT RUN (T-0634): one
    // rule, switched on by name. `targets` is that rule's own reading - the
    // nodes it measured, in whichever buckets hold them, and how many of
    // those it could not decide.
    $placeholders = [
      '__AXE_OPTIONS__' => self::axeOptions(),
      '__AXE_RULE__' => self::AXE_RULE_ENABLED_BY_NAME,
    ];
    $session->executeScript(str_replace(array_keys($placeholders), array_values($placeholders), <<<'JS'
      window.agoraAxeResult = null;
      window.axe.run(document, __AXE_OPTIONS__).then(function (r) {
        var buckets = ['passes', 'violations', 'incomplete', 'inapplicable'];
        var found = [];
        buckets.forEach(function (b) {
          var hit = r[b].some(function (x) { return x.id === 'heading-order'; });
          if (hit) { found.push(b); }
        });
        var sized = [];
        var nodes = {passes: 0, violations: 0, incomplete: 0, inapplicable: 0};
        buckets.forEach(function (b) {
          r[b].forEach(function (x) {
            if (x.id !== '__AXE_RULE__') { return; }
            if (sized.indexOf(b) === -1) { sized.push(b); }
            nodes[b] += x.nodes.length;
          });
        });
        window.agoraAxeResult = {
          rules: r.passes.length + r.violations.length + r.incomplete.length + r.inapplicable.length,
          violations: r.violations.map(function (v) {
            var where = v.nodes.slice(0, 3).map(function (n) {
              return n.target.join(',') + ' :: ' + n.html.slice(0, 120);
            });
            return v.id + ' x' + v.nodes.length + ' @ ' + where.join(' | ');
          }),
          bucket: found.length ? found.join('+') : 'absent',
          targets: {
            bucket: sized.length ? sized.join('+') : 'absent',
            measured: nodes.passes + nodes.violations + nodes.incomplete,
            incomplete: nodes.incomplete
          }
        };
      }).catch(function (e) {
        window.agoraAxeResult = {
          rules: 0,
          violations: ['axe.run() threw: ' + String(e)],
          bucket: 'absent',
          targets: {bucket: 'absent', measured: 0, incomplete: 0}
        };
      });
      JS));
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
    // 2.5.8 WAS ASKED, AND IT MEASURED SOMETHING (T-0634). The shape of the
    // heading-order line above, one step stricter: every page here carries
    // links, so a rule switched on by name that measured no target - landing
    // nowhere, or only in `inapplicable` - did not do its job, and 2.5.8
    // would drop out of the run as silently as it was absent until now.
    $this->assertGreaterThan(0, $axe['targets']['measured'], sprintf(
      '%s: %s was switched on by name for WCAG 2.5.8 and measured no target on this page (it landed in: %s). A rule that measured nothing cannot have passed.',
      $name,
      self::AXE_RULE_ENABLED_BY_NAME,
      $axe['targets']['bucket'],
    ));

    return ['structure' => $structure, 'axe' => $axe];
  }

  /**
   * Scans the one logged-in page: Config Guardian's dashboard (D-061, B).
   *
   * WHY THIS PAGE, AND ONLY THIS ONE. The administrative interface is Gin,
   * Coffee, Navigation and the rest of what the Drupal CMS administration
   * recipe installs, none of which this project can fix, and auditing all of
   * it was refused as D-061's option C. Config Guardian differs in the one
   * respect that decides the question: this package chose it, installs it and
   * configures it itself (recipe.yml, D-059), and ships a role whose purpose
   * is to read this page. So the page is measured, and what was measured is
   * printed in the log a GREEN run leaves behind.
   *
   * WHAT IT MEASURES, AND WHAT IT MAY NOT BE READ AS. Almost all the markup on
   * this page was written by other projects. The criterion has two halves:
   * zero violations in markup this package owns - counted conservatively, as
   * every violation that cannot be shown to sit inside an installed project's
   * own markup - and every other violation matched one by one against
   * INSTALLED_MARKUP_VIOLATIONS. IT IS NOT A CLAIM THAT THE PAGE CONFORMS. It
   * does not: Config Guardian's own palette puts seven text nodes under the
   * AA contrast floor, and the summary says so on every run, green included.
   *
   * WHY IT WAITS FOR CRON. Five of the seven are the header cells of a table
   * the dashboard renders only once a snapshot exists, and on a new site the
   * first snapshot is taken by the first cron run. Measured, not assumed: after
   * the recipe is applied `system.cron_last` is NULL; it is STILL NULL
   * immediately after the first request, because automated_cron runs after the
   * response is sent; read again eight seconds later, it is set and the
   * snapshot exists. The nine anonymous scans normally cover that gap many
   * times over, but "normally" is timing, and a gate must not depend on timing.
   * So the run waits for the first cron run to finish, and then asserts the
   * table has a row before axe is asked about it (I-062).
   *
   * @param string $axe_source
   *   The contents of core's axe-core bundle.
   */
  protected function scanTheGovernanceDashboard(string $axe_source): void {
    // -- (a) declare the page, reading its path and title from the router --
    $route_provider = \Drupal::service('router.route_provider');
    $title = (string) $route_provider->getRouteByName(self::DASHBOARD_ROUTE)->getDefault('_title');
    $this->assertNotSame('', $title, sprintf('The route %s must declare a title, or the heading asserted for it is the empty string and proves nothing.', self::DASHBOARD_ROUTE));
    $pages = [
      [
        'name' => 'config-guardian-dashboard',
        'path' => Url::fromRoute(self::DASHBOARD_ROUTE)->toString(),
        'heading' => $title,
      ],
    ];
    $declared = count($pages);
    $this->assertSame(self::DECLARED_LOGGED_IN_PAGES, $declared, sprintf(
      'The logged-in half of this gate declares %d surfaces and DECLARED_LOGGED_IN_PAGES says %d. One moved without the other.',
      $declared,
      self::DECLARED_LOGGED_IN_PAGES,
    ));
    // Every declared page has an expectation set of its own, and no set is
    // left over for a page that stopped being scanned.
    $this->assertSame(
      array_column($pages, 'name'),
      array_keys(self::INSTALLED_MARKUP_VIOLATIONS),
      'Every logged-in page must have exactly one declared set of installed-markup violations, and every set a page.',
    );

    // How much of the module this covers, read from the router rather than
    // typed. It is printed, not asserted: it is here so that the green log
    // says what this gate did NOT scan.
    $module_routes = count(array_filter(
      array_keys(iterator_to_array($route_provider->getAllRoutes())),
      static fn (string $name): bool => str_starts_with($name, 'config_guardian.'),
    ));

    // -- (b) log in as this package's own role, and nothing else ------------
    $account = $this->drupalCreateUser([], 'agora_governance_witness');
    $this->assertInstanceOf(UserInterface::class, $account);
    $account->addRole(self::LOGGED_IN_ROLE);
    $account->save();
    $roles = $account->getRoles();
    $expected_roles = [RoleInterface::AUTHENTICATED_ID, self::LOGGED_IN_ROLE];
    sort($roles);
    sort($expected_roles);
    $this->assertSame($expected_roles, $roles, sprintf('The logged-in page is read by a user holding the %s role and nothing else; any other role changes what the page is served.', self::LOGGED_IN_ROLE));
    $this->drupalLogin($account);

    // -- (c) wait for the first cron run, whose snapshot the page lists -----
    // Bounded, and it fails by name rather than scanning a page that lacks
    // the table the expectation set is partly about.
    $deadline = microtime(TRUE) + 60;
    \Drupal::state()->resetCache();
    while (\Drupal::state()->get('system.cron_last') === NULL && microtime(TRUE) < $deadline) {
      usleep(250000);
      \Drupal::state()->resetCache();
    }
    $this->assertNotNull(\Drupal::state()->get('system.cron_last'), 'The site\'s first cron run did not finish within 60 seconds. automated_cron runs it after the first request following installation, and that run takes the snapshot the dashboard lists; without it the recent-snapshots table is not on the page at all.');

    // -- (d) scan, accumulating; the assertions come after the loop ---------
    $admin_theme = (string) \Drupal::config('system.theme')->get('admin');
    $default_theme = (string) \Drupal::config('system.theme')->get('default');
    $results = [];
    foreach ($pages as $page) {
      $name = $page['name'];
      $this->drupalGet($page['path']);
      $this->assertNotNull(
        $this->assertSession()->waitForElement('css', self::DASHBOARD_WRAPPER, 15000),
        sprintf('%s: "%s" was never present at %s. If the page answered "Access denied", the %s role has lost the permission that reaches it.', $name, self::DASHBOARD_WRAPPER, $page['path'], self::LOGGED_IN_ROLE),
      );

      // The structure first, before axe is injected, as for the nine: a page
      // that failed to render is a page axe reports as clean (I-062).
      $structure = $this->getSession()->evaluateScript(str_replace('__WRAPPER__', self::DASHBOARD_WRAPPER, <<<'JS'
        (function () {
          var h1 = document.querySelectorAll('h1');
          var s = window.drupalSettings || {};
          return {
            h1: h1.length,
            h1Text: h1.length ? (h1[0].textContent || '').trim() : '',
            wrapper: document.querySelectorAll('__WRAPPER__').length,
            snapshotRows: document.querySelectorAll('__WRAPPER__ .cg-card--snapshots table tbody tr').length,
            theme: s.ajaxPageState && s.ajaxPageState.theme ? s.ajaxPageState.theme : '',
            viewport: window.innerWidth + 'x' + window.innerHeight,
            text: (document.body.innerText || '').trim().length
          };
        })()
        JS));
      $this->assertIsArray($structure, "$name: the page returned no structure at all.");
      $this->assertSame(1, $structure['h1'], sprintf('%s: exactly one <h1> (found %d: "%s").', $name, $structure['h1'], $structure['h1Text']));
      $this->assertSame($page['heading'], $structure['h1Text'], sprintf('%s: served the page it is for - expected the heading "%s" and found "%s".', $name, $page['heading'], $structure['h1Text']));
      $this->assertSame(1, $structure['wrapper'], sprintf('%s: one dashboard root (found %d).', $name, $structure['wrapper']));
      // THE PREMISE OF THE OWNERSHIP SPLIT, MEASURED RATHER THAN ASSUMED. The
      // page is rendered by the administration theme and not by the theme
      // this template ships. If that ever changed, the chrome around the
      // dashboard would be this package's own markup and every foreign
      // attribution below would be wrong.
      $this->assertSame($admin_theme, $structure['theme'], sprintf('%s: rendered by the administration theme "%s" (found "%s").', $name, $admin_theme, $structure['theme']));
      $this->assertNotSame($default_theme, $structure['theme'], "$name: must not be rendered by the theme this template ships, or its chrome is this package's own markup and the ownership split does not hold.");
      $this->assertGreaterThan(self::TEXT_FLOOR, $structure['text'], sprintf('%s: the page has content to be accessible about (%d characters of rendered text, floor %d).', $name, $structure['text'], self::TEXT_FLOOR));
      $this->assertGreaterThanOrEqual(1, $structure['snapshotRows'], "$name: the recent-snapshots table has a row before axe is asked about it. Five declared violations are its header cells, and without a snapshot the table is not rendered at all.");

      // -- axe: the same bundle, the same options and the same three
      // definitions as ::scanPage(): `rules` is the sum of the four buckets,
      // `bucket` is where heading-order landed and `targets` is what the rule
      // switched on by name measured, so the two summary lines count the
      // same things. What is added is ownership, PER NODE: the projects whose
      // anchor contains it, read from the live DOM. A target-size violation
      // is a violation like any other here, attributed and matched against
      // the declared set by the same code.
      $session = $this->getSession();
      $session->executeScript($axe_source);
      $this->assertTrue(
        (bool) $session->evaluateScript('typeof window.axe === "object"'),
        "$name: the axe-core bundle was executed but defined no `axe` global.",
      );
      $placeholders = [
        '__ANCHORS__' => (string) json_encode(self::OWNERSHIP_ANCHORS),
        '__AXE_OPTIONS__' => self::axeOptions(),
        '__AXE_RULE__' => self::AXE_RULE_ENABLED_BY_NAME,
      ];
      $session->executeScript(str_replace(array_keys($placeholders), array_values($placeholders), <<<'JS'
        (function (anchors) {
          window.agoraAxeLoggedIn = null;
          window.axe.run(document, __AXE_OPTIONS__).then(function (r) {
            var buckets = ['passes', 'violations', 'incomplete', 'inapplicable'];
            var found = buckets.filter(function (b) {
              return r[b].some(function (x) { return x.id === 'heading-order'; });
            });
            var sized = [];
            var counted = {passes: 0, violations: 0, incomplete: 0, inapplicable: 0};
            buckets.forEach(function (b) {
              r[b].forEach(function (x) {
                if (x.id !== '__AXE_RULE__') { return; }
                if (sized.indexOf(b) === -1) { sized.push(b); }
                counted[b] += x.nodes.length;
              });
            });
            var nodes = [];
            r.violations.forEach(function (v) {
              v.nodes.forEach(function (n) {
                var el = null;
                if (typeof n.target[0] === 'string') {
                  try { el = document.querySelector(n.target[0]); } catch (e) { el = null; }
                }
                nodes.push({
                  rule: v.id,
                  target: n.target.join(' '),
                  owners: Object.keys(anchors).filter(function (o) {
                    return el !== null && el.closest(anchors[o]) !== null;
                  }),
                  html: (n.html || '').slice(0, 120)
                });
              });
            });
            window.agoraAxeLoggedIn = {
              rules: r.passes.length + r.violations.length + r.incomplete.length + r.inapplicable.length,
              violated: r.violations.length,
              nodes: nodes,
              bucket: found.length ? found.join('+') : 'absent',
              targets: {
                bucket: sized.length ? sized.join('+') : 'absent',
                measured: counted.passes + counted.violations + counted.incomplete,
                incomplete: counted.incomplete
              }
            };
          }).catch(function (e) {
            window.agoraAxeLoggedIn = {
              rules: 0,
              violated: 0,
              nodes: [{rule: 'axe.run() threw', target: String(e), owners: [], html: ''}],
              bucket: 'absent',
              targets: {bucket: 'absent', measured: 0, incomplete: 0}
            };
          });
        })(__ANCHORS__);
        JS));
      $this->assertJsCondition('window.agoraAxeLoggedIn !== null', 60000, "$name: axe.run() never settled within 60 seconds.");
      $axe = $session->evaluateScript('window.agoraAxeLoggedIn');
      $this->assertIsArray($axe, "$name: axe returned no result object.");

      // Attribute every node. Inside exactly one anchor: that project's.
      // Inside none, or inside two: not shown to be somebody else's, so it is
      // counted as this package's own.
      $ours = [];
      $installed = [];
      $by_owner = [];
      foreach ($axe['nodes'] as $node) {
        $key = sprintf('%s @ %s', $node['rule'], $node['target']);
        if (count($node['owners']) !== 1) {
          $ours[] = sprintf('%s [%s] :: %s', $key, $node['owners'] ? implode('+', $node['owners']) : 'inside no installed project', $node['html']);
          continue;
        }
        $installed[] = sprintf('%s [%s]', $key, $node['owners'][0]);
        $by_owner[$node['owners'][0]] = ($by_owner[$node['owners'][0]] ?? 0) + 1;
      }
      $expected = array_map(
        static fn (array $entry): string => sprintf('%s @ %s [%s]', $entry[0], $entry[1], $entry[2]),
        self::INSTALLED_MARKUP_VIOLATIONS[$name],
      );
      sort($installed);
      sort($expected);
      $results[$name] = [
        'rules' => $axe['rules'],
        'bucket' => $axe['bucket'],
        'targets' => $axe['targets'],
        'viewport' => $structure['viewport'],
        'violated' => $axe['violated'],
        'nodes' => count($axe['nodes']),
        'ours' => $ours,
        'installed' => $installed,
        'expected' => $expected,
        'byOwner' => $by_owner,
      ];
    }

    // -- (e) the summary, built BEFORE any assertion below ------------------
    // So a failure carries it as its message and a pass hands it to the
    // reporting hook. Its prefix deliberately differs from the nine's line:
    // tests/bin/packaged-claims reads that one from the trace and requires
    // exactly one of it.
    $scanned_count = count($results);
    $rules = array_column($results, 'rules');
    $by_owner = [];
    foreach ($results as $result) {
      foreach ($result['byOwner'] as $owner => $n) {
        $by_owner[$owner] = ($by_owner[$owner] ?? 0) + $n;
      }
    }
    ksort($by_owner);
    $targets = array_column($results, 'targets');
    $targets_measured = array_column($targets, 'measured');
    $summary = sprintf(
      'agora_transparency axe gate, logged in: %d of %d declared pages scanned (%s), as a user holding only the %s role, %d-%d axe rules run per page, heading-order reported on %d of %d pages, %s (WCAG 2.5.8) measured %d targets on %d of %d pages at a %s viewport, %d of them left incomplete; %d violation nodes over %d rules - %d in markup this package owns, %d in installed markup it did not write, matched by rule, selector and owner against the %d declared (%s). %d routes are named config_guardian.* and this gate scans %d of them.',
      $scanned_count,
      $declared,
      implode(', ', array_column($pages, 'path')),
      self::LOGGED_IN_ROLE,
      $rules ? min($rules) : 0,
      $rules ? max($rules) : 0,
      count(array_filter(array_column($results, 'bucket'), static fn (string $b): bool => $b !== 'absent')),
      $scanned_count,
      self::AXE_RULE_ENABLED_BY_NAME,
      array_sum($targets_measured),
      count(array_filter($targets_measured, static fn (int $n): bool => $n > 0)),
      $scanned_count,
      implode(', ', array_unique(array_column($results, 'viewport'))),
      array_sum(array_column($targets, 'incomplete')),
      array_sum(array_column($results, 'nodes')),
      array_sum(array_column($results, 'violated')),
      array_sum(array_map('count', array_column($results, 'ours'))),
      array_sum(array_map('count', array_column($results, 'installed'))),
      array_sum(array_map('count', array_column($results, 'expected'))),
      $by_owner ? implode(', ', array_map(
        static fn (string $owner, int $n): string => "$owner $n",
        array_keys($by_owner),
        $by_owner,
      )) : 'none',
      $module_routes,
      $scanned_count,
    );
    // Appended to the file ::tearDownAfterClass() prints, after the nine's
    // line, so a green log carries both. Writing a file emits no output.
    @file_put_contents(self::summaryPath(), "\n" . $summary, FILE_APPEND);

    // -- (f) the assertions ---------------------------------------------------
    $this->assertSame(array_column($pages, 'name'), array_keys($results), $summary);
    foreach ($results as $name => $result) {
      // A rule that did not run cannot have passed (I-045).
      $this->assertGreaterThan(0, $result['rules'], "$name: $summary");
      $this->assertNotSame('absent', $result['bucket'], "$name: axe must have reported the heading-order rule; it landed in no bucket at all, which is not the same as passing. $summary");
      // And the rule switched on by name measured a target (T-0634), exactly
      // as ::scanPage() requires of each of the nine.
      $this->assertGreaterThan(0, $result['targets']['measured'], sprintf('%s: %s was switched on by name for WCAG 2.5.8 and measured no target on this page (it landed in: %s). A rule that measured nothing cannot have passed. %s', $name, self::AXE_RULE_ENABLED_BY_NAME, $result['targets']['bucket'], $summary));
      // THE FIRST HALF OF THE CRITERION: nothing in markup this package owns.
      $this->assertSame([], $result['ours'], sprintf('%s: %d violation nodes are not inside any installed project\'s declared markup, so they are counted as this package\'s own: %s. %s', $name, count($result['ours']), implode(' | ', $result['ours']), $summary));
      // THE SECOND HALF: every other violation is exactly a declared one, and
      // every declared one is still there.
      $this->assertSame($result['expected'], $result['installed'], sprintf(
        '%s: the violations in installed markup are not the declared set. New, not declared: %s. Declared, not found: %s. Found: %s. %s',
        $name,
        implode(' | ', array_diff($result['installed'], $result['expected'])) ?: 'none',
        implode(' | ', array_diff($result['expected'], $result['installed'])) ?: 'none',
        implode(' | ', $result['installed']),
        $summary,
      ));
    }
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
