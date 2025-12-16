# 📜 Changelog

Minden lényeges változás ebben a fájlban kerül dokumentálásra, a [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) és a [Semantic Versioning](https://semver.org/) elvei szerint.

## [1.2.3] – 2025-12-16
### Changed
- „Units sold” üzenet a termékoldalakon (`$units_sold_message`), többes/singuláris szám támogatással
- „Free shipping” üzenet a termékoldalakon (`$free_shipping_limit_message`), az aktuális vásárló szállítási országát figyelembe véve
- Automatikus fallback geolokáció, ha a vásárló országa nincs megadva
- Csak engedélyezett szállítási módok figyelembe vétele

---

## [1.2.2] – 2025-12-16
### Added
- `post_faq` támogatás a single template-ben
- Bootstrap alapú FAQ accordion megjelenítés
- Többféle accordion viselkedés támogatása (`standard`, `collapsed`, `always_open`)

---

## [1.2.1] – 2025-10-20
### Added
- PHPDoc stílusú kommentek hozzáadva a `wc_szamlazz_xml` filterhez

### Changed
- A Számlázz.hu XML generálás módosítása: a `rendelesSzam` mező alapértelmezetten üres értéket kap

---

## [1.2.0] – 2025-10-10
### Added
- Új **AJAX rendszer** bevezetése (`ajax/php/`, `ajax/js/`)
- **Flexible Content sections** ACF integráció
- **Hero section** komponens (`_section-hero.scss`)

### Changed
- SCSS struktúra refaktorálva, moduláris felépítés (`components/`, `cards/`, `sections/`)
- Template struktúra egységesítve (`template-parts/`)
- Theme constants optimalizálása (`define()` értékek)

### Fixed
- Contact form AJAX hibakezelés

### Removed
- Régi inline script hivatkozások (`header.php`, `footer.php`)

---

## [1.1.0] – 2025-09-15
### Added
- WooCommerce integráció
- `enqueue_contact_form_ajax_scripts()` funkció
- localize script adatok

### Changed
- CSS és JS verziókezelés `filemtime()` alapján

---

## [1.0.0] – 2025-08-01
### Added
- Alap WordPress sablonstruktúra létrehozása
- `theme_scripts()`
- SCSS és Bootstrap integráció
- ACF alapbeállítások és Flexible Content támogatás
