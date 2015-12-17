<?php
namespace BattlePHP\API;
/**
 * Response
 *
 */
class Response{
	public $errors;
	public $body;

	public function to_json(){
		return json_encode($this);
	}
}
