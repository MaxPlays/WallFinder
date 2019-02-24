var mymap = L.map('map').setView([49.4497, 11.0682], 12);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWF4cGxheXMiLCJhIjoiY2pydW16M2NhMHg4bTQzcHExZXo1NmZ6cyJ9.pP7oLqWGYW-H8qE3uD71XA', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox.streets',
    accessToken: ''
}).addTo(mymap);

mymap.locate({setView: true});

/*function onLocationFound(e) {
    var radius = e.accuracy / 2;

    L.marker(e.latlng).addTo(mymap).bindPopup("Your location");
}

mymap.on('locationfound', onLocationFound);*/

function onLocationError(e) {
    alert(e.message);
}

mymap.on('locationerror', onLocationError);
