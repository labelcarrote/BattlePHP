<?php
require_once 'core/event/db/DomainEventDB.class.php';
require_once 'core/event/DomainEvent.class.php';

class DomainEventManager{

	// -----------------
	// ---- QUERIES ----
	// -----------------

	public static function get_event($event_id){
		$eventdb = DomainEventDB::getInstance()->get_event($event_id);
		if($eventdb === null)
			return null;

		return DomainEvent::create_event_from_db($eventdb);
	}

	public static function count_all_events_in_search($names, $element_id){
		return DomainEventDB::getInstance()->count_all_events_in_search($names,$element_id);
	}
	
	public static function search_events($page_id, $nb_event_by_page, $names = null, $element_id = null, $ordered_by = null, $in_descending_order = true){
		$table_eventdb = DomainEventDB::getInstance()->search_events($page_id,$nb_event_by_page,$names,$element_id,$ordered_by,$in_descending_order);
		return DomainEvent::create_events_from_db($table_eventdb);
	}

	// -------------------------------------------
	// ---- COMMANDS : Create, Update, Delete ----
	// -------------------------------------------

	public static function save_event($event){
		if(!isset($event))
			return false;

		return DomainEventDB::getInstance()->upsert_event($event);
	}

	public static function delete_event($event_id){
		return DomainEventDB::getInstance()->delete_event($event_id);
	}
}
?>