<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="\o/">
		<title>{$title} - BattlePHP</title>
	</head>
	<body>
		<p>
			I'm the index page from BattlePHP's <strong>{$title}</strong> example, which illustrates how the routing works in BattlePHP :
		</p>
		<p>
			- <a href="{$batl_current_app_virtual_url}home">/home</a> or <a href="{$batl_current_app_virtual_url}">/</a> = current page, calls <strong>ActionHome::index()</strong><br>
			- <a href="{$batl_current_app_virtual_url}home/page1">/home/page1</a> = a sub page of the 'home' section, calls <strong>ActionHome::page1()</strong><br>
			- <a href="{$batl_current_app_virtual_url}lol">/lol</a> = another section called 'lol', calls <strong>ActionLol::index()</strong><br>
			- <a href="{$batl_current_app_virtual_url}lol/wat">/lol/wat</a> = another sub page called 'wat' from the 'lol' section, calls <strong>ActionLol::wat()</strong>
		</p>
	</body>
</html>