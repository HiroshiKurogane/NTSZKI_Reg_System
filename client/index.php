<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NTSZKI Jelentkezés</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
  <main class="page-shell">
      <section class="form-panel">
        <div class="brand-row">
          <img src="./assets/images/logo.png" alt="NTSZKI logó" class="brand-mark">
          <div>
            <div class="eyebrow"><span class="eyebrow-dot"></span> NTSZKI Jelentkezés</div>
            <h1 class="page-title">Egy letisztult <span>jelentkezési felület</span>.</h1>
          </div>
        </div>

        <p class="page-copy">Töltsd ki az alábbi adatokat, és küldd el a jelentkezésedet néhány egyszerű lépésben.</p>

        <form class="application-form" action="../server/api/func/addJelentkezo.php" method="post">
          <div>
            <label for="teljesNev" class="form-label">Teljes név</label>
            <input type="text" class="form-control" id="teljesNev" name="teljesNev" required placeholder="A teljes nevedet írd ide.">
          </div>

          <div>
            <label for="iskola" class="form-label">Iskola</label>
            <input type="text" class="form-control" id="iskola" name="iskola" required placeholder="Az iskola neve ahonnan jöttél.">
          </div>

          <div class="input-group">
            <label class="input-group-text" for="last_year">Osztály</label>
            <select class="form-select" id="last_year" name="last_year" required>
              <option value="" selected disabled>Választás...</option>
              <option value="1">8.-os vagyok</option>
              <option value="0">7.-es vagyok</option>
            </select>
          </div>

          <div class="input-group">
            <label class="input-group-text" for="kollegium">Kollégium</label>
            <select class="form-select" id="kollegium" name="kollegium" required>
              <option value="" selected disabled>Választás...</option>
              <option value="1">Szeretnék kollégista lenni</option>
              <option value="0">Nem szeretnék kollégista lenni</option>
            </select>
          </div>

          <div class="input-group">
            <label class="input-group-text" for="szakNev">Szakma</label>
            <select class="form-select" id="szakNev" name="szakNev" required>
              <option value="" selected disabled>Választás...</option>
              <option value="Szoftverfejlesztő- és tesztelő">Szoftverfejlesztő- és tesztelő</option>
              <option value="Pénzügyi szmávitelező">Pénzügyi szmávitelező</option>
            </select>
          </div>

          <div class="submit-row">
            <p class="form-note">Ellenőrizd az adatokat, majd küldd el a jelentkezést.</p>
            <button type="submit" class="btn-submit">Jelentkezés elküldése</button>
          </div>
        </form>
      </section>
  </main>

</body>
</html>