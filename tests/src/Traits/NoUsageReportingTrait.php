<?php

declare(strict_types=1);

/**
 * Stops every site this suite installs from reporting usage to Drupal.org.
 *
 * THE LEAK. This recipe applies `drupal_cms_site_template_base` (2.2.0;
 * formerly `drupal_cms_admin_ui`), which installs core's
 * `update` module, and `core_recommended_maintenance`, which installs
 * `automated_cron`. The first web request a test makes once both are present
 * runs cron, and `update` then asks the release-history server about every
 * enabled project, sending a `site_key`, the version and the list of enabled
 * modules - what core itself calls "usage information" in
 * UpdateFetcher::buildFetchUrl(). With no override that request goes to
 * `https://updates.drupal.org/release-history`, so every test site was reported
 * as one more site running this template's theme and modules.
 *
 * WHY THE OVERRIDE IS WRITTEN HERE. It goes into the test site's settings.php
 * BEFORE Drupal is installed: the same method, for the same reason, that core
 * uses to stop security advisories from being fetched during tests (see
 * FunctionalTestSetupTrait::prepareSettings()). A value in stored
 * configuration would have no single place to go: InstallTest's first cron
 * runs on the front-page request that initMink() makes inside setUp(), before
 * any test code runs, and in the classes that apply the recipe themselves
 * `update.settings` does not exist until the recipe installs `update`, so the
 * value would have to follow every applyRecipe() call.
 *
 * WHAT IT DOES NOT CHANGE. A settings.php override writes no stored
 * configuration and installs or removes nothing: `update` stays installed,
 * cron still runs and the fetch is still attempted - against a closed port on
 * the loopback interface, where it fails at once and cannot reach any other
 * host.
 *
 * WHAT A GUARDED SITE IS LIKE. It has no release data: `update` records every
 * project as not fetched. The suite passed method for method, with the same
 * assertions, with and without this trait when it was added (T-0635), but one
 * consequence is worth knowing before writing the next test.
 * `automatic_updates` runs its status checks whenever a module is
 * uninstalled, and when one is installed outside the installer, a recipe
 * apply or a config sync; its version-policy check then throws "The project
 * 'drupal' can not be updated because its status is not-fetched". A test that
 * calls the module installer directly will meet that exception. It comes from
 * being offline, not from this trait, and removing the trait is not the fix.
 *
 * ENFORCED. tests/bin/no-usage-reporting fails the gate when a class that
 * reaches BrowserTestBase or WebDriverTestBase does not use this trait, when a
 * class shadows its prepareSettings(), or when this file stops pinning the
 * fetch URL to a loopback address.
 */
trait NoUsageReportingTrait {

  /**
   * {@inheritdoc}
   */
  protected function prepareSettings() {
    parent::prepareSettings();
    $this->writeSettings([
      'config' => [
        'update.settings' => [
          'fetch' => [
            'url' => (object) [
              'value' => 'http://127.0.0.1:9/agora-tests-never-report-usage',
              'required' => TRUE,
            ],
          ],
        ],
      ],
    ]);
  }

}
