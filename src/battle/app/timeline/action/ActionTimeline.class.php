<?php
// WIP
use BattlePHP\Core\Controller;
use BattlePHP\Storage\FileSystemIO;
use BattlePHP\API\Response;		
use BattlePHP\Event\DomainEventSpecification;
use BattlePHP\Event\DomainEventRepository;
use BattlePHP\Core\Request;

//require_once 'core/storage/FileSystemIO.class.php';
//require_once 'core/model/AjaxResult.class.php';
//require_once 'core/event/DomainEventSpecification.class.php';
require_once 'app/timeline/model/EventStore.class.php';
require_once 'app/timeline/model/EventViewFactory.class.php';
require_once 'app/timeline/model/TextWatcher.class.php';
require_once 'app/timeline/model/FAPBattleWatcher.class.php';

class ActionTimeline extends Controller{

	const SECTION_DASHBOARD = "dashboard";
	const SECTION_TIME = "time";
	const FORM_DATE_FORMAT = "m/d/Y";

	public function index(){
		$since_date = (isset($_GET['date1'])) ? DateTime::createFromFormat(self::FORM_DATE_FORMAT,$_GET['date1']) : null;
		$cigarette_watcher = new CigaretteWatcher($since_date);
		$picture_watcher = new PictureWatcher($since_date);
		$text_watcher = new TextWatcher($since_date);
		$fapbattle_watcher = new FAPBattleWatcher($since_date);

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
				case 'add_fapbattle':
					$fapbattle_watcher->submit_fapbattle($_POST);
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
		$this->assign('section',self::SECTION_DASHBOARD);
		$this->assign('cigarette_watcher',$cigarette_watcher);
		$this->assign('picture_watcher',$picture_watcher);
		$this->assign('text_watcher',$text_watcher);
		$this->assign('fapbattle_watcher',$fapbattle_watcher);
		$this->assign('event_views',$event_views);
		$this->display_view('index.tpl');
	}

	public function developpers(){
		$this->display_view('index.tpl');
	}

	// [/timeline/api]
	// Treats any POST command in JSON {data:{submit: ...,...} and respond in JSON {errors:"";body:""})
	// - POST add_event
	// {data:{submit:'add_event',event_name:'CigaretteSmoked',event:{"date":{"date":"2014-11-27 17:45:52.000000","timezone_type":3,"timezone":"Europe\/Berlin"},"excuse":"bidon"}}}
	// - GET get_events (?m=get_battle_result_details&battle_id=124)

	// Note : 
	// TextAdded {"id":"","date":{"date":"2014-11-28 18:46:47.000000","timezone_type":3,"timezone":"Europe\/Berlin"},"txt":"TISTREET","html":""}
	// PictureAdded {"file_name":"tlt_b7062d58e40fc1a78398ea9fc7101cde.jpg","width":1366,"height":767,"size":687774,"date":{"date":"2014-11-27 18:15:35.000000","timezone_type":3,"timezone":"Europe\/Berlin"}}
	// CigaretteSmoked {"date":{"date":"2014-11-27 17:45:52.000000","timezone_type":3,"timezone":"Europe\/Berlin"},"excuse":"bidon"} 
	// Fap
	public function api(){
		$response = new Response();

		// POST
		if(isset($_POST['data'])){
			$query = json_decode(stripcslashes($_POST['data']),true);
			$submit = $query['submit'];
			switch ($submit) {
				case "add_event" :
					$event_name = $query['event_name'];
					$event_value = json_encode($query['event']);
					DomainEventRepository::save_event(
						DomainEvent::create_unique_event($event_name,DomainEvent::TYPE_USER_COMMAND, 0, $event_value),
						ConfigurationTimeline::TABLE_EVENTS	
					);
					break;
				default: break;
			}
		}
		// GET
		elseif(isset($_GET['m'])){
			$service_method = Request::isset_or($_GET['m'],null);
			switch ($service_method){
				case 'get_events' :
					$response = new Response();
					$battle_id = Request::isset_or($_GET['battle_id'],null);
					$response->body = BattleManager::get_battle($battle_id,AuthManager::get_current_user_id());
				default: break;
			}
		}

		echo $response->to_json();
		return;
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