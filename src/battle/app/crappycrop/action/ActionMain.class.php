<?php
require_once 'core/storage/Uploader.class.php';

class ActionMain extends Controller{
	public function index(){
		if(isset($_POST['data'])){
			$res = json_decode(stripcslashes($_POST['data']),true);
			$submit = $res['submit'];
			if($submit == "save"){
				$img = $res['image'];
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$data = base64_decode($img);
				$success = file_put_contents('app/crappycrop/public/images/result.jpg', $data);
			}
		}
		$this->display_view('index3.tpl');
	}
}
?>