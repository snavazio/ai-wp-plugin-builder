---
description: Package a verified plugin folder into a distributable .zip using wp dist-archive.
---

Package the named plugin folder (default `build/<slug>`) into `dist/<slug>.<version>.zip`.

Preconditions — refuse to package unless BOTH hold:
- `npm run verify -- <plugin-dir>` passes every gate (activation + Plugin Check security clean).
- The folder contains a `.distignore` so dev files (tests, phpunit config, composer, node_modules) are excluded.

Steps:
1. Confirm verification is green (re-run `verify` if unsure).
2. Read the header `Version` from the main plugin file.
3. Package: `wp dist-archive <plugin-dir> dist/ --plugin-dirname=<slug>` (respects `.distignore`).
4. Confirm the `.zip` has exactly one root folder named `<slug>` and report the output path + size.

Never package a plugin that has not passed the harness.
