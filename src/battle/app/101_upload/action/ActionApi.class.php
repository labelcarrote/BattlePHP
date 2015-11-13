<?php
use BattlePHP\Core\Controller;
use BattlePHP\Core\Request;
use BattlePHP\Api\Response;
use BattlePHP\Storage\Uploader;
/**
 * CLASS ActionLol (Controller)
 *
 * Another example controller for 101_upload app.
 * 
 * Pages:
 * - index : /api
 *
 */
class ActionApi extends Controller{
	const DEFAULT_FOLDER = "app/101_upload/storage/";
	// [/api]
	public function index(){
		$response = new Response();
		// POST
		if(isset($_POST['data'])){
			$data = json_decode($_POST['data'],false); // true = array, false = object (stdClass)
			switch ($data->submit) {
				// TODO submit in data json object
				// TODO pass upload in json 
				case 'upload_file':
					$extensions = [".jpg",".png",".jpeg",".JPG",".gif",".zip"];

					file_put_contents(self::DEFAULT_FOLDER.$data->file_name, file_get_contents($data->file));

					try{
						/*$file = Uploader::process_form_file("file",CardStore::get_folder().$card_name,2000000,$extensions);
						// returns files list
						$this->assign("card", CardStore::get_card($card_name));*/
						$response->body = "NOICE";//$this->fetch_view("element.file_set.tpl");
					}catch(Exception $e){ 
						$response->errors = "DON'T DO THAT : ".$e->getMessage();
					}
					break;
			}
		}else{
			$response->errors = "Unknown API method.";
		}
		var_dump($_POST);
		echo $response->to_json();
	}
}