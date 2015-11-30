<?php
use BattlePHP\Core\Controller;
use BattlePHP\Core\Request;
require_once __DIR__.'/../model/UploadFileForm.php';
require_once __DIR__.'/../model/DatFileManager.php';
/**
 * CLASS ActionHome (Controller)
 *
 * An example controller for 101_upload app.
 * 
 * Pages:
 * - index : /home or /
 *
 */
class ActionHome extends Controller{

	// [/home,/]
	public function index(){
		$rendering_mode = Request::isset_or($_GET["mode"], null);

		// DEFAULT display
		if($rendering_mode !== "zen"){
			$this->assign([
				'title' => "101_upload",
				'upload_form' => new UploadFileForm(),
				'dat_file' => DatFileManager::get_dat_file()
			]);
			$this->display_view('section.index.tpl');
		}
		// ZEN display
		else{
			$this->display_view(
				'section.zen.tpl', 
				[
					'title' => "101_upload?mode=zen",
					'upload_form' => new UploadFileForm(),
					'dat_file' => DatFileManager::get_dat_file()
				]
			);
		}
	}
}
