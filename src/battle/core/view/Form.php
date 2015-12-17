<?php
namespace BattlePHP\View;
/**
 * Form
 *
 */
class Form{
	protected $errors = null;

	public function add_error($field,$error){
		if($this->errors == NULL)
			$this->errors = array();
		$this->errors[$field] = $error;
	}

	public function get_errors(){
		return $this->errors;
	}
}
