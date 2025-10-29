# Force WWW Redirect for Shopware 6

Redirect requests between www and non-www based on configuration. Choose one behavior:

- Redirect non-www → www
- Redirect www → non-www

Both behaviors preserve the full request URI (path and query) and use a 301 redirect.

## Why use this plugin?
While redirects can be configured via `.htaccess`, Shopware overwrites `.htaccess` during system updates, making `.htaccess` modifications non-persistent. This plugin provides a stable, upgrade-safe solution that survives Shopware updates.

## Features
- Toggle each behavior independently via plugin configuration
- Preserves scheme (http/https), path and query
- Lightweight Symfony event subscribers

## Installation
1. Copy the plugin into `custom/plugins/NetzkollektivForceWwwRedirect`
2. Install and activate in Admin or via CLI:
```bash
bin/console plugin:refresh
bin/console plugin:install --activate NetzkollektivForceWwwRedirect
bin/console cache:clear
```

## Configuration
Administration → Settings → System → Plugins → Force WWW Redirect

Settings:
- Enable redirect to www (`enableForceWww`): Redirect non-www to www
- Enable redirect to non-www (`enableForceNonWww`): Redirect www to non-www

Notes:
- Only enable one mode at a time to avoid conflicting redirects.
- Disable during local development if undesired.

## How it works
Two Symfony subscribers listen on `KernelEvents::REQUEST` and issue a permanent redirect when their respective mode is enabled.

## Compatibility
- Shopware 6 (requires `shopware/core`)

## License
MIT

---

<a href="https://netzkollektiv.com" title="NETZKOLLEKTIV - E-Commerce & Shopware Agentur"><img src="https://netzkollektiv.com/wp-content/themes/netzkollektiv-2019/assets/images/logos/netzkollektiv-dark.svg" width="200px" alt="NETZKOLLEKTIV logo"></a>