---
name: sbom-y-licencias
description: Use when adding, upgrading or evaluating any dependency for the site template — running composer require, editing the require section, listing a project in recommended.yml, or reviewing whether a module's release status, security coverage or license allows it into the SBOM.
---

# Dependency policy, SBOM and licences

## Core principle

In this project **every dependency is a signed decision, not a `composer require`**. The
marketplace requires an SBOM with security coverage status, and forbids unstable releases and patches.
A dependency without a justification in `specs/000-project/DECISIONS.md` **does not exist**.

## Gateway — the four questions

Before adding ANYTHING, in this order:

1. **Does something Drupal CMS already ships solve it?** If yes → use that. The end.
2. **Does it have a stable release?** No dev, no alpha, no beta, no RC. If not → **out**.
3. **Does it have Drupal security team coverage?** If not → **out**, barring a signed escalation.
4. **Is the licence compatible?** GPL-2.0-or-later for anything derived from Drupal.

If all four pass → add it **and** write its line in `DECISIONS.md` in the same change.

## Absolute prohibitions

| Forbidden | Example | Why |
|---|---|---|
| Unstable releases | `^2.1-beta3`, `1.0-alpha1`, `dev-main` | Literal marketplace requirement |
| Patches | `composer-patches`, `patches` section | Forbidden by the starter kit |
| Exact pins | `"drupal/x": "1.2.3"` | Forbidden; use `^1.2` |
| Relaxed `minimum-stability` | `"minimum-stability": "beta"` | Masks the previous problem |
| Secrets in config or repo | AI keys, tokens | Non-negotiable #3 |

## The `DECISIONS.md` line

Every contrib module needs, as a minimum:

```
- **D-0NN** · SBOM: `drupal/<module>` ^X.Y — what it provides (1 line), why Drupal CMS does not cover it,
  security coverage status, licence. Signed by [andres] YYYY-MM-DD.
```

## Licences — manifest

| Asset type | Expected licence |
|---|---|
| Code derived from Drupal (recipes, themes, modules) | GPL-2.0-or-later |
| Typefaces | OFL or another free one — **never** a restrictively licensed font |
| Demo images and media | CC0, own work, or with documented rights |
| Demo text content | Own work |

Rule from the kit: *"You must possess legal rights to all included content"*. If you cannot name the
licence of an asset, **it does not go in**.

## Special case: the starter kit does not count

`drupal_cms_site_template_base` has no stable releases — only branches. **This is not a violation**:
it is **copied** as scaffolding, never declared in `require`. It does not enter the SBOM. Do not flag
it as a finding of the `no-unstable-deps` invariant.

## `recommended.yml` (Project Browser)

Literal warning from the file itself: list **only** projects with stable, supported releases.
A project in beta there will not be installable by most users (Composer's default
`minimum-stability`).

## Rationalisations and reality

| Excuse | Reality |
|---|---|
| "The beta is stable in practice" | The requirement is formal, not an opinion about quality |
| "It's only a development dependency" | If it is in `require`, it travels to the user |
| "I'll pin it so it is reproducible" | Pins are explicitly forbidden |
| "A small patch and I'll remove it later" | Patches are forbidden; the "later" never comes |
| "I'll document it afterwards" | The DECISIONS line goes in the SAME change |
| "It's what the starter kit ships in 2.x" | Whatever the kit ships does not excuse you; your SBOM is yours |

## Red flags — STOP and escalate

- You are about to write `-beta`, `-alpha`, `-rc` or `dev-` in `composer.json`
- You are looking for how to relax `minimum-stability`
- You add a module "and I'll justify it later"
- You cannot say under which licence a font or an image you are about to include is
