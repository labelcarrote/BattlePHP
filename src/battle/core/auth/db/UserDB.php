<?php
namespace BattlePHP\Core\Auth;
use \PDO;
use \Configuration;
use \DateTime;
require_once "core/auth/db/query.php";

/**--------------------------------------------------------------------
 * UserDB:
 * 
 * repository who loves to CRUD the users with their roles and profiles
 * 
 * --------------------------------------------------------------------
 */
class UserDB{

	const DB_DATE_FORMAT = "Y-m-d H:i:s";

	/* ---- Constructor / PDO ---- */
	
	// PDO connection
	private $con = null;
	// single instance of self shared among all instances
	private static $instance = null;

	// private constructor (singleton)
	private function __construct(){
		$host = Configuration::DB_HOST;
		$dbname = Configuration::DB_NAME;
		$dbuser = Configuration::DB_USER;
		$dbpass = Configuration::DB_PASS;
		try{
			$this->con = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass, array(\PDO::ATTR_PERSISTENT => true));
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	public static function getInstance(){
		if (!self::$instance instanceof self)
			self::$instance = new self;
		return self::$instance;
	}

	public function __clone(){
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}

	public function __wakeup(){
		trigger_error('Deserializing is not allowed.', E_USER_ERROR);
	}

	private function get_statement($key){
		global $DB_QUERY;
		return $this->con->prepare($DB_QUERY[$key]);
	}

	private function prepare($query){
		return $this->con->prepare($query);
	}

	private function query_error($exception) {
		echo $exception->getMessage();
	}

	// ---- HELPERS ----

	private function user_row_to_array($row){
		$user['id'] = $row['user_id'];
		$user['role_id'] = stripslashes($row['role_id']);
		$user['application'] = stripslashes($row['application']);
		$user['mail'] = stripslashes($row['mail']);
		$user['login'] = stripslashes($row['login']);
		$user['hashed_password'] = stripslashes($row['hashed_password']);
		$user['date_last_password_update'] = stripslashes($row['date_last_password_update']);
		$user['has_confirmed'] = ($row['has_confirmed'] > 0);
		$user['date_creation'] = stripslashes($row['date_creation']);
		$user['date_last_connection'] = stripslashes($row['date_last_connection']);
		$user['last_ip'] = stripslashes($row['last_ip']);
		$user['marked_for_deletion'] = ($row['marked_for_deletion'] > 0);
		$user['marked_for_deletion_date'] = stripslashes($row['marked_for_deletion_date']);
		$user['confirmation_token'] = stripslashes($row['confirmation_token']);
		return $user;
	}


	// ---- COMMANDS : Create, Update, Delete 

	// [CREATE/UPDATE] Creates user if user_id == 0, updates user otherwise
	public function upsert_user($user){
		try{
			$index = 1;
			if($this->get_user($user->id) !== null){
				$stmt = $this->get_statement("update_user");
				$stmt->bindValue($index++, $user->role_id, PDO::PARAM_INT);
				$stmt->bindValue($index++, $user->mail, PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->login, PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->hashed_password, PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->has_confirmed, PDO::PARAM_INT);
				$stmt->bindValue($index++, $user->date_creation->format(self::DB_DATE_FORMAT), PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->last_ip, PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->marked_for_deletion, PDO::PARAM_INT);
				$stmt->bindValue($index++, $user->marked_for_deletion_date->format(self::DB_DATE_FORMAT), PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->confirmation_token, PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->application, PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->id, PDO::PARAM_INT);
				$stmt->execute();
				return true;
			}else{
				$stmt = $this->get_statement("create_user");
				$stmt->bindValue($index++, $user->role_id, PDO::PARAM_INT);
				$stmt->bindValue($index++, $user->mail, PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->login, PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->hashed_password, PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->last_ip, PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->application, PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->confirmation_token, PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->date_creation->format(self::DB_DATE_FORMAT), PDO::PARAM_STR);
				$stmt->bindValue($index++, $user->has_confirmed, PDO::PARAM_INT);

				$res = $stmt->execute();
				return $res;
			}
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [UPDATE] Updates the given user
	public function update_user_password($user_id,$new_hashed_password){
		try{
			$now = new DateTime();
			$stmt = $this->get_statement("update_user_password");
			$stmt->bindValue(':hashed_password', $new_hashed_password, PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->bindValue(':date_last_password_update', $now->format(self::DB_DATE_FORMAT));
			$stmt->execute();
			return $stmt->rowCount() > 0;
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [UPDATE] Updates a user last connection informations (ip,date)
	public function update_user_last_connection($user_id, $user_ip){
		try{
			$now = new DateTime();
			$stmt = $this->get_statement("update_user_last_connection");
			$stmt->bindValue(1, $user_ip);
			$stmt->bindValue(2, $now->format(self::DB_DATE_FORMAT));
			$stmt->bindValue(3, $user_id, PDO::PARAM_INT);
			$stmt->execute();
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [UPDATE] Regenerates user confirmation_token
	public function regenerate_user_confirmation_token($user){
		try{
			$stmt = $this->get_statement("regenerate_user_confirmation_token");
			$token = sha1(uniqid($user->login, true));
			$stmt->bindValue(1, $token);
			$stmt->bindValue(2, $user->id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->rowCount() > 0;
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [UPDATE] Validate a user account from a confirmation token
	public function validate_user_account($confirmation_token){
		try{
			$stmt = $this->get_statement("validate_user_account");
			$stmt->bindParam(1, $confirmation_token);
			$stmt->execute();
			return $stmt->rowCount() > 0;
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [DELETE] deletes the given user (from its id)
	public function delete($user_id){
		try{
			$stmt = $this->get_statement("delete_user");
			$stmt->bindParam(1, $user_id, PDO::PARAM_INT);
			$stmt->execute();
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// ---- QUERIES

	// [READ]
	public function count_all_users($application){
		try{
			$stmt = $this->get_statement("count_all_users");
			$stmt->bindParam(1, $application);
			$stmt->execute();
			return $stmt->fetchColumn();
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [READ]
	public function exists_user_from_login($login,$application){
		try{
			$stmt = $this->get_statement("exists_user_from_login");
			$stmt->bindParam(1, $login);
			$stmt->bindParam(2, $application);
			$stmt->execute();
			return $stmt->rowCount() > 0;
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [READ]
	public function exists_user_from_mail($mail,$application){
		try{
			$stmt = $this->get_statement("exists_user_from_mail");
			$stmt->bindParam(1, $mail);
			$stmt->bindParam(2, $application);
			$stmt->execute();
			return $stmt->rowCount() > 0;
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [READ] the user identified by $id
	public function get_user($user_id){
		try{
			$stmt = $this->get_statement("read_user");
			$stmt->bindParam(1, $user_id);
			$stmt->execute();
			$row = $stmt->fetch();
			if(!$row)
				return null;
			return $this->user_row_to_array($row);
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [READ]
	public function get_user_from_login($login,$application){
		try{
			$stmt = $this->get_statement("read_user_from_login");
			$stmt->bindParam(1, $login);
			$stmt->bindParam(2, $application);
			$stmt->execute();
			$row = $stmt->fetch();
			if(!$row)
				return null;
			return $this->user_row_to_array($row);
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}
	
	// [READ]
	public function get_user_from_mail($mail,$application){
		try{
			$stmt = $this->get_statement("read_user_from_mail");
			$stmt->bindParam(1, $mail);
			$stmt->bindParam(2, $application);		
			$stmt->execute();
			$row = $stmt->fetch();
			if(!$row)
				return null;
			return $this->user_row_to_array($row);
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [READ]
	public function get_user_from_confirmation_token($confirmation_token,$application){
		try{
			$stmt = $this->get_statement("read_user_from_confirmation_token");
			$stmt->bindParam(1, $confirmation_token);
			$stmt->bindParam(2, $application);		
			$stmt->execute();
			$row = $stmt->fetch();
			if(!$row)
				return null;
			return $this->user_row_to_array($row);
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [READ] some users (paged)
	public function get_users($page_id, $nb_user_by_page, $application){
		try{
			// id of the first user to get (offset)
			$i = ($page_id - 1) * $nb_user_by_page;
			
			$stmt = $this->get_statement("read_users_of_page");
			$stmt->bindParam(1, $application);
			$stmt->bindParam(2, $i, PDO::PARAM_INT);// doesn't work without PDO::PARAM_INT (bug pdo)
			$stmt->bindParam(3, $nb_user_by_page, PDO::PARAM_INT); // same here
			$stmt->execute();
			
			$table_user = array();
			while ($row = $stmt->fetch())
				$table_user[] = $this->user_row_to_array($row);
			return $table_user;
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [READ] users arising from the given search
	public function search($keywords){
		try{
			$stmt = $this->get_statement("search_user");
			$stmt->bindParam(1, $keywords);
			$stmt->execute();

			$table_user = array();
			while ($row = $stmt->fetch())
				$table_user[] = $this->user_row_to_array($row);
			return $table_user;
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}
}
