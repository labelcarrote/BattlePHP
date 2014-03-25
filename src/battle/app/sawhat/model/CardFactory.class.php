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
		$previous_is_multiple_line = false;
		$previous_html_closure_tag = '';

		foreach($lines as $line){
			if($init){
				$this->parse_special_properties($line);
			}else{
				$this->text_code .= stripslashes($line);
				$card_element = new CardElement($name,$this->color,$line,$recursive_level);

				// close multiple line tag
				if($previous_is_multiple_line 
					&& (!$card_element->multiple_line || $previous_html_closure_tag != $card_element->html_closure_tag))
				{
					// clean previous line //
					$last_element_id = count($this->elements) - 1;
					$this->elements[$last_element_id]->html = str_replace("\n",'',$this->elements[$last_element_id]->html);
					$this->elements[$last_element_id]->html = str_replace("\r",'',$this->elements[$last_element_id]->html);
					
					$tag_added = $previous_html_closure_tag == 'pre' ? '</code>' : '';
					$this->elements[] = (object)array('html'=>$tag_added.'</'.$previous_html_closure_tag.'>');
				}
				
				// open multiple line tag
				if($card_element->multiple_line	
					&& (!$previous_is_multiple_line || $previous_html_closure_tag != $card_element->html_closure_tag))
				{
					$class = $card_element->html_closure_tag == 'pre' ? 'class="code line-numbers language-none"' : '';
					$tag_added = $card_element->html_closure_tag == 'pre' ? '<code class="language-none">' : '';
					$this->elements[] = (object)array('html'=>'<'.$card_element->html_closure_tag.' '.$class.'>'.$tag_added);
				}
				
				$this->elements[] = $card_element;
				
				$previous_is_multiple_line = $card_element->multiple_line;
				$previous_html_closure_tag = $card_element->html_closure_tag;
			}

			if(trim($line) == '')
				$init = false;
		}
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
		$card_name = preg_replace('/([a-zA-Z0-9]+)(_|\-){1}([a-zA-Z0-9]+)/','$1 $3',$card_name);
		$card_name = preg_replace('/([a-zA-Z0-9]+)__([a-zA-Z0-9]+)/','$1: $2',$card_name);
		$card_name = preg_replace('/([a-zA-Z0-9]+)--([a-zA-Z0-9]+)/','$1-$2',$card_name);
		$card_name = preg_replace('/([a-zA-Z0-9]+)_-_([a-zA-Z0-9]+)/','$1 - $2',$card_name);
		
		return $card_name;
	}
}

class CardElement{
	public $html;
	public $cards;
	public $multiple_line = false;
	public $html_closure_tag = '';
	
	public function __construct($card_name, $card_color, $line, $recursive_level = 0){
		// Empty line
		if(trim($line) === ''){
			$this->html = '<br>';
			return;
		}
		
		$this->card_name = $card_name;
		$need_newline = true;
		$html_tags_needing_no_newline = array(
			'li',
			'div',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'pre'
		);
		
		$bbcode_array = self::bbcode_to_html($line,$card_color,$recursive_level);
		$this->multiple_line = $bbcode_array['multiple_line'];
		$this->html_closure_tag = $bbcode_array['closure_tag'];
		$clean = $bbcode_array['html_code'];

		// replace vars
		$s = array('[ROOT_URL]','[IMAGE_URL]');
		$r = array(
			Request::get_application_virtual_root(),
			Request::get_root_url().CardStore::get_folder().$this->card_name.'/'
		);
		$clean = str_replace($s,$r,$clean);
		
		// find html element
		$html_tag = '';
		if(preg_match('/^<(\w+)( |>).+$/',$clean,$matches))
			$html_tag = $matches[1];
		elseif(preg_match('/^.*<\/(\w+)>$/',$clean,$matches))
			$html_tag = $matches[1];

		$need_newline = in_array($html_tag,$html_tags_needing_no_newline) || $this->html_closure_tag == 'pre' ? false : $need_newline;
		
		// Cards!
		if(preg_match('/^\[([\S]*)\]$/',$clean,$matches)){
			$cards_names = array_slice(explode('|', $matches[1]),0,3);
			$column_count = count($cards_names);
			if(!isset($this->cards))
				$recursive_level++;
			
			$this->html .= '<div class="column_container">';
			foreach($cards_names as $card_name) {
				if($recursive_level < CardStore::MAX_RECURSIVE_LEVEL)
					$this->cards[] = CardStore::get($card_name,$recursive_level);
				elseif($recursive_level == CardStore::MAX_RECURSIVE_LEVEL)
					$included_card = CardStore::get($card_name,$recursive_level);
				
				if(isset($included_card)){
					$view_manager = Viewer::getInstance();
					$view_manager->assign('logged',AuthHelper::is_authenticated());
					$view_manager->assign('card',$included_card);
					$banner_content = $view_manager->fetch_view('element.card.banner.tpl');
					$this->html .= '<div class="size1of'.$column_count.' left">'.$banner_content.'</div>';
				}
			}
			$this->html .= '</div>';
			$need_newline = false;
		}
		// ...
		else{
			$this->html = $clean;
		}
		$this->html .= $need_newline ? '<br>' : '';
	}
	
	public static function bbcode_to_html($string, $color = '', $recursive_level = 0, $need_string_cleaning = true){
		$multiple_line = false;
		$closure_tag = '';
		$html = $string;

		// Code (1/2) => no more parsing
		if(preg_match('/^  (.+)$/',$html,$matches)){
			//$clean = '<pre class="code">'.$matches[1].'</pre>';
			$html = $matches[1];
			$multiple_line = true;
			$closure_tag = 'pre';
			return array('html_code' => $html, 'multiple_line' => $multiple_line, 'closure_tag' => $closure_tag);
		}
		
		// parse line ...
		$html = $need_string_cleaning ? stripslashes(trim(strip_tags($html))) : $html;
		
		// CLASSIC BBCODE
		// code (2/2)
		$html = preg_replace('/^\[code=(\w+)\]$/', '<pre class="code line-numbers language-$1"><code>', $html);
		$html = preg_replace('/^\[\/code\]$/', '</code></pre>', $html);
		// columns
		$html = preg_replace('/\[column=(\d)\]/', '<div class="column_$1">', $html);
		$html = preg_replace('/\[\/column\]/', '</div>', $html);
		// links (1/2)
		$html = preg_replace('/\[url=(.+)\](.+)\[\/url\]/', '<a style="color:'.$color.'" href="$1">$2</a>', $html);
		$html = preg_replace('/\[url\](.+)\[\/url\]/', '<a style="color:'.$color.'" href="$1">$1</a>', $html);
		// images (1/2)
		$html = preg_replace('/\[img=(.+)\]/', '<img src="$1" alt="" />', $html);
		$html = preg_replace('/\[img\](.+)\[\/img\]/', '<img src="$1" alt="" />', $html);
		// text u,i,s,b
		if(preg_match('/(.*)\[u\](.+)\[\/u\](.*)/',$html,$matches)){
			$html = self::bbcode_to_html($matches[2],$color,$recursive_level,false);
			$html = $matches[1].'<u>'.$html['html_code'].'</u>'.$matches[3];
		}
		if(preg_match('/(.*)\[i\](.+)\[\/i\](.*)/',$html,$matches)){
			$html = self::bbcode_to_html($matches[2],$color,$recursive_level,false);
			$html = $matches[1].'<i>'.$html['html_code'].'</i>'.$matches[3];
		}
		if(preg_match('/(.*)_([a-zA-Z0-9 _-]+)_(.*)/',$html,$matches)){
			$html = self::bbcode_to_html($matches[2],$color,$recursive_level,false);
			$html = $matches[1].'<i>'.$html['html_code'].'</i>'.$matches[3];
		}
		if(preg_match('/(.*)\[s\](.+)\[\/s\](.*)/',$html,$matches)){
			$html = self::bbcode_to_html($matches[2],$color,$recursive_level,false);
			$html = $matches[1].'<s>'.$html['html_code'].'</s>'.$matches[3];
		}
		if(preg_match('/(.*)\[b\](.+)\[\/b\](.*)/',$html,$matches)){
			$html = self::bbcode_to_html($matches[2],$color,$recursive_level,false);
			$html = $matches[1].'<b>'.$html['html_code'].'</b>'.$matches[3];
		}
		if(preg_match('/(.*)\*([a-zA-Z0-9 _-]+)\*(.*)/',$html,$matches)){
			$html = self::bbcode_to_html($matches[2],$color,$recursive_level,false);
			$html = $matches[1].'<b>'.$html['html_code'].'</b>'.$matches[3];
		}
		// List
		if(preg_match('/^(\d+\-|\-) (.+)$/',$html,$matches)){
			$html = self::bbcode_to_html(trim($matches[2], '- '),$color,$recursive_level,false);
			$html = '<li>'.$html['html_code'].'</li>';
			$multiple_line = true;
			$closure_tag = $matches[1] !== '-' ? 'ol' : 'ul';
		}
		// Images (2/2)
		elseif(preg_match('/^(https?:.+\.(?:png|jpg|jpe?g|gif))?$/',$html,$matches)){
			$html = '<img src="[ROOT_URL]'.$matches[1].'" alt="image"/>';
		}
		// Links (2/2)
		elseif(preg_match('/^(.+) (https?:[\S]+)$/',$html,$matches)){
			$html = '<a style="color:'.$color.'" href="'.$matches[2].'">'.$matches[1].'</a>';
		}
		elseif(preg_match('/^(https?:[\S]+)$/',$html,$matches)){
			$html = '<a style="color:'.$color.'" href="'.$matches[1].'">'.$matches[1].'</a>';
		}
		elseif(preg_match('/(\s)+(https?:[\S]+)(\s)+/',$html,$matches)){
			$html = $matches[1].'<a style="color:'.$color.'" href="'.$matches[2].'">'.$matches[2].'</a>'.$matches[3];
		}
		// Headers/titles
		elseif(preg_match('/^([\=]{1,5}) (.+)$/',$html,$matches)){
			$header_level = (7-count(str_split($matches[1]))) + $recursive_level;
			$tag_name = $header_level <= 6 ? 'h'.$header_level : 'div';
			$html = '<'.$tag_name.' style="border-color:'.$color.'">'.trim($matches[2], '= ').'</'.$tag_name.'>';
		}
		elseif(preg_match('/^([\-]{2,}) (.+)$/',$html,$matches)){
			$header_level = 7-count(str_split($matches[1])) + $recursive_level;
			$tag_name = $header_level < 4 ? 'h'.$header_level : 'h4';
			$html = '<'.$tag_name.' class="noborder">'.trim($matches[2], '- ').'</'.$tag_name.'>';
		}
		// Link to card
		elseif(preg_match('/^\#([\S]*)$/',$html,$matches)){
			$html = '<a style="color:'.$color.'" href="[ROOT_URL]'.$matches[1].'"><b><span class="bigger">&rsaquo;</span>&nbsp;'.$matches[1]
			.'</b></a> <a href="#" class="load_card" data-action="load" data-card-name="'.$matches[1].'" style="color:'.$color.'">( load )</a>';
		}
		// Local File / Image
		elseif(preg_match('/^\@([\S]*)$/',$html,$matches)){
			if(preg_match('/^\@.+\.(?:png|jpg|jpe?g|gif)?$/',$html)) 
				$html = '<img src="[IMAGE_URL]'.$matches[1].'" alt="image"/>';
			else
				$html = '<a style="color:'.$card_color.'" href="[IMAGE_URL]'.$matches[1].'">'.$matches[1].'</a>';
		}
		
		return array('html_code' => $html, 'multiple_line' => $multiple_line, 'closure_tag' => $closure_tag);
	}
}
?>