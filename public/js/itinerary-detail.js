document.addEventListener('DOMContentLoaded', () => {
    const mapDiv = document.getElementById('itinerary-map');
    if (!mapDiv) return;

    const locations = JSON.parse(mapDiv.dataset.locations || '[]');

    let center = [7.75, 48.58]; // défaut : Strasbourg
    if (
        locations.length > 0 &&
        locations[0].data &&
        locations[0].data.longitude &&
        locations[0].data.latitude
    ) {
        center = [locations[0].data.longitude, locations[0].data.latitude];
    }

    const map = new maplibregl.Map({
        container: 'itinerary-map',
        style: "https://api.jawg.io/styles/jawg-lagoon.json?access-token=5Hj1eNrhxICEmCUA3n50cFITkKNPsXWcZFw3JKMebObcRBGRIBQnT1RQlRQ4RRoG",
        zoom: 10,
        center: center
    });

    maplibregl.setRTLTextPlugin(
        'https://unpkg.com/@mapbox/mapbox-gl-rtl-text@0.3.0/dist/mapbox-gl-rtl-text.js'
    );

    const bounds = new maplibregl.LngLatBounds();
    const markers = {};


    function createMarkers() {
        locations.forEach(item => {
            const loc = item.data; 
            const hasGeo = loc.geo && Array.isArray(loc.geo.coordinates);
            if (!hasGeo) return;

            const [lon, lat] = loc.geo.coordinates;
            bounds.extend([lon, lat]);

            const element = document.createElement('div');
            element.classList.add('map-marker');
            element.style.backgroundImage = 'url("/img/regular-pin-small.png")';
            element.style.width = "45px";
            element.style.height = "45px";
            element.style.backgroundSize = "100%";
            element.style.cursor = "pointer";

            const popup = new maplibregl.Popup({
                offset: [0, -48],
                closeButton: false,
                className: 'custom-popup'
            }).setHTML(`
                <div class="popup-content">
                    <div class="popup-header">
                        <span class="name">${loc.locationName}</span>
                        ${loc.city ? `<span class="city"> — ${loc.city}</span>` : ''}
                    </div>
                    <div class="popup-header">
                        ${loc.type?.typeName ? `<span class="type">${loc.type.typeName}</span>` : ''}
                        ${loc.averageRating ? `<span class="rating"><i class="fa-solid fa-star"></i> ${Number(loc.averageRating).toFixed(1)}</span>` : ''}
                    </div>
                </div>
            `);

            const marker = new maplibregl.Marker({ element, anchor: 'bottom' })
                .setLngLat([lon, lat])
                .setPopup(popup)
                .addTo(map);

            element.addEventListener('mouseenter', () => marker.togglePopup());
            element.addEventListener('mouseleave', () => marker.togglePopup());

            markers[loc.id] = { marker, popup, element };
        });
    }

    function updateRouteLine() {

        const coords = locations
            .slice() 
            .sort((a, b) => a.order - b.order)
            .filter(item => item.data.geo && Array.isArray(item.data.geo.coordinates))
            .map(item => {
                const [lon, lat] = item.data.geo.coordinates;
                return [lon, lat];
            });

     
        if (coords.length < 2) {
            if (map.getSource('itinerary-route')) {
                const emptyGeoJSON = {
                    type: "FeatureCollection",
                    features: []
                };
                map.getSource('itinerary-route').setData(emptyGeoJSON);
            }
            return;
        }

        const routeGeoJSON = {
            type: "FeatureCollection",
            features: [
                {
                    type: "Feature",
                    geometry: {
                        type: "LineString",
                        coordinates: coords
                    },
                    properties: {}
                }
            ]
        };

        if (map.getSource('itinerary-route')) {
            map.getSource('itinerary-route').setData(routeGeoJSON);
        } else {
      
            map.addSource('itinerary-route', {
                type: 'geojson',
                data: routeGeoJSON
            });

            map.addLayer({
                id: 'itinerary-route-line',
                type: 'line',
                source: 'itinerary-route',
                layout: {
                    'line-cap': 'round',
                    'line-join': 'round'
                },
                paint: {
                    'line-color': '#8146CC',
                    'line-width': 4,
                    'line-opacity': 0.8
                }
            });
        }
    }

    map.on('load', () => {
        createMarkers();
        updateRouteLine();

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
    });

    window.updateItineraryOrderOnMap = function(newOrderIdsInDomOrder) {

        newOrderIdsInDomOrder.forEach((locId, index) => {
            const match = locations.find(entry => entry.data.id === locId || entry.id === locId);
            if (match) {
                match.order = index; 
            }
        });

        updateRouteLine();
    };
});
