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
  <style>
 span.similarity {
   text-align: left;
   font-weight: 900;
 }
 section.paralaxback::after {
       transform: translateZ(-1px) scale(1.99);
    background-size: cover;
 }
  </style>
</head>
<body>
<?php
require_once 'Classes/Pic.php';
require_once 'Classes/GetContent.php';
$page = "Einsendungen";
session_start();
require_once('sidebar-buildingblock.php');
  require_once('konfiguration.php');
 ?>
<main id=cont>
  <section class="paralaxback">
    <div>
      <h1>ABGEGEBEN</h1>
    </div>
  </section>
  <section class="list_cont">
  <?php
  $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
$statement = $pdo->prepare("SELECT uploaded_images.fileName as fnU, locations.PID, jobs.score FROM jobs INNER JOIN uploaded_images ON uploaded_images.ID = jobs.uploaded_image INNER JOIN locations ON locations.ID = jobs.location");
$result = $statement->execute();
$respo = $statement->fetchAll();
foreach ($respo as $key) {
    $content= new GetContent();
$ret = $content->getContentFromWebWithKey($key['PID']);
  ?>
    <article>
      <div class="img" style="background-image: url('<?php echo $ret->url; ?>')">

    </div>
      <div class="img sec" style="background-image: url('uploads/<?php echo $key['fnU'] ?>')">

      </div>
      <span class="title"><?php echo $ret->desc; ?></span>
      <span class="ort"><i class="material-icons">location_on</i><?php echo str_replace(";",", ", $ret->ort); ?></span>

      <?php
      $class = "medium";
      if($key['score'] > 70)
      $class = "full";
      if($key['score'] < 20)
      $class = "none";
      if($key['score'] > -1){
       ?>
      <span class="similarity <?php echo $class; ?>"><?php echo $key['score']; ?>% ÜBEREINSTIMMUNG</span>
      <?php
    } else {
      ?>
     <span class="score">NOCH NICHT VERARBEITET</span>
      <?php
    }
     ?>
    </article>

  <?php
}
   ?>
 </section>


</main>
</body>
</html>
