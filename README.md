<h1>🧩 BorSpirit x NagyDanielEV WordPress Theme</h1>
<p><strong>Verzió:</strong> v1.0<br>
<strong>Készítette:</strong> Nagy Dániel<br>
<strong>Dátum:</strong> 2025. október 10.</p>

<hr>

<section>
  <h2>🎯 Cél és Megindoklás</h2>
  <p>
    A BorSpirit x NagyDanielEV WordPress Theme célja, hogy <strong>egységes, moduláris és jól dokumentált WordPress sablon</strong> alapot biztosítson a projekt fejlesztői számára. Az egységes fejlesztési környezet elősegíti a <strong>hatékony csapatmunkát</strong>, a <strong>minőségbiztosítást</strong> és a <strong>könnyű karbantarthatóságot</strong>.
  </p>

  <h3>Előnyök</h3>
  <ul>
    <li>🧱 <strong>Egységes fejlesztési folyamat</strong> – azonos struktúra, konvenciók és szabványok minden fejlesztő számára.</li>
    <li>🔧 <strong>Könnyebb karbantartás</strong> – átlátható és konzisztens kódstruktúra.</li>
    <li>✍️ <strong>Olvasható, tiszta kód (Clean Code)</strong> – gyorsabb hibakeresés, jobb érthetőség.</li>
    <li>🎨 <strong>Konzisztens arculat</strong> – egységes megjelenés a cég webes projektjei között.</li>
  </ul>
</section>

<hr>

<section>
  <h2>🎯 Fejlesztési Sztenderdek és Irányelvek</h2>

  <h3>1️⃣ Bevezetés</h3>
  <p>
    Az egyedi WordPress sablon célja, hogy rugalmas, moduláris és bővíthető fejlesztési alapot nyújtson, amely a CPT-k (Custom Post Types), ACF mezők és Bootstrap komponensek köré épül. A struktúra célja, hogy minden elem — a sablonfájloktól a SCSS modulokig — egységes névkonvenciót, logikát és technológiai szintet kövessen.
  </p>
  <p><strong>A rendszer filozófiája:</strong> „Minden tartalom komponens, minden komponens újrahasznosítható.”</p>

  <h3>2️⃣ Kódstruktúra és Fájlrendszer</h3>
  <p>A sablon fájlrendszere logikusan szervezett, a felelősségek szétválasztásának elvét követi:</p>
  <ul>
    <li><code>inc/</code> – kódbázis logikai rétegei (pl. include_scripts.php, register_post_types.php, register_taxonomies.php, theme_scripts.php)</li>
    <li><code>ajax/</code> – PHP és JS alapú aszinkron műveletek</li>
    <li><code>template-parts/</code> – vizuális és logikai komponensek (cards, sections, forms, sidebars stb.)</li>
    <li><code>assets/</code> – minden frontend erőforrás: SCSS, JS, képek és buildelt fájlok</li>
    <li><code>acf-json/</code> – ACF mezők exportálása JSON formátumban, verziókövetéshez</li>
  </ul>
  <p>Cél: teljes átláthatóság és minimális duplikáció. Egy fejlesztőnek bármikor könnyen meg kell találnia, hogy egy funkció vagy megjelenítés melyik réteghez tartozik.</p>

  <h3>3️⃣ Névkonvenciók</h3>
  <ul>
    <li>Fájlnevek, SCSS: kebab-case (pl. <code>card-project.php</code>, <code>_section-hero.scss</code>)</li>
    <li>PHP függvények: snake_case, prefixszel (pl. <code>theme_enqueue_scripts()</code>)</li>
    <li>JS változók: camelCase</li>
    <li>CPT és Taxonomy slugs: kisbetű, kötőjellel (pl. <code>product</code>, <code>product-category</code>)</li>
  </ul>
</section>

<hr>

<section>
  <h2>💻 OOP + Clean Code</h2>
  <p>A WordPress sablon fejlesztése során az <strong>OOP (Objektumorientált programozás)</strong> és a <strong>Clean Code</strong> elvek alkalmazása kiemelten fontos a moduláris, karbantartható és skálázható kód érdekében.</p>

  <h3>OOP alapelvek</h3>
  <ul>
    <li><strong>Osztályok (Classes):</strong> valós entitások reprezentálása, például egyedi post type-ok, taxonómiák, vagy asset-kezelők.</li>
    <li><strong>Objektumok (Objects):</strong> az osztály példányai, konkrét entitások.</li>
    <li><strong>Metódusok (Methods):</strong> osztályhoz tartozó funkciók (pl. `register()` egy CPT regisztrálására).</li>
    <li><strong>Properties (Tulajdonságok):</strong> osztály adatai, amelyek beállíthatók és lekérhetők.</li>
  </ul>

  <h3>Clean Code alapelvek</h3>
  <ul>
    <li><strong>Olvashatóság:</strong> a kód nevei és struktúrája önmagáért beszéljenek.</li>
    <li><strong>Single Responsibility Principle:</strong> egy osztály vagy funkció csak egy feladatot lásson el.</li>
    <li><strong>DRY (Don't Repeat Yourself):</strong> duplikáció kerülése, minden logika egyszer szerepeljen.</li>
    <li><strong>Kód struktúra:</strong> logikus mappák, fájlok, prefixek és névkonvenciók használata.</li>
    <li><strong>Kommentek:</strong> minden függvényhez PHPDoc blokk, a kód nevéből is érthető legyen a működés.</li>
  </ul>
  <p>Az OOP + Clean Code alkalmazása biztosítja, hogy a sablon moduláris, könnyen karbantartható, tesztelhető és skálázható legyen, különösen nagyobb projektek vagy csapatmunka esetén.</p>
</section>

<hr>

<section>
  <h2>🧠 Technológiai Alapok</h2>
  <table>
    <thead>
      <tr><th>Technológia</th><th>Szerepe</th></tr>
    </thead>
    <tbody>
      <tr><td>WordPress</td><td>Tartalomkezelő rendszer (CMS)</td></tr>
      <tr><td>Bootstrap</td><td>Frontend keretrendszer (reszponzív dizájn és komponensek)</td></tr>
      <tr><td>​​Advanced Custom Fields (ACF)</td><td>Egyedi mezők kezelése</td></tr>
      <tr><td>Custom post type (CPT)</td><td>Egyedi tartalomtípusok létrehozása</td></tr>
      <tr><td>Custom taxonomy</td><td>Egyedi kategóriatípusok létrehozása</td></tr>
      <tr><td>SASS / SCSS</td><td>Strukturált és változóalapú stílusírás</td></tr>
      <tr><td>Webpack</td><td>Asset buildelés és optimalizálás</td></tr>
      <tr><td>OOP + Clean Code</td><td>Olvasható, moduláris és fenntartható PHP struktúra</td></tr>
      <tr><td>Git</td><td>Verziókezelés és csapatmunka támogatása</td></tr>
    </tbody>
  </table>
</section>

<hr>

<section>
  <h2>🧭 Kódstílus és Verziókezelés</h2>
  <ul>
    <li>PHP: PSR-12, Composer autoload</li>
    <li>SCSS: BEM konvenció, moduláris</li>
    <li>JS: ES6+, jQuery kerülése, ha lehetséges</li>
    <li>HTML: szemantikus, akadálymentes (A11Y)</li>
    <li>Branch-ek: <code>feature/</code>, <code>fix/</code>, <code>release/</code></li>
    <li>Commit prefixek: <code>add:</code>, <code>fix:</code>, <code>refactor:</code></li>
    <li>Dokumentáció: <code>CHANGELOG.md</code></li>
    <li>Code review minden merge előtt</li>
  </ul>
</section>

<hr>

<section>
  <h1>⚙️ Telepítés</h1>
  <ul>
    <li>WordPress fájlok másolása</li>
    <li>Felesleges pluginek és sablonok törlése</li>
    <li>Adatbázis létrehozása</li>
    <li>A <code>wp-config.php</code> fájl beállítása</li>
    <li>Local szerver elindítása</li>
    <li>WordPress telepítése</li>
    <li>Sablon letöltése Git segítségével a themes mappába: <code>git clone</code></li>
    <li>Sablon gyökérkönyvtárában: <code>composer install</code> és <code>npm install</code></li>
    <li>Fejlesztői környezet indítása: <code>npm run dev</code> vagy <code>npm run prod</code></li>
    <li>Pluginek bekapcsolása</li>
    <li>ACF sync</li>
    <li>Nem használt section, css, js fájlok és funkciók törlése</li>
  </ul>
  <b>Fontos: Composer szükséges az npm parancsokhoz!</b>
</section>

<hr>

<section>
  <h2>🧩 Egyedi WordPress sablon a következőkre alapozva</h2>
  <p>Fejlesztésünk célja egy egyedi WordPress sablon létrehozása, amely kiemelkedő teljesítményt és testreszabhatóságot kínál. A sablon alapját a következő technológiák adják:</p>

  <h3>🔹 ACF (Advanced Custom Fields)</h3>
  <ul>
    <li>Testreszabható admin mezők</li>
    <li>Felhasználóbarát tartalomkezelés</li>
    <li>Gyorsabb adminisztráció</li>
  </ul>

  <h3>🔹 Bootstrap</h3>
  <ul>
    <li>Reszponzív grid rendszer</li>
    <li>Egységes komponensek</li>
    <li>Könnyen testreszabható változók</li>
  </ul>

  <h3>📦 Custom Post Types (CPT)</h3>
  <p>Minden post type a <code>register_post_types.php</code> fájlban kerül létrehozásra.</p>
  <ul>
    <li>Egyedi tartalomtípusok (pl. hírek, projektek)</li>
    <li>Saját mezők, taxonómiák és sablonok rendelhetők hozzá</li>
    <li>Külön menüpont az admin felületen</li>
    <li>Könnyíti a tartalom szervezését és szűrését</li>
    <li>Sablonokhoz rendelhetők (<code>single-{post_type_neve}.php</code>)</li>
  </ul>

  <h3>🏷️ Custom Taxonomies</h3>
  <p>Minden taxonomy a <code>register_taxonomies.php</code> fájlban kerül létrehozásra.</p>
  <ul>
    <li>Egyedi taxonómiák a CPT-khez (pl. szolgáltatások, projekttípusok)</li>
    <li>Hierarchikus (kategória-szerű) vagy címke-szerű struktúra</li>
    <li>Admin felületen szűrés és csoportosítás</li>
    <li>Sablonokhoz rendelhetők (<code>taxonomy-{taxonomy_neve}.php</code>)</li>
  </ul>
</section>

<hr>

<section>
  <h2>🔧 Theme Constants (define)</h2>
  <p>A <code>constants.php</code> határozza meg a sablon alapkonstansait:</p>
  <ul>
    <li>Konstansok globális, változtathatatlan értékek tárolására a theme-ben</li>
    <li>Segít egységesen hivatkozni útvonalakra, URL-ekre, oldal-azonosítókra és beállításokra</li>
    <li>Példák: <code>TEMPLATE_PATH</code>, <code>ASSETS_URI</code>, <code>HOME_PAGE_ID</code>, <code>ASSETS_VERSION</code></li>
    <li>Megkönnyíti a fejlesztést és csökkenti a hibalehetőségeket</li>
  </ul>
</section>

<hr>

<section>
  <h2>🖥️ Theme CSS & JS betöltés</h2>
  <ul>
    <li>Theme-specifikus CSS és JS betöltése (<code>styles.css</code> és <code>scripts.js</code>)</li>
    <li>Dinamikus adatok átadása JavaScript-nek <code>wp_localize_script</code>-tel:
      <ul>
        <li><code>ajaxurl</code> – AJAX hívásokhoz</li>
        <li><code>resturl</code> – REST API eléréshez</li>
        <li><code>themeurl</code>, <code>siteurl</code> – theme/site útvonalak</li>
        <li>Fordítások (<code>read_more</code>, <code>read_less</code>)</li>
      </ul>
    </li>
  </ul>
  <p>Ez a funkció biztosítja, hogy a theme minden oldalon **egységesen, modulárisan és optimalizáltan** töltse be a stílusokat és szkripteket.</p>
</section>

<hr>

<section>
  <h2>⚡ AJAX Funkciók</h2>
  <p>Minden AJAX funkció a <code>register_ajax.php</code> fájlban létrehozva.</p>
  <ul>
    <li>Aszinkron adatküldés és -fogadás a frontenden (pl. űrlapok, szűrők)</li>
    <li>PHP backend fájlok a <code>/ajax/php/</code> mappában</li>
    <li>JS fájlok a <code>/ajax/js/</code> mappában, betöltés a <code>wp_enqueue_script</code>-tel</li>
    <li>Dinamikus adatok átadása a JS-nek <code>wp_localize_script</code> segítségével (pl. <code>ajax_url</code>, felhasználói ID, üzenetek)</li>
    <li>Hiba- és státuszkezelés logolással (<code>error_log</code>) és frontenden</li>
    <li>Segít a felhasználói élmény javításában: oldalletöltés nélkül frissül az adat</li>
  </ul>
</section>

<hr>

<section>
  <h2>🧱 Fájlrendszer és Fejlesztési Szabványok</h2>
  <h3>📁 Functions mappa</h3>
  <p>Minden egyedi funkció külön fájlban a <code>inc</code> mappában, egyértelmű felelősségi körrel:</p>
  <pre>
    - inc/
      - include_scripts.php
      - register_ajax.php
      - register_post_types.php
      - register_taxonomies.php
      - theme_scripts.php
  </pre>

  <h3>📜 Fájlnevezési konvenciók</h3>
  <ul>
    <li>kisbetűk + alsóvonás</li>
    <li>rövid, leíró fájlnevek</li>
    <li>egy funkció = egy felelősség</li>
  </ul>
</section>

<hr>

<section>
  <h2>📄 Oldalsablonok (Single / Archive)</h2>
  <pre>
    <code>
      single-news.php
      archive-news.php
    </code>
  </pre>
  <p>Regisztrálás filterekkel:</p>
  <pre>
    <code>
      add_filter('single_template', 'news_cpt_single_template');
      add_filter('archive_template', 'news_cpt_archive_template');
    </code>
  </pre>
</section>

<hr>

<section>
    <h2>📂 Template-parts mappa struktúrája</h2>
    <pre>
      <code>
        template-parts/
        ├── blocks/                 # Általános blokkok (pl. CTA, icon-box, grid elemek)
        ├── cards/                  # Kártya típusú elemek (pl. hírek, termékek, projektek)
        ├── dialogs/                # Pop-up ablakok, modálisok
        ├── forms/                  # Űrlapok (pl. kapcsolat, hírlevél)
        ├── global/                 # Globális részek (header, footer, navigation)
        ├── queries/                # Loop-ok és egyedi lekérdezések (pl. WP_Query sablonok)
        ├── sections/               # Oldalonkénti szekciók (ACF Flexible Content elemek)
        │   ├── section-hero.php         # Hero szekció (kiemelt tartalom, háttérkép, cím, CTA)
        │   ├── section-gallery.php      # Képgaléria szekció
        │   ├── section-testimonials.php # Vélemények / referenciák szekció
        │   └── section-contact.php      # Kapcsolat szekció
        ├── sidebars/               # Oldalsáv komponensek
        └── flexible-elements.php   # ACF „Flexible Content” logika betöltése
      </code>
    </pre>
    <ul>
      <li><strong>Újrahasználhatóság:</strong> Bármelyik oldalhoz vagy post típushoz újra felhasználható részek.</li>
      <li><strong>Modularitás:</strong> Külön mappákba szervezett funkciók és blokkok.</li>
      <li><strong>ACF integráció:</strong> A <code>flexible-elements.php</code> és a <code>sections/</code> mappa az ACF “Flexible Content” mezőihez kapcsolódik.</li>
      <li><strong>Rugalmas oldalépítés:</strong> Az admin felületen az oldalak szekciói (pl. hero, galéria, kontakt) szabadon hozzáadhatók és átrendezhetők.</li>
      <li><strong>Egységes naming és struktúra:</strong> Könnyen megtalálható, logikusan felépített fájlrendszer minden modulhoz.</li>
    </ul>
</section>
<hr>

<section>
  <h2>🎨 SCSS és BEM Szabályok</h2>
  <p>A stílusok moduláris felépítése a fenntarthatóság és újrahasznosíthatóság elvét követi.</p>
  <p>SCSS szerkezet:</p>
  <pre>
    <code>
      scss/
      ├── components/                 # Komponensek
      │   ├── blocks/                 # Általános blokkok
      │   │   └── _block-base.scss        # Alap blokkstílusok (spacing, layout)
      │   ├── cards/                  # Kártyák
      │   │   ├── _card-base.scss         # Kártyák általános alapstílusai
      │   │   └── _card-post.scss         # Egyedi kártyastílus bejegyzésekhez (Post CPT)
      │   ├── global/                 # Globális stílusok (header, footer)
      │   ├── headlines/              # Címsorok, tipográfia
      │   ├── navigations/            # Menü- és navigációs elemek
      │   ├── pages/                  # Oldalspecifikus stílusok
      │   ├── sections/               # Oldalszekciók
      │   │   ├── _section-base.scss      # Általános szekcióstílusok (padding, háttér, grid)
      │   │   └── _section-hero.scss      # Hero szekció (kiemelt tartalom a kezdőlapon)
      │   ├── sidebars/               # Oldalsávok
      │   └── sliders/                # Csúszkák, galériák
      │
      │   ├── _blocks.scss
      │   ├── _cards.scss
      │   ├── _global.scss
      │   ├── _headlines.scss
      │   ├── _navigation.scss
      │   ├── _pages.scss
      │   ├── _sections.scss
      │   ├── _sidebars.scss
      │   └── _sliders.scss
      ├── vendors/                    # Külső könyvtárak (pl. Bootstrap, Swiper)
      ├── _variables.scss             # Színek, méretek, tipográfia, mixinek
      └── styles.scss                 # Főfájl, amely importálja az összes SCSS modult
    </code>
  </pre>
  <ul>
    <li><strong>_block-base.scss:</strong> minden blokk alapstílusát tartalmazza (pl. margók, padding, reszponzív elrendezés)</li>
    <li><strong>Modularitás:</strong> külön fájl minden komponensnek az átláthatóság érdekében</li>
    <li><strong>Egységes naming:</strong> BEM konvenció és logikus struktúra</li>
    <li><strong>Vendors mappa:</strong> külső könyvtárak (Bootstrap, Swiper) elkülönítve</li>
  </ul>

  <h3>BEM elnevezési konvenció</h3>
  <ul>
    <li><code>.block</code> – fő komponens</li>
    <li><code>.block__element</code> – belső elem</li>
    <li><code>.block--modifier</code> – módosító / állapot</li>
    <li>Állapotok: <code>.is-active</code>, <code>.is-open</code></li>
    <li>JS: <code>.js-nav-toggle</code></li>
  </ul>

  <h3>📘 BEM Módszer Magyarázata</h3>
  <p>
    A <strong>BEM</strong> (Block, Element, Modifier) egy moduláris, logikusan felépített névkonvenció a frontend fejlesztéshez. Lényege, hogy a HTML és CSS kódot olyan egységekre bontjuk, amelyek:
  </p>
  <ul>
    <li><strong>Block:</strong> önálló, újrahasználható komponens (pl. <code>menu</code>, <code>button</code>, <code>card</code>)</li>
    <li><strong>Element:</strong> a blokk része, nem létezhet önállóan (pl. <code>card__title</code>, <code>card__description</code>)</li>
    <li><strong>Modifier:</strong> a blokk vagy elem állapotát vagy variánsát jelzi (pl. <code>button--primary</code>, <code>button--disabled</code>)</li>
  </ul>
  <p>
    A BEM célja a <strong>modularitás, átláthatóság és karbantarthatóság</strong> biztosítása. A jól felépített BEM struktúrával a kód könnyen érthető, skálázható, és minimalizálhatók a CSS-ütközések.
  </p>
</section>

<hr>

<section>
  <h2>🧰 JS és SVG struktúra</h2>
  <p>JS fájlok az <code>assets/src/js</code> mappában:</p>
  <pre>
    <code>
      import './valami.js';
      import $ from 'jquery';
    </code>
  </pre>

  <p>SVG ikonok az <code>assets/src/svg</code> mappában, használatuk:</p>
  <pre><code>&lt;svg class="icon icon-valami"&gt;
  &lt;use xlink:href="#icon-valami"&gt;&lt;/use&gt;
&lt;/svg&gt;</code></pre>

  <p>Képek helye: <code>assets/src/images</code> → Webpack után: <code>assets/dist/images</code></p>
</section>

<hr>

<section>
  <h2>Verziózás folyamata</h2>
  <ol>
    <li>Kód módosítása → tesztelés</li>
    <li>Changelog bejegyzés → verziószám növelése</li>
    <li>Git commit</li>
  </ol>
</section>

<section>
  <h3>📘 CHANGELOG.md – Verziókövetési Irányelvek</h3>
  <p>A CHANGELOG.md fájl célja, hogy áttekinthetően dokumentálja a fejlesztés történetét — minden módosítást, újítást, hibajavítást és visszavonást. Ez segít a fejlesztőknek, tesztelőknek és projektvezetőknek abban, hogy kövessék a változásokat, megértsék a verziók közti különbségeket, és biztosítsák a konzisztens kiadáskezelést. Minden lényeges változás ebben a fájlban kerül dokumentálásra, a [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) és a [Semantic Versioning](https://semver.org/) elvei szerint. A legfrissebb verzió mindig legfelül szerepel.</p>
  <ul>
    <li>Added – új funkciók</li>
    <li>Changed – módosítások</li>
    <li>Fixed – hibajavítások</li>
    <li>Removed – elavult elemek</li>
  </ul>

  <pre>
## [v1.0.1] – 2025-10-15
### Added
- Új "Projektek" CPT
- Hero szekció bővítve videó támogatással

### Fixed
- Mobilmenü z-index hiba javítva

### Changed
- SCSS struktúra módosítva: különválasztott _mixins.scss

### Removed
- Régi "Kapcsolat" shortcode, már nem használatos
- Elavult CSS mixinek törölve
  </pre>
</section>

<section>
  <h3>🧾 Git Használati Irányelvek</h3>
  <ul>
    <li><strong>Branch naming:</strong> <code>feature/</code>, <code>fix/</code>, <code>release/</code></li>
    <li><strong>Commit üzenetek:</strong> rövidek, leírók (pl. <code>fix: header logo alignment</code>)</li>
    <li><strong>Main branch:</strong> mindig stabil, élesíthető állapotban</li>
    <li><strong>Pull request review:</strong> minden módosítást ellenőrzés után merge-ölj</li>
  </ul>
</section>

<hr>

<section>
  <h2>✅ Összegzés</h2>
  <p>A <strong>BorSpirit x RevindDigital WordPress Theme</strong> egy modern, egységes és skálázható fejlesztői alap, amely:</p>
  <ul>
    <li>gyorsítja a fejlesztést,</li>
    <li>csökkenti a hibákat,</li>
    <li>támogatja a közös kódminőségi elveket,</li>
    <li>biztosítja a konzisztens megjelenést minden projekten belül.</li>
  </ul>
</section>

<footer>
  <p><strong>Készült:</strong><br>Nagy Dániel EV<br>📅 2025 — folyamatos fejlesztés alatt<br>📚 Verzió: v1.0</p>
</footer>
