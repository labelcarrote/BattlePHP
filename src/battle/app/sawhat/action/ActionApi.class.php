<?php
use BattlePHP\Core\Controller;
use BattlePHP\Core\Auth\AuthManager;
use BattlePHP\Core\Auth\Identity;
use BattlePHP\Api\Response;
use BattlePHP\Storage\Uploader;
use BattlePHP\Core\Request;
use BattlePHP\Imaging\ImageHelper;
use BattlePHP\Storage\FileSystemIO;
require_once 'app/sawhat/config/config_sawhat.php';
require_once 'app/sawhat/model/Card.class.php';
require_once 'app/sawhat/model/CardStore.class.php';
require_once 'app/sawhat/model/ColorScheme.class.php';
require_once 'app/sawhat/model/NavigationHelper.class.php';
require_once 'app/sawhat/model/SearchHelper.class.php';
/**********************************************************************
* ActionApi
*
* SAWHAT API controller
*
* - index()
*   - [POST] save_card
*   - [POST] add_file_to_card 
*   - [GET] search : /api/?m=search&query=WAT
* 
* - TODO
*   - [GET] "@card_name/@command" commands=[edit,as_code,as_html,search???]
*   - [GET] "@card_name" card_name=[all_cards,starred]
*
*   - [POST] logout
*   - [POST] login
*
* @author touchypunchy
*
***********************************************************************/
class ActionApi extends Controller{

	const ERR_UNKNOWN_API_METHOD = "Unknown API method.";
	const ERR_NOT_AUTENTICATED = "You need to be authentified to do that.";
	const ERR_FILE_TOO_BIG = "Too big.";
	const ERR_FILE_EXT_NOT_ALLOWED = "File extension not allowed.";
	
	// [/sawhat/api]
	public function index(){
		$response = new Response();
		$batl_is_logged = AuthManager::is_authenticated();

		// [GET] query
		if(isset($_GET['m'])) {
			$service_method = Request::isset_or($_GET['m'],null);
			switch ($service_method) {
				// /sawhat/api?m=get_card&name=bugs&version=
				case 'get_card' :
					$card_name = Request::isset_or($_GET['name'],null);
					$card_version = Request::isset_or($_GET["version"],null);
					$card = ($card_version !== null)
						? CardStore::get_card_version($card_name,$card_version)
						: CardStore::get_card($card_name);
					$this->assign('card',$card);
					$this->assign('show_banner',Request::isset_or($_GET['show_banner'],1));
					$card->html = $this->fetch_view('element.card.tpl');
					$response->body = $card;
					break;
				case 'search' :
					// TODO
					$query = Request::isset_or($_GET['query'],null);
					$cards = CardStore::get_all_cards($query);
					$this->assign('batl_is_logged',$batl_is_logged);
					$response->body = $cards;
					break;
				default:
					$response->errors = self::ERR_UNKNOWN_API_METHOD;
					break;
			}
		}
		// [POST] json data
		else{
			$data = json_decode(file_get_contents('php://input'), false); // true = array, false = object (stdClass)
			if($data === null){
				//$response->errors = self::ERR_UNKNOWN_API_METHOD;
				$this->assign('title', "API | ");
				$this->display_page('section.api.tpl');
				return;
			}else{
				switch ($data->submit) { 
					case 'save_card' :
						$card = CardStore::get_card($data->card_name);
						if($card === null || !$card->is_private || ($card->is_private && $batl_is_logged)){
							$response->body = [
								'is_saved' => CardStore::upsert($data->card_name,$data->card_txt,$data->card_color,$data->card_is_private),
								'return_url' => Request::get_application_virtual_root().$data->card_name
							];
						}else{
							$response->errors = self::ERR_NOT_AUTENTICATED;
						}
						break;
					case 'add_file_to_card':
						if(CardStore::exist($data->card_name)){
							$extensions = ["jpg","jpeg","png","gif","zip"];
							$extension = strtolower(pathinfo($data->file_name,PATHINFO_EXTENSION));
							$is_extension_allowed = in_array($extension, $extensions);
							$size = strlen($data->file);
			    			if($size > CardStore::MAX_FILE_SIZE){
			    				$response->errors = self::ERR_FILE_TOO_BIG;
			    			}elseif($is_extension_allowed === false){
								$response->errors = self::ERR_FILE_EXT_NOT_ALLOWED;
							}else{
								try{
									FileSystemIO::save_file(
										Request::get_application_path().CardStore::DIR.$data->card_name."/".$data->file_name,
										$data->file
									);
									// returns files list
									$this->assign("card", CardStore::get_card($data->card_name));
									$response->body = $this->fetch_view("element.file_set.tpl");
									//
									/*$response->body = [
										'dat_file_url' => $dat_file->url,
										'dat_file_date_modified' => $dat_file->date_modified->format("d/m/Y H:i:s")
									];*/
								}catch(Exception $e){ 
									$response->errors = "DON'T DO THAT";
								}
							}
						}
						break;
					default :
						$response->errors = self::ERR_UNKNOWN_API_METHOD;
						break;
				}
			}
		}
		echo $response->to_json();
	}
}
