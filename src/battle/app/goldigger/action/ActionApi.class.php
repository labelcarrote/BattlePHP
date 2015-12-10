<?php
use BattlePHP\Core\Controller;
use BattlePHP\Core\Request;
use BattlePHP\Api\Response;
require_once __DIR__.'/../model/GoldRater.php';
/********************************************************************
* CLASS ActionApi (Controller)
*
* API / Service for Goldigger to get gold rate and refresh it.
* 
* Methods:
* - /api/rate : rate()
* - /api/refresh_rate : refresh_rate()
*
*********************************************************************/
class ActionApi extends Controller{

	// [GET /api/rate]
	public function rate(){
		$response = new Response();
		$response->body = GoldRater::get_rate();
		echo $response->to_json();
	}

	// [GET /api/refresh_rate]
	public function refresh_rate(){
		GoldRater::refresh_rate();
		echo "rate refreshed";
	}
}