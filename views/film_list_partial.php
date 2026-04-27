<?php if (count($films) > 0): ?>
<div class="row">
    <?php foreach ($films as $film): ?>
        <div class="col-sm-6 col-md-4 col-lg-3 mb-4 film-item" data-film-id="<?php echo $film['id']; ?>">
            <article class="film card h-100">
                <a href="/film-rating-app/film/<?php echo $film['id']; ?>" class="card-img-top-link">
                    <img src="/film-rating-app/<?php echo htmlspecialchars($film['poster'] ?? 'uploads/NULL.jpg'); ?>" 
                        alt="Plakat <?php echo htmlspecialchars($film['title']); ?>" class="card-img-top">
                </a>
                <div class="card-body d-flex flex-column">
                    <h2 class="card-title h5">
                        <a href="/film-rating-app/film/<?php echo $film['id']; ?>" class="text-primary">
                            <?php echo htmlspecialchars($film['title']); ?>
                        </a>
                    </h2>
                    <p class="card-text"><?php echo htmlspecialchars($film['description']); ?></p>
						<div class="mt-auto">
							<?php if (!empty($film['category_name'])): ?>
								<p class="text-muted mb-1"><small>Kategoria: <?php echo htmlspecialchars($film['category_name']); ?></small></p>
							<?php else: ?>
								<p class="text-muted mb-1"><small>Kategoria: Nieokreślona</small></p>
							<?php endif; ?>
							<p class="text-muted"><small>Data premiery: <?php echo htmlspecialchars($film['release_year']); ?></small></p>
						</div>
                </div>
                <div class="card-footer">
                    <p class="mb-0">
                        <img src="/film-rating-app/images/star_colored2.svg" alt="Gwiazdka oceny" class="mr-1" style="width: 20px; height: 20px;">
                        <?php echo number_format($film['average_rating'], 1); ?> / 5
                    </p>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<p class="text-center">Brak filmów do wyświetlenia.</p>
<?php endif; ?>

<nav id="paginationContainer" class="d-flex justify-content-center mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <button class="page-link" data-page="<?php echo $i; ?>"><?php echo $i; ?></button>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
