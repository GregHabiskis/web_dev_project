<?php
$pageTitle = 'About | SalesPro';
$pageDescription = 'Learn more about SalesPro, our mission, values, and leadership team.';
$activeNav = 'about';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';
?>

  <main id="main">
    <section class="page-head">
      <div class="wrap">
        <p class="crumbs"><a href="index.php">Home</a> / About</p>
        <h1>About SalesPro</h1>
        <p>We help people find technology that fits the way they live and work.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap split">
        <div>
          <h2 style="font-size:30px;letter-spacing:-.8px;margin-bottom:14px">Our story</h2>
          <p style="color:var(--muted);margin-bottom:14px">
            SalesPro started with a simple idea: buying a computer should be clear and stress-free. We carry trusted laptops, desktops, printers and accessories, and explain the differences in plain language.
          </p>
          <p style="color:var(--muted)">
            Today we serve customers across the country, from students and families to growing businesses, with fair prices, fast delivery and support that picks up the phone.
          </p>
        </div>
        <div class="panel">
          <h3 style="margin-bottom:10px">Our promise</h3>
          <p style="color:var(--muted);line-height:1.6;">
            Honest advice, quality products and help whenever you need it. We test our devices thoroughly and stand by everything we sell.
          </p>
        </div>
      </div>
    </section>

    <section class="section alt">
      <div class="wrap">
        <div class="section-heading">
          <h2>What we value</h2>
        </div>
        <div class="grid g3">
          <div class="feature">
            <h3>Clarity</h3>
            <p>No jargon. We explain what matters so you can choose with confidence.</p>
          </div>
          <div class="feature">
            <h3>Quality</h3>
            <p>Every product is tested and backed by a full warranty.</p>
          </div>
          <div class="feature">
            <h3>Care</h3>
            <p>We stay with you long after the sale with reliable technical support.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <div class="section-heading">
          <h2>Meet the team</h2>
        </div>
        <div class="grid g4 team">
          <div class="feature"><div class="avatar">PB</div><h3>Pritam Bairagi</h3><p>Founder &amp; CEO</p></div>
          <div class="feature"><div class="avatar">MH</div><h3>Muttakin Hassan</h3><p>Head of Sales</p></div>
          <div class="feature"><div class="avatar">AA</div><h3>Abhishak Alex</h3><p>Customer Support</p></div>
          <div class="feature"><div class="avatar">S</div><h3>Sudipto</h3><p>Operations Manager</p></div>
          <div class="feature"><div class="avatar">FS</div><h3>Fahim</h3><p>IT Support</p></div>
        </div>
      </div>
    </section>
  </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

