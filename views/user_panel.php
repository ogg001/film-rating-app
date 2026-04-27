<?php
require dirname(__DIR__) . '/config.php';

if (!isset($_GET['username'])) {
    header('Location: /film-rating-app');
    exit;
}

$username = $_GET['username'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Użytkownik nie istnieje.";
    exit;
}

$isOwnProfile = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id'];
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil użytkownika - <?php echo htmlspecialchars($user['username']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/film-rating-app/css/style.css">
</head>
<body>

<header class="bg-primary text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="/film-rating-app/" class="d-flex align-items-center text-white">
                <img src="/film-rating-app/images/logo.svg" alt="Film Rating App Logo" class="logo-image mr-2" style="height: 50px;">
                <span class="app-title h4 mb-0">Film-Rating-App</span>
            </a>
        </div>
        
        <nav>
            <?php if (isset($_SESSION['username'])): ?>
                <div class="d-flex align-items-center">
                    <a href="/film-rating-app/user/<?php echo htmlspecialchars($_SESSION['username']); ?>" class="text-white d-flex align-items-center text-decoration-none">
                        <img src="/film-rating-app/images/user_icon2.png" alt="Ikona użytkownika" class="mr-2" style="width: 20px; height: 20px;">
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </a>
                    <a href="/film-rating-app/logout" class="btn btn-light btn-sm ml-3">Wyloguj się</a>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="/film-rating-app/admin_panel" class="btn btn-outline-light btn-sm ml-2">Panel Administratora</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <a href="/film-rating-app/login" class="btn btn-light btn-sm">
                    <img src="/film-rating-app/images/user_icon.png" alt="Ikona użytkownika" class="mr-1" style="width: 20px; height: 20px;">
                    Zaloguj się
                </a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
			<section class="mb-5">
				<h2 class="text-primary">Podstawowe informacje</h2>
				<div class="card p-3">
					<p><strong>Nazwa użytkownika:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
					<p><strong>Data założenia konta:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
					<p>
						<strong>Status konta:</strong>
						<?php if ($user['is_blocked'] == 0): ?>
							<span class="badge badge-success">Aktywny</span>
						<?php else: ?>
							<span class="badge badge-danger">Zablokowany</span>
						<?php endif; ?>
					</p>

					<?php if ($isOwnProfile): ?>
						<form id="changePasswordForm" method="POST" action="/film-rating-app/actions/change_password.php">
							<div class="form-group">
								<label for="currentPassword">Aktualne hasło:</label>
								<input type="password" id="currentPassword" name="current_password" class="form-control" required>
							</div>
							<div class="form-group">
								<label for="newPassword">Nowe hasło:</label>
								<input type="password" id="newPassword" name="new_password" class="form-control" required>
							</div>
							<button type="submit" class="btn btn-primary">Zmień hasło</button>
						</form>
					<?php endif; ?>
				</div>
			</section>

            <section class="mb-5">
                <h2 class="text-primary">Statystyki</h2>
                <div class="card p-3">
                    <?php
                    $userId = $user['id'];

                    $stmt = $pdo->prepare("SELECT COUNT(*) AS review_count FROM reviews WHERE user_id = :user_id");
                    $stmt->execute(['user_id' => $userId]);
                    $reviewCount = $stmt->fetchColumn();

                    $stmt = $pdo->prepare("SELECT SUM(likes) AS total_likes, SUM(dislikes) AS total_dislikes FROM reviews WHERE user_id = :user_id");
                    $stmt->execute(['user_id' => $userId]);
                    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

                    $stmt = $pdo->prepare("SELECT AVG(rating) AS avg_rating FROM reviews WHERE user_id = :user_id");
                    $stmt->execute(['user_id' => $userId]);
                    $avgRating = $stmt->fetchColumn();
                    ?>

                    <p><strong>Średnia przyznanych ocen:</strong> <?php echo number_format((float)$avgRating, 2); ?></p>
                    <p><strong>Liczba napisanych recenzji:</strong> <?php echo $reviewCount; ?></p>
                    <p><strong>Łączna liczba otrzymanych polubień:</strong> <?php echo (int)$stats['total_likes']; ?></p>
                    <p><strong>Łączna liczba otrzymanych dislajków:</strong> <?php echo (int)$stats['total_dislikes']; ?></p>
                </div>
            </section>

            <section>
                <h2 class="text-primary">Historia aktywności</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Aktywność</th>
                                <th>Film / Użytkownik</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->prepare(" 
                                (SELECT 
                                    'Dodano recenzję' AS action_type, 
                                    f.title AS film_title, 
                                    NULL AS target_username, 
                                    f.id AS film_id, 
                                    r.created_at AS action_date
                                 FROM reviews r 
                                 JOIN films f ON r.film_id = f.id 
                                 WHERE r.user_id = :user_id)
                                
                                UNION ALL
                                
                                (SELECT 
                                    'Edytowano recenzję' AS action_type, 
                                    f.title AS film_title, 
                                    NULL AS target_username, 
                                    f.id AS film_id, 
                                    r.last_edited_at AS action_date
                                 FROM reviews r 
                                 JOIN films f ON r.film_id = f.id 
                                 WHERE r.user_id = :user_id AND r.last_edited_at IS NOT NULL)
                                
                                UNION ALL
                                
                                (SELECT 
                                    'Usunięto recenzję' AS action_type, 
                                    f.title AS film_title, 
                                    NULL AS target_username, 
                                    f.id AS film_id, 
                                    ual.created_at AS action_date
                                 FROM user_activity_log ual 
                                 JOIN films f ON ual.film_id = f.id 
                                 WHERE ual.user_id = :user_id AND ual.action_type = 'delete_review')
                                
                                UNION ALL
                                
                                (SELECT 
                                    'Polubiono recenzję' AS action_type, 
                                    NULL AS film_title, 
                                    u.username AS target_username, 
                                    r.film_id AS film_id, 
                                    rv.created_at AS action_date
                                 FROM review_votes rv 
                                 JOIN reviews r ON rv.review_id = r.id 
                                 JOIN users u ON r.user_id = u.id 
                                 WHERE rv.user_id = :user_id AND rv.vote = 'like')
                                
                                UNION ALL
                                
                                (SELECT 
                                    'Oceniono negatywnie recenzję' AS action_type, 
                                    NULL AS film_title, 
                                    u.username AS target_username, 
                                    r.film_id AS film_id, 
                                    rv.created_at AS action_date
                                 FROM review_votes rv 
                                 JOIN reviews r ON rv.review_id = r.id 
                                 JOIN users u ON r.user_id = u.id 
                                 WHERE rv.user_id = :user_id AND rv.vote = 'dislike')
                                
                                ORDER BY action_date DESC
                            ");
                            $stmt->execute(['user_id' => $user['id']]);
                            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($activities):
                                foreach ($activities as $activity): ?>
                                    <tr onclick="window.location.href='/film-rating-app/film/<?php echo htmlspecialchars($activity['film_id']); ?>';" style="cursor: pointer;">
                                        <td><?php echo htmlspecialchars($activity['action_type']); ?></td>
                                        <td>
                                            <?php if (!empty($activity['film_title'])): ?>
                                                Film: <strong><?php echo htmlspecialchars($activity['film_title']); ?></strong>
                                            <?php elseif (!empty($activity['target_username'])): ?>
                                                Użytkownik: <strong><?php echo htmlspecialchars($activity['target_username']); ?></strong>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><small class="text-muted"><?php echo htmlspecialchars($activity['action_date']); ?></small></td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Brak aktywności do wyświetlenia.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>

<footer class="bg-primary text-white py-3">
    <div class="container d-flex justify-content-center align-items-center">
        <p class="mb-0">&copy; 2025 Film-Rating-App</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('changePasswordForm')?.addEventListener('submit', function(event) {
        event.preventDefault();
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;

        fetch('/film-rating-app/actions/change_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `current_password=${encodeURIComponent(currentPassword)}&new_password=${encodeURIComponent(newPassword)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Hasło zostało pomyślnie zmienione.');
                } else {
                    alert(data.error || 'Wystąpił błąd podczas zmiany hasła.');
                }
            })
            .catch(error => {
                console.error('Błąd:', error);
                alert('Wystąpił problem z połączeniem z serwerem.');
            });
    });
</script>

</body>
</html>
