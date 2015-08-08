<?php
/**
 * AjaxResult
 *
 */
class AjaxResult{
	public $errors;
	public $body;

	public function to_json(){
		return json_encode($this);
	}
}
?>