document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.remove-visited-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.locationId;
            if (!confirm('Voulez-vous retirer ce lieu de vos lieux déjà visités ?')) return;

            try {
                const response = await fetch(`/visited/location/${id}`, {
                    method: 'DELETE',
                });

                if (response.ok) {
                    // pour supprimer la carte visuellement
                    btn.closest('.bg-white').remove();
                } else {
                    await response.json();
                }
            } catch (error) {
                console.error(error);              
            }
        });
    });
});
