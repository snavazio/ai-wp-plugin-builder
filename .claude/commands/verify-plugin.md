---
description: Run a plugin folder through the full verification harness (all 8 gates).
---

Run the verification harness against the plugin directory the user names (default to `build/<slug>` if they
just built one, otherwise ask which folder).

Steps:
1. Run: `npm run verify -- <plugin-dir>`
2. If Docker is unavailable, note it and offer `--no-docker` for the static gates only.
3. Report the per-gate table. If any gate FAILED, name the failing gate(s) and quote the specific errors so
   the coder can fix them. Do not declare success unless every gate is PASS.

The source of truth is the objective gate output, never a subjective read of the code. A plugin is only done
when it activates with zero PHP fatals AND passes WordPress Plugin Check with no security errors.
