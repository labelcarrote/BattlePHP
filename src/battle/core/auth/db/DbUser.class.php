<?php
require_once "core/db/Db.class.php";
require_once "core/auth/db/query.php";

/**--------------------------------------------------------------------
 * UserDB:
 * 
 * repository who loves to CRUD the users with their roles and profiles
 * 
 * --------------------------------------------------------------------
 */
class UserDB extends DB{

	/* ---- Constructors ---- */
	
	// single instance of self shared among all instances
	private static $instance = null;

	// private constructor (singleton)
	private function __construct(){
		parent::connect();
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

	/* ---- Methods---- */

	private function user_row_to_array($row){
		$user['id'] = $row['btl_user_id'];
		$user['role_id'] = stripslashes($row['btl_role_id']);
		//$user['role_name'] = stripslashes($row['role_name']);
		$user['application'] = stripslashes($row['application']);
		$user['mail'] = stripslashes($row['mail']);
		$user['login'] = stripslashes($row['login']);
		$user['hashpassword'] = stripslashes($row['hashpassword']);
		$user['has_confirmed'] = ord($row['has_confirmed']);
		$user['date_creation'] = stripslashes($row['date_creation']);
		$user['last_ip'] = stripslashes($row['last_ip']);
		$user['mark_for_deletion'] = ord($row['mark_for_deletion']);
		$user['mark_for_deletion_date'] = stripslashes($row['mark_for_deletion_date']);
		$user['confirmation_token'] = stripslashes($row['confirmation_token']);
		return $user;
	}

	// [CREATE] creates a new user (id auto incremented)
	public function add($user){
		try{
			$stmt = $this->get_statement("create_user");
			$stmt->bindParam(1, $user->role_id);
			$stmt->bindParam(2, $user->mail);
			$stmt->bindParam(3, $user->login);
			$stmt->bindParam(4, $user->hashpassword);
			$stmt->bindParam(5, $user->last_ip);
			$stmt->bindParam(6, $user->application);
			return $stmt->execute();
		}
		catch(PDOException $e){
			$this->query_error($e);
			Logger::trace($e);
		}
	}

	// [READ] the user identified by $id
	public function get_user($id){
		try{
			$stmt = $this->get_statement("read_user");
			$stmt->bindParam(1, $id);
			$stmt->execute();
			$row = $stmt->fetch();
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
			return $this->user_row_to_array($row);
		}
		catch(PDOException $e){
			$this->query_error($e);
		}
	}
	
	public function get_user_profile($user_id){
		
	}

	// [READ] some users (paged)
	public function get_users_of_page($page_id, $nb_user_by_page, $application){
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
			$stmt->bindParam(1, $user->btl_role_id);
			$stmt->bindParam(2, $user->mail);
			$stmt->bindParam(3, $user->login);
			$stmt->bindParam(4, $user->hashpassword);
			$stmt->bindParam(5, $user->has_confirmed);
			$stmt->bindParam(6, $user->date_creation);
			$stmt->bindParam(7, $user->last_ip);
			$stmt->bindParam(8, $user->mark_for_deletion);
			$stmt->bindParam(9, $user->mark_for_deletion_date);
			$stmt->bindParam(10, $user->confirmation_token);
			$stmt->bindParam(11, $user->application);
			$stmt->bindParam(12, $user->id);

			$stmt->execute();
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