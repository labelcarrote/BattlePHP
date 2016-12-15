<?php
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
class ActionHome extends BattlePHP\Core\Controller{

	// [/home,/]
	public function index(){
		$rendering_mode = Request::isset_or($_GET["mode"], null);
		
		$upload_form = new UploadFileForm(DatFileManager::MAX_FILE_SIZE);
		$upload_form->mode = $rendering_mode;

		$dat_file = DatFileManager::get_dat_file();

		switch ($rendering_mode) {
			// ZEN display
			case 'zen':
				$this->display_view(
					'section.zen.tpl', 
					[
						'title' => "NanoChan",
						'upload_form' => $upload_form,
						'dat_file' => $dat_file
					]
				);
				break;
			// Button display
			case 'button':
				$this->display_view(
					'section.button.tpl', 
					[
						'title' => "101_upload?mode=button",
						'upload_form' => $upload_form,
						'dat_file' => $dat_file
					]
				);
				break;
			// Default display
			default:
				$this->display_view(
					'section.index.tpl',
					[
						'title' => "101_upload",
						'upload_form' => $upload_form,
						'dat_file' => $dat_file
					]
				);
				break;
		}
	}
}
