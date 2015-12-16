<?php
use BattlePHP\Core\Auth\AuthManager;
use BattlePHP\Imaging\ImageHelper;
use BattlePHP\Core\Viewer;
require_once 'app/sawhat/model/CardElement.class.php';
/**********************************************************************
* Card
*
* @author jonpotiron, touchypunchy
*
***********************************************************************/
class Card implements JsonSerializable{
	
	const DEFAULT_COLOR = '#f90';

	public $is_private = false;
	public $is_recursive;
	public $color = self::DEFAULT_COLOR;
	public $is_light = true;
	public $properties;
	public $last_edit;
	public $name;
	public $display_name;
	public $text_code = '';
	public $elements;
	public $html = '';
	public $files;
	public $exists = false;
	public $history;
	
	public function __construct($name, $lines = [], $recursive_level = 0){
		$this->exists = CardStore::exist($name);
		$this->name = $name;
		$this->display_name = self::get_display_name($name);
		$this->lines = $lines;
		$this->is_recursive = $recursive_level > 0;
		$this->recursive_level = $recursive_level;
		$this->elements = [];
		$init = true;
		$element = null;
		$previous_element = (object)['multiple_line' => false, 'html_closure_tag' => ''];
		$need_closure = false;
		
		foreach($lines as $line){
			if($init){
				$this->parse_special_properties($line);
			}else{
				$this->text_code .= $line;
				$line = html_entity_decode($line,ENT_COMPAT,'UTF-8');
				$card_element = new CardElement($name,$line,$recursive_level);

				// close multiple line tag
				if($previous_element->multiple_line
					&& (!$card_element->multiple_line || $previous_element->html_closure_tag != $card_element->html_closure_tag))
				{
					$this->elements[] = $this->close_multiple_line_tag($previous_element);
					$need_closure = false;
				}
				// open multiple line tag
				if($card_element->multiple_line	
					&& (!$previous_element->multiple_line || $previous_element->html_closure_tag != $card_element->html_closure_tag))
				{
					$this->elements[] = $this->open_multiple_line_tag($card_element);
					$need_closure = true;
				}
				// must add newline for post parser
				$card_element->html .= "\n";

				if(empty($card_element->coding_language))
					$this->elements[] = $card_element;
					
				$previous_element = $card_element;
			}
			if(trim($line) == '')
				$init = false;
		}
		if($need_closure){
			$this->elements[] = $this->close_multiple_line_tag($previous_element);
		}
		$this->get_style_definition();
		
		// set html as a whole
		$card_to_include = array();
		foreach($this->elements AS $k => $element){
			if(isset($element->cards) && count($element->cards) > 0){
				$this->html .= '<div class="column_container auto_clear">';
				foreach($element->cards AS $card){
					$view_manager = Viewer::getInstance();
					$view_manager->assign('batl_is_logged',AuthManager::is_authenticated());
					$view_manager->assign('card',$card);
					$card_content = $view_manager->fetch_view('element.card.tpl');
					$this->html .= 
						'<div class="unit size1of'.count($element->cards).'">'
							.'<div class="darker include">'
								.str_replace("\t",'',$card_content)
							.'</div>'
						.'</div>'
					;
				}
				$this->html .= '</div>';
			} else {
				$this->html .= $element->html;
			}
		}
		// over-parse with external parser
		$this->html = Parsedown::instance()->parse($this->html);

		// @TODO : need cleaning ?
		//$this->html = stripslashes(strip_tags($this->html));
	}

	public function get_all_links(){
		preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $this->text_code, $output);
		return $output[0];
	}

	public function __toString(){
		$res = "name=".$this->name;
		$res .= "lastedit=".$this->lastedit;
		$res .= "color=".$this->color;
		$res .= "is_private=".$this->is_private;
		foreach($this->elements as $element)
			$res .= $element;
		return '';
	}

	// ---- JsonSerializable

	public function jsonSerialize() {
        return [
        	"is_private" => $this->is_private,
			"is_recursive" => $this->is_recursive,
			"color" => $this->color,
			"is_light" => $this->is_light,
			"properties" => $this->properties,
			"last_edit" => $this->last_edit,
			"name" => $this->name,
			"display_name" => $this->display_name,
			"files" => $this->files,
			"exists" => $this->exists,
			"history" => $this->history,
			"text_code" => $this->text_code,
			"html" => $this->html
        ];
    }

	// ---- Helper Methods ----

	private static function get_display_name($card_name){
		// bad hack: we use ? as a temporary pattern to insert -
		$card_name = str_replace('_-_',' ? ',$card_name);
		$card_name = str_replace('--','?',$card_name);
		$card_name = str_replace('__',': ',$card_name);
		$card_name = str_replace('_',' ',$card_name);
		$card_name = str_replace('-',' ',$card_name);
		$card_name = str_replace('?','-',$card_name);
		
		return $card_name;
	}

	private function parse_special_properties($line){
		//TODO : IP
		if(preg_match("/lastedit: ([\d]+)/",trim($line),$matches)){
			$date = new DateTime($matches[1]);
			$this->last_edit = $date->format('Y-m-d');
		}
		elseif(preg_match("/color: (#[A-Fa-f0-9]{3,6})/",trim($line),$matches)){
			$this->color = $matches[1];
			$perceived_brightness = ImageHelper::rgb_to_perceived_brightness(ImageHelper::hex_to_rgb($this->color));
			$this->is_light = $perceived_brightness < 0.83 ? true : false;
		}
		elseif(preg_match("/is_private/",trim($line))){
			$this->is_private = true;
		}
	}

	private function get_style_definition(){
		$this->style_definition =
			'#'.$this->name.' a:not(.white_text):not(.lighter_text):not(.black_text):not(.darker_text):not(.btn){color:'.$this->color.';}'
			.'#'.$this->name.' h2,#'.$this->name.' h3,#'.$this->name.' h4{border-color:'.$this->color.';}'
			.'#'.$this->name.' .card__content, #'.$this->name.' .files{border-color:rgba('.implode(',',ImageHelper::hex_to_rgb($this->color)).',0.2);}'
			.'#'.$this->name.' .banner:not(.loadable){background-color:'.$this->color.';}'
		;
	}
	
	private function open_multiple_line_tag($card_element){
		return (object)['html' => '<'.$card_element->html_closure_tag.'>'];
	}
	
	private function close_multiple_line_tag($card_element){
		$last_element_id = count($this->elements) - 1;
		$this->elements[$last_element_id]->html = str_replace("\n",'',$this->elements[$last_element_id]->html);
		$this->elements[$last_element_id]->html = str_replace("\r",'',$this->elements[$last_element_id]->html);
		
		return (object)['html' => '</'.$card_element->html_closure_tag.'>'];
	}

	public function to_json(){
		return json_encode($this);
	}
}
