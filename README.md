<h1>🧩 BorSpirit x NagyDanielEV WordPress Theme</h1>
<p><strong>Verzió:</strong> v1.0<br>
<strong>Készítette:</strong> Nagy Dániel<br>
<strong>Dátum:</strong> 2025. október 10.</p>

<hr>

<section>
  <h2>🎯 Cél és Megindoklás</h2>
  <p>
    A BorSpirit x NagyDanielEV WordPress Theme célja, hogy <strong>egységes, moduláris és jól dokumentált WordPress sablon</strong> alapot biztosítson a cég fejlesztői számára. Az egységes fejlesztési környezet elősegíti a <strong>hatékony csapatmunkát</strong>, a <strong>minőségbiztosítást</strong> és a <strong>könnyű karbantarthatóságot</strong>.
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
  <h1>⚙️ Telepítés</h1>
  <ul>
    <li>WordPress fájlok másolása</li>
    <li>Felesleges pluginek és sablonok törlése</li>
    <li>Adatbázis létrehozása</li>
    <li>A <code>wp-config.php</code> fájl beállítása</li>
    <li>Local szerver elindítása</li>
    <li>WordPress telepítése</li>
    <li>Sablon letöltése Git segítségével a themes mappába</li>
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
  <h2>🧠 Technológiai Alapok</h2>
  <table>
    <thead>
      <tr><th>Technológia</th><th>Szerepe</th></tr>
    </thead>
    <tbody>
      <tr><td>WordPress</td><td>Tartalomkezelő rendszer (CMS)</td></tr>
      <tr><td>Bootstrap</td><td>Frontend keretrendszer (reszponzív dizájn és komponensek)</td></tr>
      <tr><td>​​Advanced Custom Fields (ACF)</td><td>Egyedi mezők kezelése</td></tr>
      <tr><td>Custom post types (CPT)</td><td>Egyedi tartalomtípusok létrehozása</td></tr>
      <tr><td>SASS / SCSS</td><td>Strukturált és változóalapú stílusírás</td></tr>
      <tr><td>Webpack</td><td>Asset buildelés és optimalizálás</td></tr>
      <tr><td>OOP + Clean Code</td><td>Olvasható, moduláris és fenntartható PHP struktúra</td></tr>
      <tr><td>Git</td><td>Verziókezelés és csapatmunka támogatása</td></tr>
    </tbody>
  </table>
</section>

<hr>

<section>
  <h2>🧱 Fájlrendszer és Fejlesztési Szabványok</h2>
  <h3>📁 Functions mappa</h3>
  <p>Minden egyedi funkció külön fájlban a <code>functions</code> mappában:</p>
  <pre>
- functions/
  - header_customization.php
  - navigation_functions.php
  - post_customization.php
  - widget_functions.php
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
  <h2>🎨 SCSS és BEM Szabályok</h2>
  <p>SCSS szerkezet:</p>
  <pre>
    <code>
scss/
├── components/
│   ├── blocks/
│   ├── cards/
│   ├── global/
│   ├── headlines/
│   ├── navigations/
│   ├── pages/
│   ├── sections/
│   ├── sidebars/
│   └── sliders/
│       ├── _blocks.scss
│       ├── _cards.scss
│       ├── _global.scss
│       ├── _headlines.scss
│       ├── _navigation.scss
│       ├── _pages.scss
│       ├── _sections.scss
│       ├── _sidebars.scss
│       └── _sliders.scss
├── vendors/
│   └── (pl. Bootstrap, Swiper, stb.)
├── _variables.scss
└── styles.scss
    </code>
  </pre>

  <h3>BEM elnevezési konvenció</h3>
  <ul>
    <li><code>.block</code> – fő komponens</li>
    <li><code>.block__element</code> – belső elem</li>
    <li><code>.block--modifier</code> – módosító / állapot</li>
    <li>Állapotok: <code>.is-active</code>, <code>.is-open</code></li>
    <li>JS: <code>.js-nav-toggle</code></li>
  </ul>

  <h3>📂 SCSS struktúra</h3>
  <ul>
    <li><strong>components/</strong> – komponensek
      <ul>
        <li>blocks/ – blokkok</li>
        <li>cards/ – kártyák</li>
        <li>global/ – globális stílusok</li>
        <li>headlines/ – címsorok</li>
        <li>navigations/ – navigációk</li>
        <li>pages/ – oldalak</li>
        <li>sections/ – szekciók</li>
        <li>sidebars/ – oldalsávok</li>
        <li>sliders/ – csúszkák</li>
        <li>_blocks.scss, _cards.scss, _global.scss, _headlines.scss, _navigation.scss, _pages.scss, _sections.scss, _sidebars.scss, _sliders.scss – komponens fájlok</li>
      </ul>
    </li>
    <li><strong>vendors/</strong> – külső könyvtárak (pl. Bootstrap, Swiper)</li>
    <li><strong>_variables.scss</strong> – színek, méretek, tipográfia</li>
    <li><strong>styles.scss</strong> – összefoglaló fájl, amely importálja az összes SCSS fájlt</li>
  </ul>
</section>

<hr>

<section>
  <h2>🧩 ACF, CPT és Bootstrap integráció</h2>
  <h3>🔹 ACF (Advanced Custom Fields)</h3>
  <ul>
    <li>Testreszabható admin mezők</li>
    <li>Felhasználóbarát tartalomkezelés</li>
    <li>Gyorsabb adminisztráció</li>
  </ul>

  <h3>🔹 CPT (Custom Post Type)</h3>
  <ul>
    <li>Egyedi tartalomtípusok (pl. hírek, projektek)</li>
    <li>Taxonómiák és mezők hozzárendelése</li>
  </ul>

  <h3>🔹 Bootstrap</h3>
  <ul>
    <li>Reszponzív grid rendszer</li>
    <li>Egységes komponensek</li>
    <li>Könnyen testreszabható változók</li>
  </ul>
</section>

<hr>

<section>
  <h2>🧠 OOP és Clean Code</h2>
  <ul>
    <li>Külön osztályok (pl. CPT, Widget, Shortcode)</li>
    <li>Namespace és autoload a Composer segítségével</li>
    <li>Egyszerű, olvasható, karbantartható kód</li>
  </ul>
</section>

<hr>

<section>
  <h2>🧰 JS és SVG struktúra</h2>
  <p>JS fájlok az <code>assets/src/js</code> mappában:</p>
  <pre><code>import './valami.js';
import $ from 'jquery';</code></pre>

  <p>SVG ikonok az <code>assets/src/svg</code> mappában, használatuk:</p>
  <pre><code>&lt;svg class="icon icon-valami"&gt;
  &lt;use xlink:href="#icon-valami"&gt;&lt;/use&gt;
&lt;/svg&gt;</code></pre>

  <p>Képek helye: <code>assets/src/images</code> → Webpack után: <code>assets/dist/images</code></p>
</section>

<hr>

<section>
  <h2>📄 Oldalsablonok (Single / Archive)</h2>
  <pre><code>single-news.php  
archive-news.php</code></pre>
  <p>Regisztrálás filterekkel:</p>
  <pre><code>add_filter('single_template', 'news_cpt_single_template');
add_filter('archive_template', 'news_cpt_archive_template');</code></pre>
</section>

<hr>

<section>
  <h2>🧾 Git Használati Irányelvek</h2>
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
  <p>
    A <strong>BorSpirit / RevindDigital WordPress Theme</strong> egy modern, egységes és skálázható fejlesztői alap, amely:
  </p>
  <ul>
    <li>gyorsítja a fejlesztést,</li>
    <li>csökkenti a hibákat,</li>
    <li>támogatja a közös kódminőségi elveket,</li>
    <li>biztosítja a konzisztens megjelenést minden projekten belül.</li>
  </ul>
</section>

<footer>
  <p><strong>Készült:</strong> Revind Digital fejlesztői csapat<br>
  📅 2023 — folyamatos fejlesztés alatt<br>
  📚 Verzió: v0.1</p>
</footer>
