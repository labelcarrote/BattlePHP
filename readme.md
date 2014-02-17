BattlePHP
==========
BattlePHP is a small open source MVC PHP framework by examples.

It includes some websites we've made using it, that ranges from the most basic single page apps (Partycul, CrappyCrop, FingerZen) to the more mature websites like Sawhat (another text-based-wiki).

Demo : 
http://labelcarrote.com/battle/ 

Features
--------
- Includes many delightfull samples 
- Follows best practices as much as possible
- Simple "REST routing" by folder hierarchy convention (also uses some sinatra-inspired query parameters retrieval)
- Uses Smarty for templating (optional)
- Uses pdo / mysql in authentication layer (optional)

Hierarchy
---------
/src/battle/
- app : Where the wilds apps are
- config : Set your shared setting (DB,SMTP,Smarty Tpl Dir, etc) here
- core : Core framework
- lib : Shared php or js libraries used by core and apps
- public : Shared static files (home-made javascript, fonts, icons etc)
- tmp/tpl_comp : Default smarty template cache directory

Installation
------------
- Download and unzip in your web directory
- Go to http://[your_web_directory]/src/battle/ in your favorite browser

Optional (for examples that require a database) :
- Change your database access in config/config.php
- Execute the "install.sql" of every app you wish to install (in : app/[app_name]/_install/)

Create a new website / application
----------------------------------
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
    