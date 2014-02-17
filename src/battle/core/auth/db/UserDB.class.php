<?php
require_once "core/auth/db/query.php";

/**--------------------------------------------------------------------
 * UserDB:
 * 
 * repository who loves to CRUD the users with their roles and profiles
 * 
 * --------------------------------------------------------------------
 */
class UserDB{

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
			$this->con = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass, array(PDO::ATTR_PERSISTENT => true));
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

	/* ---- Methods---- */

	private function user_row_to_array($row){
		$user['id'] = $row['user_id'];
		$user['role_id'] = stripslashes($row['role_id']);
		//$user['role_name'] = stripslashes($row['role_name']);
		$user['application'] = stripslashes($row['application']);
		$user['mail'] = stripslashes($row['mail']);
		$user['login'] = stripslashes($row['login']);
		$user['hashed_password'] = stripslashes($row['hashed_password']);
		$user['has_confirmed'] = ($row['has_confirmed'] > 0);
		$user['date_creation'] = stripslashes($row['date_creation']);
		$user['last_ip'] = stripslashes($row['last_ip']);
		$user['marked_for_deletion'] = ($row['marked_for_deletion'] > 0);
		$user['marked_for_deletion_date'] = stripslashes($row['marked_for_deletion_date']);
		$user['confirmation_token'] = stripslashes($row['confirmation_token']);
		return $user;
	}

	// [CREATE] creates a new user (id auto incremented)
	public function add($user){
		try{
			$stmt = $this->get_statement("create_user");
			$stmt->bindValue(1, $user->role_id, PDO::PARAM_INT);
			$stmt->bindValue(2, $user->mail, PDO::PARAM_STR);
			$stmt->bindValue(3, $user->login, PDO::PARAM_STR);
			$stmt->bindValue(4, $user->hashed_password, PDO::PARAM_STR);
			$stmt->bindValue(5, $user->last_ip, PDO::PARAM_STR);
			$stmt->bindValue(6, $user->application, PDO::PARAM_STR);
			$stmt->bindValue(7, $user->confirmation_token, PDO::PARAM_STR);
			return $stmt->execute();
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

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
	public function get_user($id){
		try{
			$stmt = $this->get_statement("read_user");
			$stmt->bindParam(1, $id);
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

	// [UPDATE] updates the given user
	public function update($user){
		try{
			$stmt = $this->get_statement("update_user");
			$stmt->bindValue(1, $user->role_id, PDO::PARAM_INT);
			$stmt->bindValue(2, $user->mail, PDO::PARAM_STR);
			$stmt->bindValue(3, $user->login, PDO::PARAM_STR);
			$stmt->bindValue(4, $user->hashed_password, PDO::PARAM_STR);
			$stmt->bindValue(5, $user->has_confirmed);
			$stmt->bindValue(6, $user->date_creation, PDO::PARAM_STR);
			$stmt->bindValue(7, $user->last_ip, PDO::PARAM_STR);
			$stmt->bindValue(8, $user->mark_for_deletion);
			$stmt->bindValue(9, $user->mark_for_deletion_date, PDO::PARAM_STR);
			$stmt->bindValue(10, $user->confirmation_token, PDO::PARAM_STR);
			$stmt->bindValue(11, $user->application, PDO::PARAM_STR);
			$stmt->bindValue(12, $user->id, PDO::PARAM_INT);

			$stmt->execute();
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
			return $stmt->execute();
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}

	// [DELETE] deletes the given user (from its id)
	public function delete($userid){
		try{
			$stmt = $this->get_statement("delete_user");
			$stmt->bindParam(1, $userid);
			$stmt->execute();
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}
}
?>