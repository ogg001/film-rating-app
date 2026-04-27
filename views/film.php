<?php
require 'config.php'; 

if (isset($_GET['film_id'])) {
    $film_id = (int)$_GET['film_id'];

    $stmt = $pdo->prepare("
        SELECT f.*, 
               c.name AS category_name, 
               COALESCE(AVG(r.rating), 0) AS average_rating, 
               COUNT(r.id) AS review_count
        FROM films f
        LEFT JOIN categories c ON f.category = c.id
        LEFT JOIN reviews r ON f.id = r.film_id
        WHERE f.id = :film_id
        GROUP BY f.id
    ");
    $stmt->bindParam(':film_id', $film_id, PDO::PARAM_INT);
    $stmt->execute();
    $film = $stmt->fetch(PDO::FETCH_ASSOC);

    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    $stmt_reviews = $pdo->prepare("
        SELECT r.*, u.username 
        FROM reviews r
        LEFT JOIN users u ON r.user_id = u.id
        WHERE r.film_id = :film_id
        ORDER BY (r.user_id = :user_id) DESC, r.created_at DESC
    ");
    $stmt_reviews->bindParam(':film_id', $film_id, PDO::PARAM_INT);
    $stmt_reviews->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_reviews->execute();

    $reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

?>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div id="flash-message" class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_SESSION['flash_message']['message']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Szczegóły filmu</title>
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

<div id="content">
    <div class="container my-5">

    <?php if (isset($_GET['success_review']) && $_GET['success_review'] == 1): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Recenzja została pomyślnie dodana!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <?php if ($_GET['error'] === 'already_reviewed'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Możesz dodać tylko jedną recenzję dla tego filmu.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php elseif ($_GET['error'] === 'server_error'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Wystąpił problem z serwerem. Spróbuj ponownie później.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php elseif ($_GET['error'] === 'invalid_request'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Nieprawidłowe żądanie. Spróbuj ponownie.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
	
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <?php if ($film): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h1 class="card-title text-primary"><?php echo htmlspecialchars($film['title']); ?></h1>
                            <p><strong>Opis:</strong> <?php echo nl2br(htmlspecialchars($film['description'])); ?></p>
								<?php if (!empty($film['category_name'])): ?>
									<p><strong>Kategoria:</strong> <?php echo htmlspecialchars($film['category_name']); ?></p>
								<?php else: ?>
									<p><strong>Kategoria:</strong>Nieokreślona</p>
								<?php endif; ?>
                            <p><strong>Data premiery:</strong> <?php echo htmlspecialchars($film['release_year']); ?></p>
                            
                            <?php if (!empty($film['duration'])): ?>
                                <?php
                                    $hours = floor($film['duration'] / 60); 
                                    $minutes = $film['duration'] % 60; 
                                ?>
                                <p><strong>Czas trwania:</strong> 
                                    <?php echo $hours . 'h ' . $minutes . 'm'; ?>
                                </p>
                            <?php endif; ?>

							<p>
								<img src="/film-rating-app/images/star_colored2.svg" alt="Gwiazdka oceny" class="mr-1" style="width: 20px; height: 20px;">
								<span id="film-average-rating"><?php echo number_format($film['average_rating'], 1); ?></span> / 5

								<span id="film-review-count-container">
									<?php if ($film['review_count'] == 1): ?>
										<small>(na podstawie <span id="film-review-count">1</span> oceny)</small>
									<?php else: ?>
										<small>(na podstawie <span id="film-review-count"><?php echo $film['review_count']; ?></span> ocen)</small>
									<?php endif; ?>
								</span>
							</p>
                        </div>

                        <div class="poster-container text-center my-3">
                            <?php if (!empty($film['poster'])): ?>
                                <img src="/film-rating-app/<?php echo htmlspecialchars($film['poster']); ?>" 
                                     alt="Plakat filmu <?php echo htmlspecialchars($film['title']); ?>" 
                                     class="img-fluid" style="max-width:300px;">
                            <?php else: ?>
                                <img src="/film-rating-app/uploads/NULL.jpg" alt="Brak plakatu" 
                                     class="img-fluid" style="max-width:300px;">
                            <?php endif; ?>
                        </div>
                    </div>

					<h2 class="text-primary">Recenzje:</h2>
					<div class="sort-reviews mb-3" data-review-id="<?php echo $review['id']; ?>" data-film-rating="<?php echo htmlspecialchars($review['film_rating']); ?>">
						<label for="sortReviews" class="mr-2">Sortuj według:</label>
						<select id="sortReviews" class="form-control d-inline w-auto">
											<option value="created_at_desc">Najnowsze</option>
											<option value="created_at_asc">Najstarsze</option>
											<option value="rating_desc">Najwyższa ocena recenzji</option>
											<option value="rating_asc">Najniższa ocena recenzji</option>
											<option value="film_rating_desc">Najwyższa ocena filmu</option>
											<option value="film_rating_asc">Najniższa ocena filmu</option>
						</select>
					</div>
					
					<div class="reviews-container">
						<?php if (count($reviews) > 0): ?>
							<?php foreach ($reviews as $review): ?>
								<div class="card review mb-3" 
								 data-review-id="<?php echo $review['id']; ?>" 
								 data-film-rating="<?php echo htmlspecialchars($review['rating']); ?>" 
								 data-created-at="<?php echo strtotime($review['created_at']); ?>">
									<div class="card-body">
										<p class="mb-1">
											<strong>
												<a href="/film-rating-app/user/<?php echo htmlspecialchars($review['username']); ?>" class="text-decoration-none">
													<?php echo htmlspecialchars($review['username']); ?>
												</a>
											</strong> 
											(<?php echo (int)$review['rating']; ?> / 5)
										</p>
										<p class="mb-1"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
										<p>
											<small>Dodano: <?php echo htmlspecialchars($review['created_at']); ?></small>
											<?php if (!empty($review['last_edited_at'])): ?>
												<br><small>Edytowano: <?php echo htmlspecialchars($review['last_edited_at']); ?></small>
											<?php endif; ?>
										</p>
									</div>
									<div class="review-votes">
										<?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']): ?>
											<form class="d-inline delete-review-form" onsubmit="event.preventDefault(); deleteReview(<?php echo $review['id']; ?>);">
												<button type="submit" class="btn btn-secondary btn-sm" title="Usuń recenzję">🗑</button>
											</form>
											<button class="btn btn-warning btn-sm"
												onclick="openEditReviewModal(
													<?php echo $review['id']; ?>,
													'<?php echo htmlspecialchars($review['review_text'], ENT_QUOTES); ?>',
													<?php echo (int)$review['rating']; ?>
												);" title="Edytuj recenzję">
												✎
											</button>
										<?php endif; ?>
										<button class="btn btn-success btn-sm vote-btn" data-review-id="<?php echo $review['id']; ?>" data-vote="like">
											👍 <?php echo $review['likes']; ?>
										</button>
										<button class="btn btn-danger btn-sm vote-btn" data-review-id="<?php echo $review['id']; ?>" data-vote="dislike">
											👎 <?php echo $review['dislikes']; ?>
										</button>
									</div>
								</div>
							<?php endforeach; ?>
						<?php else: ?>
							<p class="text-muted no-reviews-message">Brak recenzji do wyświetlenia.</p>
						<?php endif; ?>
					</div>

					<div id="editReviewModal" class="modal">
						<div class="modal-content">
							<span class="close" onclick="closeEditReviewModal()">&times;</span>
							<h2>Edytuj swoją recenzję</h2>
							<form id="editReviewForm" onsubmit="event.preventDefault(); submitEditReview();">
								<div class="form-group">
									<label for="editReviewText">Treść recenzji:</label>
									<textarea id="editReviewText" class="form-control" rows="4" required></textarea>
								</div>
								<div class="form-group">
									<label for="editReviewRating">Ocena:</label>
									<select id="editReviewRating" class="form-control w-auto" required>
										<?php for ($i = 1; $i <= 5; $i++): ?>
											<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
										<?php endfor; ?>
									</select>
								</div>
								<input type="hidden" id="editReviewId">
								<button type="submit" class="btn btn-primary">Zapisz zmiany</button>
							</form>
						</div>
					</div>

                    <?php if (isset($_SESSION['username'])): ?>
                        <h3 class="text-primary">Dodaj recenzję:</h3>
                        <form action="/film-rating-app/actions/add_review.php" method="POST">
                            <div class="form-group">
                                <textarea name="review_text" class="form-control" rows="4" placeholder="Twoja recenzja"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="rating">Ocena:</label>
                                <select name="rating" class="form-control w-auto">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <input type="hidden" name="film_id" value="<?php echo $film_id; ?>">
                            <button type="submit" class="btn btn-primary">Dodaj recenzję</button>
                        </form>
                    <?php endif; ?>

                <?php else: ?>
                    <p class="text-danger">Film o takim ID nie istnieje.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>	
<footer class="bg-primary text-white py-3">
    <div class="container d-flex justify-content-center align-items-center">
        <p class="mb-0">&copy; 2025 Film-Rating-App</p>
    </div>
</footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
	<script src="/film-rating-app/js/scripts.js"></script>
    </body>
    </html>
    <?php
} else {
    echo "Brak ID filmu w URL!";
}
?>
