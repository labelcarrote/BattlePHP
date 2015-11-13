<?php
use BattlePHP\Core\Controller;
use BattlePHP\Core\Auth\AuthManager;
use BattlePHP\Core\Auth\Identity;
use BattlePHP\Api\Response;
use BattlePHP\Storage\Uploader;
use BattlePHP\Core\Request;
use BattlePHP\Imaging\ImageHelper;
require_once 'app/sawhat/config/config_sawhat.php';
require_once 'app/sawhat/model/Card.class.php';
require_once 'app/sawhat/model/CardStore.class.php';
require_once 'app/sawhat/model/ColorScheme.class.php';
require_once 'app/sawhat/model/NavigationHelper.class.php';
require_once 'app/sawhat/model/SearchHelper.class.php';
/**
 * ActionApi
 *
 * SAWHAT API controller
 * - index()
 *   - [POST] addfile
 * 
 * - TODO
 *   - [POST] save (card)
 *   - [POST] login
 *   - [POST] logout
 *   - [POST] search
 *   - [GET] "@card_name/@command" commands=[edit,as_code,as_html,search???]
 *   - [GET] "@card_name" card_name=[all_cards,starred]
 *
 * @author touchypunchy
 *
 */
class ActionApi extends Controller{
	
	// [/sawhat/api]
	// Treats any POST command in JSON {data:{submit: ...,...} and respond in JSON {errors:"";body:""})
	// - addfile -> add_file
	public function index(){
		$batl_is_logged = AuthManager::is_authenticated();
		$response = new Response();
		// POST
		if(isset($_POST['data'])){
			$data = json_decode($_POST['data'],false); // true = array, false = object (stdClass)
			switch ($data->submit) {
				case 'save_card' :
					$card = CardStore::get_card($data->card_name);
					if($card === null || !$card->is_private || ($card->is_private && $batl_is_logged)){
						$response->body = [
							'is_saved' => CardStore::upsert($data->card_name,$data->card_txt,$data->card_color,$data->card_is_private),
							'return_url' => Request::get_application_virtual_root().$data->card_name
						];
					}else{
						$response->errors = "DON'T DO THAT";
					}
					break;
				// TODO submit in data json object
				// TODO pass upload in json 
				case 'add_file_to_card':
					$card_name = Request::isset_or($_POST['name'], "");
					if(CardStore::exist($card_name)){
						$extensions = [".jpg",".png",".jpeg",".JPG",".gif",".zip"];
						try{
							$file = Uploader::process_form_file("file",CardStore::get_folder().$card_name,2000000,$extensions);
							// returns files list
							$this->assign("card", CardStore::get_card($card_name));
							$response->body = $this->fetch_view("element.file_set.tpl");
						}catch(Exception $e){ 
							$response->errors = "DON'T DO THAT";
						}
					}
					break;
			}
		}
		$response->errors = "WAT";
		echo $response->to_json();
	}
}
