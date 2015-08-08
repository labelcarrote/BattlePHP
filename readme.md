BattlePHP
==========
BattlePHP is a small open source MVC PHP framework by examples.

It includes some sample-websites we've made, that ranges from the most basic single page apps (Partycul, CrappyCrop, FingerZen) to the more mature websites like Sawhat (another text-based-wiki).

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


Installation with Composer
--------------------------
- Download and unzip in your web directory
- in /src/battle run :
```
> composer install
```
- Go to http://[your_web_directory]/src/battle/ in your favorite browser

Optional (for examples that require a database) :
- Change your database access in config/config.php
- Execute the "install.sql" of every app you wish to install (in : app/[app_name]/_install/)

Installation without Composer
-----------------------------
- Download and unzip the special package [TODO] with all dependencies in your web directory
- Go to http://[your_web_directory]/src/battle/ in your favorite browser

Optional (for examples that require a database) :
- Change your database access in config/config.php
- Execute the "install.sql" of every app you wish to install (in : app/[app_name]/_install/)

Dependencies
------------

Core: 
Only server-side dependencies, Smarty for templating, PHPMailer, PHPass for hashing passwords, and HTMLPurifier.
```
> composer install
```
```
"smarty/smarty": "3.1.21",
"ezyang/htmlpurifier": "4.6.0",
"hautelook/phpass": "dev-master",
"phpmailer/phpmailer": "dev-master"
```

- Smarty : 3.1.21
- HTML Purifier : 4.6.0
- Phpass : dev-master
- PHPMailer : dev-master

Applications [11]:
- bap :  minim.js v.O, processing v.?
- colorflipper : normalize.css, jquery v1.10.1, jquery.minicolors v.?
- fingerzen : reset.css, jquery-1.10.2
- ground : reset.css, jquery-1.10.2
- mirror : reset.css
- pansho : reset.css, snap.svg-0.2.0
- partycul : reset.css, jquery v1.10.1
- pipix : SL...
- processing : reset.css, processing-1.4.1
- sawhat : reset.css, font-awesome-4.1.0, jquery v1.10.1, bootstrap v3.1.1, jasny-bootstrap v3.1.3, prism v.0
- timeline : reset.css, jquery-1.10.2, font-awesome-4.2.0
- tomatoro : reset.css, jquery v1.10.1, jquery.cookie v1.3.1, kiecoo.js

- Resume: 
  - reset.css [9]
  - normalize.css [1]
  - jquery : "1.10.1" [4] + "1.10.2" [3] = [7]
  - font-awesome : "4.1.0" [1] + "4.2.0" [1] = [2]
  - bootstrap : "v3.1.1" [1]
  - jasny-bootstrap : "v3.1.3" [1]
  - prism : "?" [1]
  - snap.svg : "0.2.0" [1]
  - jquery.minicolors : "?" [1]
  - jquery.cookie : "1.3.1" [1]

  Note : no server side dependencies in applications, only frontend js and css

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
    