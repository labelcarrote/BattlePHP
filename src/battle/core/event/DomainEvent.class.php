<?php
require_once 'core/model/ValueObject.class.php';

/**
 * DomainEvent
 */
class DomainEvent extends ValueObject{

	public function __construct($eventdb = null){
		$this->fields = array(
			'id' => 0,
			'event_name' => '',
			'event_type' => '',
			'element_id' => 0,
			'user_id' => 0,
			'date' => new DateTime(),
			'old_value' => '',
			'new_value' => '',
		);

		if(is_array($eventdb)){
			foreach($eventdb as $field => $value)
				$this->$field = $value; //note : $this->$field ! with a $ before the field!
		}
	}

	public static function create_unique_event($name = null,$type_id = null,$user_id = null, $new_value = null, $element_id = null, $old_value = null){
		$event = new self();
		$event->id = self::guidv4();
		$event->date = new DateTime();
		$event->event_name = $name; 
		$event->event_type = $type_id;
		$event->user_id = $user_id;
		$event->element_id = $element_id;
		$event->new_value = $new_value;
		$event->old_value = $old_value;
		return $event;
	}

	public static function create_event_from_db($eventdb){
		return new DomainEvent($eventdb);
	}
	
	public static function create_events_from_db($eventsdb){
		$events = array();
		foreach($eventsdb as $eventdb)
			$events[] = self::create_event_from_db($eventdb);
		return $events;
	}

	public static function create_dummy(){
		$event = new self();
		$event->id = self::guidv4();;
		$event->event_name = "Admin.events";
		$event->event_type = 0;
		$event->element_id = 124;
		$event->user_id = 1;
		$event->date = new DateTime();
		$event->old_value = "blbllblbllbllbl";
		$event->new_value = "emlmedledkzmf";
		return $event;
	}

	// ---- Helpers ----

	public static function guidv4(){
	    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	      // 32 bits for "time_low"
	      mt_rand(0, 0xffff), mt_rand(0, 0xffff),
	      // 16 bits for "time_mid"
	      mt_rand(0, 0xffff),
	      // 16 bits for "time_hi_and_version",
	      // four most significant bits holds version number 4
	      mt_rand(0, 0x0fff) | 0x4000,
	      // 16 bits, 8 bits for "clk_seq_hi_res",
	      // 8 bits for "clk_seq_low",
	      // two most significant bits holds zero and one for variant DCE1.1
	      mt_rand(0, 0x3fff) | 0x8000,
	      // 48 bits for "node"
	      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	    );
	  
		//return uniqid();
		/*// http://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
	    $data = openssl_random_pseudo_bytes(16);
	    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
	    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
	    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));*/
	}
}
?>