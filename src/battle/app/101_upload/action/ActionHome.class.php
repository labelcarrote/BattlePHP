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
		switch ($rendering_mode) {
			case 'zen':
				$this->display_view(
					'section.zen.tpl', 
					[
						'title' => "NanoChan",
						'upload_form' => new UploadFileForm(),
						'dat_file' => DatFileManager::get_dat_file()
					]
				);
				break;
			case 'button':
				$this->display_view(
					'section.button.tpl', 
					[
						'title' => "101_upload?mode=button",
						'upload_form' => new UploadFileForm(),
						'dat_file' => DatFileManager::get_dat_file()
					]
				);
				break;
			default:
				$this->assign([
					'title' => "101_upload",
					'upload_form' => new UploadFileForm(),
					'dat_file' => DatFileManager::get_dat_file()
				]);
				$this->display_view('section.index.tpl');
				break;
		}
	}
}
