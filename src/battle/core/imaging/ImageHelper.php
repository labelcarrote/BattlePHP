<?php
use BattlePHP\Storage;
namespace BattlePHP\Imaging;
/**
* Image Helper (WIP)
*/
class ImageHelper{
	// http://salman-w.blogspot.fr/2008/10/resize-images-using-phpgd-library.html
	const THUMBNAIL_MAX_WIDTH = 320;
	const THUMBNAIL_MAX_HEIGHT = 320;

	const SMALL_SIZE = 320;
	const MEDIUM_SIZE = 960;
	const LARGE_SIZE = 1920;
	const EXTRA_LARGE_SIZE = 3840;

	const DEFAULT_SIZE_SUFFIX = "";
	const SMALL_SIZE_SUFFIX = "_s";
	const MEDIUM_SIZE_SUFFIX = "_m";
	const LARGE_SIZE_SUFFIX = "_l";
	const EXTRA_LARGE_SUFFIX = "_xl";

	const JPG_QUALITY = 90; // 0-100 (0:small,ugly - 100:fat,beautifull)
	const PNG_QUALITY = 6;  // 0-9 (0:nocompression - 9:maxcompress)

	// - retrieves an image from $source_image_path
	// - if the image is wider than the specified $max_width and $max_height (or medium size if not specified), 
	//   resizes it keeping its ratio, otherwise compresses it
	// - stores it in the specified output path $thumbnail_image_path
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

	public static function fit_image_from_ratio($source_image_path, $thumbnail_image_path, $ratio){
		list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
		$source_gd_image = self::create_gd_image($source_image_path,$source_image_type);
		if($source_gd_image === false)
			return false;

		$source_aspect_ratio = $source_image_width / $source_image_height;
		$thumbnail_aspect_ratio = $ratio;
		if($thumbnail_aspect_ratio < $source_aspect_ratio) {
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

	// generates all sizes : s,m,l !!
	public static function generate_all_sizes($source_image_path){
		// s
		self::resize_image(
			$source_image_path,
			self::add_suffix_to_file_name($source_image_path, self::SMALL_SIZE_SUFFIX),
			self::SMALL_SIZE,
			self::SMALL_SIZE
		);

		// m 
		self::resize_image(
			$source_image_path,
			self::add_suffix_to_file_name($source_image_path, self::MEDIUM_SIZE_SUFFIX),
			self::MEDIUM_SIZE,
			self::MEDIUM_SIZE
		);

		// l 
		self::resize_image(
			$source_image_path,
			self::add_suffix_to_file_name($source_image_path, self::LARGE_SIZE_SUFFIX),
			self::LARGE_SIZE,
			self::LARGE_SIZE
		);

		return true;
	}

	public static function delete_all_sizes($source_image_path,$do_delete_source = false){
		if($do_delete_source === true)
			FileSystemIO::delete_file($source_image_path);
		FileSystemIO::delete_file(self::add_suffix_to_file_name($source_image_path, self::SMALL_SIZE_SUFFIX));
		FileSystemIO::delete_file(self::add_suffix_to_file_name($source_image_path, self::MEDIUM_SIZE_SUFFIX));
		FileSystemIO::delete_file(self::add_suffix_to_file_name($source_image_path, self::LARGE_SIZE_SUFFIX));
	}

	public static function add_suffix_to_file_name($file_name, $suffix){
		return self::str_lreplace(".", $suffix.".", $file_name);
	}

	/* Gets rgb values from a hexadecimal color value
	 *
	 * @param string $hex
	 * @return array [r,g,b]
	 *
	 */
	public static function hex_to_rgb($hex){
		$hex = str_replace('#','',$hex);
		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		return array($r,$g,$b);
	}
	
	/* Gets hsl values from a rgb array
	 *
	 * @param array $rgb [r,g,b]
	 * @return array [h,s,l]
	 *
	 */
	public static function rgb_to_hsl($rgb){
		$r = $rgb[0]/255;
		$g = $rgb[1]/255;
		$b = $rgb[2]/255;
		$max = max($r,$g,$b);
		$min = min($r,$g,$b);
		$h = $s = $l = ($max+$min)/2;
		
		if($max == $min){
			// achromatic
			$h = $s = 0;
		} else {
			$diff = $max-$min;
			$s = $l > 0.5 ? $diff/(2-$max-$min) : $diff/($max+$min);
			switch($max){
				case $r:
					$h = ($g-$b)/$diff+($g < $b ? 6 : 0);
					break;
				case $g:
					$h = ($b-$r)/$diff+2;
					break;
				case $b:
					$h = ($r-$g)/$diff+4;
					break;
			}
			$h /= 6;
		}
		
		return array($h,$s,$l);
	}
	
	/*
	 * Gets the perveived brightness from a rgb array
	 * thanks to http://alienryderflex.com/hsp.html
	 *
	 */
	public static function rgb_to_perceived_brightness($rgb){
		return (sqrt((0.299*pow($rgb[0],2))+(0.587*pow($rgb[1],2))+(0.114*pow($rgb[2],2)))) / 255;
	}

	public static function create_text_image($text, $output_path, $width = null, $height = null){
		// header("Content-type: image/png");
		$font  = 2;
		$lines = preg_split("/\r\n|\n|\r/", $text);
		$lines_count = count($lines);
		if($lines_count <= 1){
			$width  = 24 + imagefontwidth($font) * strlen($text);
			$height = 24 + imagefontheight($font);
			$image = imagecreatetruecolor ($width,$height);
			$white = imagecolorallocate ($image,255,255,255);
			$black = imagecolorallocate ($image,0,0,0);
			imagefill($image,0,0,$black);
			imagestring ($image,$font,12,12,$text,$white);
		}else{
			$longest_line = "";
			foreach ($lines as $line) {
				if(strlen($line) > strlen($longest_line))
					$longest_line = $line;
			}
			$width  = 24 + imagefontwidth($font) * strlen($longest_line);
			$height = 24 + (imagefontheight($font)+6) * $lines_count;
			$image = imagecreatetruecolor ($width,$height);
			$white = imagecolorallocate ($image,255,255,255);
			$black = imagecolorallocate ($image,0,0,0);
			imagefill($image,0,0,$black);
			$i = 1;
			foreach ($lines as $line) {
				imagestring ($image,$font,12,(12+6)*$i,$line,$white);	
				$i++;
			}
		}
		// WIP 
		//imagettftext($image, 14, 0, 12, 12, $white, $font, $text );

		imagepng($image, $output_path, self::PNG_QUALITY);
		imagedestroy($image);
	}


	// ---- Helpers ----
	
	private static function str_lreplace($search, $replace, $subject){
	    $pos = strrpos($subject, $search);
	    if($pos !== false)
	        $subject = substr_replace($subject, $replace, $pos, strlen($search));
	    return $subject;
	}

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
}
