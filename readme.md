Battle.PHP
==========

Installation
------------

- Download and unzip in your web directory
- Change your database access in config/config.php
- Execute the "install.sql" of every app you wish to install (in : app/[app_name]/_install/)

Create an App/Site
------------------
- Create a folder there : app/[app_name]
- Add a controller in your app : app/[app_name]/action/ActionHome.class.php

    ```php
    <?php  
    class ActionHome extends Controller{  
    	//Display the Home page   
        public function index(){  
    		$this->display_view('index.tpl');  
    	}  
    }?>
    ```

- Add a view to your app : app/[app_name]/view/index.tpl
    
    ```html
    <!doctype html>  
    <html>  
    	<head>  
    		<meta charset="UTF-8">  
    		<meta name="description" content="An empty Site." />  
    		<title>Battle</title>  
    	</head>  
    	<body>  
    	    <p>Hello Empty Site</p>
    	</body>  
    </html>
    ```
    