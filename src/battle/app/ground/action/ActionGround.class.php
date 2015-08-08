<?php
use BattlePHP\Core\Controller;
use BattlePHP\Storage\FileSystemIO;
use BattlePHP\Api\Response;

class ActionGround extends Controller{

	const STORAGE_PATH = "app/ground/storage";

	public function index(){
		$path = self::STORAGE_PATH;		
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

	public function api(){
		if(isset($_POST['data'])){
			$res = json_decode(stripcslashes($_POST['data']),true);
			$submit = $res['submit'];
			$response = new Response();

			switch ($submit){
				case "get_folder" : {
    				$path = $res['path'];
    				$path = str_replace('/..', '', $path);
    				$path = str_replace('//', '/', $path);
					if(!strncmp($path, self::STORAGE_PATH, strlen(self::STORAGE_PATH))){
						// NADA
					}else{
						$path = self::STORAGE_PATH;
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
					$response->body .= $this->fetch_view('element.folder.tpl');
					break;
				}
    			case "get_parent_folder" : {

    				$path = $res['path'];
					$path = substr($path, 0, strrpos($path, '/'));
					$path = str_replace('/..', '', $path);
					//if path start with self::STORAGE_PATH
					if(!strncmp($path, self::STORAGE_PATH, strlen(self::STORAGE_PATH))){
						// NADA
					}else{
						$path = self::STORAGE_PATH;
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
					$response->body .= $this->fetch_view('element.folder.tpl');
					break;
				}
			}
			echo $response->to_json();
			return;
		}
	}
}