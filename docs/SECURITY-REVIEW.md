# Security review

Reviewed 18 October 2026 against the application source in this release. This was a bounded source review of app, routes, bootstrap, configuration, migrations and relevant views/tests; no external service or production system was tested.

## Result

No unresolved cross-school file access, partner-to-staff escalation, or public CMS draft leak was found in the reviewed routes. Three concrete issues were identified and corrected by the implementation owner during the review:

1. **Malformed password reset token reached the password broker** — AuthController::reset accepted a token array because the input rule only required its presence. With an active reset record, the password verifier raised a TypeError. The token is now required to be a string of at most 255 characters. A regression test verifies array and oversized values are rejected without changing the password.
2. **Password recovery link depended on the request Host** — the framework default link builder could use an incoming Host if that request reached the application. The provider now builds reset links from the configured APP_URL; production URL generation and an exact trusted-host rule use the same configuration. The notification regression verifies that a hostile request origin cannot alter the action URL, including when APP_URL contains a base path.
3. **Escaped active PDF names bypassed the raw scanner** — a valid PDF using /Open#41ction, /Java#53cript and /J#53 was accepted by the previous literal-name scan. The scanner now decodes PDF name escapes and rejects active features, encrypted PDFs and compressed object streams. Regression tests reject encoded JavaScript and encoded ObjStm samples, and confirm an ordinary PDF remains uploadable/readable.

Admin-created email values are also trimmed/lowercased before uniqueness validation; a case-variant duplicate regression passes. Password-recovery email transport failures are caught and retain the generic guest response.

## Access and data checks

- Portal lists/details/files use MaterialAccess, including school identity, current active grant, optional expiry, published activity, published material and reached publication date.
- File reads repeat the permission check on the server; downloads additionally require both school and asset permission. Asset filenames are generated UUID paths on a private disk outside public. The filesystem configuration creates no public storage symlink.
- Deactivated accounts or schools lose access through ActiveAccount. New accounts have generated unknown passwords and use the framework password-reset flow; no fixed initial password is introduced.
- Role enforcement occurs in the route group and the resource controller. Editors can manage activities/materials, and cannot manage accounts, roles, schools, grants, public pages, public articles, settings or logs. Partners cannot reach administrative mutations.
- Public pages and articles require published and verified status; articles also require a reached publication date. Public image delivery additionally requires a reference from a published article. Output bodies escape text before adding line breaks.
- Session IDs are regenerated at login/password change; logout invalidates the session. Password reset/change removes other database sessions and rotates remember tokens. The supplied production configuration selects database sessions, encrypted session contents, secure and HTTP-only cookies, and SameSite=Lax.
- Mutation forms include CSRF tokens and routes retain the framework web middleware. PHPUnit skips CSRF in its testing environment, so the automated suite alone does not demonstrate a real HTTP 419 response.
- Unsupported controlled video publication is rejected while its provider integration is disabled. A controlled item has no player embed or exposed provider ID in the rendered detail test.
- The public export test inserts private sentinel accounts, schools, activities, materials and files, and confirms they do not occur in the output. Only published verified public content and referenced public images are exported.

## Limits and deployment dependencies

The PDF check is deliberately conservative validation, not antivirus, a full PDF sanitizer, or a guarantee against every reader vulnerability. It rejects forms, encryption and object streams as well as active features; legitimate PDFs using those features must be re-exported to an ordinary supported PDF before upload. Only authorized staff may upload. Private viewing necessarily transfers the file bytes to an authorized browser and does not prevent copying.

No external penetration test, real SMTP delivery, third-party video-provider authorization or actual Nimbus deployment was performed in this review. MySQL/MariaDB testing was pending when this document was written; see TEST-REPORT.md and any later deployment report. Configure APP_URL with the exact HTTPS production origin before enabling public access. The production database session driver is part of the tested account-revocation design.

## Validation

The complete scripted suite passed: **41 tests, 1609 assertions** on PHP 8.4.26 with SQLite in memory. The detailed coverage, exact command and local file-readability workaround are in TEST-REPORT.md. The workaround belongs only to this sandbox and must not ship in production dependencies. No source application file was changed by the reviewing agent; its changes are the regression test and these review documents.

