<?php
$films = $films ?? [];

$page = isset($_POST['page']) ? (int)$_POST['page'] : (isset($_GET['page']) ? (int)$_GET['page'] : 1);
$page = max($page, 1);

$filmsPerPage = 12;
$offset = ($page - 1) * $filmsPerPage;

$selectedCategory = $_POST['category'] ?? $_GET['category'] ?? null;
$selectedYearMin = $_POST['year_min'] ?? $_GET['year_min'] ?? null;
$selectedYearMax = $_POST['year_max'] ?? $_GET['year_max'] ?? null;
$selectedSort = $_POST['sort'] ?? $_GET['sort'] ?? 'release_year DESC';

$whereClauses = [];
if ($selectedCategory) {
    $whereClauses[] = "f.category = :category";
}
if (!empty($selectedYearMin) && !empty($selectedYearMax)) {
    $whereClauses[] = "f.release_year BETWEEN :year_min AND :year_max";
}

$whereSql = $whereClauses ? "WHERE " . implode(" AND ", $whereClauses) : "";

$sql = "
    SELECT f.*, COALESCE(AVG(r.rating), 0) AS average_rating, c.name AS category_name
    FROM films f
    LEFT JOIN reviews r ON f.id = r.film_id
    LEFT JOIN categories c ON f.category = c.id
    $whereSql
    GROUP BY f.id
    ORDER BY $selectedSort
    LIMIT :limit OFFSET :offset
";

try {
    $stmt = $pdo->prepare($sql);

    if ($selectedCategory) {
        $stmt->bindParam(':category', $selectedCategory);
    }
    if (!empty($selectedYearMin) && !empty($selectedYearMax)) {
        $stmt->bindParam(':year_min', $selectedYearMin, PDO::PARAM_INT);
        $stmt->bindParam(':year_max', $selectedYearMax, PDO::PARAM_INT);
    }

    $stmt->bindValue(':limit', $filmsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("SQL Query: $sql");
    error_log("Films fetched: " . count($films));
} catch (PDOException $e) {
    error_log("SQL Error: " . $e->getMessage());
    $films = [];
}

$totalFilmsStmt = $pdo->prepare("SELECT COUNT(DISTINCT f.id) 
    FROM films f
    LEFT JOIN reviews r ON f.id = r.film_id
    $whereSql");

if ($selectedCategory) {
    $totalFilmsStmt->bindParam(':category', $selectedCategory);
}
if (!empty($selectedYearMin) && !empty($selectedYearMax)) {
    $totalFilmsStmt->bindParam(':year_min', $selectedYearMin, PDO::PARAM_INT);
    $totalFilmsStmt->bindParam(':year_max', $selectedYearMax, PDO::PARAM_INT);
}

try {
    $totalFilmsStmt->execute();
    $totalFilms = (int)$totalFilmsStmt->fetchColumn();
    $totalPages = ceil($totalFilms / $filmsPerPage);

    error_log("Total Films: $totalFilms");
    error_log("Total Pages: $totalPages");
} catch (PDOException $e) {
    error_log("Error counting total films: " . $e->getMessage());
    $totalFilms = 0;
    $totalPages = 1;
}

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    include 'views/film_list_partial.php';
    exit;
}

?>

<?php
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista filmów</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
<div id="content">
	<main class="container my-4">
		<div class="row">
			<aside class="aside-container col-md-3">
				<div class="bg-light p-3 rounded">
					<div class="d-flex justify-content-between align-items-center">
						<h5 class="mb-0">Filtry</h5>
						<button id="resetFilters" class="btn btn-sm btn-light" title="Wyczyść filtry">
							<i class="fas fa-redo-alt"></i>
						</button>
					</div>
					<form id="filterForm" class="mt-3">
						<div class="form-group">
							<label for="category">Kategoria</label>
								<select name="category" id="category" class="form-control">
									<option value="">Wybierz kategorię</option>
									<?php foreach ($categories as $category): ?>
										<option value="<?php echo htmlspecialchars($category['id']); ?>">
											<?php echo htmlspecialchars($category['name']); ?>
										</option>
									<?php endforeach; ?>
								</select>
						</div>
						
						<div class="form-group">
							<label for="yearRange">Rok premiery</label>
								<div id="slider-container" 
								 data-year-min="<?php echo $_GET['year_min'] ?? 1901; ?>" 
								 data-year-max="<?php echo $_GET['year_max'] ?? 2025; ?>">
								</div>
							
							<input type="hidden" id="yearMin" name="year_min" value="1901">
							<input type="hidden" id="yearMax" name="year_max" value="2025">
							<div class="text-center mt-2">
								<span id="yearRangeDisplay">1905 - 2025</span>
							</div>
						</div>
						<h5>Sortowanie</h5>
						<div class="form-group">
							<select name="sort" id="sort" class="form-control">
								<option value="release_year DESC">Najnowsze</option>
								<option value="release_year ASC">Najstarsze</option>
								<option value="average_rating DESC">Najwyżej oceniane</option>
								<option value="average_rating ASC">Najgorzej oceniane</option>
							</select>
						</div>
						<button type="submit" class="btn btn-primary btn-block">Zastosuj</button>
					</form>
				</div>
			</aside>
			
			<section id="filmList" class="film-list-container col-md-9">
				<?php include 'views/film_list_partial.php'; ?>
			</section>
			
		</div>
	</main>
</div>
<footer class="bg-primary text-white py-3">
    <div class="container d-flex justify-content-center align-items-center">
        <p class="mb-0">&copy; 2025 Film-Rating-App</p>
    </div>
</footer>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/film-rating-app/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterForm = document.getElementById('filterForm');
    const filmList = document.getElementById('filmList');
    const resetFiltersButton = document.getElementById('resetFilters');
    const slider = document.getElementById('slider-container');
    const yearMinInput = document.getElementById('yearMin');
    const yearMaxInput = document.getElementById('yearMax');
    const categorySelect = document.getElementById('category');
    const sortSelect = document.getElementById('sort');
    let selectedPage = 1;

    const yearMin = parseInt(slider.getAttribute('data-year-min'), 10) || 1901;
    const yearMax = parseInt(slider.getAttribute('data-year-max'), 10) || 2025;

    noUiSlider.create(slider, {
        start: [yearMin, yearMax],
        connect: true,
        range: {
            'min': 1901,
            'max': 2025
        },
        step: 1,
        tooltips: false,
        format: {
            to: (value) => Math.round(value),
            from: (value) => Number(value)
        }
    });

    slider.noUiSlider.on('update', (values) => {
        const [min, max] = values;
        yearMinInput.value = min;
        yearMaxInput.value = max;
        document.getElementById('yearRangeDisplay').textContent = `${min} - ${max}`;
    });

    function loadFilms(page = 1) {
        const formData = new FormData(filterForm);
        formData.append('page', page);

        fetch(`/film-rating-app/home`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newFilmList = doc.querySelector('.row');
                const newPagination = doc.querySelector('#paginationContainer');

                if (newFilmList && newFilmList.children.length > 0) {
                    if (filmList.querySelector('.row')) {
                        filmList.querySelector('.row').replaceWith(newFilmList);
                    } else {
                        filmList.innerHTML = '';
                        filmList.appendChild(newFilmList);
                    }

                    if (newPagination) {
                        const existingPagination = document.getElementById('paginationContainer');
                        if (existingPagination) {
                            existingPagination.replaceWith(newPagination);
                        } else {
                            filmList.appendChild(newPagination);
                        }
                        attachPaginationListeners();
                    }
					limitFilmDescriptions();
                } else {
                    filmList.innerHTML = `
                        <div class="text-center mt-4">
                            <p class="text-muted">Brak filmów do wyświetlenia.</p>
                        </div>
                    `;
                    const existingPagination = document.getElementById('paginationContainer');
                    if (existingPagination) {
                        existingPagination.remove();
                    }
                }

                updateActivePaginationButton(page);
            })
            .catch(error => {
                console.error('Błąd podczas ładowania filmów:', error);
            });
    }

    function attachPaginationListeners() {
        const paginationButtons = document.querySelectorAll('.pagination .page-link');
        paginationButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                selectedPage = parseInt(button.getAttribute('data-page'), 10);
                loadFilms(selectedPage);
            });
        });
    }

    function updateActivePaginationButton(page) {
        const paginationButtons = document.querySelectorAll('.pagination .page-item');
        paginationButtons.forEach(button => {
            button.classList.remove('active');
        });
        const activeButton = document.querySelector(`.pagination .page-item [data-page="${page}"]`);
        if (activeButton) {
            activeButton.parentElement.classList.add('active');
        }
    }

    filterForm.addEventListener('submit', (event) => {
        event.preventDefault();
        selectedPage = 1;
        loadFilms(selectedPage);
    });

    resetFiltersButton.addEventListener('click', () => {
        categorySelect.value = '';

        slider.noUiSlider.set([1901, 2025]);

        sortSelect.value = 'release_year DESC';

        selectedPage = 1;
        loadFilms(selectedPage);
    });

    attachPaginationListeners();
});
</script>
</body>
</html>
