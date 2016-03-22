<?php
use BattlePHP\Model\Entity;
use BattlePHP\Event\DomainEvent;
use BattlePHP\Event\DomainEventSpecification;
use BattlePHP\Event\DomainEventRepository;
//require_once 'core/event/DomainEvent.class.php';
//require_once 'core/event/DomainEventRepository.class.php';
//require_once 'core/model/ValueObject.class.php';
require_once 'app/timeline/config/config.php';

class CigaretteEventsSpecification extends DomainEventSpecification{

	const ALL_CIGARETTE_EVENT_NAMES = "CigaretteSmoked";

	public function __construct($since_date){
		$this->names = self::ALL_CIGARETTE_EVENT_NAMES;
		$this->date1 = $since_date;
		$this->date2 = new DateTime();
	}
}

/********************************************************************
* CLASS CigaretteWatcher (& Form)
*
*********************************************************************/
class CigaretteWatcher extends Entity{
	
	const ALL_CIGARETTE_EVENT_NAMES = "CigaretteSmoked";

	public $type ="cigarette";
	public $tpl_name = "watcher.cigarette.tpl";
	public $tpl_form_name = "form.cigarette.tpl";
	
	public $since_date = null;
	public $count_since = 0;
	public $cigarettes = []; 


	public function __construct($since_date = null){
		$this->since_date = $since_date;
		$this->load();
	}

	private function load(){
		$this->cigarettes = DomainEventRepository::search_events(new CigaretteEventsSpecification($this->since_date),ConfigurationTimeline::TABLE_EVENTS);
		$this->count_since = count($this->cigarettes);
	}

	// ---- Public Methods ----

	public function submit_cigarette($params){
		if($params !== null){
			$cigarette = Cigarette::create_from_params($params);
			DomainEventRepository::save_event(
				DomainEvent::create_unique_event(Cigarette::EVENT,DomainEvent::TYPE_USER_COMMAND, 0, $cigarette->to_json()),
				ConfigurationTimeline::TABLE_EVENTS	
			);
			$this->load();
		}
	}

	public function delete($cigarette_id){
	}
}
