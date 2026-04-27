let currentFilmId;

document.addEventListener('DOMContentLoaded', () => {
    const sortReviewsSelect = document.getElementById('sortReviews');
    const reviewsContainer = document.querySelector('.reviews-container');

    if (sortReviewsSelect) {
        sortReviewsSelect.addEventListener('change', () => {
            const sortBy = sortReviewsSelect.value;
            const reviews = Array.from(reviewsContainer.querySelectorAll('.card.review'));

            reviews.sort((a, b) => {
                const ratingA = parseInt(a.querySelector('strong').nextSibling.textContent.match(/\d+/));
                const ratingB = parseInt(b.querySelector('strong').nextSibling.textContent.match(/\d+/));

                const likesA = parseInt(a.querySelector('button[data-vote="like"]').textContent.trim().replace('👍', '')) || 0;
                const likesB = parseInt(b.querySelector('button[data-vote="like"]').textContent.trim().replace('👍', '')) || 0;

                const dislikesA = parseInt(a.querySelector('button[data-vote="dislike"]').textContent.trim().replace('👎', '')) || 0;
                const dislikesB = parseInt(b.querySelector('button[data-vote="dislike"]').textContent.trim().replace('👎', '')) || 0;

                const filmRatingA = parseFloat(a.dataset.filmRating) || 0;
                const filmRatingB = parseFloat(b.dataset.filmRating) || 0;

                const scoreA = likesA - dislikesA;
                const scoreB = likesB - dislikesB;


                switch (sortBy) {
                    case 'created_at_desc':
                        return b.dataset.createdAt - a.dataset.createdAt;
                    case 'created_at_asc':
                        return a.dataset.createdAt - b.dataset.createdAt;
                    case 'rating_desc':
                        return scoreB - scoreA || ratingB - ratingA;
                    case 'rating_asc':
                        return scoreA - scoreB || ratingA - ratingB;
                    case 'film_rating_desc':
                        return filmRatingB - filmRatingA;
                    case 'film_rating_asc':
                        return filmRatingA - filmRatingB;
                    default:
                        return 0;
                }
            });

            reviews.forEach(review => reviewsContainer.appendChild(review));
        });
    }
});


function openEditModal(film) {
	
    document.getElementById("filmId").value = film.id;
    document.getElementById("title").value = film.title;
    document.getElementById("description").value = film.description;
    document.getElementById("release_year").value = film.release_year;

    const categoryElement = document.getElementById("edit_category");
    if (categoryElement) {
        const categoryOption = categoryElement.querySelector(`option[value="${film.category}"]`);
        if (categoryOption) {
            categoryOption.selected = true;
        } else {
            categoryElement.value = "";
        }
    }

    document.getElementById("edit_duration").value = film.duration || "";

    const posterInput = document.getElementById("poster");
    if (posterInput) {
        posterInput.value = "";
        posterInput.setCustomValidity("");
        posterInput.classList.remove("is-invalid");
        posterInput.classList.remove("is-valid");
    }

    const posterPreview = document.getElementById("posterPreview");
    if (posterPreview) {
        posterPreview.src = `/film-rating-app/${film.poster}`;
    }

    const inputs = document.querySelectorAll('#editFilmForm .form-control');
    inputs.forEach(input => {
        input.classList.remove('is-invalid', 'is-valid');
        input.setCustomValidity('');
    });

    document.getElementById("editModal").style.display = "block";
}

	function closeEditModal() {
		document.getElementById("editModal").style.display = "none";
	}

function openReviewsModal(filmId) {
    currentFilmId = filmId;

    fetch(`/film-rating-app/actions/get_reviews.php?film_id=${filmId}`)
        .then(response => response.json())
        .then(reviews => {
            const reviewsContainer = document.getElementById('reviewsContent');

            if (!reviewsContainer) {
                console.error("Element reviewsContent nie został znaleziony.");
                return;
            }

            reviewsContainer.innerHTML = '';

            if (reviews.length === 0) {
                reviewsContainer.innerHTML = '<p>Brak recenzji.</p>';
            } else {
                reviews.forEach(review => {
                    const reviewDiv = document.createElement('div');

                    reviewDiv.setAttribute('data-review-id', review.id);

                    reviewDiv.innerHTML = `
                        <p><strong>${review.username}</strong> (${review.rating} / 5)</p>
                        <p>${review.review_text}</p>
                        <button class="btn btn-danger btn-sm" onclick="deleteReview(${review.id}, true)" autocomplete="off">Usuń recenzję</button>
                        <hr>
                    `;

                    reviewsContainer.appendChild(reviewDiv);
                });
            }
        })
        .catch(error => console.error('Błąd podczas ładowania recenzji:', error));

    document.getElementById('reviewsModal').style.display = 'block';
}

function closeReviewsModal() {
	document.getElementById('reviewsModal').style.display = 'none';
}

function deleteReview(reviewId, isAdmin = false) {
    const endpoint = isAdmin
        ? `/film-rating-app/actions/delete_review_admin.php`
        : `/film-rating-app/actions/delete_review_author.php`;

    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `review_id=${encodeURIComponent(reviewId)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Wystąpił problem z odpowiedzią serwera.');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('Recenzja została pomyślnie usunięta!', 'success');

            if (isAdmin) {
                const reviewElement = document.querySelector(`#reviewsModal div[data-review-id="${reviewId}"]`);
                if (reviewElement) {
                    reviewElement.remove();
                }

                const reviewsContainer = document.getElementById('reviewsContent');
                if (reviewsContainer && reviewsContainer.children.length === 0) {
                    reviewsContainer.innerHTML = '<p>Brak recenzji.</p>';
                }

            } else {
                const reviewElement = document.querySelector(`.card.review[data-review-id="${reviewId}"]`);
                if (reviewElement) {
                    reviewElement.remove();
                }

                const reviewsContainer = document.querySelector('.container.my-5 .row .col-md-8');
                const remainingReviews = reviewsContainer.querySelectorAll('.card.review');

                if (remainingReviews.length === 0) {
                    let noReviewsMessage = reviewsContainer.querySelector('.no-reviews-message');
                    if (!noReviewsMessage) {
                        noReviewsMessage = document.createElement('p');
                        noReviewsMessage.className = 'text-muted no-reviews-message';
                        noReviewsMessage.textContent = 'Brak recenzji do wyświetlenia.';
                        reviewsContainer.insertBefore(noReviewsMessage, reviewsContainer.querySelector('h3.text-primary'));
                    }
                }
            }

            if (!isAdmin 
                && typeof data.averageRating !== 'undefined'
                && typeof data.reviewCount !== 'undefined') {
                
                const filmAverageRatingEl = document.getElementById('film-average-rating');
                const filmReviewCountEl   = document.getElementById('film-review-count');
                const filmReviewCountContainer = document.getElementById('film-review-count-container');

                if (filmAverageRatingEl) {
                    filmAverageRatingEl.textContent = data.averageRating;
                }

                if (filmReviewCountEl && filmReviewCountContainer) {
                    filmReviewCountEl.textContent = data.reviewCount;

                    const count = parseInt(data.reviewCount, 10);
                    if (count === 1) {
                        filmReviewCountContainer.innerHTML = 
                          `<small>(na podstawie <span id="film-review-count">1</span> oceny)</small>`;
                    } else {
                        filmReviewCountContainer.innerHTML = 
                          `<small>(na podstawie <span id="film-review-count">${count}</span> ocen)</small>`;
                    }
                }
            }

        } else {
            showAlert(data.error || 'Wystąpił błąd podczas usuwania recenzji.', 'danger');
        }
    })
    .catch(error => {
        console.error('Błąd:', error);
        showAlert('Wystąpił błąd podczas usuwania recenzji.', 'danger');
    });
}

function openEditReviewModal(reviewId) {
    const reviewCard = document.querySelector(`.card.review[data-review-id="${reviewId}"]`);

    if (reviewCard) {
        const reviewText = reviewCard.querySelector('.card-body .mb-1:nth-of-type(2)').textContent.trim();
        const reviewRatingMatch = reviewCard.querySelector('strong').nextSibling.textContent.match(/\d+/);
        const reviewRating = reviewRatingMatch ? parseInt(reviewRatingMatch[0], 10) : '';

        const reviewIdInput = document.getElementById('editReviewId');
        const reviewTextInput = document.getElementById('editReviewText');
        const reviewRatingInput = document.getElementById('editReviewRating');

        reviewIdInput.value = reviewId;
        reviewTextInput.value = reviewText;
        reviewRatingInput.value = reviewRating;

        document.getElementById('editReviewModal').style.display = 'block';
    }
}

function closeEditReviewModal() {
    document.getElementById('editReviewModal').style.display = 'none';
}

function updateFilmRatingSummary() {
    const allReviews = document.querySelectorAll('.card.review');
    const totalReviews = allReviews.length;

    if (totalReviews === 0) {
        return;
    }

    const totalRating = Array.from(allReviews).reduce((sum, review) => {
        const rating = parseFloat(review.dataset.filmRating || 0);
        return sum + rating;
    }, 0);

    const averageRating = (totalRating / totalReviews).toFixed(1);

    const averageRatingElement = document.querySelector('.card-body p img + span');
    const reviewCountElement = document.querySelector('.card-body p small');

    if (averageRatingElement) {
        averageRatingElement.textContent = `${averageRating} / 5`;
    }

    if (reviewCountElement) {
        reviewCountElement.textContent = totalReviews === 1
            ? `(na podstawie 1 oceny)`
            : `(na podstawie ${totalReviews} ocen)`;
    }
}


function submitEditReview() {
    const reviewId = document.getElementById('editReviewId').value;
    const reviewText = document.getElementById('editReviewText').value.trim();
    const reviewRating = document.getElementById('editReviewRating').value;

    if (!reviewText) {
        alert('Treść recenzji nie może być pusta.');
        return;
    }

    fetch(`/film-rating-app/actions/edit_review.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${encodeURIComponent(reviewId)}&review_text=${encodeURIComponent(reviewText)}&rating=${encodeURIComponent(reviewRating)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const reviewCard = document.querySelector(`.card.review[data-review-id="${reviewId}"]`);
            if (reviewCard) {
                const reviewBody = reviewCard.querySelector('.card-body');

                const usernameElement    = reviewBody.querySelector('strong');
                const ratingElement      = usernameElement ? usernameElement.nextSibling : null;
                const reviewTextElement  = reviewBody.querySelector('.mb-1:nth-of-type(2)');
                const editedAtElement    = reviewBody.querySelector('small:nth-of-type(2)');

                if (ratingElement) {
                    ratingElement.textContent = ` (${reviewRating} / 5)`;
                    reviewCard.dataset.filmRating = reviewRating;
                }

                if (reviewTextElement) {
                    reviewTextElement.textContent = reviewText;
                }

                const localDateTime = formatDateToLocalString(new Date());
					if (editedAtElement) {
						editedAtElement.textContent = `Edytowano: ${localDateTime}`;
					} else {
						const createdAtElement = reviewBody.querySelector('small:nth-of-type(1)');
						if (createdAtElement) {
							createdAtElement.insertAdjacentHTML(
								'afterend',
								`<br><small>Edytowano: ${localDateTime}</small>`
							);
						} else {
							reviewBody.insertAdjacentHTML(
								'beforeend',
								`<small>Edytowano: ${localDateTime}</small>`
							);
						}
					}
            }

            if (typeof data.averageRating !== 'undefined' && typeof data.reviewCount !== 'undefined') {
                const filmAverageRatingEl = document.getElementById('film-average-rating');
                const filmReviewCountEl   = document.getElementById('film-review-count');
                const filmReviewCountContainer = document.getElementById('film-review-count-container');

                if (filmAverageRatingEl) {
                    filmAverageRatingEl.textContent = data.averageRating;
                }

                if (filmReviewCountEl && filmReviewCountContainer) {
                    filmReviewCountEl.textContent = data.reviewCount;

                    if (parseInt(data.reviewCount, 10) === 1) {
                        filmReviewCountContainer.innerHTML = 
                            `<small>(na podstawie <span id="film-review-count">1</span> oceny)</small>`;
                    } else {
                        filmReviewCountContainer.innerHTML = 
                            `<small>(na podstawie <span id="film-review-count">${data.reviewCount}</span> ocen)</small>`;
                    }
                }
            }

            const sortReviewsSelect = document.getElementById('sortReviews');
            if (sortReviewsSelect) {
                sortReviewsSelect.dispatchEvent(new Event('change'));
            }

            showAlert('Recenzja została pomyślnie zaktualizowana!', 'success');
            closeEditReviewModal();
        } else {
            showAlert(data.error || 'Wystąpił błąd podczas edycji recenzji.', 'danger');
        }
    })
    .catch(error => {
        console.error('Błąd:', error);
        showAlert('Wystąpił problem z połączeniem z serwerem.', 'danger');
    });
}

function formatDateToLocalString(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

window.onclick = function(event) {
    if (event.target == document.getElementById("editModal")) {
        closeEditModal();
    } else if (event.target == document.getElementById("reviewsModal")) {
        closeReviewsModal();
    } else if (event.target == document.getElementById("manageCategoriesModal")) {
        closeManageCategoriesModal();
    } else if (event.target == document.getElementById("editReviewModal")) {
        closeEditReviewModal();
    }
};

	function limitFilmDescriptions() {

		const descriptions = document.querySelectorAll(".card-text");
		descriptions.forEach(desc => {
			let text = desc.innerText;

			const words = text.split(" ");

			if (words.length > 20) {
				text = words.slice(0, 20).join(" ") + "…";
			}

			if (text.length > 127) {
				text = text.substring(0, 127).trim() + "…";
			}

			desc.innerText = text;
		});
	}
	
	function showNotification(message, type = 'success') {
    const notificationContainer = document.getElementById('notification-container') || createNotificationContainer();
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.role = 'alert';
    notification.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;

    notificationContainer.appendChild(notification);

    setTimeout(() => {
        notification.classList.remove('show');
        notification.classList.add('fade');
        setTimeout(() => notification.remove(), 500);
    }, 5000);
}

function createNotificationContainer() {
    const container = document.createElement('div');
    container.id = 'notification-container';
    container.style.position = 'fixed';
    container.style.top = '20px';
    container.style.right = '20px';
    container.style.zIndex = '1050';
    document.body.appendChild(container);
    return container;
}

	document.addEventListener('DOMContentLoaded', function () {
		
    if (document.getElementById('filmList')) {
        limitFilmDescriptions();
    }

    const deleteButtons = document.querySelectorAll('.delete-review-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const reviewId = this.getAttribute('data-review-id');

            if (confirm('Czy na pewno chcesz usunąć tę recenzję?')) {
                fetch('/film-rating-app/actions/delete_review_author.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `review_id=${encodeURIComponent(reviewId)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const reviewCard = this.closest('.card.review');
                        reviewCard.remove();
                        showNotification('Recenzja została pomyślnie usunięta!', 'success');
                    } else {
                        showNotification('Wystąpił problem podczas usuwania recenzji.', 'danger');
                        console.error(data.error || 'Błąd serwera.');
                    }
                })
                .catch(error => {
                    showNotification('Wystąpił błąd połączenia z serwerem.', 'danger');
                    console.error('Błąd:', error);
                });
            }
        });
    });
	
    const voteButtons = document.querySelectorAll('.vote-btn');

    voteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const reviewId = this.dataset.reviewId;
            const vote = this.dataset.vote;

            fetch('/film-rating-app/actions/vote_review.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `review_id=${encodeURIComponent(reviewId)}&vote=${encodeURIComponent(vote)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error);
                    }
                })
                .catch(error => console.error('Błąd:', error));
        });
    });
		
	const registrationForm = document.getElementById("registrationForm");

	if (registrationForm) {
		const usernameInput = document.getElementById("username");
		const passwordInput = document.getElementById("password");
		const passwordRepeatInput = document.getElementById("password_repeat");

		let submissionAttempted = false;

		registrationForm.addEventListener("submit", function (event) {
			let hasError = false;

			registrationForm.querySelectorAll("input").forEach(input => {
				if (!input.checkValidity()) {
					hasError = true;
					input.classList.add("is-invalid");
					input.classList.remove("is-valid");
				} else {
					input.classList.remove("is-invalid");
					input.classList.add("is-valid");
				}
			});

			if (hasError) {
				event.preventDefault();
				submissionAttempted = true;
			} else {
				registrationForm.querySelectorAll("input").forEach(input => {
					input.classList.remove("is-valid", "is-invalid");
				});
			}
		});

		usernameInput.addEventListener("input", function () {
			const usernameValue = usernameInput.value;
			const usernameError = usernameInput.nextElementSibling;

			if (!usernameValue) {
				usernameInput.setCustomValidity("Proszę wprowadzić nazwę użytkownika.");
				usernameError.textContent = "Proszę wprowadzić nazwę użytkownika.";
			} else if (usernameValue.length < 3) {
				usernameInput.setCustomValidity("Nazwa użytkownika musi mieć co najmniej 3 znaki.");
				usernameError.textContent = "Nazwa użytkownika musi mieć co najmniej 3 znaki.";
			} else if (!/^[a-zA-Z0-9]+$/.test(usernameValue)) {
				usernameInput.setCustomValidity("Nazwa użytkownika nie może zawierać znaków specjalnych.");
				usernameError.textContent = "Nazwa użytkownika nie może zawierać znaków specjalnych.";
			} else {
				usernameInput.setCustomValidity("");
				usernameError.textContent = "";
			}

			updateInputValidationState(usernameInput);
		});

		passwordInput.addEventListener("input", function () {
			const passwordValue = passwordInput.value;
			const passwordError = passwordInput.nextElementSibling;

			if (!passwordValue) {
				passwordInput.setCustomValidity("Proszę wprowadzić hasło.");
				passwordError.textContent = "Proszę wprowadzić hasło.";
			} else if (passwordValue.length < 8) {
				passwordInput.setCustomValidity("Hasło musi mieć co najmniej 8 znaków.");
				passwordError.textContent = "Hasło musi mieć co najmniej 8 znaków.";
			} else {
				passwordInput.setCustomValidity("");
				passwordError.textContent = "";
			}

			updateInputValidationState(passwordInput);
			validatePasswordRepeat();
		});

		passwordRepeatInput.addEventListener("input", validatePasswordRepeat);

		function validatePasswordRepeat() {
			const passwordValue = passwordInput.value;
			const repeatValue = passwordRepeatInput.value;
			const repeatError = passwordRepeatInput.nextElementSibling;

			if (repeatValue !== passwordValue) {
				passwordRepeatInput.setCustomValidity("Hasła muszą być takie same.");
				repeatError.textContent = "Hasła muszą być takie same.";
			} else if (repeatValue.length < 8) {
				passwordRepeatInput.setCustomValidity("Powtórzone hasło musi mieć co najmniej 8 znaków.");
				repeatError.textContent = "Powtórzone hasło musi mieć co najmniej 8 znaków.";
			} else {
				passwordRepeatInput.setCustomValidity("");
				repeatError.textContent = "";
			}

			updateInputValidationState(passwordRepeatInput);
		}

		function updateInputValidationState(input) {
			if (input.checkValidity()) {
				input.classList.add("is-valid");
				input.classList.remove("is-invalid");
			} else {
				input.classList.add("is-invalid");
				input.classList.remove("is-valid");
			}
		}
	}
		
	const flashMessage = document.getElementById('flash-message');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.classList.remove('show');
            flashMessage.classList.add('fade');
            setTimeout(() => flashMessage.remove(), 500);
        }, 5000);
    }
		
	
	
});

function validateForm(form) {
	const title = form.querySelector('#title');
	const description = form.querySelector('#description');

	if (title.value.length > 20) {
		title.classList.add('is-invalid');
		title.nextElementSibling.innerText = 'Tytuł może zawierać maksymalnie 20 znaków.';
		return false;
	} else {
		title.classList.remove('is-invalid');
	}

	if (description.value.length > 200) {
		description.classList.add('is-invalid');
		description.nextElementSibling.innerText = 'Opis może zawierać maksymalnie 200 znaków.';
		return false;
	} else {
		description.classList.remove('is-invalid');
	}

	return true;
	}
	
function openManageCategoriesModal() {
    const addCategoryForm = document.getElementById('addCategoryForm');
    const categoryNameInput = document.getElementById('category_name');
    const categoryError = document.getElementById('categoryError');

    addCategoryForm.reset();
    addCategoryForm.classList.remove('was-validated');
    categoryNameInput.classList.remove('is-invalid', 'is-valid');
    categoryError.textContent = 'Proszę podać poprawną nazwę kategorii (tylko litery, max. 15 znaków).';

    document.getElementById('manageCategoriesModal').style.display = 'block';
}

function closeManageCategoriesModal() {
    const addCategoryForm = document.getElementById('addCategoryForm');

    addCategoryForm.reset();
    addCategoryForm.classList.remove('was-validated');

    document.getElementById('manageCategoriesModal').style.display = 'none';
}

function submitDeleteCategory() {
    const form = document.getElementById('deleteCategoryForm');
    const deleteCategorySelect = document.getElementById('delete_category');

    deleteCategorySelect.setCustomValidity('');

    if (!deleteCategorySelect.value) {
        deleteCategorySelect.setCustomValidity('Proszę wybrać kategorię do usunięcia.');
        form.classList.add('was-validated');
        return;
    }

    fetch('/film-rating-app/actions/delete_category_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `name=${encodeURIComponent(deleteCategorySelect.options[deleteCategorySelect.selectedIndex].text)}`,
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const dropdowns = [
                    document.getElementById('add_category'),
                    document.getElementById('delete_category'),
                    document.getElementById('edit_category'),
                ];

                dropdowns.forEach(dropdown => {
                    if (dropdown) {
                        const optionToRemove = Array.from(dropdown.options).find(
                            option => option.text === data.name
                        );
                        if (optionToRemove) {
                            optionToRemove.remove();
                        }
                    }
                });

                form.reset();
                form.classList.remove('was-validated');
                deleteCategorySelect.setCustomValidity('');
                showAlert(data.message, 'success');
            } else {
                deleteCategorySelect.setCustomValidity(data.error || 'Nie udało się usunąć kategorii.');
                form.classList.add('was-validated');
            }
        })
        .catch(error => {
            console.error('Błąd podczas usuwania kategorii:', error);
            deleteCategorySelect.setCustomValidity('Wystąpił problem z połączeniem.');
            form.classList.add('was-validated');
        });
}



function submitAddCategory() {
    const form = document.getElementById('addCategoryForm');
    const categoryNameInput = document.getElementById('category_name');
    const categoryError = document.getElementById('categoryError');

    categoryNameInput.setCustomValidity('');
    categoryNameInput.classList.remove('is-invalid', 'is-valid');
    categoryError.textContent = 'Proszę podać poprawną nazwę kategorii (tylko litery, max. 15 znaków).';

    const categoryName = categoryNameInput.value.trim();
    const regex = /^[A-Za-zĄąĆćĘęŁłŃńÓóŚśŹźŻż\s]{1,15}$/;

    if (!regex.test(categoryName)) {
        categoryNameInput.setCustomValidity('Invalid');
        categoryNameInput.classList.add('is-invalid');
        form.classList.add('was-validated');
        return;
    }

    fetch('/film-rating-app/actions/add_category_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `name=${encodeURIComponent(categoryName)}`
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                categoryNameInput.classList.add('is-valid');

                updateCategoryDropdowns(data.id, data.name);

                showAlert('Kategoria została pomyślnie dodana!', 'success');

                form.reset();
                form.classList.remove('was-validated');

                closeManageCategoriesModal();
            } else {
                categoryNameInput.setCustomValidity('Invalid');
                categoryNameInput.classList.add('is-invalid');
                categoryError.textContent = data.error || 'Nie udało się dodać kategorii.';
                form.classList.add('was-validated');
            }
        })
        .catch(error => {
            console.error('Błąd podczas dodawania kategorii:', error);
            categoryNameInput.setCustomValidity('Invalid');
            categoryNameInput.classList.add('is-invalid');
            categoryError.textContent = 'Wystąpił problem z połączeniem.';
            form.classList.add('was-validated');
        });
}

function updateCategoryDropdowns(id, name) {
    const dropdowns = [
        document.getElementById('add_category'),
        document.getElementById('delete_category'),
        document.getElementById('edit_category'),
    ];

    dropdowns.forEach(dropdown => {
        if (dropdown) {
            const newOption = document.createElement('option');
            newOption.value = id;
            newOption.textContent = name;
            dropdown.appendChild(newOption);

            dropdown.classList.remove('is-invalid');
            dropdown.setCustomValidity('');
        }
    });

    const deleteCategoryForm = document.getElementById('deleteCategoryForm');
    if (deleteCategoryForm) {
        deleteCategoryForm.classList.remove('was-validated');
    }
}

	function showAlert(message, type) {
		const alertContainer = document.getElementById('alert-container') || createAlertContainer();
		const alert = document.createElement('div');
		alert.className = `alert alert-${type} alert-dismissible fade show custom-alert`;
		alert.role = 'alert';
		alert.innerHTML = `
			${message}
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		`;

		alertContainer.appendChild(alert);

		setTimeout(() => {
			alert.classList.remove('show');
			alert.classList.add('fade');
			setTimeout(() => alert.remove(), 500);
		}, 5000);
	}

	function createAlertContainer() {
		const container = document.createElement('div');
		container.id = 'alert-container';
		container.style.position = 'fixed';
		container.style.top = '20px';
		container.style.right = '20px';
		container.style.zIndex = '1050';
		document.body.appendChild(container);
		return container;
	}

	document.addEventListener('DOMContentLoaded', function () {
		if (window.location.pathname.includes('admin_panel')) {
			console.log("Walidacja aktywna dla admin_panel");

			const addFilmForm = document.getElementById('addFilmForm');
			const allowedExtensions = ["jpg", "jpeg", "png"];

			if (addFilmForm) {
				addFilmForm.setAttribute('novalidate', true);

				addFilmForm.querySelectorAll('input, select, textarea').forEach(input => {
					input.addEventListener('input', function () {
						if (input.classList.contains('is-invalid') && input.checkValidity()) {
							input.classList.remove('is-invalid');
							input.classList.add('is-valid');
						}
					});
				});

				addFilmForm.querySelectorAll('input[type="file"]').forEach(input => {
					input.addEventListener('change', function () {
						const file = input.files[0];
						const fileExtension = file ? file.name.split('.').pop().toLowerCase() : "";

						if (file && !allowedExtensions.includes(fileExtension)) {
							input.setCustomValidity("Nieprawidłowy format pliku. Dozwolone formaty: JPG, JPEG, PNG.");
							input.classList.add('is-invalid');
							input.classList.remove('is-valid');
						} else {
							input.setCustomValidity("");
							input.classList.remove('is-invalid');
							if (file) {
								input.classList.add('is-valid');
							}
						}
					});
				});

				addFilmForm.addEventListener('submit', function (event) {
					let isFormValid = true;

					addFilmForm.querySelectorAll('input, select, textarea').forEach(input => {
						if (input.type === 'file') {
							const file = input.files[0];
							const fileExtension = file ? file.name.split('.').pop().toLowerCase() : "";

							if (file && !allowedExtensions.includes(fileExtension)) {
								input.setCustomValidity("Nieprawidłowy format pliku. Dozwolone formaty: JPG, JPEG, PNG.");
								input.classList.add('is-invalid');
								input.classList.remove('is-valid');
								isFormValid = false;
							}
						} else if (!input.checkValidity()) {
							input.classList.add('is-invalid');
							input.classList.remove('is-valid');
							isFormValid = false;
						}
					});

					if (!isFormValid) {
						event.preventDefault();
						event.stopPropagation();
					}
				});
			}
		}
	});

document.addEventListener('DOMContentLoaded', function () {
    if (window.location.pathname.includes('admin_panel')) {
        console.log("Walidacja aktywna dla admin_panel (formularz edycji)");

        const editFilmForm = document.getElementById('editFilmForm');
        const allowedExtensions = ["jpg", "jpeg", "png"];

        if (editFilmForm) {
            editFilmForm.setAttribute('novalidate', true);

            editFilmForm.querySelectorAll('input, select, textarea').forEach(input => {
                input.addEventListener('input', function () {
                    if (input.classList.contains('is-invalid') && input.checkValidity()) {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid'); 
                    }
                });
            });

            const posterInput = document.getElementById('poster');
            if (posterInput) {
                posterInput.addEventListener('change', function () {
                    const file = posterInput.files[0];
                    const fileExtension = file ? file.name.split('.').pop().toLowerCase() : "";

                    if (file && !allowedExtensions.includes(fileExtension)) {
                        posterInput.setCustomValidity("Nieprawidłowy format pliku. Dozwolone formaty: JPG, JPEG, PNG.");
                        posterInput.classList.add('is-invalid');
                        posterInput.classList.remove('is-valid');
                    } else {
                        posterInput.setCustomValidity("");
                        posterInput.classList.remove('is-invalid');
                        if (file) {
                            posterInput.classList.add('is-valid');
                        }
                    }
                });
            }

            editFilmForm.addEventListener('submit', function (event) {
                let isFormValid = true;

                editFilmForm.querySelectorAll('input, select, textarea').forEach(input => {
                    if (input.type === 'file') {
                        const file = input.files[0];
                        const fileExtension = file ? file.name.split('.').pop().toLowerCase() : "";

                        if (file && !allowedExtensions.includes(fileExtension)) {
                            input.setCustomValidity("Nieprawidłowy format pliku. Dozwolone formaty: JPG, JPEG, PNG.");
                            input.classList.add('is-invalid');
                            input.classList.remove('is-valid');
                            isFormValid = false;
                        }
                    } else if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                        input.classList.remove('is-valid');
                        isFormValid = false;
                    }
                });

                if (!isFormValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        }
    }
});

        window.openEditModal = function (filmData) {
            const inputs = document.querySelectorAll('#editFilmForm .form-control');
            inputs.forEach(input => {
                input.classList.remove('is-invalid');
                input.classList.remove('is-valid');
                input.setCustomValidity('');
            });
		
            document.getElementById("filmId").value = filmData.id;
            document.getElementById("title").value = filmData.title;
            document.getElementById("description").value = filmData.description;
            document.getElementById("release_year").value = filmData.release_year;

            const categoryElement = document.getElementById("edit_category");
            if (categoryElement) {
                categoryElement.value = filmData.category || "";
            }

            const durationElement = document.getElementById("edit_duration");
            if (durationElement) {
                durationElement.value = filmData.duration || "";
            }

            const posterInput = document.getElementById("poster");
            if (posterInput) {
                posterInput.value = "";
                posterInput.setCustomValidity("");
                posterInput.classList.remove("is-invalid");
                posterInput.classList.remove("is-valid");
            }

            const posterPreview = document.getElementById("posterPreview");
            if (posterPreview) {
                posterPreview.src = `/film-rating-app/${filmData.poster}`;
            }

            document.getElementById("editModal").style.display = "block";
        };


        const deleteButtons = document.querySelectorAll('.delete-user-button');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                const userId = this.dataset.userId;

                if (confirm('Czy na pewno chcesz usunąć tego użytkownika?')) {
                    fetch('/film-rating-app/actions/delete_user_action.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `user_id=${encodeURIComponent(userId)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
                            if (userRow) {
                                userRow.remove();
                            }
                            showAlert(data.message, 'success');
                        } else {
                            showAlert(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Błąd:', error);
                        showAlert('Wystąpił błąd podczas usuwania użytkownika.', 'danger');
                    });
                }
            });
        });
		
		const blockButtons = document.querySelectorAll('.toggle-block-user-button');

blockButtons.forEach(button => {
    button.addEventListener('click', function (event) {
        event.preventDefault();

        const userId = this.dataset.userId;
        const isBlocked = this.dataset.isBlocked === "1" ? 0 : 1;

        if (confirm(isBlocked ? 'Czy na pewno chcesz zablokować tego użytkownika?' : 'Czy na pewno chcesz odblokować tego użytkownika?')) {
            fetch('/film-rating-app/actions/toggle_block_user_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `user_id=${encodeURIComponent(userId)}&is_blocked=${encodeURIComponent(isBlocked)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
                    if (userRow) {
                        const statusCell = userRow.querySelector('td:nth-child(3)');
                        const blockButton = userRow.querySelector('.toggle-block-user-button');

                        if (isBlocked) {
                            statusCell.innerHTML = '<span class="text-danger">Zablokowany</span>';
                            blockButton.textContent = 'Odblokuj';
                        } else {
                            statusCell.innerHTML = '<span class="text-success">Aktywny</span>';
                            blockButton.textContent = 'Zablokuj';
                        }

                        blockButton.dataset.isBlocked = isBlocked;
                    }
                    showAlert(data.message, 'success');
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Błąd:', error);
                showAlert('Wystąpił błąd podczas zmiany statusu użytkownika.', 'danger');
            });
        }
    });
});
		
     const alerts = document.querySelectorAll('.alert');

    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 500);
        }, 5000);

        const closeButton = alert.querySelector('.close');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            });
        }
});
