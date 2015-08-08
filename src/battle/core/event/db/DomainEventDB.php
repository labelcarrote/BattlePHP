<?php
namespace BattlePHP\Event;
use \Configuration;
use \PDO;

/**--------------------------------------------------------------------
 * DomainEventDB
 * --------------------------------------------------------------------
 */
class DomainEventDB{

	const DB_DATE_FORMAT = "Y-m-d H:i:s";
	const DEFAULT_TABLE = "btl_events";

	// Queries
	private $queries = array();

	/* ---- Constructor / PDO ---- */
	
	// PDO connection
	private $con = null;

	// Single instance of self shared among all instances
	private static $instance = null;

	// Private constructor (singleton)
	private function __construct(){
		$host = Configuration::DB_HOST;
		$dbname = Configuration::DB_NAME;
		$dbuser = Configuration::DB_USER;
		$dbpass = Configuration::DB_PASS;
		try{
			$this->con = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass, array(PDO::ATTR_PERSISTENT => true));
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	public static function getInstance($table_name = null){
		if (!self::$instance instanceof self)
			self::$instance = new self;

		self::$instance->set_queries(($table_name === null) ? self::DEFAULT_TABLE : $table_name);
		//self::$instance->create_table_if_not_exist();

		return self::$instance;
	}

	public function __clone(){
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}

	public function __wakeup(){
		trigger_error('Deserializing is not allowed.', E_USER_ERROR);
	}

	public function create_table_if_not_exist(){
		$stmt = $this->get_statement("create_table_event_if_not_exist");
		return $stmt->execute();
	}

	public function set_queries($table_name){
		$this->queries = array();
		$this->queries["create_table_event_if_not_exist"] = 
		"CREATE TABLE IF NOT EXISTS `".$table_name."` (
		  `event_id` char(36) NOT NULL,
		  `event_name` varchar(255) NOT NULL,
		  `event_type` int(11) NOT NULL,
		  `element_id` bigint(20) NOT NULL,
		  `user_id` bigint(20) NOT NULL,
		  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `old_value` text NOT NULL,
		  `new_value` text NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		ALTER TABLE `".$table_name."` ADD PRIMARY KEY (`event_id`);";
		$this->queries["exists_event_from_id"] = 
		"SELECT 1 FROM ".$table_name." WHERE event_id = ?";
		$this->queries["create_event"] = 
		"INSERT INTO ".$table_name." (event_id,event_name,event_type,element_id,user_id,date,old_value,new_value) VALUES (?,?,?,?,?,?,?,?)";
		$this->queries["update_event"] = 
		"UPDATE ".$table_name." SET event_name = ?, event_type = ?, element_id = ?, user_id = ?, date = ?, old_value = ?, new_value = ? WHERE event_id = ?";
		$this->queries["select_event"] = 
		"SELECT * FROM ".$table_name."";
		$this->queries["read_event"] = 
		"SELECT * FROM ".$table_name." WHERE event_id = ?";
		$this->queries["count_all_events"] = 
		"SELECT count(*) FROM ".$table_name." event ";
		$this->queries["read_events"] = 
		"SELECT * FROM btl_events ORDER BY event_id	DESC LIMIT ?,?";
		$this->queries["delete_event"] = 
		"DELETE FROM ".$table_name." WHERE event_id = ?";
	}

	private function get_query($key){
		return $this->queries[$key];
	}

	private function get_statement($key){
		return $this->con->prepare($this->queries[$key]);
	}

	private function prepare($query){
		return $this->con->prepare($query);
	}

	private function query_error($exception) {
		echo $exception->getMessage();
	}

	// ---- HELPERS ----

	private function event_row_to_array($row){
		$event['id'] = $row['event_id'];
		$event['event_name'] = stripslashes($row['event_name']);
		$event['event_type'] = $row['event_type'];
		$event['element_id'] = $row['element_id'];
		$event['user_id'] = $row['user_id'];
		$event['date'] = stripslashes($row['date']);
		$event['old_value'] = stripslashes($row['old_value']);
		$event['new_value'] = stripslashes($row['new_value']);
		return $event;
	}

	// ---- COMMANDS : Create, Update, Delete 

	// [UPDATE] updates the given event, or creates a new event if it doesn't exist yet
	public function upsert_event($event){
		try{
			if($this->exists_event_from_id($event->id)){
				$stmt = $this->get_statement("update_event");
				$stmt->bindValue(1, $event->event_name);
				$stmt->bindValue(2, $event->event_type, PDO::PARAM_INT);
				$stmt->bindValue(3, $event->element_id, PDO::PARAM_INT);
				$stmt->bindValue(4, $event->user_id, PDO::PARAM_INT);
				$stmt->bindValue(5, $event->date->format(self::DB_DATE_FORMAT), PDO::PARAM_STR);
				$stmt->bindValue(6, $event->old_value);
				$stmt->bindValue(7, $event->new_value);
				$stmt->bindValue(8, $event->id, PDO::PARAM_INT);
				$stmt->execute();
				return true;
				//return($stmt->rowCount() > 0)
			}else{
				$stmt = $this->get_statement("create_event");
				$stmt->bindValue(1, $event->id, PDO::PARAM_INT);
				$stmt->bindValue(2, $event->event_name);
				$stmt->bindValue(3, $event->event_type, PDO::PARAM_INT);
				$stmt->bindValue(4, $event->element_id, PDO::PARAM_INT);
				$stmt->bindValue(5, $event->user_id, PDO::PARAM_INT);
				$stmt->bindValue(6, $event->date->format(self::DB_DATE_FORMAT), PDO::PARAM_STR);
				$stmt->bindValue(7, $event->old_value);
				$stmt->bindValue(8, $event->new_value);
				return $stmt->execute();
			}
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [DELETE]
	public function delete_event($event_id){
		try{
			$stmt = $this->get_statement("delete_event");
			$stmt->bindValue(1, $event_id);
			$stmt->execute();
			return $stmt->rowCount() > 0;
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// ---- QUERIES

	// [READ]
	public function exists_event_from_id($event_id){
		try{
			
			$stmt = $this->get_statement("exists_event_from_id");
			$stmt->bindValue(1, $event_id);
			$stmt->execute();
			return $stmt->rowCount() > 0;
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [READ] the event identified by $event_id
	public function get_event($event_id){
		try{
			$stmt = $this->get_statement("read_event");
			$stmt->bindParam(1, $event_id);
			$stmt->execute();
			$row = $stmt->fetch();
			if(!$row)
				return null;
			return $this->event_row_to_array($row);
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	public function count_all_events_in_search($names, $element_id){
		try{
			// Construct query
			$query = $this->get_query("count_all_events");

			$names_array = explode(',', $names);
			$any_names = isset($names);
			if($any_names)
				$query .= " WHERE event_name IN (".str_pad('',count($names_array) * 2 - 1,'?,').")";

			$any_element_id = isset($element_id) && $element_id > 0;
			if($any_element_id)
				$query .= " AND element_id = ?";

			// Bind values
			$stmt = $this->prepare($query);
			
			$index = 1;
			if($any_names){
				for($j = 0; $j < count($names_array); $j++)
					$stmt->bindValue($index++, $names_array[$j], PDO::PARAM_STR);
			}
			if($any_element_id){
				$stmt->bindValue($index++, $element_id, PDO::PARAM_INT);	
			}
			$stmt->execute();
			return $stmt->fetchColumn();
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [READ] events from the given search
	public function search_events($page_id, $nb_event_by_page, $names , $element_id = null, $ordered_by = null, $in_descending_order = null, $date1 = null, $date2 = null){
		try{
			// Construct query
			$query = $this->get_query("select_event");

			$names_array = explode(',', $names);
			$any_names = isset($names);
			$query .= ($any_names)
				? " WHERE event_name IN (".str_pad('',count($names_array) * 2 - 1,'?,').")"
				: " WHERE 1=1";
			
			$any_element_id = isset($element_id) && $element_id > 0;
			if($any_element_id)
				$query .= " AND element_id = ?";

			$any_dates = isset($date1) && isset($date2);
			if($any_dates){
				$query .= " AND date BETWEEN ? AND ?";
			}

			$order = ($in_descending_order === true) ? "DESC" : "ASC";
			$ordered_by = ($ordered_by === null || $ordered_by === "") ? "date" : $ordered_by;
			$query .= " ORDER BY ".$ordered_by." ".$order." LIMIT ?,?";

			// Bind values
			$stmt = $this->prepare($query);
			
			$index = 1;
			if($any_names){
				for($j = 0; $j < count($names_array); $j++)
					$stmt->bindValue($index++, $names_array[$j], PDO::PARAM_STR);
			}
			if($any_element_id){
				$stmt->bindValue($index++, $element_id, PDO::PARAM_INT);	
			}
			if($any_dates){
				$stmt->bindValue($index++, $date1->format(self::DB_DATE_FORMAT));	
				$stmt->bindValue($index++, $date2->format(self::DB_DATE_FORMAT));	
			}
			
			$i = ($page_id - 1) * $nb_event_by_page;
			$stmt->bindValue($index++, $i, PDO::PARAM_INT);
			$stmt->bindValue($index, $nb_event_by_page, PDO::PARAM_INT);
			$stmt->execute();

			$table_event = array();
			while ($row = $stmt->fetch())
				$table_event[] = $this->event_row_to_array($row);
			return $table_event;
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}
}
