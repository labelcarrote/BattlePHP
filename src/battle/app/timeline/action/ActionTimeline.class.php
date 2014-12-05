<?php

require_once 'core/storage/FileSystemIO.class.php';
require_once 'core/model/AjaxResult.class.php';
require_once 'core/event/DomainEventSpecification.class.php';
require_once 'app/timeline/model/EventStore.class.php';
require_once 'app/timeline/model/EventViewFactory.class.php';
require_once 'app/timeline/model/TextWatcher.class.php';

class ActionTimeline extends Controller{

	const SECTION_DASHBOARD = "dashboard";
	const SECTION_TIME = "time";
	const FORM_DATE_FORMAT = "m/d/Y";

	public function index(){
		$since_date = (isset($_GET['date1'])) ? DateTime::createFromFormat(self::FORM_DATE_FORMAT,$_GET['date1']) : null;
		$cigarette_watcher = new CigaretteWatcher($since_date);
		$picture_watcher = new PictureWatcher($since_date);
		$text_watcher = new TextWatcher($since_date);

		if(isset($_POST['submit'])){
			$submit = $_POST['submit'];
			switch ($submit) {
				case 'smoked_cigarette':
					$cigarette_watcher->submit_cigarette($_POST);
					break;
				case 'upload_picture':
					$picture_watcher->submit_picture_upload();
					break;
				case 'add_text':
					$text_watcher->submit_text($_POST);
				default: break;
			}
			header("Location: ".Request::get_application_virtual_root());
		}

		$this->assign_all_dates();
		$this->assign('section',self::SECTION_DASHBOARD);
		$this->assign('cigarette_watcher',$cigarette_watcher);
		$this->assign('picture_watcher',$picture_watcher);
		$this->assign('text_watcher',$text_watcher);
		$this->display_view('index.tpl');
	}

	public function time(){
		if(isset($_POST['submit'])){
			$submit = $_POST['submit'];
			switch ($submit) {
				case 'delete_event':
					$event_id = $_POST['event_id'];
					EventStore::delete_event($event_id);
					break;
				default: break;
			}
			header("Location: ".Request::get_application_virtual_root());
		}
		
		$event_specification = new DomainEventSpecification();
		$event_specification->page_id = 1;
		$event_specification->nb_event_by_page = 100;
		$event_specification->names = (isset($_GET['types'])) ? $_GET['types'] : null;
		$event_specification->in_descending_order = true;
		$event_specification->date1 = (isset($_GET['date1'])) ? DateTime::createFromFormat(self::FORM_DATE_FORMAT,$_GET['date1']) : null;
		$event_specification->date2 = new DateTime();

		$event_views = EventViewFactory::events_to_views(
			DomainEventRepository::search_events($event_specification,ConfigurationTimeline::TABLE_EVENTS),
			$this->view_manager
		);

		$this->assign_all_dates();
		$this->assign('section',self::SECTION_TIME);
		$this->assign('event_views',$event_views);
		$this->display_view('index.tpl');
	}

	private function assign_all_dates(){
		$now = new DateTime();
		$yesterday = new DateTime();
		$yesterday->setTimezone(new DateTimeZone('UTC'));
		$yesterday->sub(new DateInterval("P1D"));// 1 day

		$last_week = new DateTime();
		$last_week->setTimezone(new DateTimeZone('UTC'));
		$last_week->sub(new DateInterval("P1W"));// 1 week

		$last_month = new DateTime();
		$last_month->setTimezone(new DateTimeZone('UTC'));
		$last_month->sub(new DateInterval("P1M"));// 1 month

		$last_year = new DateTime();
		$last_year->setTimezone(new DateTimeZone('UTC'));
		$last_year->sub(new DateInterval("P1Y"));// 1 year

		$this->assign('now',$now);
		$this->assign('yesterday',$yesterday);
		$this->assign('last_week',$last_week);
		$this->assign('last_month',$last_month);
		$this->assign('last_year',$last_year);
	}
}
?>