<?php
include_once __DIR__ . '/../controller.php';

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method !== 'POST') {
    http_response_code(405);
    header('Content-Type: text/html; charset=UTF-8');
    ?>
    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nem engedélyezett művelet</title>
        <style>
            body {
                margin: 0;
                min-height: 100vh;
                display: grid;
                place-items: center;
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #08111f, #111d35);
                color: #eef4ff;
            }

            .card {
                width: min(560px, calc(100% - 2rem));
                padding: 2rem;
                border-radius: 24px;
                background: rgba(13, 22, 41, 0.92);
                border: 1px solid rgba(255, 255, 255, 0.12);
                box-shadow: 0 24px 80px rgba(0, 0, 0, 0.4);
                text-align: center;
            }

            h1 {
                margin: 0 0 0.75rem;
            }

            p {
                margin: 0;
                color: #b8c4dd;
                line-height: 1.7;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <h1>Ez az oldal csak beküldésre használható</h1>
            <p>Kérlek, a jelentkezési űrlapon keresztül nyisd meg újra.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$teljesNev = $_POST['teljesNev'] ?? '';
$szakNev = $_POST['szakNev'] ?? '';
$iskola = $_POST['iskola'] ?? '';
$kollegium = $_POST['kollegium'] ?? 0;
$lastYear = $_POST['last_year'] ?? 0;

if ($kollegium === null || $kollegium === '' || $kollegium === 'NULL') {
    $kollegium = 0;
}

if ($kollegium === 'on') {
    $kollegium = 1;
}

if ($lastYear === null || $lastYear === '' || $lastYear === 'NULL') {
    $lastYear = 0;
}

if ($lastYear === 'on') {
    $lastYear = 1;
}

$controller = new Controller();
$result = $controller->addJelentkezo($teljesNev, $szakNev, $iskola, $kollegium, $lastYear);

header('Content-Type: text/html; charset=UTF-8');

if ($result) {
    ?>
    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Köszönjük a jelentkezésed!</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            :root {
                --bg: #071d2f;
                --bg-2: #123d6b;
                --card: rgba(13, 22, 41, 0.82);
                --border: rgba(255, 255, 255, 0.12);
                --text: #eef4ff;
                --muted: #bdd3ee;
                --accent: #8ed8ff;
                --accent-2: #5c8fe3;
                --accent-3: #f6c9d8;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 1rem;
                font-family: 'Manrope', sans-serif;
                color: var(--text);
                background:
                    radial-gradient(circle at top left, rgba(142, 216, 255, 0.34), transparent 30%),
                    radial-gradient(circle at 80% 20%, rgba(246, 201, 216, 0.22), transparent 25%),
                    linear-gradient(135deg, var(--bg), var(--bg-2));
            }

            .card {
                width: min(780px, 100%);
                border-radius: 30px;
                border: 1px solid var(--border);
                background: var(--card);
                box-shadow: 0 30px 90px rgba(0, 0, 0, 0.45);
                backdrop-filter: blur(18px);
                overflow: hidden;
                position: relative;
            }

            .card::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    linear-gradient(145deg, rgba(255, 255, 255, 0.08), transparent 35%),
                    radial-gradient(circle at top right, rgba(110, 231, 255, 0.12), transparent 30%);
                pointer-events: none;
            }

            .content {
                position: relative;
                z-index: 1;
                padding: 2rem;
            }

            .brand {
                display: inline-flex;
                align-items: center;
                gap: 0.8rem;
                margin-bottom: 1.5rem;
                padding: 0.65rem 0.9rem;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.06);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }

            .brand img {
                width: 44px;
                height: 44px;
                object-fit: contain;
            }

            .eyebrow {
                font-size: 0.88rem;
                color: var(--muted);
                letter-spacing: 0.02em;
            }

            h1 {
                margin: 0;
                font-size: clamp(2.2rem, 4vw, 4rem);
                line-height: 1.02;
                letter-spacing: -0.05em;
            }

            .highlight {
                background: linear-gradient(135deg, #ffffff 0%, #d8efff 38%, #79aef6 100%);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
            }

            .subtitle {
                margin: 1rem 0 1.5rem;
                max-width: 56rem;
                color: var(--muted);
                font-size: 1.04rem;
                line-height: 1.75;
            }

            .summary {
                display: grid;
                gap: 0.9rem;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                margin: 1.5rem 0 2rem;
            }

            .item {
                padding: 1rem 1.05rem;
                border-radius: 20px;
                background: rgba(255, 255, 255, 0.06);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }

            .item .label {
                display: block;
                font-size: 0.82rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #c6d1ea;
                margin-bottom: 0.45rem;
            }

            .item .value {
                font-weight: 700;
                font-size: 1rem;
                color: #f6f9ff;
            }

            .actions {
                display: flex;
                gap: 0.85rem;
                flex-wrap: wrap;
            }

            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 3.2rem;
                padding: 0.85rem 1.2rem;
                border-radius: 16px;
                text-decoration: none;
                font-weight: 800;
                transition: transform 180ms ease, box-shadow 180ms ease;
            }

            .button:hover {
                transform: translateY(-2px);
            }

            .button-primary {
                color: #08111f;
                background: linear-gradient(135deg, #9fe0ff 0%, #5d8fe5 52%, #f6c9d8 120%);
                box-shadow: 0 18px 34px rgba(93, 143, 229, 0.18);
            }

            .button-secondary {
                color: var(--text);
                background: rgba(255, 255, 255, 0.06);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            @media (max-width: 700px) {
                .content {
                    padding: 1.35rem;
                }

                .summary {
                    grid-template-columns: 1fr;
                }

                .actions {
                    flex-direction: column;
                }

                .button {
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>
        <main class="card">
            <div class="content">
                <div class="brand">
                    <img src="../../../client/assets/images/logo.png" alt="NTSZKI logó">
                    <div class="eyebrow">NTSZKI Jelentkezés</div>
                </div>

                <h1><span class="highlight">Köszönjük a jelentkezésed!</span></h1>
                <p class="subtitle">
                    A jelentkezésed sikeresen megérkezett. Hamarosan feldolgozzuk az adatokat, és a megadott iránynak megfelelően további lépésekről is értesítünk.
                </p>

                <div class="summary">
                    <div class="item">
                        <span class="label">Jelentkező neve</span>
                        <span class="value"><?php echo e($teljesNev); ?></span>
                    </div>
                    <div class="item">
                        <span class="label">Iskola</span>
                        <span class="value"><?php echo e($iskola); ?></span>
                    </div>
                    <div class="item">
                        <span class="label">Szak</span>
                        <span class="value"><?php echo e($szakNev); ?></span>
                    </div>
                    <div class="item">
                        <span class="label">Kollégium</span>
                        <span class="value"><?php echo ((string) $kollegium === '1') ? 'Igen' : 'Nem'; ?></span>
                    </div>
                </div>

                <div class="actions">
                    <a class="button button-primary" href="../../../client/index.php">Vissza a jelentkezéshez</a>
                </div>
            </div>
        </main>
    </body>
    </html>
    <?php
} else {
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hiba a mentés közben</title>
        <style>
            body {
                margin: 0;
                min-height: 100vh;
                display: grid;
                place-items: center;
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #08111f, #111d35);
                color: #eef4ff;
            }

            .card {
                width: min(560px, calc(100% - 2rem));
                padding: 2rem;
                border-radius: 24px;
                background: rgba(13, 22, 41, 0.92);
                border: 1px solid rgba(255, 255, 255, 0.12);
                box-shadow: 0 24px 80px rgba(0, 0, 0, 0.4);
                text-align: center;
            }

            h1 {
                margin: 0 0 0.75rem;
            }

            p {
                margin: 0;
                color: #b8c4dd;
                line-height: 1.7;
            }

            a {
                display: inline-block;
                margin-top: 1.25rem;
                color: #8ed8ff;
                text-decoration: none;
                font-weight: 700;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <h1>Nem sikerült elmenteni a jelentkezést</h1>
            <p>Próbáld meg újra, vagy térj vissza az űrlaphoz és ellenőrizd az adatokat.</p>
            <a href="../../../client/index.php">Vissza a jelentkezéshez</a>
        </div>
    </body>
    </html>
    <?php
}
?>