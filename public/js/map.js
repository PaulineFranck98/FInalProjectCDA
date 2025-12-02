document.addEventListener('DOMContentLoaded', function () {
	// var map = L.map('map').setView([51.505, -0.09], 13);
	// L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
	//     maxZoom: 19,
	//     attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
	// }).addTo(map);
	const map = new maplibregl.Map({
		container: 'map',
		style: "https://api.jawg.io/styles/jawg-lagoon.json?access-token=5Hj1eNrhxICEmCUA3n50cFITkKNPsXWcZFw3JKMebObcRBGRIBQnT1RQlRQ4RRoG",
		zoom: 3,
		center: [0, 0]
	});
	maplibregl.setRTLTextPlugin(
		'https://unpkg.com/@mapbox/mapbox-gl-rtl-text@0.3.0/dist/mapbox-gl-rtl-text.js'
	);
});