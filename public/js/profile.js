document.addEventListener('DOMContentLoaded', () => {
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.target;
            const display = document.getElementById(`display-${target}`);
            const form = document.getElementById(`form-${target}`);

            display.classList.toggle('hidden');
            form.classList.toggle('hidden');
        });
    });

    const cancelButtons = document.querySelectorAll('.cancel-btn');
    cancelButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const form = e.target.closest('form');
            const display = document.getElementById(`display-${form.dataset.field}`);
            form.classList.add('hidden');
            display.classList.remove('hidden');
        });
    });

    document.querySelectorAll('#form-username, #form-email').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const field = form.dataset.field;
            const value = form.querySelector('input').value;

            const response = await fetch(`/user/profile/update-${field}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ [field]: value })
            });

            const result = await response.json();
            if (result.success) {
                document.getElementById(`display-${field}`).textContent = value;
                form.classList.add('hidden');
                document.getElementById(`display-${field}`).classList.remove('hidden');
            } else {
                alert(result.error || "Erreur lors de la mise à jour.");
            }
        });
    });

    const uploadInput = document.getElementById('profile-upload');
    const profileImg = document.getElementById('profile-img');

    if (uploadInput) {
        uploadInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('profilePicture', file);

            try {
                const response = await fetch('/user/profile/update-picture', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    profileImg.src = result.newPath;
                } else {
                    alert(result.error || "Erreur lors de la mise à jour de la photo.");
                }
            } catch (err) {
                alert("Erreur réseau ou serveur.");
                console.error(err);
            }
        });
    }

    document.getElementById('delete-account-btn').addEventListener('click', () => {
        document.getElementById('delete-modal').classList.remove('hidden');
    });

    document.getElementById('cancel-delete').addEventListener('click', () => {
        document.getElementById('delete-modal').classList.add('hidden');
    });
});