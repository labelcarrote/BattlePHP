<?php
class ActionHome extends Controller{

	// WORDS FOR FINGERS
	private $words = array("sun","bananas","finger","shake","cheese","zen","words","milk","moon","cream");

	// [home] Display the Home page
	public function index(){
		$this->display_view('index.tpl');
	}

	// ---- AJAX ----

	// [home/words] Some words
	public function words(){
		echo json_encode($this->words);
	}
}
?>