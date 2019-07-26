<?php

//$app->post('/', App\Controllers\HomeController::class .':authorizationCheck');

/*$app->group('/auth', function () {

	$this->post('/ok', App\Controllers\HomeController::class .':accueil');

});*/

$app->group('/utils', function () {

	$this->post('/getSolde', App\Controllers\paiement_marchandController::class .':getSolde');

	$this->post('/updateCaution', App\Controllers\paiement_marchandController::class .':updateCaution');
	
	$this->post('/listUsers', App\Controllers\paiement_marchandController::class .':listUsers');
	
	$this->post('/listDeposit', App\Controllers\paiement_marchandController::class .':listDeposit');
	
	$this->post('/listOperation', App\Controllers\paiement_marchandController::class .':listOperation');

	$this->post('/listOperationByPoint', App\Controllers\paiement_marchandController::class .':listOperationByPoint');

	
	

});







