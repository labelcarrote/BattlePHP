<?php 
/**
* Criteria/Specification of a DomainEventRepository search (cf. DDD Specification Pattern)
*/
class DomainEventSpecification{
	public $names = null;
	public $element_id = null;
	
	public $page_id = 1;
	public $nb_event_by_page = 100;
	public $ordered_by = null;
	public $in_descending_order = true;

	public $date1 = null;
	public $date2 = null;
}
?>