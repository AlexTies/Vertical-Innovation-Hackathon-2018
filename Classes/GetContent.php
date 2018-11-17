<?php
/**
 * Created by PhpStorm.
 * User: Alex Larentis
 * Date: 16/11/2018
 * Time: 22:55
 */

require_once 'WikiMedia.php';

class GetContent
{

    private $url = 'http://daten.buergernetz.bz.it/services/kksSearch/collect/lichtbild?fl=*&rows=1600&wt=json';
      private $wikiUrl = 'https://de.wikipedia.org/api/rest_v1/page/summary/';
    private $output = [];

    private $pics = array();

    /**
     * GetContent constructor.
     */


    /**
      * GetContent constructor.
      * @param string $url
      */

     public function setUrl($url)
     {
         this.$url = $url;
     }

    public function getContentFromWeb()
    {
        require_once('./konfiguration.php');
        $contents = file_get_contents($this->url.'&q=*:*');
        $values=json_decode($contents);
        $filter = array();

        $pdo = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATENBANK, MYSQL_BENUTZER,MYSQL_KENNWORT, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

        $qry = "SELECT * FROM `jobs` INNER JOIN locations ON jobs.location = locations.ID INNER JOIN uploaded_images ON uploaded_images.ID = jobs.uploaded_image;";
        $statement = $pdo->prepare($qry);
        $result = $statement->execute();
        $respo = $statement->fetchAll();
        $fin = array();
        $medium = array();
        $wip = array();

          foreach ($respo as $key) {

            if(isset($key['score'])){
            if($key['score'] > 70){
              array_push($fin, $key['PID']);
            } else if($key['score'] >= 20 ){
              array_push($medium, $key['PID']);
            } else if($key['score'] < 0){
              array_push($wip, $key['PID']);
            }
          }
        }

        foreach ($values->response->docs as $pic){

            $thing = true;
            if(isset($pic->CP_geo[0])){
                if(isset($pic->DE[0])) {
                    if(isset($pic->BE_de[0])) {
                    if(!in_array($pic->priref, $filter)){
                        foreach ($pic->ip_de as $det){
                            if (strpos($det, 'orträt') !== false) {
                                $thing = false;
                            }
                        }
                        if($thing){
                            $single = new Pic();
                            $single->id = $pic->priref;
                            $single->url = $pic->B1p_url[0];
                            $single->de = $pic->DE[0];
                            $single->name = $pic->VV_de[0];
                            $single->geo = $pic->CP_geo[0];
                            $single->desc = $pic->BE_de[0];
                            if(in_array($pic->priref, $fin)){
                              $single->status = 'fin.png';
                            } else if(in_array($pic->priref, $medium)){
                              $single->status = 'medium.png';
                            } else if(in_array($pic->priref, $wip)){
                              $single->status = 'wip.png';
                            } else {
                              $single->status = 'none.png';
                            }
                            array_push($filter, $pic->priref);
                            $this->pics[] = $single;
                        }

                    }
                  }
                }
            }
        }
    }

    public function getContentFromWebWithKey($key)
        {
            $contents = file_get_contents($this->url.'&q=priref:'.$key);
            $values=json_decode($contents);
            $filter = array();

            //echo $this->url.'&q=priref:'.$key;

            foreach ($values->response->docs as $pic){

                $single = new Pic();
                $single->id = $pic->priref;
                $single->url = $pic->B1p_url[0];
                $single->de = $pic->DE[0];
                $single->name = $pic->VV_de[0];
                $single->geo = $pic->CP_geo[0];
                $single->desc = $pic->BE_de[0];
                $single->ort = $pic->CP_de[0];
                if(isset($pic->ip_de))
                foreach ($pic->ip_de as $key) {
                    $single->altNames[] = $key;
                }
                else var_dump($pic);


                array_push($filter, $pic->priref);

                return $single;
            }


        }


    /**
     * @author urMom with colaboration in UrAnus wtf?
     */
        public function convertToJSArray()
    {

        $full = array();
        foreach ($this->pics as $pic){
            $split = explode(",",$pic->geo);
            array_push($full, array($split[0], $split[1], $pic->id, $pic->desc, $pic->status));

        }
        echo "var locations = " . json_encode($full);
            /*echo $pic->id;
            echo "<img src=\"" . $pic->url . "\" />";
            echo $pic->de;
            echo $pic->name;
            echo $pic->geo;*/
        }


        public function getSummary($value)
        {
          $single = new WikiMedia();

          $context = stream_context_create(array(
              'http' => array('ignore_errors' => true),
          ));

            $contents = file_get_contents($this->wikiUrl. $value, false, $context);
            $values=json_decode($contents);
            $filter = array();
            if(isset($values->content_urls->desktop->page)){
              $single->link = $values->content_urls->desktop->page;
              $single->extract_html = $values->extract_html;
            }else {
              $single->failure = true;
            }


          return $single;
          }


    }
