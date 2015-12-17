<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="Ground">
		<meta name="author" content="label carrote">
		<meta name="keywords" content="label carrote, battle, jon dalton, touchypunchy, p2b, moustachu, mitch">
		<title>Ground</title>
		<link rel="stylesheet" href="{$batl_root_url}public/css/reset.css">
		<link rel="stylesheet" href="{$batl_current_app_url}public/css/ground.css">
		<script type="text/javascript" src="{$batl_root_url}lib/jquery/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="{$batl_current_app_url}public/js/ground.js?var={rand(1, 2000)}"></script>
	</head>
	<body>
		<img class="centered_img hidden" alt="" src="">
		<form class="ground_api_form" action="{$batl_current_app_virtual_url}ground/api"></form>
		<section id="explorer" class="content whitebg hidden shadow">
			{include file="element.folder.tpl"}
		</section>
		<div id="bottombar" class="graybg1">
			<div class="left"></div>
			<span class="bottombar_right element_index paddingright"></span>
			<div class="element_name"></div>
		</div>
	</body>
</html>