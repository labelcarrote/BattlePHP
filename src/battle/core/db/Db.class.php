<?php
/**
 * DB (Abstract)
 *
 * Database connection using PDO
 *
 */
abstract class DB{
	// connection
	protected $con = null;

	/**
	 * Connect to database using the datasource (dsn) defined in configuration
	 */
	// TODO : add application param = null for shared db queries (user !!!)
	protected function connect(){
		// retrieve current application queries
		$application = Request::isset_or($_SESSION['application'],null);
		// TODO : temp
		/*$application = ($application === "sawhat") ? "flipapart" : $application;
		require_once "app/$application/db/query.php";*/

		// create connection to db
		$host = Configuration::DB_HOST;
		$dbname = Configuration::DB_NAME;
		$dbuser = Configuration::DB_USER;
		$dbpass = Configuration::DB_PASS;

		try{
			$this->con = new PDO(
				"mysql:host=$host;dbname=$dbname",
				$dbuser,
				$dbpass,
				array(PDO::ATTR_PERSISTENT => true));
		}
		catch(PDOException $e){
			echo "<br>Could not connect to database: $host<br>";
			echo "<strong>ERROR! ".$e->getMessage()."</strong><br>";
		}
	}

	protected function get_statement($key){
		global $DB_QUERY;
		//Logger::trace($DB_QUERY[$key]);
		return $this->con->prepare($DB_QUERY[$key]);
	}

	protected function query_error($exception) {
		echo '<br><strong>ERROR!</strong>';
		echo '<br>Could not successfully run query <br>';
		echo $exception->getMessage();
	}
}
?>
