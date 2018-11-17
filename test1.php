<?php
  require_once 'Classes/GetContent.php';
  require_once 'Classes/Pic.php';
  require_once 'Classes/WikiMedia.php';

  $wiki;

  $content = new GetContent();
  $ret = $content->getContentFromWebWithKey("60074628");
  $wiki;

  foreach ($ret->altNames as $key) {
    $wiki = $content->getSummary($key);
    if (!$wiki->failure) {
      break;
    }
  }

  echo $wiki->link;


 ?>
