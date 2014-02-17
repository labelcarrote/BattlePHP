<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width"/> 
		<meta name="description" content="A tool to share things between an organisation's members." />
		<meta name="author" content="label carrote"/>
		<meta name="keywords" content="label carrote, battle" />
		<title>SAWHAT {$card->name}</title>
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