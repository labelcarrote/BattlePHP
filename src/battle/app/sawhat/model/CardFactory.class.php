<?php
class Card{
	const DEFAULT_COLOR = '#f90';

	public $is_private = false;
	public $is_recursive;
	public $color = self::DEFAULT_COLOR;
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
	
	public function __construct($name, $lines = array(), $recursive_level = 0){
		$this->exists = CardStore::exist($name);
		$this->name = $name;
		$this->display_name = self::get_display_name($name);
		$this->lines = $lines;
		$this->is_recursive = $recursive_level > 0;
		$this->recursive_level = $recursive_level;
		$this->elements = array();
		$init = true;
		$element = null;
		$previous_element = (object)array('multiple_line'=>false,'html_closure_tag'=>'');
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
					$this->elements[] = $this->close_mutliple_line_tag($previous_element);
					$need_closure = false;
				}
				// open multiple line tag
				if($card_element->multiple_line	
					&& (!$previous_element->multiple_line || $previous_element->html_closure_tag != $card_element->html_closure_tag))
				{
					$this->elements[] = $this->open_mutliple_line_tag($card_element);
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
			$this->elements[] = $this->close_mutliple_line_tag($previous_element);
		}
		$this->get_style_definition();
		
		// set html as a whole
		$card_to_include = array();
		foreach($this->elements AS $k => $element){
			if(isset($element->cards) && count($element->cards) > 0){
				$this->html .= '<div class="column_container">';
				foreach($element->cards AS $card){
					$view_manager = Viewer::getInstance();
					$view_manager->assign('logged',AuthHelper::is_authenticated());
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
				$this->html .= '<div class="clearer"></div></div>';
			} else {
				$this->html .= $element->html;
			}
		}
		// over-parse with external parser
		$parsedown = new Parsedown();
		$this->html = $parsedown->parse($this->html);

		// @TODO : need cleaning ?
		//$this->html = stripslashes(strip_tags($this->html));
	}
	
	private function get_style_definition(){
		$this->style_definition =
			'#'.$this->name.' a:not(.white_text):not(.lighter_text):not(.black_text):not(.darker_text):not(.btn){color:'.$this->color.';}'
			.'#'.$this->name.' h2,#'.$this->name.' h3,#'.$this->name.' h4,#'.$this->name.' .things, #'.$this->name.' .files{border-color:'.$this->color.';}'
			.'#'.$this->name.' .banner:not(.loadable){background-color:'.$this->color.';}'
		;
	}
	
	private function open_mutliple_line_tag($card_element){
		return (object)array('html'=>'<'.$card_element->html_closure_tag.'>');
	}
	private function close_mutliple_line_tag($card_element){
		$last_element_id = count($this->elements) - 1;
		$this->elements[$last_element_id]->html = str_replace("\n",'',$this->elements[$last_element_id]->html);
		$this->elements[$last_element_id]->html = str_replace("\r",'',$this->elements[$last_element_id]->html);
		
		return (object)array('html'=>'</'.$card_element->html_closure_tag.'>');
	}
	
	public function parse_special_properties($line){
		//TODO : IP
		if(preg_match("/lastedit: ([\d]+)/",trim($line),$matches)){
			$date = new DateTime($matches[1]);
			$this->last_edit = $date->format('Y-m-d');
		}
		elseif(preg_match("/color: (#[A-Fa-f0-9]{3,6})/",trim($line),$matches)){
			$this->color = $matches[1];
		}
		elseif(preg_match("/is_private/",trim($line))){
			$this->is_private = true;
		}
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
	public static function get_display_name($card_name){
		// bad hack: we use ? as a temporary pattern to insert -
		$card_name = str_replace('_-_',' ? ',$card_name);
		$card_name = str_replace('--','?',$card_name);
		$card_name = str_replace('__',': ',$card_name);
		$card_name = str_replace('_',' ',$card_name);
		$card_name = str_replace('-',' ',$card_name);
		$card_name = str_replace('?','-',$card_name);
		
		return $card_name;
	}

	public function get_all_links(){
		preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $this->text_code, $output);
		return $output[0];
	}
}

class CardElement{
	public $html = '';
	public $cards;
	public $multiple_line = false;
	public $html_closure_tag = '';
	public $coding_language = '';
	
	public function __construct($card_name, $line, $recursive_level = 0){
		// Empty line
		if(trim($line) === ''){
			$this->html = '';
			return;
		}
		
		$this->card_name = $card_name;
		
		// Replace newlines
		$s = array("\r","\n");
		$r = array('','');
		$line = str_replace($s,$r,$line);
		
		// Cards!
		if(preg_match('/^\[#([a-zA-Z0-9\|#_-]+)\]$/',$line,$matches)){
			$cards_names = array_slice(explode('|', $matches[1]),0,3);
			$column_count = count($cards_names);
			if(!isset($this->cards))
				$recursive_level++;
			$this->html .= '<div class="column_container">';
			foreach($cards_names as $card_name) {
				$card_name = str_replace('#','',$card_name);
				if($recursive_level < CardStore::MAX_RECURSIVE_LEVEL)
					$this->cards[] = CardStore::get($card_name,$recursive_level);
				elseif($recursive_level == CardStore::MAX_RECURSIVE_LEVEL)
					$included_card = CardStore::get($card_name,$recursive_level);
				if(isset($included_card)){
					$view_manager = Viewer::getInstance();
					$view_manager->assign('logged',AuthHelper::is_authenticated());
					$view_manager->assign('card',$included_card);
					$banner_content = $view_manager->fetch_view('element.card.banner.tpl');
					$this->html .= '<div class="size1of'.$column_count.' left" id="'.$included_card->name.'"><style>'.$included_card->style_definition.'</style>'.$banner_content.'</div>';
				}
			}
			$this->html .= '<div class="clearer"></div></div>';
		}
		// Parse line
		else{
			$bbcode_array = $this->bbcode_to_html($line,$recursive_level);
			$this->multiple_line = $bbcode_array['multiple_line'];
			$this->html_closure_tag = $bbcode_array['closure_tag'];
			$this->coding_language = $bbcode_array['coding_language'];
			$line = $bbcode_array['html_code'];
			$this->html = $line;
		}
	}
	
	public function bbcode_to_html($string, $recursive_level = 0){
		$multiple_line = false;
		$closure_tag = '';
		$html = $string;

		// CLASSIC BBCODE
		// Images (2/2)
		if(preg_match('/^(https?:.+\.(?:png|jpg|jpe?g|gif))?$/',$html,$matches)){
			$html = '<img src="'.$matches[1].'" alt="image" />';
		}
		// Link to card
		elseif(preg_match('/^\#([\S]*)$/',$html,$matches)){
			$html = '<div class="loadable_card">'
				.'<div class="banner loadable">'
				.'<a href="[ROOT_URL]'.$matches[1].'" class="white_text '.(!CardStore::exist($matches[1]) ? 'striked light' : '').'" title="'.Card::get_display_name($matches[1]).'">'
				.'<b><span class="bigger">&rsaquo;</span>&nbsp;'.Card::get_display_name($matches[1]).'</b>'
				.'</a>'
				.'<a class="right lighter_text load_card" data-action="load" data-card-name="'.Request::get_application_virtual_root().'/'.$matches[1].'">LOAD</a>'
				.'<div class="clearer"></div>'
				.'</div>'
				.'<div class="darker include hidden"></div>'
				.'</div>';
		}
		// Local File / Image
		elseif(preg_match('/^\@([\S]+)$/',$html,$matches)){
			if(preg_match('/^\@.+\.(?:png|jpg|jpe?g|gif)?$/',$html)) 
				$html = '<img src="[IMAGE_URL]'.$matches[1].'" alt="image" />';
			else
				$html = '<a href="[IMAGE_URL]'.$matches[1].'">'.$matches[1].'</a>';
		}
		
		// Replace vars
		$s = array('[ROOT_URL]','[IMAGE_URL]');
		$r = array(
			Request::get_application_virtual_root(),
			Request::get_root_url().CardStore::get_folder().$this->card_name.'/',
		);
		$html = str_replace($s,$r,$html);
		
		return array('html_code' => $html, 'multiple_line' => $multiple_line, 'closure_tag' => $closure_tag, 'coding_language' => '');
	}
}
?>