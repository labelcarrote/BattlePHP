<?php
require_once 'core/storage/FileSystemIO.class.php';
require_once 'app/timeline/model/Cigarette.class.php';
require_once 'app/timeline/model/CigaretteWatcher.class.php';
require_once 'app/timeline/model/Picture.class.php';
require_once 'app/timeline/model/PictureWatcher.class.php';


/********************************************************************
* CLASS EventViewFactory
*
*********************************************************************/
class EventViewFactory{

	public static function event_to_view($event,$viewer){
		$view = null;
		switch ($event->event_name){
			case Cigarette::EVENT: 
				$viewer->assign("cigarette", new Cigarette($event));
				$view = $viewer->fetch_view("element.cigarette.tpl");
				break;
			case Picture::EVENT: 
				$viewer->assign("picture", new Picture($event));
				$view = $viewer->fetch_view("element.picture.tpl");
				break;
			case Text::EVENT: 
				$viewer->assign("text", new Text($event));
				$view = $viewer->fetch_view("element.text.tpl");
				break;
			default:
				break;
		}
		return $view;
	}

	public static function events_to_views($events,$viewer){
		$table_event_view = array();
		foreach ($events as $event){
			$event_view = self::event_to_view($event,$viewer);
			if($event_view !== null)
				$table_event_view[] = $event_view;
		}
		return $table_event_view;
	}

	public static function event_to_object($event){
		switch ($event->event_name){
			case Cigarette::EVENT: 
				return new Cigarette($event);
			case Picture::EVENT: 
				return new Picture($event);
			case Text::EVENT: 
				return new Text($event);
			default:
				return null;
		}
	}
}
?>