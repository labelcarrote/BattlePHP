<?php
namespace BattlePHP\Model;
/**
 * Entity
 *
 */
abstract class Entity{
	public $fields = array();
	protected $errors = null;

	public function __set($property, $value){
		if(isset($this->fields[$property]))
			$this->fields[$property] = $value;
	}

	public function __get($property){
		if(isset($this->fields[$property]))
			return $this->fields[$property];
		else
			return 0;
	}

	public function __isset($property){
	    return isset($this->fields[$property]);
	} 

	public function __toString(){
		print_r($this->fields);
		return '';
	}
}
