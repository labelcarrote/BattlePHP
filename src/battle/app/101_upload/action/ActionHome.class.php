<?php
use BattlePHP\Core\Controller;
use BattlePHP\Core\Request;
require_once 'app/101_upload/model/UploadFileForm.php';
require_once 'app/101_upload/model/DatFileManager.php';
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
		$upload_form = new UploadFileForm();

		$this->assign([
			'title' => "101_upload",
			'upload_form' => $upload_form,
			'dat_file_url' => DatFileManager::get_dat_file_url() 
		]);
		
		$this->display_view('section.index.tpl');
	}
}