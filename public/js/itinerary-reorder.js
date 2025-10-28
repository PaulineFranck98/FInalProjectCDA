document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('itinerary-locations');
    if (!list) return;

    const itineraryId = list.dataset.itineraryId;

    new Sortable(list, {
        animation: 150,
        handle: '.location-item', // zone draggable
        ghostClass: 'bg-violet-100',
        onEnd: async function () {
            const order = Array.from(list.querySelectorAll('.location-item'))
                               .map(el => el.dataset.locationId);

            try {
                const response = await fetch(`/itinerary/${itineraryId}/reorder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ order }),
                });

                const result = await response.json();
                if (!response.ok) throw new Error(result.error || 'Erreur serveur');

                console.log('Nouvel ordre sauvegardé :', order);
            } catch (error) {
                console.error('Erreur lors de la mise à jour de l’ordre', error);
                alert("Une erreur est survenue lors de la mise à jour de l’ordre.");
            }
        }
    });
});
