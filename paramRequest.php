 <?php
//Depot Orange Money
function depot(Request $request, Response $response, $args){ 
    $requette = "1/".$montant."/".$numero;
} 

//Retrait Orange Money
function depot(Request $request, Response $response, $args){ 
    $requette = "2/".$numero."/".$montant;
}

//Retrait avec code Orange Money
function depot(Request $request, Response $response, $args){ 
     $requette = "3/".$codeRetrait."/".$prenom."/".$nom."/".$dateNaissanse."/".$cni."/".$numero."/".$montant;
}


//Depot tigo cash
function depot(Request $request, Response $response, $args){ 
    $requette = "1/".$numero."/".$montant;
} 

//Retrait tigo cash
function depot(Request $request, Response $response, $args){ 
    $requette = "2/".$numero."/".$montant;
}

//Retrait avec code tigo cash
function depot(Request $request, Response $response, $args){ 
   $requette = "4/".$codeRetrait."/".$typePiece."/".$cni."/".$montant."/".$numero;
}


$data =json_encode(array("token" => "4cd6526371c082310bb1ff05affe63eb3f84ea457","customer" => $numero,"amount" => $montant));

//Depot wizall
function depot(Request $request, Response $response, $args){ 
    $data =json_encode(array("token" => "4cd6526371c082310bb1ff05affe63eb3f84ea457","receiver_phone_number" => $numero,"amount" => $montant));
} 

//Retrait wizall
function depot(Request $request, Response $response, $args){ 
   $data =json_encode(array("token" => "4cd6526371c082310bb1ff05affe63eb3f84ea457","customer" => $numero,"amount" => $montant));
}





