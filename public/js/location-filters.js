document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("filtersForm");
    const container = document.getElementById("locationsContainer");
    const mapDiv = document.getElementById("search-map");
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
            <div id="loader" class="flex flex-col items-center justify-center text-violet-700 py-8 w-full">
                <i class="fa-solid fa-circle-notch fa-spin text-2xl mb-2"></i>
                <span>Chargement...</span>
            </div>
        `;

        try {
            const response = await fetch(`${url}?${queryString}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });

            const html = await response.text();

            // j'extrais la partie du HTML qui contient les lieux
            const temp = document.createElement("div");
            temp.innerHTML = html;

            const newContainer = temp.querySelector("#locationsContainer");


            if (newContainer) {
                container.innerHTML = newContainer.innerHTML;

                initPagination();
                
                if (typeof initItineraryModal === "function") {
                    initItineraryModal();
                }
            }

            const newLocationsData = newContainer?.dataset.locations;
            if (newLocationsData && mapDiv) {
                mapDiv.dataset.locations = newLocationsData;
                initMap();
            }
        } catch (error) {
            console.error("Erreur AJAX:", error);
            container.innerHTML = `
                <p class="text-center text-red-500 py-8">Une erreur est survenue. Veuillez réessayer.</p>
            `;
        }
    }
    
    function initPagination() {
        const paginationLinks = container.querySelectorAll("nav a");
        paginationLinks.forEach(link => {
            link.addEventListener("click", (e) => {
                e.preventDefault();
                const urlObj = new URL(link.href);
                const newPage = urlObj.searchParams.get("page") || 1;

                // je mets à jour la valeur du champ caché
                if (pageInput) pageInput.value = newPage;

                fetchResults();
            });
        });
    }

    // retourne à la page 1 quand le filtre change
    form.querySelectorAll("input").forEach(input => {
        const eventName = input.type === "range" || input.type === "text" ? "input" : "change";
        input.addEventListener(eventName, () => {
            if (pageInput) pageInput.value = 1;
            clearTimeout(window._filterTimeout);
            window._filterTimeout = setTimeout(fetchResults, 300);
        });
    });

    initPagination();
});
