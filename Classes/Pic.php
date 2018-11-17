<?php
/**
 * Created by PhpStorm.
 * User: Alex Larentis
 * Date: 16/11/2018
 * Time: 21:40
 */

class Pic{
    public $id = '';
    public $url = '';
    public $geo = '';
    public $de = '';
    public $name =   '';

    public $altName;

    public function getParsedDate(){
        return substr($this->de, 7, 2).".". substr($this->de, 5, 2). ".".substr($this->de, 0, 4);
    }
}
