<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /film-rating-app/");
    exit;
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administratora</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/film-rating-app/css/style.css">
</head>
<body>
<div id="alert-container"></div>
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

<main class="container my-5">
    <h1 class="text-center text-primary mb-4">Panel Administratora</h1><br>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div id="alert-success-add" class="alert alert-success alert-dismissible fade show" role="alert">
        Film został pomyślnie dodany!
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['success_edit']) && $_GET['success_edit'] == 1): ?>
    <div id="alert-success-edit" class="alert alert-success alert-dismissible fade show" role="alert">
        Film został pomyślnie zaktualizowany!
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['success_delete']) && $_GET['success_delete'] == 1): ?>
    <div id="alert-success-delete" class="alert alert-success alert-dismissible fade show" role="alert">
        Film został pomyślnie usunięty!
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div id="alert-error" class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php
        switch ($_GET['error']) {
            case 'film_exists':
                echo "Film o podanym tytule i roku już istnieje.";
                break;
            case 'invalid_poster':
                echo "Nieprawidłowy format plakatu. Dozwolone formaty to JPG, JPEG, PNG.";
                break;
            case 'upload_failed':
                echo "Nie udało się przesłać plakatu.";
                break;
            case 'server_error':
                echo "Wystąpił błąd serwera. Spróbuj ponownie.";
                break;
            default:
                echo "Wystąpił nieznany błąd.";
        }
        ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
	
    <section aria-labelledby="add-film-header" class="mb-5">
        <h2 id="add-film-header" class="text-center text-primary mb-4">Dodaj Film</h2>
        <form id="addFilmForm" action="/film-rating-app/add_film_action" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm needs-validation" novalidate>
            <div class="form-group">
                <label for="add_title">Tytuł:</label>
                <input type="text" id="add_title" name="title" class="form-control" required maxlength="40">
                <div class="invalid-feedback">
                    Proszę podać tytuł filmu (max 40 znaków).
                </div>
            </div>

            <div class="form-group">
                <label for="add_description">Opis:</label>
                <textarea id="add_description" name="description" class="form-control" required maxlength="200"></textarea>
                <div class="invalid-feedback">
                    Proszę podać opis filmu (max 200 znaków).
                </div>
            </div>

            <div class="form-group">
                <label for="add_release_year">Rok wydania:</label>
                <input type="number" id="add_release_year" name="release_year" class="form-control" min="1901" max="2025" required>
                <div class="invalid-feedback">
                    Proszę podać rok wydania w zakresie 1901-2025.
                </div>
            </div>
				
			<div class="form-group">
				<label for="add_category">Kategoria:</label>
				<div class="input-group">
			<select id="add_category" name="category" class="form-control" required>
				<option value="">Wybierz kategorię</option>
				<?php foreach ($categories as $category): ?>
					<option value="<?php echo htmlspecialchars($category['id']); ?>">
						<?php echo htmlspecialchars($category['name']); ?>
					</option>
				<?php endforeach; ?>
			</select>
					<div class="input-group-append">
						<button type="button" class="btn btn-outline-secondary" onclick="openManageCategoriesModal()" title="Zarządzaj kategoriami">
							<i class="fas fa-cog"></i>
						</button>
					</div>
					<div class="invalid-feedback">
						Proszę wybrać kategorię filmu.
					</div>
				</div>
			</div>

            <div class="form-group">
                <label for="add_duration">Czas trwania (w minutach):</label>
                <input type="number" id="add_duration" name="duration" class="form-control" min="1" max="300" required>
                <div class="invalid-feedback">
                    Proszę podać czas trwania filmu w minutach (do 300min).
                </div>
            </div>

			<div class="form-group">
				<label for="add_poster">Plakat:</label>
				<input type="file" id="add_poster" name="poster" class="form-control-file" accept=".jpg,.jpeg,.png">
				<div class="invalid-feedback">
					Proszę przesłać plik w formacie JPG, JPEG lub PNG.
				</div>
			</div>

            <button type="submit" class="btn btn-primary btn-block">Dodaj film</button>
        </form>
    </section>

    <section aria-labelledby="manage-films-header" class="mb-5">
        <h2 id="manage-films-header" class="text-center text-primary mb-4">Zarządzaj Filmami</h2>
        <?php
        $stmt = $pdo->query("SELECT * FROM films ORDER BY release_year DESC");
        $films = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($films) > 0): ?>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Tytuł</th>
                        <th>Rok wydania</th>
                        <th>Opcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($films as $film): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($film['title']); ?></td>
                            <td><?php echo htmlspecialchars($film['release_year']); ?></td>
                            <td>
                                <form action="/film-rating-app/delete_film_action" method="POST" style="display:inline;">
                                    <input type="hidden" name="film_id" value="<?php echo $film['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Czy na pewno chcesz usunąć ten film?')">Usuń</button>
                                </form>
                                <button class="btn btn-warning btn-sm" 
        onclick="openEditModal(<?php echo htmlspecialchars(json_encode($film)); ?>)">Edytuj</button>

                                <button class="btn btn-info btn-sm" onclick="openReviewsModal(<?php echo $film['id']; ?>)">Podgląd Recenzji</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Brak filmów do zarządzania.</p>
        <?php endif; ?>
    </section>
	<section aria-labelledby="manage-users-header" class="mb-5">
		<h2 id="manage-users-header" class="text-center text-primary mb-4">Zarządzaj Użytkownikami</h2>
		<?php
		$stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
		$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($users) > 0): ?>
			<table class="table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Nazwa użytkownika</th>
						<th>Status</th>
						<th>Akcje</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($users as $user): ?>
						<tr data-user-id="<?php echo $user['id']; ?>">
							<td><?php echo htmlspecialchars($user['id']); ?></td>
							<td><?php echo htmlspecialchars($user['username']); ?></td>
							<td>
								<?php echo ($user['is_blocked'] ?? 0) ? '<span class="text-danger">Zablokowany</span>' : '<span class="text-success">Aktywny</span>'; ?>
							</td>
							<td>
								<button class="btn btn-danger btn-sm delete-user-button" data-user-id="<?php echo $user['id']; ?>">
									Usuń
								</button>
								<button class="btn btn-warning btn-sm toggle-block-user-button" data-user-id="<?php echo $user['id']; ?>" data-is-blocked="<?php echo $user['is_blocked'] ?? 0; ?>">
									<?php echo ($user['is_blocked'] ?? 0) ? 'Odblokuj' : 'Zablokuj'; ?>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else: ?>
			<p class="text-center">Brak użytkowników do zarządzania.</p>
		<?php endif; ?>
	</section>
</main>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edytuj Film</h2>
 <form id="editFilmForm" action="/film-rating-app/edit_film_action" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    <input type="hidden" name="film_id" id="filmId">
    <div class="form-group">
        <label for="title">Tytuł:</label>
        <input type="text" id="title" name="title" class="form-control" required maxlength="40">
		<div class="invalid-feedback">
            Proszę podać tytuł filmu (max 40 znaków).
        </div>
    </div>
    <div class="form-group">
        <label for="description">Opis:</label>
        <textarea id="description" name="description" class="form-control" required maxlength="200"></textarea>
		<div class="invalid-feedback">
            Proszę podać opis filmu (max 200 znaków).
        </div>
    </div>
	<div class="form-group">
		<label for="release_year">Rok wydania:</label>
		<input type="number" id="release_year" name="release_year" class="form-control" min="1901" max="2025" required>
		<div class="invalid-feedback">
			Proszę podać rok wydania w zakresie 1901-2025.
		</div>
	</div>
		<div class="form-group">
			<label for="edit_category">Kategoria:</label>
				<select id="edit_category" name="category" class="form-control" required>
					<option value="">Wybierz kategorię</option>
					<?php foreach ($categories as $category): ?>
						<option value="<?php echo htmlspecialchars($category['id']); ?>" 
							<?php echo isset($film['category']) && $film['category'] == $category['id'] ? 'selected' : ''; ?>>
							<?php echo htmlspecialchars($category['name']); ?>
						</option>
					<?php endforeach; ?>
				</select>
			<div class="invalid-feedback">
				Proszę wybrać kategorię filmu.
			</div>
		</div>
    <div class="form-group">
        <label for="edit_duration">Czas trwania (w minutach):</label>
        <input type="number" id="edit_duration" name="duration" class="form-control" min="1" max="300" required>
		<div class="invalid-feedback">
            Proszę podać czas trwania filmu w minutach (do 300min).
        </div>
    </div>
    <div class="form-group">
        <label for="poster">Plakat:</label>
        <input type="file" id="poster" name="poster" accept="image/*" class="form-control-file">
		    <div class="invalid-feedback">
        Proszę przesłać plik w formacie JPG, JPEG lub PNG.
    </div>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Zapisz zmiany</button>
</form>

    </div>
</div>

<div id="reviewsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeReviewsModal()">&times;</span>
        <h2>Recenzje filmu</h2>
        <div id="reviewsContent"></div>
    </div>
</div>

<div id="manageCategoriesModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeManageCategoriesModal()">&times;</span>
        <h2>Zarządzaj kategoriami</h2>
        <form id="addCategoryForm" class="needs-validation mb-4" novalidate>
			<div class="form-group">
				<label for="category_name">Dodaj nową kategorię:</label>
				<input type="text" id="category_name" name="name" class="form-control" 
					   required maxlength="15" pattern="[A-Za-zĄąĆćĘęŁłŃńÓóŚśŹźŻż\s]+"
					   title="Proszę wpisać tylko litery (max. 15 znaków).">
				<div id="categoryError" class="invalid-feedback">
					Proszę podać poprawną nazwę kategorii (tylko litery, max. 15 znaków).
				</div>
			</div>
            <button type="button" class="btn btn-primary" onclick="submitAddCategory()">Dodaj kategorię</button>
        </form>

        <form id="deleteCategoryForm" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="delete_category">Usuń istniejącą kategorię:</label>
                <select id="delete_category" name="name" class="form-control" required>
                    <option value="">Wybierz kategorię do usunięcia</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['name']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">
                    Proszę wybrać kategorię do usunięcia.
                </div>
            </div>
            <button type="button" class="btn btn-danger" onclick="submitDeleteCategory()">Usuń kategorię</button>
        </form>
    </div>
</div>

<footer class="bg-primary text-white py-3">
    <div class="container d-flex justify-content-center align-items-center">
        <p class="mb-0">&copy; 2025 Film-Rating-App</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="/film-rating-app/js/scripts.js"></script>

</body>
</html>
