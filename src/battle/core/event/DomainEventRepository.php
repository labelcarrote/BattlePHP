<?php
namespace BattlePHP\Event;

class DomainEventRepository{

	// -----------------
	// ---- QUERIES ----
	// -----------------

	public static function get_event($event_id, $table_name = null){
		$eventdb = DomainEventDB::getInstance($table_name)->get_event($event_id);
		if($eventdb === null)
			return null;

		return DomainEvent::create_event_from_db($eventdb);
	}

	public static function count_all_events_in_search($names, $element_id, $table_name = null){
		return DomainEventDB::getInstance($table_name)->count_all_events_in_search($names,$element_id);
	}

	public static function search_events($event_specification,$table_name = null){
		return DomainEvent::create_events_from_db(
			DomainEventDB::getInstance($table_name)->search_events(
				$event_specification->page_id,
				$event_specification->nb_event_by_page,
				$event_specification->names,
				$event_specification->element_id,
				$event_specification->ordered_by,
				$event_specification->in_descending_order
			)
		);
	}

	// -------------------------------------------
	// ---- COMMANDS : Create, Update, Delete ----
	// -------------------------------------------

	public static function save_event($event, $table_name = null){
		if(!isset($event))
			return false;

		return DomainEventDB::getInstance($table_name)->upsert_event($event);
	}

	public static function delete_event($event_id, $table_name = null){
		return DomainEventDB::getInstance($table_name)->delete_event($event_id);
	}
}
