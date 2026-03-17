import './bootstrap';

// Product page: rating selector (runs only when present)
document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.rating-star');
    if (!stars.length) return;
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    function updateStarDisplay(rating) {
        stars.forEach(function (star, index) {
            var starRating = 5 - index;
            var icon = star.querySelector('i');
            if (starRating <= rating) {
                icon.classList.remove('far', 'text-slate-300');
                icon.classList.add('fas', 'text-amber-400');
            } else {
                icon.classList.remove('fas', 'text-amber-400');
                icon.classList.add('far', 'text-slate-300');
            }
        });
    }
    stars.forEach(function (star) {
        star.addEventListener('click', function () {
            var rating = this.dataset.rating;
            ratingInputs.forEach(function (input) {
                if (input.value <= rating) input.checked = true;
            });
            updateStarDisplay(rating);
        });
        star.addEventListener('mouseenter', function () {
            updateStarDisplay(this.dataset.rating);
        });
    });
    var ratingSelector = document.getElementById('rating-selector');
    if (ratingSelector) {
        ratingSelector.addEventListener('mouseleave', function () {
            var checked = document.querySelector('input[name="rating"]:checked');
            updateStarDisplay(checked ? checked.value : 0);
        });
    }
});
