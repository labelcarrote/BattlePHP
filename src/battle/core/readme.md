BattlePHP - Core
================

The Router class is instanciated and called in index.php, check the request/query (after its .htaccess processing), creates the corresponding Controller and call its wanted method.

example :

http://[my_web_repository]/src/battle/magicalapp/wonderfull/great

will call the "great()"" method of a Controller named "ActionWonderfull" (file : ActionWonderfull.class.php) in the folder : 

/src/battle/app/magicalapp/action

if "great()"" is not a method in the Controller, the Router will try to find an "index()"" method and will call it.


Controllers uses the Viewer class internaly for templating : retrieving / displaying view (.tpl), assigning value to view etc). 
It provides a facade to the Viewer class, so you should just deal with the Controller's methods and forget the Viewer's.

Hierarchy
---------

Router.class.php
Request.class.php
Controller.class.php
Viewer.class.php : extends Smarty, 


/src/battle/
- app : Where the wilds apps are
- config : Set your shared setting (DB,SMTP,Smarty Tpl Dir, etc) here
- core : Core framework
- lib : Shared php or js libraries used by core and apps
- public : Shared static files (home-made javascript, fonts, icons etc)
- tmp/tpl_comp : Default smarty template cache directory


