<?php
require_once 'app/sawhat/model/CardFactory.class.php';
require_once 'app/sawhat/model/CardStore.class.php';
require_once 'core/storage/Uploader.class.php';
require_once 'core/auth/AuthHelper.class.php';
require_once 'core/model/AjaxResult.class.php';

// SAWHAT main controller
// TODO : 
// - add a ajax method to get a specific page (with edit feature?)
// - + add some client side / js for inplace open/edit

class ActionHome extends Controller{

	// Display the home page containing the home card if it exists, 
	// all the cards otherwise.
	// Treats any creation/update query submission.
	public function index(){
		$logged = AuthHelper::is_authenticated();

		// check if any form submission (save or login-to-see-the-private-card)
		if(isset($_POST['submit'])){
			$submit_action = $_POST['submit'];
			switch ($submit_action) {
				case 'save' : {
					$card_name = Request::isset_or($_POST['name'], "home");
					$card_color = Request::isset_or($_POST['color'], Card::DEFAULT_COLOR);
					$card_lines = Request::isset_or($_POST['card'], "");
					$is_private = isset($_POST['is_private']);
					$card = CardStore::get($card_name);

					// do nothing if card is private and user is not authentified
					if($card === null || !$card->is_private || ($card->is_private && $logged)){
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
					$result_code = AuthHelper::authenticate(AuthHelper::AuthTypePassword,$identity);
					$logged = AuthHelper::is_authenticated();

					if($result_code > 0){
						// TODO Error::get_message($result_code);
						$this->add_error("error $result_code");
					}else{
						$this->assign("logged", true);
					}
					break;
				}
				case 'logout' : {
					AuthHelper::unauthenticate();
					break;
				}
				case 'search' : {
					header('location: '.Request::get_application_virtual_root().$_POST['search'].'/all_cards');
					break;
				}
			}
		}
		
		$params = Request::get_params("@cardname/@action");
		if($params){
			switch($params['action']){
				case 'edit':
					$ass_card = CardStore::get($params['cardname']);
					$this->assign('card',$ass_card);
					if(($ass_card->is_private && $logged) || !$ass_card->is_private){
						$ass_card->history = CardStore::get_card_history($params['cardname']);
						$this->display_page('section.card.update.tpl');
					}else{
						$this->display_page('section.card.tpl');
					}
					break;
				case 'all_cards':
					$ass_cards = CardStore::get_all($params['cardname']);
					if(!empty($ass_cards))
						$this->assign('cards',$ass_cards);
					$this->display_page('section.card.tpl');
					break;
			}
			return;
		}
		elseif($params = Request::get_params("@cardname")){
			if($params['cardname'] == 'all_cards'){
				$ass_cards = CardStore::get_all();
				if(!empty($ass_cards)){
					$this->assign('cards',$ass_cards);
					$this->display_page('section.card.tpl');
				}
			}else{
				$ass_card = CardStore::get($params['cardname']);
				$this->assign('card',$ass_card);
				$this->display_page('section.card.tpl');
			}
			return;
		}

		$ass_card = CardStore::get("home");
		if($ass_card->exists){
			$this->assign('card',$ass_card);
		}else{
			$ass_cards = CardStore::get_all();
			if(!empty($ass_cards))
				$this->assign('cards',$ass_cards);
		}
		$this->display_page('section.card.tpl');
	}

	// [sawhat/api] Submit a file to upload in AJAX.
	// returns a AjaxResult encoded in json containing the body of the response,
	// and the errors if any error occured
	public function api(){
		$result = new AjaxResult();
		// POST
		if(isset($_POST['submit'])) {
			$submit = $_POST['submit'];
			if($submit == "addfile"){
				$card_name = Request::isset_or($_POST['name'], "");
				if($card_name != "" && CardStore::exist($card_name)){
					$extensions = array(".jpg",".png",".jpeg",".JPG",".gif");
					try{
						$file = Uploader::process_form_file("file",CardStore::get_folder().$card_name,2000000,$extensions);
					}catch(Exception $e){ 
						$result->errors = "DON'T DO THAT"; 
						echo $result->to_json(); 
						return; 
					}

					// returns files
					$card = CardStore::get($card_name);
					$this->assign('files', $card->files);
					$body = $this->fetch_view("element.file_set.tpl");
					$result->body = $body;
				}
			}
		}
		// GET WIP
		else{
			$params = Request::get_params("@action/@params");
			if($params){
				switch($params['action']){
					case 'edit':
						$ass_card = CardStore::get($params['cardname']);
						$this->assign('card',$ass_card);
						if(($ass_card->is_private && $logged) || !$ass_card->is_private){
							$ass_card->history = CardStore::get_card_history($params['cardname']);
							$this->display_page('section.card.update.tpl');
						}else{
							$this->display_page('section.card.tpl');
						}
						break;
					case 'all_cards':
						$ass_cards = CardStore::get_all($params['cardname']);
						if(!empty($ass_cards))
							$this->assign('cards',$ass_cards);
						$this->display_page('section.card.tpl');
						break;
				}
			}
		}

		echo $result->to_json();
	}
}
?>