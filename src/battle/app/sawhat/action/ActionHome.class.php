<?php
use BattlePHP\Core\Controller;
use BattlePHP\Core\Auth\AuthManager;
use BattlePHP\Core\Auth\Identity;
use BattlePHP\Api\Response;
use BattlePHP\Storage\Uploader;
use BattlePHP\Core\Request;
use BattlePHP\Imaging\ImageHelper;
require_once __DIR__.'/../config/config_sawhat.php';
require_once __DIR__.'/../model/Card.class.php';
require_once __DIR__.'/../model/CardStore.class.php';
require_once __DIR__.'/../model/ColorScheme.class.php';
require_once __DIR__.'/../model/NavigationHelper.class.php';
require_once __DIR__.'/../model/SearchHelper.class.php';
require_once __DIR__.'/../model/UploadFileForm.php';
/**********************************************************************
* ActionHome
*
* SAWHAT main controller
* 
* - index()
*   - [POST] save (card)
*   - [POST] login
*   - [POST] logout
*   - [POST] search
*   - [GET] "@card_name/@command" commands=[edit,as_code,as_html,search???]
*   - [GET] "@card_name" card_name=[all_cards,starred]
*
* - api()
*   - [POST] addfile
*
* @author jonpotiron, touchypunchy
*
***********************************************************************/
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
		$this->assign('color_schemes', ColorScheme::get_available_color_schemes());
		
		// check if any form submission (save or login-to-see-the-private-card)
		if(isset($_POST['submit'])){
			$submit_action = $_POST['submit'];
			switch ($submit_action) {
				// TODO move to api !
				case 'login' :
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
				case 'logout' :
					AuthManager::unauthenticate();
					break;
				case 'search' : 
					// parsing search terms
					$request = SearchHelper::prepare_request($_POST['search']);
					header('location: '.Request::get_application_virtual_root().'all_cards/search/?request='.urlencode($request));
					break;
			}
		}
		
		$fake_cards = ['all_cards','starred'];
		
		$params = Request::get_params("@card_name/@command");
		if($params){
			//print_r($params);
			switch($params['command']){
				case 'edit':
					if(!in_array($params['card_name'],$fake_cards)){
						$ass_card = CardStore::get_card($params['card_name']);
						// Sets palette
						$palette = $color_scheme->palette;
						$palette_by_hue = [];
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

						$this->assign([
							'card' => $ass_card,
							'upload_form' => new UploadFileForm('add_file_to_card',$ass_card->name),
							'palette' => $palette_by_hue,
							'breadcrumbs' => NavigationHelper::add_item(($ass_card->exists ? '<i>edit:</i>' : '<i>create:</i>').' '.$ass_card->display_name)
						]);
						if(($ass_card->is_private && $batl_is_logged) || !$ass_card->is_private){
							$ass_card->history = CardStore::get_card_history($params['card_name']);
							$this->display_page('section.card.update.tpl');
						}else{
							$this->display_page('section.card.tpl');
						}
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
					$card_name = Request::get_current_params();
					$ass_card = CardStore::get_card($card_name);
					$this->assign([
						'card' => $ass_card,
						'upload_form' => new UploadFileForm('add_file_to_card',$card_name),
						'breadcrumbs' => NavigationHelper::add_item($ass_card->display_name),
					]);
					$this->display_page('section.card.tpl');
					/*var_dump(self::get_broken_links($ass_card));*/
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

	// ---- Helpers ----

	private static function get_broken_links($card){
		$broken_links = [];
		$all_links = $card->get_all_links();
		foreach ($all_links as $link) {
			if(self::is_broken_link($link))
				$broken_links[] = $link;
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