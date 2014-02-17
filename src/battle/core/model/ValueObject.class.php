<?php
/**
 * ValueObject
 *
 */
abstract class ValueObject{
	protected $fields = array();
	protected $errors = null;

	public function __set($property, $value){
		if(isset($this->fields[$property]))
			$this->fields[$property] = $value;
	}

	//public function &__get($property){
	public function __get($property){
		if(isset($this->fields[$property]))
			return $this->fields[$property];
		else
			return 0;
	}

	public function __toString(){
		print_r($this->fields);
		return '';
	}
	
	/* ---- Errors ---- */

	public function add_error($field,$error){
		if($this->errors == NULL)
			$this->errors = array();
		$this->errors[$field] = $error;
	}

	public function get_errors(){
		return $this->errors;
	}
}
?>