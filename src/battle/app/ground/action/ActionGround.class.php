<?php
require_once 'core/storage/FileSystemIO.class.php';
require_once 'core/model/AjaxResult.class.php';

class ActionGround extends Controller{

	const ROOT = "app/ground/storage/";

	public function index(){
		
		$path = self::ROOT;		
		$folders = FileSystemIO::get_folders_in_dir($path."/*");
		$bg_images = null;
		$other_images = null;

		if (strpos($path,'bg') !== false)
			$bg_images = FileSystemIO::get_files_in_dir($path.'/{*.jpg,*.jpeg,*.JPG,*.png,*.gif}');
		else
			$other_images = FileSystemIO::get_files_in_dir($path.'/{*.jpg,*.jpeg,*.JPG,*.png,*.gif}');   
		
		$this->assign('path', $path);
		$this->assign('folders', $folders);
		$this->assign('bg_images', $bg_images);
		$this->assign('other_images', $other_images);
		$this->display_view('index.tpl');
	}

	public function path(){
		$path = Request::isset_or($_GET['path'],"");
	}

	public function api(){
		if(isset($_POST['data'])){
			$res = json_decode(stripcslashes($_POST['data']),true);
			$submit = $res['submit'];
			$ajax_result = new AjaxResult();

			switch ($submit){
				case "get_folder" : {
    				$path = $res['path'];
    				$path = str_replace('/..', '', $path);
    				$path = str_replace('//', '/', $path);
					if(!strncmp($path, self::ROOT, strlen(self::ROOT))){
						// NADA
					}else{
						$path = self::ROOT;
					}
    				$folders = FileSystemIO::get_folders_in_dir($path."/*");
    				$bg_images = null;
    				$other_images = null;

    				if (strpos($path,'bg') !== false)
						$bg_images = FileSystemIO::get_files_in_dir($path.'/{*.jpg,*.jpeg,*.JPG,*.png,*.gif}');
    				else
						$other_images = FileSystemIO::get_files_in_dir($path.'/{*.jpg,*.jpeg,*.JPG,*.png,*.gif}');    				
					
					$this->assign('path', $path);
					$this->assign('folders', $folders);
					$this->assign('bg_images', $bg_images);
					$this->assign('other_images', $other_images);
					$ajax_result->body .= $this->fetch_view('element.folder.tpl');
					break;
				}
    			case "get_parent_folder" : {

    				$path = $res['path'];
					$path = substr($path, 0, strrpos($path, '/'));
					$path = str_replace('/..', '', $path);
					//if path start with self::ROOT
					if(!strncmp($path, self::ROOT, strlen(self::ROOT))){
						// NADA
					}else{
						$path = self::ROOT;
					}
    				$folders = FileSystemIO::get_folders_in_dir($path."/*");
    				$bg_images = null;
    				$other_images = null;

    				if (strpos($path,'bg') !== false)
						$bg_images = FileSystemIO::get_files_in_dir($path.'/{*.jpg,*.jpeg,*.JPG,*.png,*.gif}');
    				else
						$other_images = FileSystemIO::get_files_in_dir($path.'/{*.jpg,*.jpeg,*.JPG,*.png,*.gif}');    				
					
					$this->assign('path', $path);
					$this->assign('folders', $folders);
					$this->assign('bg_images', $bg_images);
					$this->assign('other_images', $other_images);
					$ajax_result->body .= $this->fetch_view('element.folder.tpl');
					break;
				}
			}
			echo $ajax_result->to_json();
			return;
		}
	}
}
?>