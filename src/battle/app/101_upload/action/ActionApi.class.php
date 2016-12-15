<?php
use BattlePHP\Api\Response;
require_once __DIR__.'/../model/DatFileManager.php';
/********************************************************************
* CLASS ActionApi (Controller)
*
* Upload API / Service for the 101_upload app
* 
* Pages:
* - index : /api
*
*********************************************************************/
class ActionApi extends BattlePHP\Core\Controller{

	const ERR_UNKNOWN_API_METHOD = "Unknown API method.";
	const ERR_FILE_TOO_BIG = "Too big.";
	const ERR_FILE_EXT_NOT_ALLOWED = "File extension not allowed.";

	public function index(){
		$response = new Response();
		// Decode POST json data
		$data = json_decode(file_get_contents('php://input'), false); // true = array, false = object (stdClass)
		if($data !== null){
			switch ($data->submit) { 
				case 'upload_file':
					$extension = strtolower(pathinfo($data->file_name,PATHINFO_EXTENSION));
					$is_extension_allowed = in_array($extension, ["jpg","jpeg","png","gif","zip","txt"]);
					$size = strlen($data->file);
					
					if($is_extension_allowed === false){
						$response->errors = self::ERR_FILE_EXT_NOT_ALLOWED;
					}elseif($size > DatFileManager::MAX_FILE_SIZE){
						$response->errors = self::ERR_FILE_TOO_BIG;
					}else{
						try {
							$dat_file = DatFileManager::store_dat_file($extension,$data->file);
							$response->body = [
								'dat_file_url' => $dat_file->url,
								'dat_file_date_modified' => $dat_file->date_modified->format("d/m/Y H:i:s")
							];
						} catch (Exception $e) {
							$response->errors = $e;
						}
					}
					break;
				default :
					$response->errors = self::ERR_UNKNOWN_API_METHOD;
					break;
			}
		}else{
			$response->errors = self::ERR_UNKNOWN_API_METHOD;
		}
		echo $response->to_json();
	}
}