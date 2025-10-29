document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('itinerary-modal');
    const closeBtn = document.getElementById('close-itinerary-modal');
    const itineraryList = document.getElementById('itinerary-list');

    const addButtons = document.querySelectorAll('#add-to-itinerary, .add-to-itinerary');

    addButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const locationId = button.dataset.locationId;
            modal.classList.remove('hidden');

            itineraryList.innerHTML = `<p class="text-gray-600">Chargement...</p>`;

            try {
                const response = await fetch('/api/itineraries');
                const itineraries = await response.json();

                if (!Array.isArray(itineraries) || itineraries.length === 0) {
                    itineraryList.innerHTML = `
                        <p class="text-gray-600">Vous n’avez encore aucun itinéraire.</p>
                        <a href="/itinerary/new?locationId=${locationId}" 
                           class="text-violet-700 underline">Créer un itinéraire</a>
                    `;
                } else {
                    itineraryList.innerHTML = itineraries.map(it => `
                        <div class="flex justify-between items-center border-b py-2">
                            <span>${it.itineraryName}</span>
                            <a href="/itinerary/${it.id}/add-location/${locationId}" 
                               class="text-violet-700 underline hover:text-violet-900">
                               Ajouter
                            </a>
                        </div>
                    `).join('');
                }
            } catch (error) {
                itineraryList.innerHTML = `<p class="text-red-600">Erreur lors du chargement des itinéraires.</p>`;
            }
        });
    });

    closeBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });


    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
    });
});
