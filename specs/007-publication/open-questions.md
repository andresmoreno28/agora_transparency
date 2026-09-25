# Unit 007 · Open decisions

Proposed 2026-09-25. **None is signed.** Held here rather than in `specs/000-project/DECISIONS.md`
because that file is append-only and signed, and a proposal written into it reads like a ruling.

**Numbering, derived by command on 2026-09-25, before this file existed:**

```
grep -hoE '^#{2,4} +D-[0-9]{3}' specs/000-project/DECISIONS.md specs/*/open-questions.md \
  | grep -oE 'D-[0-9]{3}' | sort -u | tail -1                                ->  D-068
```

**D-069 is reserved for a pending ruling on the process-disclosure text already in the repository.
It is [andres]'s, it is not proposed in this file, and nothing in this unit decides it** — which is why
the proposals start at **D-070**. ⚠️ **A reservation written only in prose is invisible to that
command**, which reads headings: before this file it printed D-068, and it would have handed D-069 to
the next writer. So D-069 has a heading of its own, directly below, and it proposes nothing. With this
file on disk the command prints **D-074**, and the next free number is **D-075**, by the same command.

### D-069 · Reserved — the process-disclosure text ([andres]'s ruling)

Reserved here so that the numbering command above sees it. Its subject is the text at
`README.md:643-665` and `.gitattributes:68-74`, which describes how this repository is built and
reviewed and which of its files stay out of the package; a ruling on it would amend D-015.2, D-015.4
and D-016, which [andres] signed, so the ruling is his alone. No options are proposed here.

Each decision below gives the context in one line, then the options with their real cost, then a
recommendation marked ★ with its reason, and the cost of being wrong. **D-070 carries no ★**; its
own section says why.

---

## D-070 · The two launch version numbers — 👤 [andres]

*Context in one line:* the template's first stable release and the theme's launch release are one
public act, and a published version cannot be withdrawn — only superseded.

**The template's first stable version**

| | option | what it signals · what it costs |
|---|---|---|
| **A** | `1.0.0` | Drupal.org's own convention for a first stable release (*"the convention is to start from 1"*). Its branch row, `1.0.`, is created supported by default. Nothing else to do |
| **B** | `1.0.0-rc1` now, `1.0.0` about a month later | Drupal.org *suggests* an rc stays out a month. **Two public events**, against the 2026-09-24 ruling; the rc is **not installable by default** (Drupal CMS ships `minimum-stability: stable`) and **not covered** by advisories, so the month buys feedback only from people who type a stability flag |
| **C** | `0.1.0` | A numbers-only tag is a stable release by drupal.org's naming rule — and to anyone reading semantic versioning it says "initial development", the opposite of a launch. A later `1.0.0` would be a second launch |

**The theme's launch release**, which carries the 6 commits past `1.2.1`:

| | option | what it signals · what it costs |
|---|---|---|
| **A** | the next patch, `1.2.3`; `1.2.2` stays a tag with no release, as `1.0.4` and `1.0.8` already are | Continues the patch-only rule through the launch itself. No new branch row, so nothing to uncheck and no day-of-week constraint |
| **B** | release the existing `1.2.2` as tagged, then `1.2.3` for the two later commits | Two theme releases on launch day — the very thing the 2026-09-24 ruling was written to stop |
| **C** | a new minor, `1.3.0` | Marks the launch set in the version itself. A minor creates a **second supported branch row by itself** (I-113), so `1.2.` is unchecked in the same sitting — on a **Wednesday**, by D-050 part 2 — and that fixes the launch day. The template's `^1.1` admits it |

**No ★:** a version is public and cannot be withdrawn, and the standing delegation does not reach a
decision [andres] has already taken (2026-09-20). T-0701 puts both of his release rulings on disk.

**Cost of being wrong:** permanent. *"Once you release something, it's out in public and you can not
take it back"* (drupal.org's release documentation), and deleting a release breaks every build that
locked it (D-050, *"Deleting 1.0.7 — considered and rejected"*). A number chosen too high reads as
churn to the reviewer this whole project is aimed at; one chosen too low reads as unfinished.

---

## D-071 · What "the official launch" is, and when it happens — 👤 [andres]

*Context in one line:* his words of 2026-09-24 stop releases
<!-- cspell:disable -->*"hasta salir oficialmente"*<!-- cspell:enable --> ("until we officially
launch"), which is exactly this question; his words of 2026-09-20 do not mention the marketplace;
and D-012 put community publication before the marketplace — so the first thing to settle is which
event "the launch" is.

| | option | cost |
|---|---|---|
| **A ★** | **The launch is the community publication** — the first stable release, per `plan.md` §5 — on the first day its six preconditions hold on which [andres] can guarantee the next 14 days. The marketplace application follows it (D-073) | No calendar to lean on, and the launch is quiet: not in the installer, not listed (research §1.4) |
| **B** | **The launch is the marketplace listing**: apply first, and cut the first stable release only when the Drupal CMS team accepts | Reopens D-012, which made the application non-blocking. The date is set by a review queue that publishes no timeline (apply page, line 168), and until then the template has no installable release at all |
| **C** | Align the launch to an outside date — a Drupal CMS release, a DrupalCon | Waiting: the theme's accumulated fixes, the packaged text and every measurement age meanwhile; and a Drupal CMS release in between changes "the current version" the marketplace asks for, so T-0704 is redone anyway |
| **D** | Two steps: a pre-release now, the stable release later | Reopens the 2026-09-24 ruling and D-050 part 3's amendment; the pre-release is neither installable by default nor covered (D-070, option B) |

★ **A** — D-012 already chose this order, and every path into the installer or a demo needs a stable,
installable release to exist before it can be used.

⚠️ Under A, releases made after the launch and before a marketplace listing are patch releases, by the
reading applied to the 2026-09-20 ruling; his words name no end point.
⚠️ If D-070 chooses a theme minor, the launch is a Wednesday — that is D-070's cost, not a fifth option
here.

**Cost of being wrong:** A wrong → the version that reaches the marketplace was first published on a
quiet day — recoverable, because the marketplace reads the same release. B wrong → months with nothing
installable while a queue sets the date. And launching into a week [andres] cannot answer means the
first security report meets a missed promise, in public, on the first try — the asymmetry D-064 was
signed on.

---

## D-072 · A live demo at launch — 👤 [andres]

*Context in one line:* the ROADMAP asks for a Tugboat demo linked from `recipe.yml`, but drupal.org's
Tugboat builds previews of merge requests that expire, the installer's own demos moved to SimplyTest.me
on 2026-09-24, and any demo install counts as a site on the theme's public usage figure unless its
build prevents it.

| | option | cost |
|---|---|---|
| **A ★** | **No demo at launch.** The screenshot, the README and the project page carry the first impression; `.tugboat/` is deleted, as the starter kit's own header offers (*"If you don't want to integrate with Tugboat at all, delete this directory."*). A SimplyTest.me demo arrives if and when Ágora enters the curated list (D-073), built and hosted by others | No "try it" link on day one; a visitor installs locally. Nothing to host, nothing to keep current, nothing counted from this side |
| **B** | **A permanent demo [andres] hosts**, rebuilt from each release, its fetch URL pinned to the loopback value this package's test sites use, with no administrator account exposed | Hosting, and a rebuild per release; a public site of a **covered** project is a public attack surface he must patch; a demo that falls behind misrepresents the product it advertises |
| **C** | **Keep `.tugboat/`** for drupal.org's merge-request previews | They exist only for merge requests and expire 30 days after their last update — nothing to send anyone to. The kit's file builds Drupal CMS at **dev** stability and carries no usage guard, so it must be fixed under this option (T-0705) |

★ **A** — every demo path either counts on the theme's usage figure, needs hosting, or is not a demo;
and the one the ecosystem now uses comes with the listing, not before it.

⚠️ **SimplyTest.me cannot be chosen here.** It launches only curated templates, only with a stable
release `composer require` can resolve, and nothing in its build pins the fetch URL (research §3.3).
When it arrives, its launches that run cron will count as `agora_theme` sites; **the only lever is a
request to its maintainers**, which would be [andres]'s to make.

**Cost of being wrong:** A wrong → a reviewer or visitor who wanted to click through first,
recoverable at the next release. B wrong → a stale or unpatched public demo of a security-covered
project. C wrong → an unguarded install path sitting in the repository.

---

## D-073 · The marketplace application: channel and timing — 👤 [andres]

*Context in one line:* D-012 already chose community first and the marketplace afterwards; the
application page welcomes free templates from any individual, but the only channel it links is a form
for **sellers**.

What was measured (research §1.2, §1.3): the form's description is about templates *"available for
sale"* and *"becoming a template seller"*; 2 of its 12 required questions ask for a paid-template plan
and a pricing model; 0 mention a free template. Free templates do get in — 15 of the 16 curated entries
carry no price — so a route exists; it is simply not written down.

| | option | cost |
|---|---|---|
| **A** | Community only for now; revisit after a stretch of real use | The become-a-creator page calls the community route *"the best place to get feedback and build a track record"*. Cost: months outside the installer, while two government templates are in it |
| **B** | Apply at launch through the published form, answering the seller questions with "a free template; no price" | A free application through a seller's form risks being read as an incomplete sales proposal. The project's own goal is passing **on the first attempt**, and this spends it on a channel mismatch |
| **C ★** | **After the launch, ask the Drupal CMS team first** how a free template applies — through the channel the starter kit names for template questions (`#drupal-cms-templates` on Drupal Slack, `GET-STARTED.md:74`) or an issue in the Drupal CMS project — then apply by the route they name, with T-0715's material ready | One message and a wait. Nothing irreversible happens before the answer |

★ **C** — the only option that cannot spend the first attempt on the wrong door.

⚠️ **Two things the application will ask that are decided then, and flagged now:**

1. **The listing's accessibility value** has four settings — *Untested*, *WCAG 2.2 Level A*, *AA*, *AAA*
   (research §1.5) — and none says what Ágora ships: it targets AA and claims no conformance. The value
   is a public accessibility claim, and it can be no stronger than the attestation (T-0617).
2. **The pitch.** The Creator Guide calls government an *advanced vertical* and says it is open to
   *"more narrow use-cases"*. Ágora's first release is exactly that — a transparency **publication**
   portal (D-060) — and it sits beside a listed council template and a government starter rather than
   duplicating them. It should be described as what it is, not as a municipal portal.

**Cost of being wrong:** B wrong → the first attempt spent on the wrong door. A wrong → a slower road
to the installer, which is where every new Drupal CMS user starts.

---

## D-074 · What the installer card says about Ágora — 👤 [andres]

*Context in one line:* when somebody has already `composer require`d the template, the Drupal CMS
installer shows its card with any `links` and `creator` found under `extra.drupal_cms_installer` in
`recipe.yml`; the starter kit ships that block commented out, and Ágora has none.

| | option | cost |
|---|---|---|
| **A ★** | `links` with one entry — *Learn more* → the drupal.org project page — and no `creator` | Two lines of packaged YAML, seen only on the community route's install path |
| **B** | A, plus `creator: <a name or an organisation>`, shown as the template's author on the card | A public attribution line in every release; changing it takes a new release |
| **C** | Nothing | The card shows the name, the description and the screenshot, and no way to learn more |

★ **A** — the link is plainly useful and costs nothing; an author line is a public attribution only
[andres] can choose, so it stays out unless he rules B with the string. **No *Demo* link under any
option unless D-072 creates a demo.** Use the list form (`- text: … / url: …`): the curated list went
back to it on 2026-09-24 for compatibility with the 2.1.x installer.

**Cost of being wrong:** small — a new release changes it.

---

## What is not decided here

- **D-069** — reserved, pending, [andres]'s. It is a **launch precondition** for one reason only: the
  tag freezes the packaged text, and the packaged set carries text D-069 governs. No row in this unit
  edits that text, and none assumes an outcome.
- **D-054** — unit 005's reserved ruling on a third package. **Not a launch precondition**: the README's
  line about the cited assistant is true as a plan, in a section headed as a plan (T-0620).
- **D-045** — visual regression. Not a launch precondition.

## What waits on [andres], in order of need

| | what | why only him | needed before |
|---|---|---|---|
| 1 | **T-0703** — paste the theme page's replacement text | his account; the page is false today | as soon as [ejecutor]'s draft is handed |
| 2 | **Unit 006**: T-0616 and T-0633 (the keyboard walkthroughs) and its Gate B | a person's hands; his signature | the launch |
| 3 | **D-069** | reserved, his | T-0706 |
| 4 | **D-070**, **D-072**, **D-074** | version numbers, a public demo, a public attribution | wave 2 |
| 5 | **D-071** | what the launch is, and his availability for 14 days | wave 3 |
| 6 | **T-0709**, **T-0712** | releases and pages on drupal.org | launch day |
| 7 | **D-073**, then **T-0715** | his account and his name on the application | after the launch |
| 8 | **T-0714** — this unit's Gate B | his signature | closure |
