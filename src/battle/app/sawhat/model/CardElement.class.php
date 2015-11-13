<?php
use BattlePHP\Core\Viewer;
use BattlePHP\Core\Request;
use BattlePHP\Core\Auth\AuthManager;
/**
 * CardElement
 *
 * @author jonpotiron, touchypunchy
 *
 */
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
			// remove special starred card
			if(($key = array_search('#starred',$cards_names)) !== false) {
				unset($cards_names[$key]);
			}
			$column_count = count($cards_names);
			if(!isset($this->cards))
				$recursive_level++;
			$this->html .= '<div class="column_container auto_clear">';
			foreach($cards_names as $card_name) {
				$card_name = str_replace('#','',$card_name);
				if($recursive_level < CardStore::MAX_RECURSIVE_LEVEL)
					$this->cards[] = CardStore::get_card($card_name,$recursive_level);
				elseif($recursive_level == CardStore::MAX_RECURSIVE_LEVEL)
					$included_card = CardStore::get_card($card_name,$recursive_level);
				if(isset($included_card)){
					$view_manager = Viewer::getInstance();
					$view_manager->assign('batl_is_logged',AuthManager::is_authenticated());
					$view_manager->assign('card',$included_card);
					$banner_content = $view_manager->fetch_view('element.card.banner.tpl');
					$this->html .= '<div class="size1of'.$column_count.' left" id="'.$included_card->name.'"><style>'.$included_card->style_definition.'</style>'.$banner_content.'</div>';
				}
			}
			$this->html .= '</div>';
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
			if($matches[1] !== 'starred'){
				$view_manager = Viewer::getInstance();
				$ass_card = new Card($matches[1]);
				$view_manager->assign('card',$ass_card);
				$html = $view_manager->fetch_view('element.card.loadable.tpl');
			} else {
				$html = '<div class="starred_title smaller">'
					.'<span class="fa-stack">'
					.'<span class="lighter_text fa fa-circle-thin fa-stack-2x"></span>'
					.'<span class="fa fa-star fa-stack-1x"></span>'
					.'</span>'
					.'</div>'
					.'<div class="starred_container auto_clear"></div>';
			}
		}
		// Local File / Image
		elseif(preg_match('/^\@([\S]+)$/',$html,$matches)){
			$file_root = Request::get_root_url().CardStore::get_folder().$this->card_name.'/';
			$html = (preg_match('/^\@.+\.(?:png|jpg|jpe?g|gif)?$/',$html))
				? '<img src="'.$file_root.$matches[1].'" alt="image" />'
				: '<a href="'.$file_root.$matches[1].'">'.$matches[1].'</a>';
		}
		
		return array('html_code' => $html, 'multiple_line' => $multiple_line, 'closure_tag' => $closure_tag, 'coding_language' => '');
	}
}