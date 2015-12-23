<?php
use BattlePHP\Core\Controller;
require_once __DIR__.'/../config/config_sawhat.php';
require_once __DIR__.'/../model/CardStore.class.php';
require_once __DIR__.'/../model/ColorScheme.class.php';
require_once __DIR__.'/../model/NavigationHelper.class.php';
require_once __DIR__.'/../model/UploadFileForm.php';
/********************************************************************
* CLASS ActionDesignguideline (Controller)
* 
* Pages:
* - index : /designguideline
*
*********************************************************************/
class ActionDesignguideline extends Controller{
	
	// [/designguideline]
	public function index(){

		$card_name = "designguideline";
		$card = CardStore::get_card($card_name); 
		$color_scheme = new ColorScheme();

		$this->assign([
			'title' => "Design Guideline | Sawhat",
			// Assign default color scheme
			'color_scheme' => $color_scheme->name,
			// Sets color scheme available
			'color_schemes' => ColorScheme::get_available_color_schemes(),
			'card' => $card,
			'upload_form' => new UploadFileForm('add_file_to_card',$card_name),
			'breadcrumbs' => NavigationHelper::add_item($card->display_name),
		]);

		$this->display_page('section.designguideline.tpl');
	}
}