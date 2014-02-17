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

class ActionMain extends Controller{

	// Display the home page containing the home card if it exists, 
	// all the cards otherwise.
	// Treats any creation/update query submission.
	public function index(){
		$logged = AuthHelper::is_authenticated();

		// check if any form submission (save or login-to-see-the-private-card)
		if(isset($_POST['submit'])){
			if($_POST['submit'] == "save"){
				$card_name = Request::isset_or($_POST['name'], "home");
				$card_color = Request::isset_or($_POST['color'], "#f90");
				$card_lines = Request::isset_or($_POST['card'], "");
				$is_private = isset($_POST['is_private']);
				$card = CardStore::get($card_name);
				// do nothing if card is private and user is not authentified
				if(($card->is_private && $logged && $card !== null) || !$card->is_private){
					echo $card_lines;
					CardStore::upsert($card_name,$card_lines,$card_color,$is_private);
				}
			}
			elseif($_POST['submit'] == "login"){
				$identity = new Identity();
				$identity->password = Request::isset_or($_POST['password'], "prout");
				$result_code = AuthHelper::authenticate(AuthHelper::AuthTypePassword,$identity);
				if($result_code > 0){
					// TODO Error::get_message($result_code);
					$this->add_error("error $result_code");
				}else{
					$this->assign("logged", true);
					header("Location: ".Request::get_application_virtual_root());
				}
			}
		}

		$params = Request::get_params("@cardname/@action");
		if($params){
			$ass_card = CardStore::get($params['cardname']);
			if($ass_card)
				$this->assign('card',$ass_card);
			$this->display_page('section.card.update.tpl');
			return;
		}
		elseif($params = Request::get_params("@cardname")){
			$ass_card = CardStore::get($params['cardname']);
			if($ass_card){
				$this->assign('card',$ass_card);
				$this->display_page('section.card.tpl');
			}
			else{
				$card = new Card();
				$card->name = strtolower($params['cardname']);
				$card->text_code = " ";
				$card->text_code_length = 1;
				$this->assign('card',$card);
				$this->display_page('section.card.update.tpl');
			}
			return;
		}

		// TODO : show home 
		$ass_card = CardStore::get("home");
		if($ass_card){
			$this->assign('card',$ass_card);
		}else{
			$ass_cards = CardStore::get_all();
			if($ass_cards)
				$this->assign('cards',$ass_cards);
		}
		$this->display_page('section.card.tpl');
	}

	// [sawhat/api] Submit a file to upload in AJAX.
	// returns a AjaxResult encoded in json containing the body of the response,
	// and the errors if any error occured
	public function api(){
		$result = new AjaxResult();
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
		echo $result->to_json();
	}
}
?>