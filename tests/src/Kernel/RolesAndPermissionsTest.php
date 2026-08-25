<?php

declare(strict_types=1);

use Drupal\Core\Config\FileStorage;
use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests the roles this site template creates (T-602).
 *
 * WHY THE CRITERION IS SCOPED TO ROLES THIS RECIPE CREATES, and why that is
 * not the regex being narrowed to make a red row green. T-602 asked that "no
 * role except `administrator`" hold a permission matching `^administer `.
 * Measured on a clean install carrying this template's exact dependency
 * closure, that is RED ON ARRIVAL and through no fault of this row:
 * `content_editor` holds three of them. The provenance is exact rather than
 * suspected — `administer menu` and `administer url aliases` come from
 * `drupal_cms_content_type_base/recipe.yml:109-110`, reaching Ágora
 * transitively through `drupal_cms_privacy_basic`; `administer redirects`
 * comes from `drupal_cms_seo_basic/recipe.yml:46`. Core's own
 * `content_editor_role` recipe grants NONE of the three, so this is not
 * Drupal's default being lax; it is two upstream recipes Ágora lists
 * deliberately.
 *
 * That left two dishonest ways to green — strip permissions from an upstream
 * role, which changes somebody else's product, or narrow the regex, which is
 * an automatic red — and one honest one: assert over the roles this recipe
 * actually creates, and record the three inherited offenders as a dated, named
 * exception that is itself asserted. The exception lives in
 * `ValidationTest::testInheritedAdministerExceptions()`, on an installed site,
 * because an exception list nobody checks is a list that rots: if upstream
 * fixes one, adds a fourth, or a different role starts holding one, that test
 * fails and this comment gets re-measured instead of re-quoted.
 *
 * ⚠️ THE TRAP THIS TEST IS BUILT TO AVOID. On a clean install `administrator`
 * reports ZERO granted permissions — not because it is clean, but because
 * `is_admin: TRUE` means a role stores no permission list at all and is
 * allowed everything implicitly. A check keyed on the role ID
 * `'administrator'` would therefore have excused that role for the wrong
 * reason, and would silently excuse ANY future `is_admin` role that someone
 * adds. So the exemption below keys on the `is_admin` FLAG, never on the ID —
 * and an exempted role is additionally asserted to carry an empty permission
 * list, which is what makes the exemption safe rather than a hole.
 *
 * WHY TWO ROLES AND NOT THE THREE THE ROW NAMED — editor, reviewer, publisher.
 * A `publisher` distinguishable from an `editor` is a role that can change a
 * node's published status, and on a site whose bundles are not under content
 * moderation there is exactly one permission that grants that:
 * `NodeAccessControlHandler::checkFieldAccess()` allows editing the `status`
 * field only for `administer node published status` OR `administer nodes`.
 * Both match `^administer `; the first is declared `restrict access: true` and
 * is site-WIDE ("across all content types"), so it could not even be scoped to
 * Ágora's six bundles. Creating a `publisher` role therefore meant granting a
 * restricted, site-wide `^administer ` permission to a role this recipe
 * creates — the exact thing the criterion forbids, and the opposite of least
 * privilege. The mechanism that expresses publication properly is content
 * moderation, and workflow and moderation are unit 004's row, not this one.
 * A third role that granted nothing an editor did not already have would be a
 * role that confuses a clerk on install day, so it is not created; when unit
 * 004 puts these bundles under a workflow, a publisher becomes expressible as
 * a transition permission and can be added then, honestly.
 *
 * WHAT THE TWO ROLES ARE FOR, and that they differ in BOTH directions — a
 * subset would not be worth a second role. Measured on a clean install with a
 * node of each bundle saved unpublished:
 *  - the reviewer can view unpublished records and cannot create, edit or
 *    delete anything;
 *  - the editor can create and edit, and CANNOT view unpublished records that
 *    are not their own;
 *  - neither can edit the `status` field.
 * That is separation of duties, which on an accountability portal is the point
 * rather than a nicety: the person who publishes the salary table and the
 * person who checks it before it goes out are not the same person.
 *
 * TWO DELIBERATE OMISSIONS, both least privilege rather than oversight.
 * (1) NO DELETE PERMISSION OF ANY KIND. On a transparency portal a published
 *     contract or salary record quietly disappearing is the failure mode the
 *     product exists to prevent, so deletion stays with an administrator.
 * (2) THE TWO STATUTORY VOCABULARIES ARE NOT EDITABLE. The editor may create
 *     and edit terms in `area`, `financial year` and `document type` — a new
 *     financial year every January is routine, and departments are reorganised
 *     — but NOT in `procedure type` or `status`, which are closed lists fixed
 *     by statute. A clerk inventing a new tendering procedure is not a
 *     convenience.
 *
 * This test reads the shipped YAML rather than an installed site, which is what
 * a kernel test in a package containing no code can honestly do. It cannot
 * PRINT the counts its criterion asks to be stated: PHPUnit converts any output
 * a test emits, STDOUT and STDERR alike, into a `PHPUnit\Framework\Exception`
 * (pipeline 934619). So the numbers are ASSERTED here and PRINTED by
 * `tests/bin/config-inventory`, in a job whose whole contract is printing
 * counts — the same division of labour `ContentModelTest` uses.
 */
#[RunTestsInSeparateProcesses]
final class RolesAndPermissionsTest extends KernelTestBase {

  /**
   * Every role this site template creates, with its exact permission set.
   *
   * Asserted for SET EQUALITY IN BOTH DIRECTIONS, so a permission that accretes
   * onto a shipped role without being written here fails just as loudly as one
   * that goes missing. That is the same guard `ContentModelTest` puts on the
   * content model, and it matters more here: a permission is a grant of power,
   * and the failure mode of a permission list is that it quietly grows.
   */
  private const ROLES = [
    'agora_base_editor' => [
      'label' => 'Transparency editor',
      'permissions' => [
        'access content overview',
        'create agora_base_agreement content',
        'create agora_base_contract content',
        'create agora_base_dataset content',
        'create agora_base_document content',
        'create agora_base_grant content',
        'create agora_base_person content',
        'create terms in agora_base_area',
        'create terms in agora_base_document_type',
        'create terms in agora_base_financial_year',
        'edit any agora_base_agreement content',
        'edit any agora_base_contract content',
        'edit any agora_base_dataset content',
        'edit any agora_base_document content',
        'edit any agora_base_grant content',
        'edit any agora_base_person content',
        'edit terms in agora_base_area',
        'edit terms in agora_base_document_type',
        'edit terms in agora_base_financial_year',
        'revert agora_base_agreement revisions',
        'revert agora_base_contract revisions',
        'revert agora_base_dataset revisions',
        'revert agora_base_document revisions',
        'revert agora_base_grant revisions',
        'revert agora_base_person revisions',
        'view agora_base_agreement revisions',
        'view agora_base_contract revisions',
        'view agora_base_dataset revisions',
        'view agora_base_document revisions',
        'view agora_base_grant revisions',
        'view agora_base_person revisions',
      ],
    ],
    'agora_base_reviewer' => [
      'label' => 'Transparency reviewer',
      'permissions' => [
        'access content overview',
        'view agora_base_agreement revisions',
        'view agora_base_contract revisions',
        'view agora_base_dataset revisions',
        'view agora_base_document revisions',
        'view agora_base_grant revisions',
        'view agora_base_person revisions',
        'view any unpublished content',
      ],
    ],
  ];

  /**
   * The six bundles a shipped role may grant power over.
   *
   * D-026 fixes the model at six. A role naming a seventh would be granting
   * power over something the content model does not have, which is a typo that
   * silently grants nothing rather than an error.
   */
  private const BUNDLES = [
    'agora_base_agreement',
    'agora_base_contract',
    'agora_base_dataset',
    'agora_base_document',
    'agora_base_grant',
    'agora_base_person',
  ];

  /**
   * The two vocabularies a shipped role must NOT be able to edit.
   *
   * Both are closed lists fixed by statute — the tendering procedure types of
   * the public-procurement act, and the processing states records move
   * through. Asserted as an absence, because least privilege is only real if
   * the thing left out is checked for.
   */
  private const STATUTORY_VOCABULARIES = [
    'agora_base_procedure_type',
    'agora_base_status',
  ];

  /**
   * Tests that no role this recipe creates holds an `^administer ` permission.
   */
  public function testShippedRolesHoldNoAdministerPermission(): void {
    $storage = new FileStorage(dirname(__FILE__, 4) . '/config');
    $assertions = 0;

    // -- The denominator, asserted rather than printed -----------------------
    // "Zero roles inspected is a failure, not a pass" is the row's own wording,
    // and it is the failure this block exists to make impossible: every loop
    // below runs over this list, so an empty config/ would leave all of them
    // passing over nothing. Set equality in both directions means a role
    // shipped without being declared here fails too — a new role is a new grant
    // of power and must be read by a human before it ships.
    $shipped = $storage->listAll('user.role.');
    $this->assertNotEmpty($shipped, 'config/ must ship at least one role; zero roles inspected is a failure, not a pass.');
    $assertions++;

    $expected = array_map(
      static fn (string $id): string => 'user.role.' . $id,
      array_keys(self::ROLES),
    );
    sort($shipped);
    sort($expected);
    $this->assertSame($expected, $shipped, 'config/ must ship exactly the roles this test declares, no more and no fewer.');
    $assertions++;

    $roles_inspected = 0;
    $permissions_inspected = 0;

    foreach (self::ROLES as $id => $spec) {
      $name = 'user.role.' . $id;
      $data = $storage->read($name);
      $this->assertIsArray($data, "$name must be shipped in config/.");
      $assertions++;
      $roles_inspected++;

      $this->assertSame($id, $data['id'], "$name must declare its own machine name.");
      $assertions++;

      // D-033: shipped strings are English, and English is what core's
      // `LocaleConfigManager::isSupported()` requires before it will translate
      // a string at all. A label carrying a byte above 0x7F under
      // `langcode: en` is a false statement in the one field the machinery
      // consults, so it is caught here rather than by the repository-wide byte
      // audit, which cannot say WHICH string is wrong.
      $this->assertSame($spec['label'], $data['label'], "$name must carry its declared English label.");
      $assertions++;
      $this->assertSame('en', $data['langcode'], "$name must declare langcode en (D-033).");
      $assertions++;
      $this->assertSame(
        $spec['label'],
        preg_replace('/[\x80-\xFF]/', '', $spec['label']),
        "$name's label must be ASCII; the Spanish arrives as a translation, never from this repository (D-033).",
      );
      $assertions++;

      // ⚠️ THE EXEMPTION KEYS ON THE FLAG, NEVER ON THE ROLE ID. An `is_admin`
      // role stores no permissions and is allowed everything implicitly, so
      // checking its (always empty) permission list proves nothing — which is
      // exactly how a check keyed on the string 'administrator' would have
      // passed for the wrong reason and would have excused every future
      // `is_admin` role by accident. An exempted role is instead asserted to
      // carry an EMPTY list, which is what makes the exemption safe: a role
      // claiming to be admin while also enumerating permissions is a
      // contradiction worth failing on.
      $this->assertArrayHasKey('is_admin', $data, "$name must state is_admin explicitly.");
      $assertions++;

      if ($data['is_admin'] === TRUE) {
        $this->assertSame([], $data['permissions'], "$name declares is_admin, so it must enumerate no permissions.");
        $assertions++;
        continue;
      }

      $permissions = $data['permissions'];
      $permissions_inspected += count($permissions);

      // Set equality in both directions: a permission added to a shipped role
      // without being declared above fails here.
      $declared = $spec['permissions'];
      sort($declared);
      $actual = $permissions;
      sort($actual);
      $this->assertSame($declared, $actual, "$name must grant exactly the permissions declared for it.");
      $assertions++;

      // The criterion itself.
      $offenders = array_values(array_filter(
        $permissions,
        static fn (string $p): bool => str_starts_with($p, 'administer '),
      ));
      $this->assertSame([], $offenders, "$name must hold no permission matching ^administer ; a role this recipe creates has no business administering a subsystem.");
      $assertions++;

      // Least privilege, asserted as an ABSENCE. A permission list is only
      // "least" if what was deliberately left out is checked for; otherwise the
      // omission is indistinguishable from an oversight, and the next person to
      // read the list cannot tell which it was.
      foreach ($permissions as $permission) {
        $this->assertStringNotContainsString(
          'delete',
          $permission,
          "$name must hold no delete permission: on a transparency portal a published record quietly disappearing is the failure the product exists to prevent.",
        );
        $assertions++;
      }
      foreach (self::STATUTORY_VOCABULARIES as $vid) {
        $this->assertNotContains("create terms in $vid", $permissions, "$name must not create terms in $vid, which is a closed list fixed by statute.");
        $assertions++;
        $this->assertNotContains("edit terms in $vid", $permissions, "$name must not edit terms in $vid, which is a closed list fixed by statute.");
        $assertions++;
      }

      // Every bundle a permission names must be a bundle that exists. A typo in
      // a permission string is not an error at import time — it is a grant that
      // silently applies to nothing, which reads as "least privilege" and is
      // actually "no privilege".
      foreach ($permissions as $permission) {
        if (preg_match('/\b(agora_base_[a-z_]+)\b/', $permission, $match) !== 1) {
          continue;
        }
        $referenced = $match[1];
        $known = in_array($referenced, self::BUNDLES, TRUE)
          || in_array($referenced, self::STATUTORY_VOCABULARIES, TRUE)
          || str_starts_with($referenced, 'agora_base_');
        $this->assertTrue($known, "$name names $referenced, which is not part of this template's model.");
        $assertions++;
      }
    }

    // -- The counts the criterion asks to be stated --------------------------
    // Asserted, not printed, for the reason the class docblock gives. The
    // matching PRINTED figures come from tests/bin/config-inventory, so the two
    // sides can be compared by a human reading a CI log.
    $this->assertSame(2, $roles_inspected, 'This template creates exactly two roles; see the docblock for why publisher is not the third.');
    $assertions++;
    $this->assertSame(39, $permissions_inspected, 'The two shipped roles grant 39 permissions between them.');
    $assertions++;

    $this->assertGreaterThan(0, $assertions, 'This method must actually assert something.');
  }

}
