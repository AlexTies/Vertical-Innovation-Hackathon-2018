<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

<link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700,900|Roboto:100,300,400,500" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css"
 integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
 crossorigin=""/>

 <script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js"
    integrity="sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA=="
    crossorigin=""></script>
  <link rel="stylesheet" type="text/css" href="./ssc.css">
</head>
<body>
<!--
<main class="loadingscreen">
  <span class="title">HISTORICAL</span>
  <span class="title">IMAGE</span>
  <span class="title">TOUR</span>
  <span class="undertitle">Willkommen</span>
</main>
<main class="sidebar closed" id="sb">
  <a class="closebutton" href="javascript:void(0)" onclick="document.getElementById('sb').classList.toggle('closed')">
    <i class="material-icons">
close
    </i></a>

    <div class="top">
        <p>WORK<br>IN<br>PROGRESS</p>
    </div>
	<a class="selected">
        Karte
    </a>
	<a>
        Einsendungen
    </a>
</main>-->
<?php
$page = "Karte";
require_once('sidebar-buildingblock.php');
 ?>
<main>
<div id="map">

</div>
<a  href="javascript:void(0)" onclick="document.getElementById('info').classList.toggle('hidden')">
<div class="openlegend">
  <i class="material-icons">info_outline</i>
</div>
</a>
<div class="legend hidden" id="info">
  <div class="closebutton">
  <a  href="javascript:void(0)" onclick="document.getElementById('info').classList.toggle('hidden')">

      <i class="material-icons">close</i>
    </a>
  </div>
  <div class="head">
    <i class="material-icons" style="">info_outline</i>
    <span>Legende</span>
  </div>
  <?php
  session_start();
    require_once('konfiguration.php');
   ?>
  <div class="cont">
    <div class="column">
      <span class=title>Kathegorie</span>
      <a class="eintrag"><img src="./fin.png">Good</a>
      <a class="eintrag"><img src="./medium.png">Insufficent</a>
      <a class="eintrag"><img src="./wip.png">Elaborating</a>
      <a class="eintrag"><img src="./none.png">Not done</a>
    </div>
  </div>
</div>
</main>

</body>
<script>
<?php
$all = array();
$pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));


require_once 'Classes/Pic.php';
require_once 'Classes/GetContent.php';

$content= new GetContent();
$content->getContentFromWeb();
  $content->convertToJSArray();
 ?>

   var unsureENB = L.icon({iconUrl: './marker.png', iconSize: [38, 38],iconAnchor: [19, 38],popupAnchor:  [0, -34]});

var openstreet = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a>'});
var map = L.map('map', {layers: [openstreet], fullscreenControl: true});
    map.setView([46.498449, 11.352487], 8);
for (var i = 0; i < locations.length; i++) {
  var marker = L.icon({iconUrl: './' + locations[i][4], iconSize: [38, 38],iconAnchor: [19, 38],popupAnchor:  [0, -34]});

   var m = L.marker([locations[i][0], locations[i][1]], {icon: marker} );
   m.on('click', L.bind(openLink, null, locations[i][2]));
     m.addTo(map);
     m.on('mouseover', function(e){
       m.openPopup();
   });
   m.bindTooltip(locations[i][3], { direction: 'top'});

}
function openLink(args){
  console.log(args);
  window.location = './details.php?id=' + args;
}
</script>
</html>
