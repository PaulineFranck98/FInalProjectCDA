document.addEventListener('DOMContentLoaded', () => {
    const list = document.querySelector('.sortable-locations');
    if (!list) return;

    const itineraryId = list.dataset.itineraryId;

    new Sortable(list, {
        animation: 150,
        handle: '.location-item', 
        ghostClass: 'bg-violet-100',
        onEnd: async function () {
           
            const orderedIds = Array.from(list.querySelectorAll('.location-item')).map(element => element.dataset.locationId);
    
            list.querySelectorAll('.location-item').forEach((element, index) => {
                const indexSpan = element.querySelector('.location-index');
                if (indexSpan) {
                    indexSpan.textContent = index + 1;
                }
            });

            if (window.updateItineraryOrderOnMap) {
                window.updateItineraryOrderOnMap(orderedIds);
            }
            try {
                const response = await fetch(`/itinerary/${itineraryId}/reorder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ order: orderedIds }),
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || 'Erreur serveur');
                }
            } catch (e) {
                console.error('Erreur lors de la mise à jour de l’ordre', e);
                alert("Une erreur est survenue lors de la mise à jour de l’ordre.");
            }
        }
    });
});
