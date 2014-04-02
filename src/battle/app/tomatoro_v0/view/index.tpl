<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="Simple Pomodoro Timer On Browser." />
		<meta name="author" content="label carrote"/>
		<meta name="keywords" content="label carrote, battle, jon dalton, touchypunchy, p2b, moustachu, mitch" />
		<title>TOMATORO</title>
		<link rel="stylesheet" href="{$root_url}public/css/reset.css" />
		<link rel="stylesheet" href="{$current_app_url}public/css/tomatoro.css" />
		<script type="text/javascript" src="{$root_url}lib/jquery/jquery.js"></script>
		<script type="text/javascript" src="{$root_url}lib/jquery/jquery.cookie.js"></script>
		<script type="text/javascript" src="{$root_url}public/js/kiecoo.js"></script>
		<script type="text/javascript" src="{$current_app_url}public/js/chartreuse.js"></script>
		<script type="text/javascript" src="{$current_app_url}public/js/tomatoro.js"></script>
	</head>
	<body>
		<div id="canvases">
			<canvas id="layer1">Oups, your browser doesn't support Tomatoro. You should try it somewhere else!</canvas>
			<canvas id="layer2"></canvas>
			<canvas id="layer3"></canvas>
		</div>
	</body>
</html>