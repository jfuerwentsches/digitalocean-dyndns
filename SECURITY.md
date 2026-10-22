# Security notice: PHP engine RCE (21 October 2026)

Advisory: https://www.php.net/archive/2026.php (21 October 2026)

- CVE-2026-109618 / GHSA-w8hw-w9fx-j79w
- CVE-2026-109619 / GHSA-6wmf-4rv5-p3rp

Unauthenticated remote code execution in the PHP engine / request lifecycle. It affects web SAPIs including PHP-FPM and mod_php. Exploitation can happen before any application code runs.

| PHP branch | Fixed version |
|------------|---------------|
| 8.2        | 8.2.35        |
| 8.3        | 8.3.36        |
| 8.4        | 8.4.27        |
| 8.5        | 8.5.12        |

## Status of this repository

This repository has no managed runtime image. This document is **not** a deployed fix and does not patch any PHP runtime. No current execution of this script has been verified.

This project is a CLI script. The advisory describes a web request-lifecycle attack surface; it does not establish that CLI use is exploitable. A maintained, patched PHP runtime is still recommended.

## Operator actions

- Run the script only with a PHP build on a fixed version or a vendor build with documented backported fixes (ask the vendor for evidence rather than comparing version numbers blindly).
- Make sure any web SAPI on the same host is updated and restarted/reloaded where required. CLI `php -v` does not prove a patched web SAPI.
- Do not create public `phpinfo()` files to check the version.
