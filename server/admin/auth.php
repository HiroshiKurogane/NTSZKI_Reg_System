<?php
session_start();

require_once __DIR__ . '/../api/controller.php';

const ADMIN_USERNAME = 'admin';
const ADMIN_PASSWORD = 'ntszki-admin';

function e($value)
{
	return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

if (isset($_GET['logout'])) {
	$_SESSION = [];

	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}

	session_destroy();
	header('Location: auth.php');
	exit;
}

$errorMessage = '';
$noticeMessage = '';

if (isset($_SESSION['admin_notice'])) {
	$noticeMessage = (string) $_SESSION['admin_notice'];
	unset($_SESSION['admin_notice']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['delete_id']) && !empty($_SESSION['admin_logged_in'])) {
		$deleteId = (int) $_POST['delete_id'];
		if ($deleteId > 0) {
			$controller = new Controller();
			$result = $controller->deleteJelentkezo($deleteId);
			$_SESSION['admin_notice'] = $result ? 'A jelentkező sikeresen törölve lett.' : 'Nem sikerült törölni a jelentkezőt.';
		}

		header('Location: auth.php' . (isset($_GET['view']) ? '?view=' . urlencode((string) $_GET['view']) : ''));
		exit;
	}

	$username = $_POST['username'] ?? '';
	$password = $_POST['password'] ?? '';

	if (hash_equals(ADMIN_USERNAME, $username) && hash_equals(ADMIN_PASSWORD, $password)) {
		$_SESSION['admin_logged_in'] = true;
		header('Location: auth.php');
		exit;
	}

	$errorMessage = 'Hibás felhasználónév vagy jelszó.';
}

$isLoggedIn = !empty($_SESSION['admin_logged_in']);
$controller = null;
$applications = [];
$applicationCount = 0;
$boardingCount = 0;
$graduatingCount = 0;
$viewMode = $_GET['view'] ?? 'all';
$selectedSzak = $_GET['szak'] ?? 'Szoftverfejlesztő- és tesztelő';
$showGraduatingOnly = ($_GET['vegzos'] ?? '0') === '1';

$availableSzakok = [
	'Szoftverfejlesztő- és tesztelő',
	'Pénzügyi szmávitelező',
];

if ($isLoggedIn) {
	$controller = new Controller();

	if ($viewMode === 'kolis') {
		$applications = $controller->getAllKolisJelentkezo($showGraduatingOnly ? 'true' : 'false');
	} elseif ($viewMode === 'szak') {
		if (!in_array($selectedSzak, $availableSzakok, true)) {
			$selectedSzak = $availableSzakok[0];
		}

		$applications = $controller->getAllSzakJelentkezo($selectedSzak, $showGraduatingOnly ? 'true' : 'false');
	} else {
		$applications = $controller->getAllJelentkezo($showGraduatingOnly ? 'true' : 'false');
	}

	if (!is_array($applications)) {
		$applications = [];
	}

	$applicationCount = count($applications);

	foreach ($applications as $application) {
		if ((string) ($application['kollegium'] ?? '') === '1') {
			$boardingCount++;
		}

		if ((string) ($application['last_year'] ?? '') === '1') {
			$graduatingCount++;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>NTSZKI Admin</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<style>
		:root {
			--bg: #07111e;
			--bg-2: #101d35;
			--card: rgba(13, 22, 41, 0.84);
			--border: rgba(255, 255, 255, 0.12);
			--text: #eef4ff;
			--muted: #b7c4de;
			--accent: #6ee7ff;
			--accent-2: #8b5cf6;
			--accent-3: #f59e0b;
			--danger: #ff8aa1;
			--success: #63e6be;
			--shadow: 0 30px 90px rgba(0, 0, 0, 0.46);
		}

		* {
			box-sizing: border-box;
		}

		body {
			margin: 0;
			min-height: 100vh;
			padding: 1rem;
			font-family: 'Manrope', sans-serif;
			color: var(--text);
			background:
				radial-gradient(circle at top left, rgba(139, 92, 246, 0.35), transparent 30%),
				radial-gradient(circle at 80% 20%, rgba(110, 231, 255, 0.2), transparent 25%),
				radial-gradient(circle at bottom right, rgba(245, 158, 11, 0.16), transparent 24%),
				linear-gradient(135deg, var(--bg), var(--bg-2));
		}

		.shell {
			width: min(1280px, 100%);
			margin: 0 auto;
			min-height: calc(100vh - 2rem);
			display: grid;
			place-items: center;
		}

		.card {
			width: min(1120px, 100%);
			border-radius: 30px;
			border: 1px solid var(--border);
			background: var(--card);
			backdrop-filter: blur(18px);
			box-shadow: var(--shadow);
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

		.topbar {
			display: flex;
			justify-content: space-between;
			align-items: center;
			gap: 1rem;
			margin-bottom: 2rem;
		}

		.brand {
			display: inline-flex;
			align-items: center;
			gap: 0.9rem;
		}

		.brand img {
			width: 54px;
			height: 54px;
			object-fit: contain;
			border-radius: 16px;
			background: rgba(255, 255, 255, 0.06);
			border: 1px solid rgba(255, 255, 255, 0.08);
			padding: 8px;
		}

		.brand strong {
			display: block;
			font-size: 1rem;
		}

		.brand span {
			display: block;
			color: var(--muted);
			font-size: 0.9rem;
			margin-top: 0.15rem;
		}

		.logout {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			min-height: 2.9rem;
			padding: 0.8rem 1.15rem;
			border-radius: 14px;
			text-decoration: none;
			color: var(--text);
			background: rgba(255, 255, 255, 0.06);
			border: 1px solid rgba(255, 255, 255, 0.1);
			font-weight: 700;
		}

		.hero {
			display: grid;
			grid-template-columns: 1.05fr 0.95fr;
			gap: 1.25rem;
			align-items: stretch;
		}

		.hero-copy,
		.hero-panel {
			border-radius: 26px;
			border: 1px solid rgba(255, 255, 255, 0.08);
			background: rgba(255, 255, 255, 0.04);
		}

		.hero-copy {
			padding: 1.5rem;
		}

		.eyebrow {
			display: inline-flex;
			align-items: center;
			gap: 0.55rem;
			padding: 0.5rem 0.85rem;
			border-radius: 999px;
			color: var(--muted);
			background: rgba(255, 255, 255, 0.05);
			border: 1px solid rgba(255, 255, 255, 0.08);
			font-size: 0.88rem;
		}

		h1 {
			margin: 1rem 0 0.75rem;
			font-size: clamp(2rem, 4vw, 3.7rem);
			line-height: 1.02;
			letter-spacing: -0.05em;
		}

		.highlight {
			background: linear-gradient(135deg, #ffffff 0%, #b8eaff 40%, #9c7dff 100%);
			-webkit-background-clip: text;
			background-clip: text;
			color: transparent;
		}

		.lede {
			margin: 0;
			color: var(--muted);
			line-height: 1.75;
			font-size: 1.02rem;
		}

		.stats {
			display: grid;
			grid-template-columns: repeat(3, minmax(0, 1fr));
			gap: 0.9rem;
			margin-top: 1.25rem;
		}

		.stat {
			padding: 1rem;
			border-radius: 20px;
			background: rgba(255, 255, 255, 0.06);
			border: 1px solid rgba(255, 255, 255, 0.08);
		}

		.stat span {
			display: block;
			color: var(--muted);
			font-size: 0.82rem;
			text-transform: uppercase;
			letter-spacing: 0.08em;
			margin-bottom: 0.5rem;
		}

		.stat strong {
			font-size: 1.6rem;
			letter-spacing: -0.04em;
		}

		.login-card {
			padding: 1.6rem;
			display: grid;
			align-content: center;
			gap: 1rem;
		}

		.login-card h2 {
			margin: 0;
			font-size: 1.55rem;
		}

		.login-card p {
			margin: 0;
			color: var(--muted);
			line-height: 1.7;
		}

		.error {
			padding: 0.85rem 1rem;
			border-radius: 16px;
			background: rgba(255, 138, 161, 0.12);
			border: 1px solid rgba(255, 138, 161, 0.22);
			color: #ffd3dc;
		}

		.form {
			display: grid;
			gap: 0.9rem;
			margin-top: 0.5rem;
		}

		.form label {
			display: block;
			margin-bottom: 0.45rem;
			font-weight: 700;
			color: #edf3ff;
		}

		.form input {
			width: 100%;
			min-height: 3.4rem;
			border-radius: 16px;
			border: 1px solid rgba(255, 255, 255, 0.12);
			background: rgba(248, 251, 255, 0.96);
			color: #0b1220;
			padding: 0 1rem;
			font: inherit;
			outline: none;
		}

		.form input:focus {
			border-color: rgba(110, 231, 255, 0.6);
			box-shadow: 0 0 0 0.25rem rgba(110, 231, 255, 0.12);
		}

		.submit {
			min-height: 3.5rem;
			border: 0;
			border-radius: 16px;
			font: inherit;
			font-weight: 800;
			cursor: pointer;
			color: #08111f;
			background: linear-gradient(135deg, #6ee7ff 0%, #8b5cf6 55%, #f59e0b 120%);
			box-shadow: 0 18px 34px rgba(110, 231, 255, 0.16);
		}

		.panel {
			margin-top: 1.5rem;
			padding: 1.25rem;
			border-radius: 24px;
			background: rgba(255, 255, 255, 0.04);
			border: 1px solid rgba(255, 255, 255, 0.08);
		}

		.panel-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			gap: 1rem;
			margin-bottom: 1rem;
		}

		.panel-header h3 {
			margin: 0;
			font-size: 1.15rem;
		}

		.table-wrap {
			overflow-x: auto;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			min-width: 860px;
		}

		th,
		td {
			padding: 0.95rem 0.9rem;
			text-align: left;
			border-bottom: 1px solid rgba(255, 255, 255, 0.08);
			vertical-align: top;
		}

		th {
			color: #dce5fa;
			font-size: 0.84rem;
			text-transform: uppercase;
			letter-spacing: 0.08em;
		}

		td {
			color: #eef4ff;
		}

		.pill {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			padding: 0.35rem 0.75rem;
			border-radius: 999px;
			font-weight: 700;
			font-size: 0.82rem;
		}

		.pill-success {
			background: rgba(99, 230, 190, 0.14);
			color: #bff7e3;
		}

		.pill-warning {
			background: rgba(245, 158, 11, 0.16);
			color: #ffe1a9;
		}

		.pill-neutral {
			background: rgba(255, 255, 255, 0.08);
			color: #d6def0;
		}

		.muted {
			color: var(--muted);
		}

		@media (max-width: 980px) {
			.hero {
				grid-template-columns: 1fr;
			}

			.stats {
				grid-template-columns: 1fr;
			}

			.topbar {
				flex-direction: column;
				align-items: flex-start;
			}
		}

		@media (max-width: 700px) {
			.content {
				padding: 1.25rem;
			}

			.login-card,
			.hero-copy,
			.panel {
				padding: 1.1rem;
			}
		}
	</style>
</head>
<body>
	<main class="shell">
		<section class="card">
			<div class="content">
				<div class="topbar">
					<div class="brand">
						<img src="../../client/assets/images/logo.png" alt="NTSZKI logó">
						<div>
							<strong>NTSZKI Admin</strong>
							<span>Jelentkezések kezelése</span>
						</div>
					</div>

					<?php if ($isLoggedIn): ?>
						<a class="logout" href="?logout=1">Kijelentkezés</a>
					<?php endif; ?>
				</div>

				<?php if (!$isLoggedIn): ?>
					<div class="hero">
						<div class="hero-copy">
							<div class="eyebrow">Biztonságos admin belépés</div>
							<h1>Nyisd meg a <span class="highlight">jelentkezések</span> vezérlőpultját.</h1>
							<p class="lede">Itt tudod megtekinteni a beküldött jelentkezéseket, és gyors áttekintést kapsz a kollégiumi, illetve a végzős jelentkezőkről is.</p>

							<div class="stats">
								<div class="stat">
									<span>Összes jelentkező</span>
									<strong><?php echo (int) $applicationCount; ?></strong>
								</div>
								<div class="stat">
									<span>Kollégiumi igény</span>
									<strong><?php echo (int) $boardingCount; ?></strong>
								</div>
								<div class="stat">
									<span>Végzős</span>
									<strong><?php echo (int) $graduatingCount; ?></strong>
								</div>
							</div>
						</div>

						<div class="login-card">
							<h2>Bejelentkezés</h2>
							<p>Add meg az admin belépési adatokat a vezérlőpult eléréséhez.</p>

							<?php if ($errorMessage !== ''): ?>
								<div class="error"><?php echo e($errorMessage); ?></div>
							<?php endif; ?>

							<form class="form" method="post" action="auth.php">
								<div>
									<label for="username">Felhasználónév</label>
									<input type="text" id="username" name="username" autocomplete="username" required>
								</div>

								<div>
									<label for="password">Jelszó</label>
									<input type="password" id="password" name="password" autocomplete="current-password" required>
								</div>

								<button class="submit" type="submit">Belépés</button>
							</form>
						</div>
					</div>
				<?php else: ?>
					<div class="hero">
						<div class="hero-copy">
							<div class="eyebrow">Admin vezérlőpult</div>
							<h1><span class="highlight"><?php echo (int) $applicationCount; ?></span> jelentkező, teljes admin hozzáféréssel</h1>
							<p class="lede">Itt érheted el az összes olvasó és törlő funkciót. Az admin nem tud új jelentkezést létrehozni, de szűrheti, áttekintheti és törölheti a beérkezett adatokat.</p>

							<div class="stats">
								<div class="stat">
									<span>Összes jelentkező</span>
									<strong><?php echo (int) $applicationCount; ?></strong>
								</div>
								<div class="stat">
									<span>Kollégiumi igény</span>
									<strong><?php echo (int) $boardingCount; ?></strong>
								</div>
								<div class="stat">
									<span>Végzős</span>
									<strong><?php echo (int) $graduatingCount; ?></strong>
								</div>
							</div>
						</div>

						<div class="login-card">
							<h2>Gyors nézetek</h2>
							<p>Válts az összes, a kollégiumos vagy a szak szerinti nézetek között, és szűrd a végzős jelentkezőket is.</p>
						</div>
					</div>

					<div class="panel">
						<div class="panel-header">
							<h3>Szűrők</h3>
							<div class="muted">Az aktuális nézet: <?php echo e($viewMode); ?></div>
						</div>

						<div class="actions" style="margin-bottom: 1rem;">
							<a class="logout" href="?view=all">Összes</a>
							<a class="logout" href="?view=kolis<?php echo $showGraduatingOnly ? '&amp;vegzos=1' : ''; ?>">Kollégiumos</a>
							<a class="logout" href="?view=szak&amp;szak=<?php echo urlencode($selectedSzak); ?><?php echo $showGraduatingOnly ? '&amp;vegzos=1' : ''; ?>">Szak szerint</a>
							<a class="logout" href="?view=<?php echo urlencode($viewMode); ?>&amp;vegzos=<?php echo $showGraduatingOnly ? '0' : '1'; ?><?php echo $viewMode === 'szak' ? '&amp;szak=' . urlencode($selectedSzak) : ''; ?>">
								<?php echo $showGraduatingOnly ? 'Minden évfolyam' : 'Csak végzős'; ?>
							</a>
						</div>

						<form class="form" method="get" action="auth.php">
							<input type="hidden" name="view" value="szak">
							<div>
								<label for="szak">Szakirány</label>
								<select id="szak" name="szak" class="form-select" style="width: 100%; min-height: 3.4rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.12); background: rgba(248,251,255,0.96); color: #0b1220; padding: 0 1rem; font: inherit;">
									<?php foreach ($availableSzakok as $szak): ?>
										<option value="<?php echo e($szak); ?>" <?php echo $selectedSzak === $szak ? 'selected' : ''; ?>><?php echo e($szak); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<button class="submit" type="submit">Szűrés</button>
						</form>
					</div>

					<?php if ($noticeMessage !== ''): ?>
						<div class="panel" style="margin-top: 1rem; background: rgba(99, 230, 190, 0.08); border-color: rgba(99, 230, 190, 0.18);">
							<strong><?php echo e($noticeMessage); ?></strong>
						</div>
					<?php endif; ?>

					<div class="panel">
						<div class="panel-header">
							<h3>Jelentkezők listája</h3>
							<div class="muted"><?php echo (int) $applicationCount; ?> rekord</div>
						</div>

						<div class="table-wrap">
							<table>
								<thead>
									<tr>
										<th>Név</th>
										<th>Iskola</th>
										<th>Szak</th>
										<th>Kollégium</th>
										<th>Végzős</th>
										<th>Művelet</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($applications)): ?>
										<?php foreach ($applications as $application): ?>
											<tr>
												<td><?php echo e($application['teljesNev'] ?? ''); ?></td>
												<td><?php echo e($application['iskola'] ?? ''); ?></td>
												<td><?php echo e($application['szakNev'] ?? ''); ?></td>
												<td>
													<?php if ((string) ($application['kollegium'] ?? '') === '1'): ?>
														<span class="pill pill-success">Igen</span>
													<?php else: ?>
														<span class="pill pill-neutral">Nem</span>
													<?php endif; ?>
												</td>
												<td>
													<?php if ((string) ($application['last_year'] ?? '') === '1'): ?>
														<span class="pill pill-warning">Igen</span>
													<?php else: ?>
														<span class="pill pill-neutral">Nem</span>
													<?php endif; ?>
												</td>
												<td>
													<form method="post" action="auth.php<?php echo $viewMode !== 'all' ? '?view=' . urlencode($viewMode) . ($viewMode === 'szak' ? '&amp;szak=' . urlencode($selectedSzak) : '') . ($showGraduatingOnly ? '&amp;vegzos=1' : '') : ($showGraduatingOnly ? '?vegzos=1' : ''); ?>" onsubmit="return confirm('Biztosan törlöd ezt a jelentkezőt?');">
														<input type="hidden" name="delete_id" value="<?php echo (int) ($application['id'] ?? 0); ?>">
														<button type="submit" class="logout" style="min-height: 2.5rem; color: #ffd3dc; border-color: rgba(255,138,161,0.22); background: rgba(255,138,161,0.1);">Törlés</button>
													</form>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php else: ?>
										<tr>
											<td colspan="6" class="muted">Még nincs beküldött jelentkezés ebben a nézetben.</td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</section>
	</main>
</body>
</html>
