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

	const MAX_FILE_SIZE = 5242880; // 5Mio 

	// [/api]
	public function index(){
		$response = new Response();
		// POST
		if(isset($_POST['data'])){
			$data = json_decode($_POST['data'],false); // true = array, false = object (stdClass)
			switch ($data->submit) { 
				case 'upload_file':
					$extensions = ["jpg","jpeg","png","gif","zip"];
					$extension = strtolower(pathinfo($data->file_name,PATHINFO_EXTENSION));
					$is_extension_allowed = in_array($extension, $extensions);
					$size = strlen($data->file);
        			if($size > self::MAX_FILE_SIZE){
        				$response->errors = "Too big.";
        			}
					elseif($is_extension_allowed === false){
						$response->errors = "File extension not allowed.";
					}
					else{
						$dat_file_url = DatFileManager::store_dat_file($extension,$data->file);
						$response->body = $dat_file_url;//"<img src='".$dat_file_url."'>";
					}
					break;
			}
		}else{
			$response->errors = "Unknown API method.";
		}
		echo $response->to_json();
	}
}