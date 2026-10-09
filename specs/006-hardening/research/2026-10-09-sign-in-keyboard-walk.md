# The sign-in page by keyboard, 2026-10-09 (T-0542)

Machine keyboard walk of `/user/login` on a clean install of commit `58d84b2` (agora_core 1.0.0,
Config Guardian 1.0.5, agora_theme 1.2.1, core 11.4.8), with the harness of
`2026-09-23-keyboard-measurability.md` (Appendix B, byte-identical) and one added mode,
`--inject=from-top`, because core's login form puts focus in Username on load. The first
transcript is the walk; the other two are the controls, which must go red.

The transcripts below are verbatim tool output, so the spell check is off for them.

<!-- cspell:disable -->

## `signin-fromtop.log`

```

[sign-in @1280x800] /user/login  status 200
  2.1.1 reach : 32 reached of 32 predicted (unreached 0, not predicted 0, stopped being candidates during the walk 0); walk exit: left-document after 33 presses
  2.1.2 traps : forward left-document, reverse wrapped
  2.4.3 order : positive tabindex 0; DOM-order inversions 0; reverse walk mirrors forward: true; upward jumps 4 (0 up-and-left)
        trail : (no landmark) x1 -> banner x1 -> navigation "Main navigation" x3 -> search "Search published records" x2 -> main x1 -> navigation "Breadcrumb" x1 -> form x4 -> contentinfo x1 -> navigation "Find a record" x2 -> navigation "Public money" x3 -> navigation "Documents and data" x2 -> navigation "The institution" x2 -> navigation "Legal and accessibility" x4 -> contentinfo x1 -> navigation "Follow us" x4
  3.2.1       : address changes during the walks 0
  2.4.7 focus : visible 32 · subtle 0 · NONE 0 · offscreen 0   (2.4.13 AAA area+3:1 met by 32 of 32)
  2.4.11 hide : forward entirely hidden 0, partly 2 (under 50% visible 0); reverse entirely hidden 0, partly 2; causes: header.agora-page__header:14, (viewport):3014, button.shwpd.eye-close:6
  2.4.1 skip  : first stop a "Skip to main content"; Enter -> hash #main-content, focus a#main-content; next Tab a "Forgotten your password?" in main; pass true
  activation  : 3 non-link controls, click vs Enter vs Space; failing 0; links sampled 11, followed on Enter 11
  scrollers   : none overflowing
  1.4.10      : scrollWidth 1280 vs viewport 1280 (overflow 0px); offenders 0; text clipped 0
  axe         : 89 rules, 0 violation nodes []; target-size [default run: absent; asked by name: passes] (pass nodes 31, violations 0, incomplete 0)

[sign-in @320x256] /user/login  status 200
  2.1.1 reach : 32 reached of 32 predicted (unreached 0, not predicted 0, stopped being candidates during the walk 0); walk exit: left-document after 33 presses
  2.1.2 traps : forward left-document, reverse wrapped
  2.4.3 order : positive tabindex 0; DOM-order inversions 0; reverse walk mirrors forward: true; upward jumps 0 (0 up-and-left)
        trail : (no landmark) x1 -> banner x1 -> navigation "Main navigation" x3 -> search "Search published records" x2 -> main x1 -> navigation "Breadcrumb" x1 -> form x4 -> contentinfo x1 -> navigation "Find a record" x2 -> navigation "Public money" x3 -> navigation "Documents and data" x2 -> navigation "The institution" x2 -> navigation "Legal and accessibility" x4 -> contentinfo x1 -> navigation "Follow us" x4
  3.2.1       : address changes during the walks 0
  2.4.7 focus : visible 32 · subtle 0 · NONE 0 · offscreen 0   (2.4.13 AAA area+3:1 met by 32 of 32)
  2.4.11 hide : forward entirely hidden 0, partly 2 (under 50% visible 0); reverse entirely hidden 0, partly 2; causes: header.agora-page__header:14, (viewport):3029, button.shwpd.eye-close:12
  2.4.1 skip  : first stop a "Skip to main content"; Enter -> hash #main-content, focus a#main-content; next Tab a "Forgotten your password?" in main; pass true
  activation  : 3 non-link controls, click vs Enter vs Space; failing 0; links sampled 11, followed on Enter 11
  scrollers   : none overflowing
  1.4.10      : scrollWidth 320 vs viewport 320 (overflow 0px); offenders 0; text clipped 0
  axe         : 89 rules, 0 violation nodes []; target-size [default run: absent; asked by name: passes] (pass nodes 31, violations 0, incomplete 0)

==== TOTALS {"playwright":"1.63.0","chrome":"154.0.8037.98","axe":"4.13.0","base":"https://agora-t0542-kbd.ddev.site","set":"anon","inject":"from-top","commit":"58d84b2f2eff5636972a5661ae749cb86149fb6a","main":"main.agora-page__main","date":"2026-10-09T15:46:10.475Z"}
  pageViewports: 2
  skippedNotHtml: 0
  harnessFailures: 0
  stopsReached: 64
  predicted: 64
  unreached: 0
  notPredicted: 0
  droppedDuringWalk: 0
  indicatorVisible: 64
  indicatorSubtle: 0
  indicatorNone: 0
  indicatorOffscreen: 0
  aaaMeets: 64
  obscuredEntirelyForward: 0
  obscuredPartlyForward: 4
  obscuredUnderHalfForward: 0
  obscuredEntirelyReverse: 0
  obscuredPartlyReverse: 4
  traps: 0
  walksClean: 4
  reverseMirrorsForward: 2
  urlChangesDuringWalks: 0
  revisits: 0
  routesWithoutMain: 0
  skipPass: 2
  controlsTested: 6
  controlsFailing: 0
  linksTested: 22
  linksFollowed: 22
  registerFlows: 0
  scrollRegions: 0
  scrollRegionsOperable: 0
  scrollRegionsKeyboardScrolls: 0
  scrollRegionsRelyOnBrowser: 0
  axeViolationNodes: 0
  targetSizeViolationNodes: 0
  targetSizePassNodes: 62
  targetSizeIncompleteNodes: 0
  reflowOverflowPages: 0
  findings: 0
results written: signin-fromtop.json
```

## `signin-outline.log`

```

[sign-in @1280x800] /user/login  status 200
  2.1.1 reach : 22 reached of 32 predicted (unreached 10, not predicted 0, stopped being candidates during the walk 0); walk exit: left-document after 23 presses
  2.1.2 traps : forward left-document, reverse wrapped
  2.4.3 order : positive tabindex 0; DOM-order inversions 0; reverse walk mirrors forward: false; upward jumps 3 (0 up-and-left)
        trail : form x3 -> contentinfo x1 -> navigation "Find a record" x2 -> navigation "Public money" x3 -> navigation "Documents and data" x2 -> navigation "The institution" x2 -> navigation "Legal and accessibility" x4 -> contentinfo x1 -> navigation "Follow us" x4
  3.2.1       : address changes during the walks 0
  2.4.7 focus : visible 0 · subtle 5 · NONE 17 · offscreen 0   (2.4.13 AAA area+3:1 met by 0 of 22)
  2.4.11 hide : forward entirely hidden 0, partly 1 (under 50% visible 0); reverse entirely hidden 0, partly 2; causes: button.shwpd.eye-close:6, header.agora-page__header:7, (viewport):1507
  2.4.1 skip  : first stop input ""; NOT a skip link
  activation  : 2 non-link controls, click vs Enter vs Space; failing 0; links sampled 7, followed on Enter 7
  scrollers   : none overflowing
  1.4.10      : scrollWidth 1280 vs viewport 1280 (overflow 0px); offenders 0; text clipped 0
  axe         : 89 rules, 0 violation nodes []; target-size [default run: absent; asked by name: passes] (pass nodes 31, violations 0, incomplete 0)

==== TOTALS {"playwright":"1.63.0","chrome":"154.0.8037.98","axe":"4.13.0","base":"https://agora-t0542-kbd.ddev.site","set":"anon","inject":"outline","commit":"58d84b2f2eff5636972a5661ae749cb86149fb6a","main":"main.agora-page__main","date":"2026-10-09T15:54:58.072Z"}
  pageViewports: 1
  skippedNotHtml: 0
  harnessFailures: 0
  stopsReached: 22
  predicted: 32
  unreached: 10
  notPredicted: 0
  droppedDuringWalk: 0
  indicatorVisible: 0
  indicatorSubtle: 5
  indicatorNone: 17
  indicatorOffscreen: 0
  aaaMeets: 0
  obscuredEntirelyForward: 0
  obscuredPartlyForward: 1
  obscuredUnderHalfForward: 0
  obscuredEntirelyReverse: 0
  obscuredPartlyReverse: 2
  traps: 0
  walksClean: 2
  reverseMirrorsForward: 0
  urlChangesDuringWalks: 0
  revisits: 0
  routesWithoutMain: 0
  skipPass: 0
  controlsTested: 2
  controlsFailing: 0
  linksTested: 7
  linksFollowed: 7
  registerFlows: 0
  scrollRegions: 0
  scrollRegionsOperable: 0
  scrollRegionsKeyboardScrolls: 0
  scrollRegionsRelyOnBrowser: 0
  axeViolationNodes: 0
  targetSizeViolationNodes: 0
  targetSizePassNodes: 31
  targetSizeIncompleteNodes: 0
  reflowOverflowPages: 0
  findings: 19
   - sign-in @1280x800 2.1.1 unreached by Tab: a "Skip to main content" | a "Agora keyboard rig T-0542" | a "All publications" | a "Document library" | a "The institution" | input "" | button "Search" | a "Forgotten your password?" | a "Home" | input ""
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 1: input "" (form)
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 3: input "Log in" (form)
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 4: a "Agora keyboard rig T-0542" (contentinfo)
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 5: a "All publications" (navigation "Find a record")
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 6: a "Document library" (navigation "Find a record")
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 7: a "Contracts awarded" (navigation "Public money")
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 8: a "Grants awarded" (navigation "Public money")
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 9: a "Agreements signed" (navigation "Public money")
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 10: a "Budgets, accounts and plans" (navigation "Documents and data")
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 11: a "Open data" (navigation "Documents and data")
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 12: a "Who runs the council" (navigation "The institution")
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 13: a "People and their pay" (navigation "The institution")
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 14: a "Accessibility statement" (navigation "Legal and accessibility")
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 15: a "Legal notice" (navigation "Legal and accessibility")
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 16: a "Privacy notice" (navigation "Legal and accessibility")
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 17: a "Cookies" (navigation "Legal and accessibility")
   - sign-in @1280x800 2.4.7 NO visible indicator at stop 18: a "Drupal" (contentinfo)
   - sign-in @1280x800 2.4.1 skip link: {"firstStop":"input \"\"","isSkip":false,"target":null}
results written: signin-outline.json
```

## `signin-trap.log`

```

[sign-in @1280x800] /user/login  status 200
  2.1.1 reach : 22 reached of 33 predicted (unreached 11, not predicted 0, stopped being candidates during the walk 0); walk exit: left-document after 23 presses
  2.1.2 traps : forward left-document, reverse TRAP-no-movement
  2.4.3 order : positive tabindex 0; DOM-order inversions 0; reverse walk mirrors forward: null; upward jumps 3 (0 up-and-left)
        trail : form x3 -> contentinfo x1 -> navigation "Find a record" x2 -> navigation "Public money" x3 -> navigation "Documents and data" x2 -> navigation "The institution" x2 -> navigation "Legal and accessibility" x4 -> contentinfo x1 -> navigation "Follow us" x4
  3.2.1       : address changes during the walks 0
  2.4.7 focus : visible 22 · subtle 0 · NONE 0 · offscreen 0   (2.4.13 AAA area+3:1 met by 22 of 22)
  2.4.11 hide : forward entirely hidden 0, partly 1 (under 50% visible 0); reverse entirely hidden 0, partly 1; causes: button.shwpd.eye-close:6
  2.4.1 skip  : first stop input ""; NOT a skip link
  activation  : 2 non-link controls, click vs Enter vs Space; failing 0; links sampled 7, followed on Enter 7
  scrollers   : none overflowing
  1.4.10      : scrollWidth 1280 vs viewport 1280 (overflow 0px); offenders 0; text clipped 0
  axe         : 89 rules, 0 violation nodes []; target-size [default run: absent; asked by name: passes] (pass nodes 31, violations 0, incomplete 0)

==== TOTALS {"playwright":"1.63.0","chrome":"154.0.8037.98","axe":"4.13.0","base":"https://agora-t0542-kbd.ddev.site","set":"anon","inject":"trap","commit":"58d84b2f2eff5636972a5661ae749cb86149fb6a","main":"main.agora-page__main","date":"2026-10-09T15:51:23.337Z"}
  pageViewports: 1
  skippedNotHtml: 0
  harnessFailures: 0
  stopsReached: 22
  predicted: 33
  unreached: 11
  notPredicted: 0
  droppedDuringWalk: 0
  indicatorVisible: 22
  indicatorSubtle: 0
  indicatorNone: 0
  indicatorOffscreen: 0
  aaaMeets: 22
  obscuredEntirelyForward: 0
  obscuredPartlyForward: 1
  obscuredUnderHalfForward: 0
  obscuredEntirelyReverse: 0
  obscuredPartlyReverse: 1
  traps: 1
  walksClean: 1
  reverseMirrorsForward: 0
  urlChangesDuringWalks: 0
  revisits: 0
  routesWithoutMain: 0
  skipPass: 0
  controlsTested: 2
  controlsFailing: 0
  linksTested: 7
  linksFollowed: 7
  registerFlows: 0
  scrollRegions: 0
  scrollRegionsOperable: 0
  scrollRegionsKeyboardScrolls: 0
  scrollRegionsRelyOnBrowser: 0
  axeViolationNodes: 0
  targetSizeViolationNodes: 0
  targetSizePassNodes: 31
  targetSizeIncompleteNodes: 0
  reflowOverflowPages: 0
  findings: 3
   - sign-in @1280x800 2.1.1 unreached by Tab: a "Skip to main content" | a "Agora keyboard rig T-0542" | a "All publications" | a "Document library" | a "The institution" | input "" | button "Search" | div "injected trap" | a "Forgotten your password?" | a "Home" | input ""
   - sign-in @1280x800 2.1.2 TRAP-no-movement at "injected trap" (Escape frees it: false)
   - sign-in @1280x800 2.4.1 skip link: {"firstStop":"input \"\"","isSkip":false,"target":null}
results written: signin-trap.json
```

<!-- cspell:enable -->
