<?php
use BattlePHP\Core\Controller;
use BattlePHP\Core\Request;
use BattlePHP\Api\Response;
use BattlePHP\Storage\Uploader;
require_once 'app/101_upload/model/DatFileManager.php';
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
	// [/api]
	public function index(){
		$response = new Response();
		// POST
		if(isset($_POST['data'])){
			$data = json_decode($_POST['data'],false); // true = array, false = object (stdClass)
			switch ($data->submit) { 
				case 'upload_file':
					$extensions = ["jpg","png","jpeg","JPG","gif","zip"];
					$extension = strtolower(pathinfo($data->file_name,PATHINFO_EXTENSION));
					$is_extension_allowed = in_array($extension, $extensions);
					if($is_extension_allowed === false){
						var_dump($extension);
						$response->errors = "File extension not allowed.";
					}else{
						// TODO : move to DatFileManager
						$new_file_name = /*"app/101_upload/"*/ // DIRTY
							 DatFileManager::DEFAULT_FOLDER
							. DatFileManager::DEFAULT_FILENAME
							. "."
							. $extension;
            			file_put_contents("app/101_upload/".$new_file_name, file_get_contents($data->file));
						$now = new DateTime();
						$url = Request::get_application_root()
							. $new_file_name
							. "#"
							. $now->format("His");
						$response->body = "<img src='".$url."'>";
					}
					break;
			}
		}else{
			$response->errors = "Unknown API method.";
		}
		echo $response->to_json();
	}
}