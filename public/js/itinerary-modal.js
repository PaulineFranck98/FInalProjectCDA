document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('itinerary-modal');
    const openBtn = document.getElementById('add-to-itinerary');
    const closeBtn = document.getElementById('close-itinerary-modal');
    const itineraryList = document.getElementById('itinerary-list');

    if (!openBtn) return; // sécurité

    openBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        modal.classList.remove('hidden');

        const locationId = e.target.dataset.locationId;

        try {
            const response = await fetch('/api/itineraries');
            if (!response.ok) throw new Error('Erreur de récupération');
            const itineraries = await response.json();

            if (itineraries.length === 0) {
                itineraryList.innerHTML = `
                    <p class="text-gray-600 mb-3">Vous n’avez encore aucun itinéraire.</p>
                    <a href="/itinerary/new?locationId=${locationId}" 
                       class="text-violet-700 underline">Créer un itinéraire</a>
                `;
            } else {
                itineraryList.innerHTML = itineraries.map(it => `
                    <div class="flex justify-between items-center border-b py-2">
                        <span>${it.itineraryName}</span>
                        <a href="/itinerary/${it.id}/add-location/${locationId}" 
                           class="text-violet-700 hover:text-violet-900 underline">
                            Ajouter
                        </a>
                    </div>
                `).join('');
            }
        } catch (error) {
            itineraryList.innerHTML = `<p class="text-red-600">Erreur : ${error.message}</p>`;
        }
    });

    closeBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // clic à l’extérieur pour fermer
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
