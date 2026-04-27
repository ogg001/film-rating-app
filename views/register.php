<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja</title>
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

<?php
$error = null;
$success = false;

if (isset($_SERVER['REQUEST_URI'])) {
    $requestUri = $_SERVER['REQUEST_URI'];
    if (strpos($requestUri, '/register/success') !== false) {
        $success = true;
    } elseif (strpos($requestUri, '/register/error/') !== false) {
        $errorParts = explode('/', $requestUri);
        $errorCode = end($errorParts);
        switch ($errorCode) {
            case 'password_mismatch':
                $error = "Hasła nie są identyczne.";
                break;
            case 'empty_fields':
                $error = "Wszystkie pola są wymagane.";
                break;
            case 'user_exists':
                $error = "Użytkownik o podanej nazwie już istnieje.";
                break;
            case 'server_error':
                $error = "Wystąpił problem z serwerem. Spróbuj ponownie.";
                break;
            default:
                $error = "Wystąpił nieznany błąd.";
        }
    }
}
?>

<?php if ($success): ?>
    <div id="alert-success" class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
        Rejestracja zakończona sukcesem! Możesz się teraz zalogować.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div id="alert-error" class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
        <?php echo htmlspecialchars($error); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>


<main class="container my-5">
    <section aria-labelledby="registration-header" class="col-md-6 offset-md-3">
        <h2 id="registration-header" class="text-center text-primary mb-4">Zarejestruj się</h2>

        <form id="registrationForm" action="/film-rating-app/register_action" method="POST" class="card p-4 shadow-sm needs-validation" maxlength="14" novalidate >
            <div class="form-group">
                <label for="username">Nazwa użytkownika:</label>
                <input type="text" id="username" name="username" class="form-control" maxlength="12" required>
                <div class="invalid-feedback"> Proszę wprowadzić nazwę użytkownika.</div>
            </div>
            
<div class="form-group">
    <label for="password">Hasło:</label>
    <input type="password" id="password" name="password" class="form-control" required>
    <div class="invalid-feedback">Hasło musi mieć co najmniej 8 znaków.</div>
</div>

            <div class="form-group">
                <label for="password_repeat">Powtórz hasło:</label>
                <input type="password" id="password_repeat" name="password_repeat" class="form-control" required>
                <div class="invalid-feedback">Hasła muszą być takie same.</div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block mt-4">Zarejestruj</button>
        </form>

        <nav aria-label="login-navigation" class="text-center mt-3">
            <p>Masz już konto?</p>
            <a href="/film-rating-app/login" class="btn btn-outline-primary">Zaloguj się</a>
        </nav>
    </section>
</main>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/film-rating-app/js/scripts.js"></script>

</body>
</html>
