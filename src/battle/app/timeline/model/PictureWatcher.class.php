<?php
use BattlePHP\Model\Entity;
use BattlePHP\Event\DomainEvent;
use BattlePHP\Event\DomainEventSpecification;
use BattlePHP\Event\DomainEventRepository;
use BattlePHP\Storage\Uploader;
require_once 'app/timeline/config/config.php';

class PictureEventsSpecification extends DomainEventSpecification{

	const ALL_PICTURE_EVENT_NAMES = "PictureAdded";

	public function __construct($since_date){
		$this->names = self::ALL_PICTURE_EVENT_NAMES;
		$this->date1 = $since_date;
		$this->date2 = new DateTime();
	}
}

/********************************************************************
* CLASS PictureWatcher (& Form)
*
*********************************************************************/
class PictureWatcher extends Entity{
	
	const ALL_PICTURE_EVENT_NAMES = "PictureAdded";

	public $type ="picture";
	public $tpl_name = "watcher.picture.tpl";
	public $tpl_form_name = "form.picture.tpl";
	
	public $since_date = null;
	public $count_since = 0;
	public $pictures = []; 
	public $picture = null;

	public function __construct($since_date = null){
		$this->since_date = $since_date;
		$this->load();
	}

	private function load(){
		$this->pictures = DomainEventRepository::search_events(new PictureEventsSpecification($this->since_date),ConfigurationTimeline::TABLE_EVENTS);
		$this->count_since = count($this->pictures);	
	}

	// ---- Public Methods ----

	public function submit_picture_upload(){
		$this->process_form_files_upload();
		if($this->validate())
			$this->save();
	}

	private function process_form_files_upload(){
		if(isset($_FILES["picture"]) && !empty($_FILES["picture"]["name"])){
			try{
				$this->picture = new Picture();
				$prefix = "tlt_";
				$this->picture->file_name = 
					Uploader::process_form_file(
						"picture",
						$this->picture->get_folder(),
						2000000,
                        Uploader::get_img_extensions(), 
						$prefix
					);

				$path = $this->picture->get_path();
				list($width, $height) = getimagesize($path);
				$size = filesize($path);
				$this->picture->width = $width;
				$this->picture->height = $height;
				$this->picture->size = $size;
			}
			catch(Exception $e){
				$this->add_error("upload", "i18n_get(profile.avatar_uploaded_incorrect)");
			}
		}else{
			$this->add_error("picture", "No file uploaded.");	
		}
	}

	private function validate(){
		return (count($this->errors) === 0);
	}

	private function save(){
		DomainEventRepository::save_event(
			DomainEvent::create_unique_event(Picture::EVENT,DomainEvent::TYPE_USER_COMMAND, 0, $this->picture->to_json()),
			ConfigurationTimeline::TABLE_EVENTS	
		);
		$this->load();
	}
}
