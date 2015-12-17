BattlePHP
==========
BattlePHP is a small open source MVC PHP framework by [https://github.com/labelcarrote/BattlePHP/tree/master/src/battle/app](examples).

It includes some sample-websites we've made, that ranges from the single page apps (Partycul, CrappyCrop, FingerZen) to more mature websites like Sawhat (another text-based-wiki). It targets PHP 5.5 and up.

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

PHP 5.5+

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

BattlePHP - Core
================

The Core of BattlePHP essentially does a routing from a REST-friendly URL to a corresponding action or page.
This routing occurs from the main index.php, where we simply autoload the dependencies of the Core and run the Router.

The Router run() method then checks the request/query (after its .htaccess processing), and using just a few folder and naming conventions, creates the corresponding controller and calls the right action.

Actions are the public methods of any controller classes. They can be used to return a page in html or send some json data. 
So as a developper, adding a new action "hello" to an existing app "test" accessible at the URL "http://[]/test/home/hello" goes like this :

- Put the content of the 'src/battle/' folder in your root web folder, let's say in your Apache 'www/' folder.
- Go to folder : app/Test
- In the default controller : app/test/action/ActionHome.class.php
    ```php
    <?php
  use BattlePHP\Core\Controller;

  class ActionHome extends Controller{
    public function index(){
      $this->display_view('index.tpl');
    }  
    // This method ! Add this method :
    public function hello(){
      $person = "georges";
      $this->assign("famous_people",$person);
      $this->display_view('section.hello.tpl');
    }
  }
    ```

- Add a view to your app : app/test/view/section.hello.tpl
    
    ```html
    <!doctype html>
  <html>
    <head>
      <meta charset="UTF-8">
      <meta name="description" content="An empty Site." />
      <title>Hi!</title>
    </head>
    <body>
      <p>Hello, I'm {$famous_people}.</p>
    </body>
  </html>
    ```
- Visit the url, enjoy :
http://[battle_folder_path]/test/home/hello

If you try to call a method that doesn't exist yet like "welcome" at the URL http://[battle_folder_path]/test/home/welcome
the Router will try to find an "index()"" method and will call it, since the welcome() method couldn't be found.

Controllers uses the Viewer class internaly for templating : retrieving / displaying view (.tpl), assigning value to view etc). 
It provides a facade to the Viewer class, so you can just deal with the Controller's methods and forget the Viewer's.

Hierarchy
---------

Router.php
Request.php
Controller.php
Viewer.php : extends Smarty
Logger.php

Template Reserved Variables
---------------------------

BattlePHP assigns some helper smarty variables :

by Viewer:  
- $batl_root_url: : the url to access the root folder of the framework : the app/ folder. 
- $batl_current_app_virtual_url
- $batl_current_app_url
- $batl_full_url
- $batl_is_mobile_device

by Controller:  
- $batl_is_logged : if current user is logged in
- $batl_is_admin : if current user is an admin
- $batl_current_user : current User

In your smarty templates you can use these predefined variables using the usual smarty syntax:  
{$batl_is_logged})

You can also access this variable in Plain Ol' PHP using :
- $batl_root_url: : Request::get_root_url()
- $batl_current_app_virtual_url : Request::get_application_virtual_root()
- $batl_current_app_url : Request::get_application_root()
- $batl_full_url : Request::get_full_url()
- $batl_is_mobile_device : Request::is_mobile_device()
- $batl_is_logged : AuthManager::is_authenticated()
- $batl_is_admin : AuthManager::is_current_user_admin()
- $batl_current_user : AuthManager::get_user_infos()
    