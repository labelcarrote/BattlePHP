<?php
use BattlePHP\Model\Entity;
use BattlePHP\Event\DomainEvent;
use BattlePHP\Event\DomainEventSpecification;
use BattlePHP\Event\DomainEventRepository;
/*require_once 'core/model/ValueObject.class.php';
require_once 'core/event/DomainEvent.class.php';
require_once 'core/event/DomainEventRepository.class.php';*/
require_once 'app/timeline/config/config.php';

/**
* FAPBattleEventsSpecification
* Search specification to get FAPBattle events from 
*/
class FAPBattleEventsSpecification extends DomainEventSpecification{

	const ALL_FAPBATTLE_EVENT_NAMES = "FAPBattlePublished";

	public function __construct($since_date){
		$this->names = self::ALL_FAPBATTLE_EVENT_NAMES;
		$this->date1 = $since_date;
		$this->date2 = new DateTime();
	}
}

/********************************************************************
* CLASS FAPBattleWatcher (& Form)
*
*********************************************************************/
class FAPBattleWatcher extends Entity{

	public $type ="fapbattlepublished";
	public $tpl_name = "watcher.fapbattle.tpl";
	public $tpl_form_name = "form.fapbattle.tpl";
	
	public $since_date = null;
	public $count_since = 0;
	public $fapbattles = array(); 

	public function __construct($since_date = null){
		$this->since_date = $since_date;
		$this->load();
	}

	private function load(){
		$this->fapbattles = DomainEventRepository::search_events(new FAPBattleEventsSpecification($this->since_date),ConfigurationTimeline::TABLE_EVENTS);
		$this->count_since = count($this->fapbattles);
	}

	// ---- Public Methods ----

	public function submit_fapbattle($params){
		if($params !== null){
			$fapbattle = FAPBattle::create_from_params($params);
			DomainEventRepository::save_event(
				DomainEvent::create_unique_event(FAPBattle::EVENT,DomainEvent::TYPE_USER_COMMAND, 0, $fapbattle->to_json()),
				ConfigurationTimeline::TABLE_EVENTS	
			);
			$this->load();
		}
	}

	public function delete($fapbattle_id){
	}
}
?>