<?php
require_once 'core/reflection/ReflectionHelper.class.php';

class ActionMirror extends Controller{

	public function index(){
		$app_folder = "app";
		$app_name = Request::isset_or($_GET["app"],"mirror");
		$path = "$app_folder/$app_name/*";
		$sqlpath = "../../install/";

		$diagram_definitions = ReflectionHelper::generate_yuml_class_diagram_definitions($path);
		$sql_diagram_definitions = ReflectionHelper::generate_yuml_class_diagram_definitions_from_sql($sqlpath);
		$this->assign('current_app', $app_name);
		$this->assign('all_apps', Router::get_all_apps());
		$this->assign('definitions', $diagram_definitions);
		$this->assign('sqldefinitions', $sql_diagram_definitions);
		$this->display_view('index.tpl');
	}
}
?>