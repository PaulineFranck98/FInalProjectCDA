function initVisitedModal() {
    const modal = document.getElementById('visited-modal');
    const closeBtn = document.getElementById('close-visited-modal');
    const confirmBtn = document.getElementById('confirm-visited');
    const cancelBtn = document.getElementById('cancel-visited');
    const dateInput = document.getElementById('visited-date');
    let currentLocationId = null;

    document.querySelectorAll('.visited-switch').forEach(checkbox => {
        checkbox.addEventListener('change', async () => {
            currentLocationId = checkbox.dataset.locationId;

            if (checkbox.checked) {

                modal.classList.remove('hidden');
                dateInput.value = '';
            } else {

                await fetch(`/user/visited/location/${currentLocationId}`, { method: 'DELETE' });
            }
        });
    });

    confirmBtn.addEventListener('click', async () => {
        const date = dateInput.value;
        if (!date) {
            alert('Veuillez choisir une date.');
            return;
        }

        await fetch(`/user/visited/location/${currentLocationId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ visitedAt: date })
        });

        modal.classList.add('hidden');
    });


    const closeModal = () => {
        modal.classList.add('hidden');
        const checkbox = document.querySelector(`.visited-switch[data-location-id="${currentLocationId}"]`);
        if (checkbox) checkbox.checked = false;
    };

    cancelBtn.addEventListener('click', closeModal);
    closeBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
}

document.addEventListener('DOMContentLoaded', initVisitedModal);
