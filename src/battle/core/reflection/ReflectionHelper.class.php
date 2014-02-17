<?php
/**
 * Reflection Helper
 * http://www.php.net/manual/fr/book.reflection.php
 * http://yuml.me/diagram/scruffy/class/samples
 * @author touchypunchy
 */
class ReflectionHelper{
    
    // Generate yUML class diagram definition from folder.
    // example: [User|+Forename+;Surname;+HashedPassword;-Salt|+Login();+Logout()]
    public static function generate_yuml_class_diagram_definitions($app_folder = null){

		if($app_folder === null)
			return "NADA";
		
		$diagrams = array();
		$definition = "";
		
		foreach (glob($app_folder,GLOB_ONLYDIR) as $dirname) {
			$classes = $dirname."/*.class.php";
	    	foreach(glob($classes) as $filename){
				$controller_class_file_path = $filename;
				$split = explode("/",$filename);
				$controller_name = $split[count($split) - 1];
				$split = explode(".", $controller_name);
				$controller_class = $split[0];

				// Load the file
				if(file_exists($controller_class_file_path))
					require_once($controller_class_file_path);

				if (class_exists($controller_class,false)) {
					try{
						$definition .= "[$controller_class|";
						$class = new ReflectionClass($controller_class);
						$properties = $class->getProperties();
						foreach ($properties as $prop) {
							if($prop->isPublic())
								$definition .= "+".$prop->getName().";";
	         				elseif($prop->isPrivate())
	         					$definition .= "-".$prop->getName().";";
						}
						$definition .= "|";
						$methods = $class->getMethods();
						foreach ($methods as $meth) {
							if($meth->isPublic())
								$definition .= "+".$meth->name.";";
	         				/*elseif($meth->isPrivate())
	         					$definition .= "-".$meth->name.";";*/
						}
						$definition .= "],";
					}
					catch (Exception $error) {}
				}
			}
			$diagrams[ucfirst($dirname)] = $definition;
			$definition = "";
		}
		return $diagrams;
    }

    // Generate yUML class diagram definition from CREATE TABLE statements in sql files contained in given folder.
    // example: [btl_user|id int(11);name varchar(255);]
    public static function generate_yuml_class_diagram_definitions_from_sql($app_folder){
    	if($app_folder === null)
			return "NADA";
		
		$diagrams = array();
		$definition = "";
		$in_table = false;
		
		foreach (glob($app_folder,GLOB_ONLYDIR) as $dirname) {
			$classes = $dirname."/*.sql";
	    	foreach(glob($classes) as $filename){
				// Load the file
				$sql_file_as_lines = file($filename);
				foreach ($sql_file_as_lines as $line) {
					if (preg_match("/CREATE TABLE .+ `([a-zA-Z0-9_]+)`/", $line, $matches)) {
						$table_name = $matches[1];
						$in_table = true;
						$definition .= "[$table_name|";
						continue;
					}elseif(preg_match("/\) ENGINE=/", $line, $matches)) {
						$in_table = false;
						$definition .= "],";
					}

					if(!$in_table)
						continue;
					else{
						if(preg_match("/`([a-zA-Z0-9_]+)` ([a-zA-Z0-9_\(\)]+)/", $line, $matches)){
							$field_name = $matches[1];
							$field_type = $matches[2];
							$definition .= $field_name." ".$field_type.";";
						}
					}
				}
				$diagrams[ucfirst($filename)] = $definition;
				$definition = "";
			}
		}
		return $diagrams;
    }
}