document.addEventListener('click', async (e) => {
    const button = e.target.closest('.favorite-btn');
    if (!button) return;

    const itineraryId = button.dataset.itineraryId;
    const icon = button.querySelector('i');

    try {
        const response = await fetch(`/user/favorite/itinerary/${itineraryId}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        const data = await response.json();
        if (data.success) {

            const count = document.getElementById('favorites-count');

            if (data.isFavorite) {
                icon.classList.remove('fa-regular', 'text-gray-400');
                icon.classList.add('fa-solid', 'text-violet-600');
                button.title = 'Retirer des favoris';
            } else {
                icon.classList.remove('fa-solid', 'text-violet-600');
                icon.classList.add('fa-regular', 'text-gray-400');
                button.title = 'Ajouter aux favoris';
            }

            if (count) {
                count.textContent = data.favoritesCount;
            }

        }
    } catch (error) {
        console.error('Erreur favoris :', error);
    }
});
