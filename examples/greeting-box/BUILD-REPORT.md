# Local build report: Greeting Box

- **Engine:** Ollama `qwen3:30b` @ http://127.0.0.1:11434  ·  **RAG:** `nomic-embed-text`
- **Slug:** `greeting-box`  ·  **Version:** 1.0.0
- **Outcome:** ✅ passed all gates — .zip produced
- **Iterations:** 0  ·  **Duration:** 1.7 min  ·  **Local cost:** $0 (2 calls, 5554+5486 tokens)

## Verification: `greeting-box`

**Overall: ✅ PASS**  (total 40.5s)

| Gate | Status | Time | Errors | Warnings |
| --- | --- | --- | --- | --- |
| PHP lint (php -l) | ✅ pass | 57ms | 0 | 0 |
| Static structure & header checks | ✅ pass | 2ms | 0 | 0 |
| PHP_CodeSniffer (WordPress) | ✅ pass | 274ms | 0 | 0 |
| PHPStan (level 5, WP stubs) | ✅ pass | 1.4s | 0 | 0 |
| WordPress Plugin Check | ✅ pass | 7.2s | 0 | 2 |
| Install + activate (wp-env) | ✅ pass | 4.3s | 0 | 0 |
| PHPUnit smoke test | ✅ pass | 1.2s | 0 | 0 |
| Version consistency | ✅ pass | 2ms | 0 | 0 |

### PHP lint (php -l)
- _4/4 files lint-clean._

### Static structure & header checks
- _Main file: greeting-box.php_

### PHP_CodeSniffer (WordPress)
- _phpcs: 0 errors, 0 warnings._

### PHPStan (level 5, WP stubs)
- _phpstan: 0 file errors, 0 total._

### WordPress Plugin Check
**Warnings:**
- [plugin_header_nonexistent_domain_path] greeting-box.php:0 (WARNING) The "Domain Path" header in the plugin file must point to an existing folder. Found: "languages"
- [PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound] greeting-box.php:31 (WARNING) load_plugin_textdomain() has been discouraged since WordPress version 4.6. When your plugin is hosted on WordPress.org, you no longer need to manually include this function call for translations under your plugin slug. WordPress will automatically load the translations for you as needed.
- _Security: 0 errors. Other categories: 2 findings._

### Install + activate (wp-env)
- _No PHP fatals in debug.log._

### PHPUnit smoke test
- _OK (2 tests, 3 assertions)_

### Version consistency
- _Header Version: 1.0.0_
- _readme Stable tag matches: 1.0.0_
- _Checked 1 *_VERSION constant(s)._

