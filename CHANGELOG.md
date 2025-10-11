# 📜 Changelog

Minden lényeges változás ebben a fájlban kerül dokumentálásra, a [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) és a [Semantic Versioning](https://semver.org/) elvei szerint.

---

## [1.2.0] – 2025-10-10
### Added
- Új **AJAX rendszer** bevezetése (`ajax/php/`, `ajax/js/`)
- **Flexible Content sections** ACF integráció
- **Új CPT:** `borok` és `rendezvenyek`
- **Hero section** komponens (`_section-hero.scss`)

### Changed
- SCSS struktúra refaktorálva, moduláris felépítés (`components/`, `cards/`, `sections/`)
- Template struktúra egységesítve (`template-parts/`)
- Theme constants optimalizálása (`define()` értékek)

### Fixed
- Contact form AJAX hibakezelés
- Hiányzó asset verziók és cache busting javítása

### Removed
- Régi inline script hivatkozások (`header.php`, `footer.php`)

---

## [1.1.0] – 2025-09-15
### Added
- WooCommerce integráció
- `enqueue_contact_form_ajax_scripts()` funkció
- REST API endpointok és localize script adatok

### Changed
- CSS és JS verziókezelés `filemtime()` alapján

---

## [1.0.0] – 2025-08-01
### Added
- Alap WordPress sablonstruktúra létrehozása
- `theme_scripts()` és `register_post_types.php`
- SCSS és Bootstrap integráció
- ACF alapbeállítások és Flexible Content támogatás
