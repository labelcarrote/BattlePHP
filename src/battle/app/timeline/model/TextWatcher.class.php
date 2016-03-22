<?php
use BattlePHP\Model\Entity;
use BattlePHP\Event\DomainEvent;
use BattlePHP\Event\DomainEventSpecification;
use BattlePHP\Event\DomainEventRepository;
require_once 'app/timeline/config/config.php';
require_once 'app/timeline/model/Text.class.php';

class TextEventsSpecification extends DomainEventSpecification{

	const ALL_TEXT_EVENT_NAMES = "TextAdded";

	public function __construct($since_date){
		$this->names = self::ALL_TEXT_EVENT_NAMES;
		$this->date1 = $since_date;
		$this->date2 = new DateTime();
	}
}

/********************************************************************
* CLASS TextWatcher (& Form)
*
*********************************************************************/
class TextWatcher extends Entity{
	
	const ALL_TEXT_EVENT_NAMES = "TextAdded";

	public $type ="text";
	public $tpl_name = "watcher.text.tpl";
	public $tpl_form_name = "form.text.tpl";
	
	public $since_date = null;
	public $count_since = 0;
	public $texts = []; 

	public function __construct($since_date = null){
		$this->since_date = $since_date;
		$this->load();
	}

	private function load(){
		$this->texts = DomainEventRepository::search_events(new TextEventsSpecification($this->since_date),ConfigurationTimeline::TABLE_EVENTS);
		$this->count_since = count($this->texts);
	}

	// ---- Public Methods ----

	public function submit_text($params){
		if($params !== null){
			$text = Text::create_from_params($params);
			DomainEventRepository::save_event(
				DomainEvent::create_unique_event(Text::EVENT,DomainEvent::TYPE_USER_COMMAND, 0, $text->to_json()),
				ConfigurationTimeline::TABLE_EVENTS	
			);
			$this->load();
		}
	}

	public function delete($text_id){
	}
}
