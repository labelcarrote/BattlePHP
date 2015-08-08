<?php
use BattlePHP\Core\Controller;
use BattlePHP\Core\Auth\AuthManager;
use BattlePHP\API\Response;
use BattlePHP\Storage\Uploader;
use BattlePHP\Core\Request;
use BattlePHP\Imaging\ImageHelper;

require_once 'app/sawhat/config/config_sawhat.php';
require_once 'app/sawhat/model/Card.class.php';
require_once 'app/sawhat/model/CardStore.class.php';
require_once 'app/sawhat/model/ColorScheme.class.php';
require_once 'app/sawhat/model/NavigationHelper.class.php';
require_once 'app/sawhat/model/SearchHelper.class.php';

// SAWHAT main controller
class ActionHome extends Controller{

	// Display the home page containing the home card if it exists, 
	// all the cards otherwise.
	// Treats any creation/update query submission.
	public function index(){
		$batl_is_logged = AuthManager::is_authenticated();

		// Assign default color scheme
		$color_scheme = new ColorScheme();
		$this->assign('color_scheme',$color_scheme->name);
		
		// Sets color scheme available
		$files = ColorScheme::get_available_color_schemes();
		$this->assign('color_schemes',$files);
		
		// check if any form submission (save or login-to-see-the-private-card)
		if(isset($_POST['submit'])){
			$submit_action = $_POST['submit'];
			switch ($submit_action) {
				case 'save' : {
					$card_name = Request::isset_or($_POST['name'], ConfigurationSawhat::DEFAULT_CARD_NAME);
					$card_color = Request::isset_or($_POST['color'], Card::DEFAULT_COLOR);
					$card_lines = Request::isset_or($_POST['card'], "");
					$is_private = isset($_POST['is_private']);
					$card = CardStore::get_card($card_name);

					// do nothing if card is private and user is not authentified
					if($card === null || !$card->is_private || ($card->is_private && $batl_is_logged)){
						$json = array(
							'is_saved' => CardStore::upsert($card_name,$card_lines,$card_color,$is_private),
							'return_url' => Request::get_application_virtual_root().$card_name
						);
						echo json_encode($json);
						exit(0);
					}
					break;
				}
				case 'login' : {
					$identity = new Identity();
					$identity->password = Request::isset_or($_POST['password'], "prout");
					$result_code = AuthManager::authenticate(AuthManager::AuthTypePassword,$identity);
					$batl_is_logged = AuthManager::is_authenticated();

					if($result_code > 0){
						// TODO Error::get_message($result_code);
						$this->add_error("error $result_code");
					}else{
						$this->assign("logged", true);
					}
					break;
				}
				case 'logout' : {
					AuthManager::unauthenticate();
					break;
				}
				case 'search' : {
					// parsing search terms
					$request = SearchHelper::prepare_request($_POST['search']);
					header('location: '.Request::get_application_virtual_root().'all_cards/search/?request='.urlencode($request));
					break;
				}
			}
		}
		
		$fake_cards = array('all_cards','starred');
		
		$params = Request::get_params("@card_name/@command");
		if($params){
			//print_r($params);
			switch($params['command']){
				case 'edit':
					if(!in_array($params['card_name'],$fake_cards)){
						$ass_card = CardStore::get_card($params['card_name']);
						// Sets palette
						$palette = $color_scheme->palette;
						$palette_by_hue = array();
						foreach($palette AS $name => $hex){
							// Calculate HUE //
							$hsl = ImageHelper::rgb_to_hsl(ImageHelper::hex_to_rgb($hex));
							$order = '1'.str_replace('.','',sprintf("%010f", $hsl[0]))
								//.str_replace('.','',sprintf("%010f", $hsl[1]))
								.str_replace('.','',sprintf("%010f", ($hsl[2] > 0 ? 1/$hsl[2] : 999)))
								;
							$palette_by_hue[$order] = array('name' => $name, 'color' => $hex);
						}
						ksort($palette_by_hue);
						$this->assign('palette',$palette_by_hue);
						$this->assign('card',$ass_card);
						$this->assign('breadcrumbs',NavigationHelper::add_item(($ass_card->exists ? '<i>edit:</i>' : '<i>create:</i>').' '.$ass_card->display_name));
						if(($ass_card->is_private && $batl_is_logged) || !$ass_card->is_private){
							$ass_card->history = CardStore::get_card_history($params['card_name']);
							$this->display_page('section.card.update.tpl');
						}else{
							$this->display_page('section.card.tpl');
						}
					}
					break;
				case 'as_code':
					if(!in_array($params['card_name'],$fake_cards)){
						$result = new Response();
						if($params['card_name'] == 'all_cards'){
							$result->body = "";
						}else{
							$card_version = Request::isset_or($_GET["card_version"],null);
							if($card_version !== null){
								$old_card = CardStore::get_card_version($params['card_name'],$card_version);
								$result->body = $old_card->text_code;
							}else{
								$card = CardStore::get_card($params['card_name']);
								$result->body = $card->text_code;
							}
						}
						echo $result->to_json();
					}
					break;
				case 'as_html':
					if(!in_array($params['card_name'],$fake_cards)){
						$result = new Response();
						if($params['card_name'] == 'all_cards'){
							$result->body = '';
						}else{
							$ass_card = CardStore::get_card($params['card_name']);
							$this->assign('card',$ass_card);
							$this->assign('batl_is_logged',$batl_is_logged);
							$this->assign('show_banner',Request::isset_or($_GET['show_banner'],1));
							$this->assign('card_name',$ass_card->name);
							$this->assign('card_display_name',$ass_card->display_name);
							$this->assign('card_exists',$ass_card->exists);
							$result->body = $this->fetch_view('element.card.tpl');
							$result->loadable_link = $this->fetch_view('element.card.loadable.tpl');
						}
						echo $result->to_json();
					}
					break;
				case 'search':
					/* @todo
					 * Add search in card
					 */
					$request = Request::isset_or($_GET['request'],null);
					$this->assign('breadcrumbs',NavigationHelper::add_item(!is_null($request) ? '<i>search:</i> '.$request : 'nothing'));
					$ass_cards = CardStore::get_all_cards($request);
					if(!empty($ass_cards))
						$this->assign('cards',$ass_cards);
					$this->display_page('section.card.tpl');
					break;
			}
			return;
		}
		elseif($params = Request::get_params("@card_name")){
			switch($params['card_name']){
				case 'all_cards':
					$this->assign('breadcrumbs',NavigationHelper::add_item('All cards'));
					$ass_cards = CardStore::get_all_cards();
					if(!empty($ass_cards)){
						$this->assign('cards',$ass_cards);
						$this->display_page('section.card.tpl');
					}
					break;
				case 'starred':
					$this->assign('breadcrumbs',NavigationHelper::add_item('Starred'));
					$this->display_page('section.card.starred.tpl');
					break;
				default:
					// WIP TEMP FIX for wrong get_params() behavior
					/*$card_name = (isset($_GET['controller']) || (array_key_exists('controller',$_GET) && $_GET['controller'] === null)) 
						? $params['card_name'] 
						: ConfigurationSawhat::DEFAULT_CARD_NAME;
						echo $card_name . Request::get_current_params();*/
					$card_name = Request::get_current_params();
						
					$ass_card = CardStore::get_card($card_name);
					$this->assign('breadcrumbs',NavigationHelper::add_item($ass_card->display_name));
					$this->assign('card',$ass_card);
					/*var_dump(self::get_broken_links($ass_card));*/
					$this->display_page('section.card.tpl');
					break;
			}
			return;
		}


		$ass_card = CardStore::get_card(ConfigurationSawhat::DEFAULT_CARD_NAME);
		if($ass_card->exists){
			$this->assign('breadcrumbs',NavigationHelper::add_item($ass_card->display_name));
			$this->assign('card',$ass_card);
		}else{
			$this->assign('breadcrumbs',NavigationHelper::add_item('All cards'));
			$ass_cards = CardStore::get_all_cards();
			if(!empty($ass_cards))
				$this->assign('cards',$ass_cards);
		}
		$this->display_page('section.card.tpl');
	}

	// [sawhat/api] Submit a file to upload in AJAX.
	// returns a Response encoded in json containing the body of the response,
	// and the errors if any error occured
	public function api(){
		$result = new Response();
		// POST
		if(isset($_POST['submit'])) {
			$submit = $_POST['submit'];
			if($submit == "addfile"){
				$card_name = Request::isset_or($_POST['name'], "");
				if($card_name != "" && CardStore::exist($card_name)){
					$extensions = array(".jpg",".png",".jpeg",".JPG",".gif",".zip");
					try{
						$file = Uploader::process_form_file("file",CardStore::get_folder().$card_name,2000000,$extensions);
					}catch(Exception $e){ 
						$result->errors = "DON'T DO THAT"; 
						echo $result->to_json(); 
						return; 
					}

					// returns files
					$card = CardStore::get_card($card_name);
					$this->assign("card", $card);
					$body = $this->fetch_view("element.file_set.tpl");
					$result->body = $body;
				}
			}
		}
		echo $result->to_json();
	}

	// ---- Helpers ----

	private static function get_broken_links($card){
		$broken_links = array();
		$all_links = $card->get_all_links();
		foreach ($all_links as $link) {
			if(self::is_broken_link($link)){
				$broken_links[] = $link;
			}
		}
		return $broken_links;
	}

	private static function is_broken_link($url){
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HEADER, 1);
	    curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
	    $data = curl_exec($ch);
	    $headers = curl_getinfo($ch);
	    curl_close($ch);
	    $http_code = $headers['http_code'];
	    /*echo $url . " " . $http_code;*/
	    return ($http_code != '200' && $http_code != '301' && $http_code != '302' );
	}
}