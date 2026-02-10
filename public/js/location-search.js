function toggleFilters() {

    const filtersPanel = document.getElementById("filtersPanel");
    const openBtn = document.getElementById("openFilters");
    const closeBtn = document.getElementById("closeFilters");

    openBtn?.addEventListener("click", () => filtersPanel.classList.remove("translate-x-full"));
    closeBtn?.addEventListener("click", () => filtersPanel.classList.add("translate-x-full"));
}

function initMap() {
    const mapDiv = document.getElementById('search-map');
    const locations = JSON.parse(mapDiv.dataset.locations || '[]');

    let center = [7.75, 48.58];
    if (locations.length > 0 && locations[0].longitude && locations[0].latitude) {
        center = [locations[0].longitude, locations[0].latitude];
    }

    const map = new maplibregl.Map({
        container: 'search-map',
        style: "https://api.jawg.io/styles/jawg-lagoon.json?access-token=5Hj1eNrhxICEmCUA3n50cFITkKNPsXWcZFw3JKMebObcRBGRIBQnT1RQlRQ4RRoG",
        zoom: 12,
        center: center
    });

    maplibregl.setRTLTextPlugin(
        'https://unpkg.com/@mapbox/mapbox-gl-rtl-text@0.3.0/dist/mapbox-gl-rtl-text.js'
    );

    const bounds = new maplibregl.LngLatBounds();
    const markers = {};

    locations.forEach(location => {
        const hasGeo = location.geo && Array.isArray(location.geo.coordinates);
        if (hasGeo) {
            const [lon, lat] = location.geo.coordinates;
            bounds.extend([lon, lat]);

            const element = document.createElement('div');
            element.style.backgroundImage = 'url("/img/regular-pin-small.png")';
            element.style.width = "45px";
            element.style.height = "45px";
            element.style.backgroundSize = "100%";

            const popup = new maplibregl.Popup({
                offset: 25,
                closeButton: false,
                className: 'custom-popup'
            }).setHTML(`
                <div class="popup-content">
                    <div class="popup-header">
                        <a class="name"  href="/location/${location.id}">${location.locationName}</a>
                        ${location.city ? `<span class="city"> — ${location.city}</span>` : ''}
                    </div>
                    <div class="popup-header">
                        ${location.type?.typeName ? `<span class="type">${location.type.typeName}</span>` : ''}
                        ${location.averageRating ? `<span class="rating"><i class="fa-solid fa-star"></i> ${location.averageRating.toFixed(1)}</span>` : ''}
                    </div>
                </div>
            `);

            const marker = new maplibregl.Marker({ element })
                .setLngLat([lon, lat])
                .setPopup(popup)
                .addTo(map);

            element.addEventListener('mouseenter', () => marker.togglePopup());
            element.addEventListener('mouseleave', () => marker.togglePopup());

            markers[location.id] = { marker, popup, element };
        }
    });

    if (!bounds.isEmpty()) {
        map.fitBounds(bounds, {
            padding: 50,
            maxZoom: 14,
            duration: 800,
        });
    }

    const cards = document.querySelectorAll('[data-location-id]');
    cards.forEach(card => {
        const id = card.dataset.locationId;
        const item = markers[id];
        if (!item) return;

        card.addEventListener('mouseenter', () => item.marker.togglePopup());
        card.addEventListener('mouseleave', () => item.marker.togglePopup());
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initMap();
    toggleFilters();
});