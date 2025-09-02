

function toggleFilters() {
    const moreFilters = document.getElementById('more-filters');
    moreFilters.classList.toggle('hidden');
    if (!moreFilters.classList.contains('hidden')) {
        moreFilters.classList.add('flex', 'flex-col', 'gap-4', 'mt-4');
    } else {
        moreFilters.classList.remove('flex', 'flex-col', 'gap-4', 'mt-4');
    }
}

function initMap() {
    const mapDiv = document.getElementById('search-map');
    const locations = JSON.parse(mapDiv.dataset.locations);

    let center = [7.75, 48.58];
    if (locations.length > 0) {
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
   
    

    locations.forEach(location => {
        if (location.latitude && location.longitude) {
            const element = document.createElement('div');
            element.style.backgroundImage = 'url("/img/regular-pin-small.png")';
            element.style.width = "45px";
            element.style.height = "45px";
            element.style.backgroundSize = "100%";

            new maplibregl.Marker({element: element})
                .setLngLat([location.longitude, location.latitude])
                .setPopup(new maplibregl.Popup().setText(location.locationName))
                .addTo(map);
        }
    });
}

document.addEventListener('turbo:load', function() {
    // document.getElementById('show-more-filters').addEventListener('click', toggleFilters);
    initMap();
});