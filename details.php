<html>
<head>
  <?php
  session_start();
    require_once('konfiguration.php');
    $id = $_GET['id'];
    require_once 'Classes/Pic.php';
    require_once 'Classes/GetContent.php';
    $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

  if(isset($_GET['image'])) {
    $imgupdated = true;
    $image = "";
    $imgupdates =  "";
      $target_dir = "./uploads/";
      echo "1";
      if ( $_FILES['uploaddatei']['name']  != "" )
{
    // Datei wurde durch HTML-Formular hochgeladen
    // und kann nun weiterverarbeitet werden

    // Kontrolle, ob Dateityp zulässig ist
    $zugelassenedateitypen = array("image/png", "image/jpeg", "image/gif");

    if ( ! in_array( $_FILES['uploaddatei']['type'] , $zugelassenedateitypen ))
    {
        echo "<script>alert('Dateitype ist NICHT zugelassen')</span>";
    }
    else
    {
        // Test ob Dateiname in Ordnung
        if ($_FILES["uploaddatei"]["size"] < 10000000) {
        if ( $_FILES['uploaddatei']['name'] <> '' )
        {
            move_uploaded_file (
                 $_FILES['uploaddatei']['tmp_name'] ,
                 './uploads/'. $_FILES['uploaddatei']['name'] );

            $imgupdates .= "file was uploaded: ";
            $imgupdates .= '<a href="'. $target_dir . $_FILES['uploaddatei']['name'] .'">';
            $imgupdates .= '' . $target_dir . $_FILES['uploaddatei']['name'];
            $imgupdates .= '</a>';


            $statement = $pdo->prepare("INSERT INTO locations ( `PID`) VALUES (:id)");
            $statement->execute(array('id' => $id));


            $statement = $pdo->prepare("INSERT INTO uploaded_images ( `fileName`) VALUES (:fn)");
            $statement->execute(array('fn' => $_FILES['uploaddatei']['name']));

            $statement = $pdo->prepare("INSERT INTO jobs (`uploaded_image`, `location`, `score`) SELECT ID as uploaded_image, (SELECT ID FROM locations WHERE PID=:loc) as location, -1 as score FROM uploaded_images WHERE fileName = :fn;");
            $statement->execute(array('loc' => $_GET['id'], 'fn' => $_FILES['uploaddatei']['name']));
        }
        else
        {
              echo "<script>alert('file not accepted')</span>";
        }

    } else {
          echo "<script>alert('file to big')</span>";
    }
  }
}
}


   ?>
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
  <?php
  $page = "Details";
    $statement = $pdo->prepare("SELECT * FROM locations WHERE ID=:id");
    $result = $statement->execute(array('id' => $id));
    $respo = $statement->fetch();
    var_dump($respo);

    $content= new GetContent();
    $ret = $content->getContentFromWebWithKey($id);

    $wiki;

    foreach ($ret->altNames as $key) {
      $wiki = $content->getSummary($key);
      if (!$wiki->failure) {
        break;
      }
    }

   ?>
  <style>
  main {
    background-color: #9A614C;
  }
  section.paralaxback {
    height: 70vh;
  }
  section.paralaxback::after {
    background-image: url('<?php echo $ret->url . "&size=l"; ?>') !important;
  }
  section.paralaxback::before {
    background-image: url('<?php echo $ret->url . "&size=l"; ?>');
  }
  </style>
</head>
<body>
  <?php
  require_once('sidebar-buildingblock.php');


   ?>
<main id="upload" class="hidden">
  <form action="?id=<?php echo $id; ?>&image=1" method="post" enctype="multipart/form-data">

    <h2>UPLOAD</h2>
  <input type="file" name="uploaddatei" size="60" maxlength="255">
  <input id="button" type="Submit" name="submit" class="button" value="Datei hochladen">
  </form>
</main>
<main id=cont>
  <section class="paralaxback">
    <div>

    </div>
  </section>
  <h2>HERRAUSFORDERUNG</h2>
  <?php
  $qry = "SELECT * FROM `jobs` INNER JOIN locations ON jobs.location = locations.ID INNER JOIN uploaded_images ON uploaded_images.ID = jobs.uploaded_image WHERE locations.PID = :pid;";
  $statement = $pdo->prepare($qry);
  $result = $statement->execute(array('pid' => $id));
  $respo = $statement->fetch();
  if(!$respo){
   ?>
  <section class="details_cont">
    <article>
      <h1>Lade ein Bild hoch</h1>
      <p>
        Mach ein Bild, welches du dann hochlädst, und zeige dadurch anderen Mitgliedern wie es am Ort des Fotos heue aussieht.
      </p>
      <p>
        Jedes Bild bekommt einen Score, desdo ähnlicher dein Bild dem alten Bild ist, umso höher ist dein Score.
      </p>
      <a class="button" href="javascript:void(0)" onclick="document.getElementById('upload').classList.remove('hidden')">
        <i class="material-icons">add_a_photo</i>Hochladen
      </a>
    </article>

 </section>
 <?php
} else {
  ?>
  <section class="details_cont">
    <article>
      <h1>Hochgeladenes Bild</h1>
      <img class="upload" src="uploads/<?php echo $respo['fileName']; ?>" />
      <?php
      $class = "medium";
      if($respo['score'] > 70)
      $class = "full";
      if($respo['score'] < 20)
      $class = "none";
      if($respo['score'] > -1){
       ?>
      <span class="similarity <?php echo $class; ?>"><?php echo $respo['score']; ?>% ÜBEREINSTimmung</span>
      <?php
    } else {
      ?>
     <span class="similarity">noch nicht verarbeitet</span>
      <?php
    }
     ?>
      <p>
        Solltest du mit deinem Bild und dessen Score unzufrieden sein, kannst du es neu hochladen.
      </p>
      <a class="button" href="javascript:void(0)" onclick="document.getElementById('upload').classList.remove('hidden')">
        <i class="material-icons">add_a_photo</i>Neu hochladen
      </a>
    </article>

 </section>
  <?php
}
  ?>
  <h2>ÜBER DIESES BILD</h2>
  <section class="details_cont">
    <article>
      <h1>Beschreibung</h1>
      <p>
        <?php echo $ret->desc; ?>
      </p>
        <h1>Hersteller des Bildes</h1>
        <p>
          <?php echo $ret->name; ?>
        </p>
    </article>
    <?php
    if(isset($wiki)){
     ?>
  <article>
    <img src="wikipedia.png" />
	<h1>WIKIPEDIA</h1>
  <p>
    <?php echo "$wiki->extract_html"; ?>
  </p>
  <a href="<?php echo "$wiki->link"; ?>">mehr lesen...</a>
  </article>

<article>
<h1>TAGS</h1>
<?php
foreach ($ret->altNames as $key) {
  ?>
  <span><?php echo "#" . $key . " "; ?></span>
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
