<?php
/**
* Image Helper (WIP)
*/
class ImageHelper{
	// http://salman-w.blogspot.fr/2008/10/resize-images-using-phpgd-library.html
	const THUMBNAIL_MAX_WIDTH = 320;
	const THUMBNAIL_MAX_HEIGHT = 320;

	const SMALL_SIZE = 320;  // s_
	const MEDIUM_SIZE = 960; // m_
	const LARGE_SIZE = 1920; // l_
	const EXTRA_LARGE_SIZE = 3840; // l_

	const JPG_QUALITY = 90; // 0-100 (0:small,ugly - 100:fat,beautifull)
	const PNG_QUALITY = 6;  // 0-9 (0:nocompression - 9:maxcompress)

	private static function create_gd_image($source_image_path, $source_image_type){
		switch ($source_image_type) {
			case IMAGETYPE_GIF:
				return imagecreatefromgif($source_image_path);
			case IMAGETYPE_JPEG:
				return imagecreatefromjpeg($source_image_path);
			case IMAGETYPE_PNG:
				return imagecreatefrompng($source_image_path);
			default :
				return false;
		}
	}

	private static function save_gd_image($gd_image, $image_type, $image_path){
		switch ($image_type) {
			case IMAGETYPE_GIF:
				imagegif($gd_image, $image_path);
				break;
			case IMAGETYPE_JPEG:
				imagejpeg($gd_image, $image_path, self::JPG_QUALITY);
				break;
			case IMAGETYPE_PNG:
				imagepng($gd_image, $image_path, self::PNG_QUALITY);
				break;
		}
	}

	// generate a square thumbnail from a source image (cropped)
	public static function generate_thumbnail($source_image_path, $thumbnail_image_path, $max_size = null){
		if($max_size === null){
			$max_width = self::THUMBNAIL_MAX_WIDTH;
			$max_height = self::THUMBNAIL_MAX_HEIGHT;
		}

		list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
		$source_gd_image = self::create_gd_image($source_image_path,$source_image_type);
		if($source_gd_image === false)
			return false;

		$source_aspect_ratio = $source_image_width / $source_image_height;
		$thumbnail_aspect_ratio = $max_width / $max_height;
		
		if($thumbnail_aspect_ratio > $source_aspect_ratio) {
			$thumbnail_image_width = $max_width;
			$thumbnail_image_height = $max_height;
			$src_x = 0;
			$src_y = ($source_image_height - $source_image_width) / 2;
			$source_image_height = $source_image_width;
		}else{
			$thumbnail_image_width = $max_width;
			$thumbnail_image_height = $max_height;
			$src_y = 0;
			$src_x = ($source_image_width - $source_image_height) / 2;
			$source_image_width = $source_image_height;
		}
		$thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
		imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, $src_x, $src_y, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
		self::save_gd_image($thumbnail_gd_image, $source_image_type, $thumbnail_image_path);
		imagedestroy($source_gd_image);
		imagedestroy($thumbnail_gd_image);
		return true;
	}

	public static function resize_image($source_image_path, $thumbnail_image_path, $max_width = null, $max_height = null, $resize_if_smaller = false){
		if($max_width === null)
			$max_width = self::MEDIUM_SIZE;
		if($max_height === null)
			$max_height = self::MEDIUM_SIZE;

		list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
		$source_gd_image = self::create_gd_image($source_image_path,$source_image_type);
		if($source_gd_image === false)
			return false;
		
		$source_aspect_ratio = $source_image_width / $source_image_height;
		$thumbnail_aspect_ratio = $max_width / $max_height;
		if($source_image_width <= $max_width && $source_image_height <= $max_height) {
			$thumbnail_image_width = $source_image_width;
			$thumbnail_image_height = $source_image_height;
		}elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
			$thumbnail_image_width = (int) ($max_height * $source_aspect_ratio);
			$thumbnail_image_height = $max_height;
		}else {
			$thumbnail_image_width = $max_width;
			$thumbnail_image_height = (int) ($max_width / $source_aspect_ratio);
		}
		$thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
		imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
		self::save_gd_image($thumbnail_gd_image, $source_image_type, $thumbnail_image_path);
		imagedestroy($source_gd_image);
		imagedestroy($thumbnail_gd_image);
		return true;
	}

	public static function crop_image($source_image_path, $thumbnail_image_path, $x1, $y1, $w1, $h1, $max_width = null, $max_height = null){
		if($max_width === null)
			$max_width = self::LARGE_SIZE;
		if($max_height === null)
			$max_height = self::LARGE_SIZE;

		list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
		$source_gd_image = self::create_gd_image($source_image_path,$source_image_type);
		if($source_gd_image === false)
			return false;
		
		$max = max($w1,$h1);
		if($max - min($w1,$h1) <= 1)
			$w1 = $h1;
		$coef = $max_width / $max;
		$thumbnail_image_width = (int) ($w1 * $coef);
		$thumbnail_image_height = (int) ($h1 * $coef);

		$thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
		imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, $x1, $y1, $thumbnail_image_width, $thumbnail_image_height, $w1, $h1);
		self::save_gd_image($thumbnail_gd_image, $source_image_type, $thumbnail_image_path);
		imagedestroy($source_gd_image);
		imagedestroy($thumbnail_gd_image);
		return true;
	}

	public static function draw_image($source_image_path, $thumbnail_image_path, $sx, $sy, $sw, $sh, $dx, $dy, $dw, $dh, $result_image_width, $result_image_height, $max_width = null, $max_height = null){
		if($max_width === null)
			$max_width = self::EXTRA_LARGE_SIZE;
		if($max_height === null)
			$max_height = self::EXTRA_LARGE_SIZE;
		if($result_image_width > $max_width || $result_image_height > $max_height)
			return false;

		list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
		$source_gd_image = self::create_gd_image($source_image_path,$source_image_type);
		if($source_gd_image === false)
			return false;

		$thumbnail_gd_image = imagecreatetruecolor($result_image_width, $result_image_height);

		imagecopyresampled($thumbnail_gd_image, $source_gd_image, $dx, $dy, $sx, $sy, $dw, $dh, $sw, $sh);
		self::save_gd_image($thumbnail_gd_image, $source_image_type, $thumbnail_image_path);
		imagedestroy($source_gd_image);
		imagedestroy($thumbnail_gd_image);
		return true;
	}

	public static function crop_image_from_ratio($source_image_path, $thumbnail_image_path, $ratio){
		list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
		$source_gd_image = self::create_gd_image($source_image_path,$source_image_type);
		if($source_gd_image === false)
			return false;

		$source_aspect_ratio = $source_image_width / $source_image_height;
		$thumbnail_aspect_ratio = $ratio;
		if($thumbnail_aspect_ratio > $source_aspect_ratio) {
			$thumbnail_image_height = $source_image_width / $thumbnail_aspect_ratio;
			$thumbnail_image_width = $source_image_width;
			$src_x = 0;
			$src_y = ($source_image_height - $thumbnail_image_height) / 2;
			$source_image_height = $thumbnail_image_height;
		}else{
			$thumbnail_image_height = $source_image_height;
			$thumbnail_image_width = $source_image_height * $thumbnail_aspect_ratio;
			$src_y = 0;
			$src_x = ($source_image_width - $thumbnail_image_width) / 2;
			$source_image_width = $thumbnail_image_width;
		}
		$thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
		imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, $src_x, $src_y, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
		self::save_gd_image($thumbnail_gd_image, $source_image_type, $thumbnail_image_path);
		imagedestroy($source_gd_image);
		imagedestroy($thumbnail_gd_image);
		return true;
	}
}
?>