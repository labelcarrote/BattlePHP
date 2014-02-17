<?php
/**
 * Logger (Singleton)
 *
 * Note : beware of the cost of writing to text files...
 *
 * @author moustachu, touchypunchy
 *
 */
class Logger{
	
	private $traceIsEnabled = false;
	private $log_file;

	// ---- Singleton ----

	private static $instance = null;

	private static function getInstance(){
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance ;
	}
	
	private function __construct(){
		$this->traceIsEnabled = Configuration::PRODUCTION_MODE != true;
		$this->log_file = Configuration::MAIN_LOG_FILE;
	}

	/**
	 * Append message to log file (defined in Configuration::MAIN_LOG_FILE)
	 * @param string $message
	 */
	public static function trace($message){
		$instance = self::getInstance();
		if($instance->traceIsEnabled)
			error_log(date(DateTime::RFC850)." : ".$message."\n", 3, $instance->log_file);
	}
}
?>