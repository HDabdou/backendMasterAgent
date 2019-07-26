<?php

namespace App;


use Slim\Container;


class Controller {

  protected $db;
  protected $fromcrmtoken;

  public function __construct(Container $c) {
      $this->db = $c->get('db');
      $this->fromcrmtoken = "dsLDHD683_5238d11ns@sfnJDK82_3EZ7";
  }

  public function e404($message){
    header('HTTP/1.0 404 Not Found');
    $this->set('message',$message);
    $this->render('/errors/404');
    die();
  }
  
}
