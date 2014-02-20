<?php
require_once 'core/storage/Uploader.class.php';
require_once 'core/imaging/ImageHelper.class.php';

class ActionMain extends Controller{

	const IMAGE_SOURCE_PATH = 'app/crappycrop/public/images/lechat.jpg';
	const IMAGE_RESULT_PATH = 'app/crappycrop/public/images/result.jpg';

	private function str_starts_with($haystack, $needle)
	{
	     $length = strlen($needle);
	     return (substr($haystack, 0, $length) === $needle);
	}

	public function index(){
		if(isset($_POST['data'])){
			$res = json_decode(stripcslashes($_POST['data']),true);
			$submit = $res['submit'];
			if($submit == "save"){
				$img = $res['image'];
				if($this->str_starts_with($img,"data:image/png")){
					$img = str_replace('data:image/png;base64,', '', $img);
				}else{
					$img = str_replace('data:image/jpeg;base64,', '', $img);
				}
				$img = str_replace(' ', '+', $img);
				$data = base64_decode($img);

				//Note : result.jpg should be result.png sometimes ! 
				$success = file_put_contents('app/crappycrop/public/images/result.jpg', $data);
			}else if($submit == "crop_and_save"){
				$crop_data = $res['crop_data'];
				$sx = $crop_data['sx'];
				$sy = $crop_data['sy'];
				$sw = $crop_data['sw'];
				$sh = $crop_data['sh'];
				$dx = $crop_data['dx'];
				$dy = $crop_data['dy'];
				$dw = $crop_data['dw'];
				$dh = $crop_data['dh'];
				$fw = $crop_data['fw'];
				$fh = $crop_data['fh'];
				ImageHelper::draw_image(self::IMAGE_SOURCE_PATH, self::IMAGE_RESULT_PATH, $sx, $sy, $sw, $sh, $dx, $dy, $dw, $dh, $fw, $fh);
			}
		}
		$this->display_view('index4.tpl');
	}
}
?>