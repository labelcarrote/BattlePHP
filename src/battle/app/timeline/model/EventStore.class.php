<?php
use BattlePHP\Storage\FileSystemIO;
use BattlePHP\Event\DomainEventRepository;
require_once 'app/timeline/model/Cigarette.class.php';
require_once 'app/timeline/model/Picture.class.php';

/********************************************************************
* CLASS EventStore
*
*********************************************************************/
class EventStore{

	// -----------------
	// ---- QUERIES ----
	// -----------------

	// -------------------------------------------
	// ---- COMMANDS : Create, Update, Delete ----
	// -------------------------------------------

	public static function delete_event($event_id){
		$event = DomainEventRepository::get_event($event_id, ConfigurationTimeline::TABLE_EVENTS);
		if($event === null)
			return false;

		$obj = EventViewFactory::event_to_object($event);
		if($obj instanceof Picture)
			FileSystemIO::delete_file($obj->get_path());

		return DomainEventRepository::delete_event($event_id, ConfigurationTimeline::TABLE_EVENTS);
	}
}
?>