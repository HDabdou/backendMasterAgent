<?php

namespace App\Controllers;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use \App\Controller;



class paiement_marchandController extends Controller {
  private $bdd;
  public function __construct(){
    $this->bdd=new \pdo("mysql:host=localhost;dbname=mbirmiprod","root","");
  }
  public function closeConnection(){
    
  }

  public function getSolde(Request $request, Response $response, $args){
    header("Access-Control-Allow-Origin: *");
    $param = $request->getParsedBody();
   if(isset($_POST['param'])){
      $params = $_POST['param'];
      $param = json_decode($params);
    try{
        $req=$this->bdd->prepare("SELECT id_user,accessLevel,depends_on FROM authorizedsessions WHERE token=:token");
        $status=$req->execute(array(":token"=>$param->token));
        //$lastId=$this->bdd->lastInsertId();
        //$req->closeCursor();
        $data=$req->fetch();
        //var_dump($data[0]);
        if($data){
          if($data['accessLevel'] == 3){
            $id_user =  $data['depends_on'];
          }else{
            $id_user =  $data[0];
          }
          $req2=$this->bdd->prepare("SELECT caution FROM cautions WHERE id_user=:id_user");
          $rep1=$req2->execute(array(":id_user"=>$id_user));
          $caution=$req2->fetch();
          //$req2->closeCursor();
          if($caution){
            return $response->withJson(array("code"=>1,"caution"=>$caution[0]),200);
          }else{
            return $response->withJson(array("code"=>$rep1,"message"=>"erreur au niveau du serveur"),500);
          }
        }
        return $response->withJson(array("idDem"=> $lastId, "codeMessage" =>$data,"status"=>$status),200); 
       }catch(Exception $e){
        //var_dump($e);
        return $response->withJson(array("status"=>false,"message"=>"problem de connection a la base de donnee"));
      }
    }else{
      return $response->withJson(array("status"=>false,"message"=>"parametres incorrectes"));
    }
     
  }  

 public function updateCaution(Request $request, Response $response, $args){
   header("Access-Control-Allow-Origin: *");
    $param = $request->getParsedBody();
    if(isset($_POST['param'])){
      $params = $_POST['param'];
      $param = json_decode($params);

     try{
        $req=$this->bdd->prepare("SELECT id_user,accessLevel,depends_on FROM authorizedsessions WHERE token=:token");
        $status=$req->execute(array(":token"=>$param->token));
        //$lastId=$this->bdd->lastInsertId();
        //$req->closeCursor();
        $data=$req->fetch();
         
     if($data['accessLevel'] == 5){

          $id_user =  $data[0];
          $req2=$this->bdd->prepare("SELECT id_user,prenom,nom FROM users WHERE id_user=:id_user");
          $rep1=$req2->execute(array(":id_user"=>$id_user));
          $caution=$req2->fetch();
          $updater = json_encode(array('prenom' => $caution['prenom'] ,'nom' => $caution['nom'] ));
          $montantUpdate = json_encode(array('montant' => $param->montant, ));
          //if($caution){
            $reqI=$this->bdd->prepare("SELECT caution FROM cautions WHERE id_user=:id_user");
            $repI=$reqI->execute(array(":id_user"=>$param->id_receiver,));
            $cautionReceiver=$reqI->fetch();


            $reqII=$this->bdd->prepare("SELECT caution FROM cautions WHERE id_user=:id_user");
            $repII=$reqII->execute(array(":id_user"=>$id_user,));
            $cautionUpdater=$reqII->fetch();

            if($param->montant > 0){
              if($cautionUpdater[0] >= $param->montant ){

               $reqI1=$this->bdd->prepare("UPDATE cautions SET caution=:caution WHERE id_user=:id_user");
               $repI1=$reqI1->execute(array(":caution"=>intval($cautionReceiver[0] + $param->montant),":id_user"=>$param->id_receiver,));

              $reqII1=$this->bdd->prepare("UPDATE cautions SET caution=:caution WHERE id_user=:id_user");
              $repII1=$reqII1->execute(array(":caution"=>intval($cautionUpdater[0] - $param->montant),":id_user"=>$id_user,));

              //$cautionUpdater=$reqII->fetch();
              \date_default_timezone_set('UTC');
              $date=new \DateTime();
              $date=$date->format('Y-m-d H:i');

               $reqIII=$this->bdd->prepare("INSERT INTO trace(updater,operation,idprop,daterenflu,infosup) VALUES(:updater,:operation,:idprop,:daterenflu,:infosup)");
               $status=$reqIII->execute(array(":updater"=>$updater,":operation"=>'renflu',":idprop"=>$id_user,":daterenflu"=>$date,":infosup" => $montantUpdate,));


              return $response->withJson(array("code"=>1,"message"=>"deposite reussi"),200);
              }else{
                return $response->withJson(array("code"=>0,"message"=>"Solde master insuffisant"),200);
              }

            }else{
               $montantTOpositive = abs(intval($param->montant));
               if($cautionReceiver <= $montantTOpositive){

                $reqI1=$this->bdd->prepare("UPDATE cautions SET caution=:caution WHERE id_user=:id_user");
                $repI1=$reqI1->execute(array(":caution"=>intval($cautionReceiver[0] + $param->montant),":id_user"=>$param->id_receiver,));

                $reqII1=$this->bdd->prepare("UPDATE cautions SET caution=:caution WHERE id_user=:id_user");
                $repII1=$reqII1->execute(array(":caution"=>intval($cautionUpdater[0] - $param->montant),":id_user"=>$id_user,));

                //$cautionUpdater=$reqII->fetch();
                \date_default_timezone_set('UTC');
                $date=new \DateTime();
                $date=$date->format('Y-m-d H:i');

                 $reqIII=$this->bdd->prepare("INSERT INTO trace(updater,operation,idprop,daterenflu,infosup) VALUES(:updater,:operation,:idprop,:daterenflu,:infosup)");
                 $status=$reqIII->execute(array(":updater"=>$updater,":operation"=>'renflu',":idprop"=>$id_user,":daterenflu"=>$date,":infosup" => $montantUpdate,));
                return $response->withJson(array("code"=>1,"message"=>"retrait reussi"),200);
               }else{
                return $response->withJson(array("code"=>0,"message"=>"Solde point insuffisant"),200);
               }
               
            }

           
          //}else{
            //return $response->withJson(array("code"=>$rep1,"message"=>"erreur au niveau du serveur"),500);
          //}
        }
        return $response->withJson(array("codeError"=> false, "message" =>"pas autoriser"),200); 
       }catch(Exception $e){
        //var_dump($e);
        return $response->withJson(array("status"=>false,"message"=>"problem de connection a la base de donnee"));
      }
    }else{
      return $response->withJson(array("status"=>false,"message"=>"parametres incorrectes"));
    }   
  }  

  	
   
 public function listUsers(Request $request, Response $response, $args){
    header("Access-Control-Allow-Origin: *");
    $param = $request->getParsedBody();

  if(isset($_POST['param'])){
      $params = $_POST['param'];
      $param = json_decode($params);

    try{
      $req=$this->bdd->prepare("SELECT id_user,accessLevel FROM authorizedsessions WHERE token=:token");
      $status=$req->execute(array(":token"=>$param->token));
      //$lastId=$this->bdd->lastInsertId();
      //$req->closeCursor();
      $data=$req->fetch();
      //var_dump($data[0]);
      if($data['accessLevel'] == 5){
        
        $id_user =  $data[0];
        $req2=$this->bdd->prepare("SELECT u.id_user,prenom,nom,telephone,caution,date_modif FROM users u,cautions c WHERE u.id_user=c.id_user AND masterid=:id_user");
        $rep1=$req2->execute(array(":id_user"=>$id_user));
        $users=$req2->fetchAll();
        if($users){
          return $response->withJson(array("code"=>1,"users"=>$users),200);
        }else{
          return $response->withJson(array("code"=>false,"message"=>"erreur au niveau du serveur"),500);
        }
      }
      return $response->withJson(array("idDem"=> false, "message" =>"non autoriser"),200); 
     }catch(Exception $e){
      //var_dump($e);
      return $response->withJson(array("status"=>false,"message"=>"problem de connection a la base de donnee"));
    }
  }else{
    return $response->withJson(array("status"=>false,"message"=>"parametres incorrectes"));
  }
} 
 

  public function listDeposit(Request $request, Response $response, $args){
    header("Access-Control-Allow-Origin: *");
    $param = $request->getParsedBody();

    if(isset($_POST['param'])){
      $params = $_POST['param'];
      $param = json_decode($params);
      //return $response->withJson(array("code"=>1,"deposit"=> $param)
    try{
        $req=$this->bdd->prepare("SELECT id_user,accessLevel FROM authorizedsessions WHERE token=:token");
        $status=$req->execute(array(":token"=>$param->token));
        $data=$req->fetch();
        //var_dump($data[0]);
        if($data['accessLevel'] == 5){
          $id_user =  $data[0];
          //return $response->withJson(array("code"=>$id_user,"dateDebut"=>$param["dateDebut"],"dateFin"=>$param["dateFin"]),200);
          $req2I=$this->bdd->prepare("SELECT updater,daterenflu,infosup FROM trace WHERE `daterenflu` between :dateDebut and DATE_ADD(:dateFin, INTERVAL 2 DAY) AND idprop = :id_user");
          $rep1=$req2I->execute(array(":dateDebut"=>$param->dateDebut,":dateFin"=>$param->dateFin,":id_user"=>$id_user));
          $deposit=$req2I->fetchAll();
          return $response->withJson(array("rep"=>$rep1,"deposit"=>$deposit),200);
          
        }
        return $response->withJson(array("idDem"=> false, "message" =>"non autoriser"),200); 
       }catch(Exception $e){
        //var_dump($e);
        return $response->withJson(array("status"=>false,"message"=>"problem de connection a la base de donnee"));
      }
    }else{
      return $response->withJson(array("status"=>false,"message"=>"parametres incorrectes"));
    }
  } 

  public function listOperation(Request $request, Response $response, $args){
    header("Access-Control-Allow-Origin: *");
    //$param = $request->getParsedBody();
    
    //return $response->withJson(array("operations"=> $param->dateFin));
    if(isset($_POST['param'])){
      $params = $_POST['param'];
      $param = json_decode($params);
      //return $response->withJson(array("operations"=> $param->token));
      try{
        $reqOP=$this->bdd->prepare("SELECT id_user,accessLevel FROM authorizedsessions WHERE token=:token");
        $status=$reqOP->execute(array(":token"=>$param->token));
        //$lastId=$this->bdd->lastInsertId();
        
        $data=$reqOP->fetch();
        $reqOP->closeCursor();
        if($data){
          $id_user =  $data[0];
            

          $req3=$this->bdd->prepare("SELECT `nomservice`,`libelleoperation`,`montant`,`dateoperation`,commissionpdv FROM `commissions` WHERE `depends_on` IN(SELECT id_user FROM users WHERE masterid = :id_user) AND `dateoperation` BETWEEN :dateDebut AND DATE_ADD(:dateFin, INTERVAL 2 DAY)");
           $rep2=$req3->execute(array(":dateDebut"=>$param->dateDebut,":dateFin"=>$param->dateFin,":id_user"=>$id_user));
          $operation=$req3->fetchAll();
           return $response->withJson(array("code"=> true,"operations"=> $operation));
          
          $req3->closeCursor();
         /* if($operation){
            return $response->withJson(array("code"=>1,"operation"=>$operation),200);
          }*/
            
        }
         return $response->withJson(array("code"=> $users, "message" =>"non autoriser"),200); 
        
       }catch(Exception $e){
        //var_dump($e);
        return $response->withJson(array("status"=>false,"message"=>"problem de connection a la base de donnee"));
      }
    }else{
      return $response->withJson(array("status"=>false,"message"=>"parametres incorrectes"));
    }
  } 
  public function listOperationByPoint(Request $request, Response $response, $args){
    header("Access-Control-Allow-Origin: *");
    //$param = $request->getParsedBody();
    
    //return $response->withJson(array("operations"=> $param->dateFin));
    if(isset($_POST['param'])){
      $params = $_POST['param'];
      $param = json_decode($params);
      //return $response->withJson(array("operations"=> $param));
      try{
            
          $req3=$this->bdd->prepare("SELECT `nomservice`,`libelleoperation`,`montant`,`dateoperation`,commissionpdv FROM `commissions` WHERE `depends_on`=:id_user AND `dateoperation` BETWEEN :dateDebut AND DATE_ADD(:dateFin, INTERVAL 2 DAY)");
           $rep2=$req3->execute(array(":dateDebut"=>$param->dateDebut,":dateFin"=>$param->dateFin,":id_user"=>$param->id_user));
          $operation=$req3->fetchAll();
          return $response->withJson(array("code"=> true,"operations"=> $operation));
          $req3->closeCursor();
                
       }catch(Exception $e){
        //var_dump($e);
        return $response->withJson(array("status"=>false,"message"=>"problem de connection a la base de donnee"));
      }
    }else{
      return $response->withJson(array("status"=>false,"message"=>"parametres incorrectes"));
    }
  } 
}