<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
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
                <span class="mr-3">Zalogowany jako: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="/film-rating-app/logout" class="btn btn-light btn-sm">Wyloguj się</a>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="/film-rating-app/admin_panel" class="btn btn-outline-light btn-sm ml-2">Panel Administratora</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="/film-rating-app/login" class="btn btn-light btn-sm">
                    <img src="/film-rating-app/images/user_icon.png" alt="Ikona użytkownika" class="mr-1" style="width: 20px; height: 20px;">
                    Zaloguj się
                </a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container my-5">
    <section aria-labelledby="login-header" class="col-md-6 offset-md-3">
        <h2 id="login-header" class="text-center text-primary mb-4">Zaloguj się</h2>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" action="/film-rating-app/login_action" method="POST" class="card p-4 shadow-sm needs-validation" novalidate>
            <div class="form-group">
                <label for="username">Nazwa użytkownika:</label>
                <input type="text" id="username" name="username" class="form-control" required>
                <div class="invalid-feedback">Wprowadź nazwę użytkownika.</div>
            </div>

            <div class="form-group">
                <label for="password">Hasło:</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <div class="invalid-feedback">Wprowadź hasło.</div>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-4">Zaloguj</button>
        </form>

        <nav aria-label="registration" class="text-center mt-3">
            <p>Nie masz konta?</p>
            <a href="/film-rating-app/register" class="btn btn-outline-primary">Zarejestruj się</a>
        </nav>
    </section>
</main>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/film-rating-app/js/scripts.js"></script>

</body>
</html>
