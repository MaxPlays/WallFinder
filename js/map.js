var mymap = L.map('map', {
  preferCanvas: true
}).setView([49.4497, 11.0682], 12);

var popup = '<strong>Title: </strong><br><span id="wall-title">:title:</span><div class="mt-1"><strong>Description: </strong><br><span id="wall-description">:description:</span></div><div class="mt-3"><strong>Score: </strong><span id="wall-score">:score:</span></div><div class="mt-1"><strong>Vote: </strong><img class="wall-upvote" src="res/upvote.svg"></img> <img class="wall-downvote" src="res/downvote.svg"></img><span id="wall-id" style="display: none">:id:</span></div>';

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWF4cGxheXMiLCJhIjoiY2pydW16M2NhMHg4bTQzcHExZXo1NmZ6cyJ9.pP7oLqWGYW-H8qE3uD71XA', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox.streets',
    accessToken: ''
}).addTo(mymap);

mymap.locate({setView: true});

for (var i = 0; i < locations.length; i++) {
  L.circleMarker(L.latLng(locations[i].lat, locations[i].lng), {
      //color: '#3388ff',
      color: "green",
      radius: 5
  }).addTo(mymap).bindPopup('<div class="container wall">' + locations[i].id + '</div>', {
    minWidth: 200
  });
}

function onLocationFound(e) {
    var radius = e.accuracy / 2;

    L.marker(e.latlng).addTo(mymap).bindPopup("Your location");
}

mymap.on('locationfound', onLocationFound);

function onLocationError(e) {
    alert(e.message);
}

mymap.on('locationerror', onLocationError);

var a = true;

$("body").on("DOMNodeInserted", ".wall", function(){
  if($(this).hasClass("wall") && a){
    a = false;
    var element = $(this);
    var id = element.html();
    $.post("php/info.php", {id: id}).done(function(data){
      var v = JSON.parse(data);
      element.html(popup.replace(":title:", v.title).replace(":description:", v.description).replace(":score:", v.score).replace(":id:", id));
    }).fail(function(){
      element.html("Error loading info");
    }).always(function(){
      a = true;
    });
  }
});

$(document).on("click", ".wall-upvote", function(){
  var element = $(this);
  a = false;
  $.post("php/vote.php", {id: $(this).parent().find("#wall-id").html(), up: 1}).done(function(data){
    if(data.length > 0){
      $("#wall-score").html(data);
    }else{
      $("#register-modal").modal("show");
    }
    a = true;
  }).fail(function(){
    $("#wall-score").html("Error");
    a = true;
  });
});

$(document).on("click", ".wall-downvote", function(){
  var element = $(this);
  a = false;
  $.post("php/vote.php", {id: $(this).parent().find("#wall-id").html(), up: 0}).done(function(data){
    if(data.length > 0){
      $("#wall-score").html(data);
    }else{
      $("#register-modal").modal("show");
    }
    a = true;
  }).fail(function(){
    $("#wall-score").html("Error");
    a = true;
  });
});
