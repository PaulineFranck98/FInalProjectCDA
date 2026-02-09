document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("filtersForm");
    const container = document.getElementById("itinerariesContainer");
    const pageInput = document.getElementById("pageInput");
    const url = window.location.pathname;
    if (!form || !container) return;

    function getFormParams() {
        const formData = new FormData(form);
        return new URLSearchParams(formData).toString();
    }

    async function fetchResults() {
        const queryString = getFormParams();

        container.innerHTML = `
            <div class="flex flex-col items-center justify-center text-violet-700 py-8 w-full">
                <i class="fa-solid fa-circle-notch fa-spin text-2xl mb-2"></i>
                <span>Chargement...</span>
            </div>
        `;

        try {
            const response = await fetch(`${url}?${queryString}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            const html = await response.text();

            container.innerHTML = html;

            initPagination();
        } catch (error) {
            console.error("Erreur AJAX:", error);
            container.innerHTML = `
                <p class="text-center text-red-500 py-8">
                    Une erreur est survenue. Veuillez réessayer.
                </p>
            `;
        }
    }

    function initPagination() {
        const links = container.querySelectorAll("nav a[data-page]");
        links.forEach(link => {
            link.addEventListener("click", (e) => {
                e.preventDefault();
                if (pageInput) pageInput.value = link.dataset.page || "1";
                fetchResults();
            });
        });
    }

    form.querySelectorAll("input, select").forEach(input => {
        input.addEventListener("input", () => {
            clearTimeout(window._filterTimeout);
            if (pageInput) pageInput.value = "1";
            window._filterTimeout = setTimeout(fetchResults, 300);
        });
    });

    initPagination();
});
