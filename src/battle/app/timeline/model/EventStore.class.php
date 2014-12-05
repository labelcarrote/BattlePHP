<?php
require_once 'core/storage/FileSystemIO.class.php';
require_once 'app/timeline/model/Cigarette.class.php';
require_once 'app/timeline/model/CigaretteWatcher.class.php';
require_once 'app/timeline/model/Picture.class.php';
require_once 'app/timeline/model/PictureWatcher.class.php';


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

		$obj = self::event_to_object($event);
		if($obj instanceof Picture)
			FileSystemIO::delete_file($obj->get_path());

		return DomainEventRepository::delete_event($event_id, ConfigurationTimeline::TABLE_EVENTS);
	}
}
?>