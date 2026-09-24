# Security policy — Ágora Transparency

## Reporting a vulnerability

Report it privately. Do not open a public issue, and do not describe it in a forum, in a chat or
on social media before it is fixed.

Use the **Report a security vulnerability** link in the sidebar of the project's page on
Drupal.org: <https://www.drupal.org/project/agora_transparency>. It opens a new work item in the
project's issue tracker on `git.drupalcode.org` with confidentiality already turned on. Leave it
on, so that the report is not public. You need a Drupal.org account to use it.

A useful report says:

* which version is affected: a release number, or a branch and a commit;
* how to reproduce the problem, starting from a fresh install of the site template;
* what an attacker could do with it, and which permissions or role they would need;
* a proposed fix, if you have one;
* how you would like to be credited.

## What happens next

The maintainer acknowledges a report within **14 calendar days**. That is an acknowledgement
only: no time to fix is promised.

**Stable releases are covered by Drupal's
[security advisory policy](https://www.drupal.org/security-advisory-policy)**, so a report about a
stable release also enters the Drupal Security Team's process, which Drupal.org describes in
[Reporting a security issue](https://www.drupal.org/docs/develop/issues/issue-procedures-and-etiquette/reporting-a-security-issue).
The timelines of that process are the Security Team's, not a promise made here.

**Pre-releases and development code are not covered**: alpha, beta and release-candidate releases,
development snapshots and unreleased branches.

## A vulnerability in a project this package requires

This package pins no version and patches nothing: every project it requires is required with a
version range. A security release inside that range reaches your site through your own
`composer update`, and no release of this package is involved. When Composer adds a site template
to a Drupal CMS site, the template's requirements are copied into the site's own `composer.json`,
so if a fix needs a version outside that range, you widen the range there.

A vulnerability in one of those projects belongs to that project: report it through the same link
on that project's own page on Drupal.org.

## A vulnerability in configuration this package ships

A site template is applied once, when a site is created, and it has no upgrade path: a later
release changes nothing on a site that already exists. When a security fix is to configuration
this package ships, the release notes of the release that fixes it say what to change on an
existing site, by hand.
