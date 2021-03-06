<?php
use BattlePHP\Core\Request;
use BattlePHP\Core\Viewer;
use BattlePHP\Storage\FileSystemIO;
/**********************************************************************
* ColorScheme
*
* @author jonpotiron, touchypunchy
*
***********************************************************************/
class ColorScheme{
	
	const FILE_PATH = 'public/css/color_scheme/';
	public $name;
	public $is_default = false;
	public $css_path = null;
	public $palette = [];
	public $is_defined = false;
	
	public function __construct($color_scheme_name = null){
		if(is_null($color_scheme_name)){
			if(defined('ConfigurationSawhat::COLOR_SCHEME') && ConfigurationSawhat::COLOR_SCHEME !== ''){
				$color_scheme_name = ConfigurationSawhat::COLOR_SCHEME;
				$this->is_default = true;
			} else {
				$color_scheme_name = 'default';
			}
		}
		
		$this->name = $color_scheme_name;
		$css_path = Request::get_application_path().self::FILE_PATH.$this->name.'.css';
		if(is_file($css_path)){
			$this->css_path = $css_path;
			$this->is_defined = true;
		}
		
		$palette_definition_path = Request::get_application_path().self::FILE_PATH.$this->name.'_palette.less';
		if(is_file($palette_definition_path)){
			$lines = file($palette_definition_path);
			foreach($lines AS $line){
				if(preg_match('/^\s*@(.+) = #(\w+)\s*;\s*$/',$line,$matches)){
					$this->palette[$matches[1]] = $matches[2];
				}
			}
		}
	}
	
	/*
	 * Gets array of color scheme objects.
	 *
	 */
	public static function get_available_color_schemes(){
		$color_schemes = [];
		$color_schemes_css_files = FileSystemIO::get_files_in_dir(Request::get_application_path().self::FILE_PATH.'{*.css}');

		foreach($color_schemes_css_files AS $key => $css_file)
			$color_schemes[] = new ColorScheme(str_replace('.css','',$css_file->name));
		
		return $color_schemes;
	}
}