<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width"/> 
		<meta name="description" content="Come to Paris fluent wiki. Powered by Sawhat." />
		<meta name="author" content="label carrote"/>
		<meta name="robots" content="noindex, nofollow"/>
		<title>{$card->name} | Sawhat</title>
		<link rel="stylesheet" href="{$root_url}public/css/reset.css" />
		<link rel="stylesheet" href="{$root_url}lib/bootstrap/bootstrap.min.css" />
		<link rel="stylesheet" href="{$root_url}lib/bootstrap/jasny-bootstrap.min.css" />
		<link rel="stylesheet" href="{$current_app_url}public/css/sawhat.css" />
		<script type="text/javascript" src="{$root_url}lib/jquery/jquery.js"></script>
		<script type="text/javascript" src="{$root_url}public/js/json2.js"></script>
		<script type="text/javascript" src="{$root_url}lib/bootstrap/jasny-bootstrap.min.js"></script>
	</head>
	<body>
		{include file=$content}
	</body>
</html>