<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width"/> 
		<meta name="description" content="Label Carrote fluent wiki. Powered by Sawhat." />
		<meta name="author" content="label carrote"/>
		<meta name="robots" content="noindex, nofollow"/>
		<title>{if isset($card->display_name)}{$card->display_name} | {/if}{ConfigurationSawhat::SITE_TITLE}</title>
		<link rel="stylesheet" href="{$root_url}public/css/reset.css" />
		<link rel="stylesheet" href="{$root_url}lib/bootstrap/bootstrap.min.css" />
		<link rel="stylesheet" href="{$root_url}lib/bootstrap/jasny-bootstrap.min.css" />
		<link rel="stylesheet" href="{$root_url}lib/prism/prism_okaida.css" />
		<link rel="stylesheet" href="{$current_app_url}public/css/sawhat_v2.css" />
		<script type="text/javascript" src="{$root_url}lib/jquery/jquery.js"></script>
		<script type="text/javascript" src="{$root_url}lib/bootstrap/jasny-bootstrap.min.js"></script>
		<script type="text/javascript" src="{$root_url}lib/prism/prism.js"></script>
	</head>
	<body>
		<div><!-- id="super_wrapper" TODO : FIX Scrolling -->
			{include file=$content}
		</div>
		<script type="text/javascript" src="{$current_app_url}public/js/sawhat.js"></script>
	</body>
</html>