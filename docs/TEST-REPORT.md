# Scripted test report

Run date: 18 October 2026, Asia/Jakarta.

**Result: all 41 tests passed with 1609 assertions on both SQLite and MariaDB.** Runtime: PHP 8.4.26, PHPUnit 12.5.40. SQLite in-memory run: 4.218 seconds, 54 MB peak reported memory. MariaDB 11.4.8 run: 7.026 seconds, 54 MB. Cache/session/mail drivers used isolated testing configuration; no live email was sent.

## Coverage executed

| Suite | Tests | Coverage |
| --- | ---: | --- |
| AuthenticationTest | 9 | Login/logout, disabled user/school, existing session rejection, login throttling, forced password change, current password/confirmation, framework reset notification, invalid and one-use reset token |
| PortalSecurityTest | 14 | Guest and cross-school denial, private direct files, revocation/expiry, read-only vs download grants, drafts/future materials, scoped filtering, editor/partner restrictions, safe and hostile uploads, size/batch rollback, user activation and publication/grant workflow |
| PublicSiteTest | 7 | Public routes, verified/draft filtering, publication scheduling, escaped content, canonical/share metadata, private media denial, sitemap/robots restrictions, admin verification requirement |
| PublicExportTest | 4 | Public-only export with private sentinel data, referenced images only, local link crawl/pagination/base path, no login form on static output, configured HTTPS portal link, safe output-path and URL rejection |
| SecurityHardeningTest | 7 | Reset array/oversized token rejection, configured reset-email origin under hostile Host, normalized email uniqueness, escaped active PDF denial, compressed object-stream denial, ordinary PDF upload/read, unavailable controlled-video denial |

The counts above describe tests actually run, not a checklist of intended future checks.

## Execution command

Windows sandbox ACL checks report some readable files as is_readable=false, so the runner loaded Composer directly rather than using PHPUnit's bootstrap-file check. The local development vendor also already contained a temporary BinaryFileResponse readability workaround (is_file instead of isReadable) for file-response tests in this environment. The reviewing agent did not change vendor code. This test-only workaround must not be shipped; deployment validation must use the original Composer dependency implementation.

~~~powershell
& '..\runtime\php\php.exe' -r "require 'vendor/autoload.php'; exit((new PHPUnit\TextUI\Application)->run(['phpunit','--configuration','../phpunit-sandbox.xml','--colors=never']));"
~~~

The sandbox XML points at the application's tests and uses the same isolated SQLite, array session/mail and test cache configuration as phpunit.xml. The test output was saved outside the application in work/test-final.txt. A normal installation can use its standard phpunit.xml with Composer dependencies installed.

## MariaDB integration executed

An isolated portable **MariaDB 11.4.8** was initialized under the development workspace, listening only on `127.0.0.1:33078`, with grant tables enabled and a random root password. No Windows service was installed, no production database or .env was changed, and no grant-table bypass was used. The first subagent attempt could not connect because its sandbox denied loopback networking (Windows 10013); that owned server was stopped. The successful root-context run used a separate fresh datadir with the existing network authorization.

The test runner verified the exact loopback host, port, connection driver, and disposable database `ibnu_aqil_test` before executing `migrate:fresh --force` and the safe DatabaseSeeder. Both completed successfully. A temporary PHPUnit XML forced all DB settings to this MariaDB connection; the **entire same suite passed: 41 tests, 1609 assertions**, not only migration smoke tests. The Windows-only Foundation/Mime readability/writability helper and existing local BinaryFileResponse workaround were used; clean production dependencies remain separate and untouched.

The actual `--no-data` database export is included as `database/schema.mysql.sql`. It contains structure only, no INSERT/DROP statements, credentials, accounts, or uploaded data. Incidental AUTO_INCREMENT counters and MariaDB's client-only sandbox directive were removed for portability. Migrations remain the installation authority; do not import this reference schema and then run migrations on the same populated schema.

The owned MariaDB server shut down through `mariadb-admin shutdown`; the log confirmed clean InnoDB/server shutdown. Temporary client defaults and test XML containing credentials were removed. Local raw results remain outside the app at `work/mariadb-test-results.txt` and the credential-free summary at `work/mariadb-test-summary.json`. This disposable database server and its tools/data are excluded from source and Nimbus release packages.

## What this run does not prove

- MariaDB 11.4.8 was tested locally. Oracle MySQL, the actual Nimbus database version/permissions, cPanel PHP process, and production filesystem were not tested by this run.
- Actual cPanel/Nimbus configuration, HTTPS enforcement at the web server, real SMTP delivery and provider-specific private-video integration were not exercised.
- No browser automation result is included here; UI inspection is reported separately by the implementation owner.
- Laravel bypasses CSRF verification during framework tests. CSRF middleware and form tokens were inspected in source; a real HTTP check is separate.
- PDF filtering is conservative format validation, not antivirus or full sanitization. Tested hostile cases include disguised executable content, literal/escaped active features and object streams; ordinary supported PDFs still pass.


## Pemeriksaan peramban dan HTTP tambahan

Pada 18 Oktober 2026, peramban lokal membuka beranda, profil SMA, login, dashboard mitra, detail materi, dan dashboard admin. Login akun pengembangan serta logout berhasil melalui formulir nyata. PDF.js 6.4.299 menampilkan PDF contoh satu halaman di canvas tanpa error peramban; tombol zoom berubah dari 100% ke 125%, Pas layar mengembalikan 100%, dan tautan unduhan menghasilkan PDF di peramban. Navigasi antarhalaman PDF multipage belum diuji secara visual.

Menu seluler diuji dalam iframe lokal selebar 390 piksel: konten efektif 375 piksel (sisanya scrollbar), lebar gulir juga 375 piksel. Enter membuka menu dan Escape menutupnya. Ini pengujian responsif di desktop, bukan perangkat HP fisik. Harness QA tidak disertakan dalam rilis.

POST HTTP nyata ke /login tanpa token CSRF ditolak dengan status 419. Pengujian ini dilakukan di luar mode PHPUnit yang biasanya menonaktifkan CSRF.

Tangkapan layar disertakan dalam folder keluaran screenshots. Akun yang terlihat hanya data pengembangan dan tidak disertakan dalam database rilis. Pembaca PDF memakai PDF.js mandiri di hosting; tidak menghubungi CDN.
