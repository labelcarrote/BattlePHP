<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width"/> 
		<meta name="description" content="Label Carrote fluent wiki. Powered by Sawhat." />
		<meta name="author" content="label carrote"/>
		<meta name="robots" content="noindex, nofollow"/>
		<title>{if !isset($cards) && isset($card->display_name)}{$card->display_name} | {elseif isset($cards)} All cards | {/if}{ConfigurationSawhat::SITE_TITLE}</title>
		<link rel="stylesheet" href="{$root_url}public/css/reset.css" />
		<link rel="stylesheet" href="{$root_url}lib/bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" href="{$root_url}lib/bootstrap/css/jasny-bootstrap.min.css" />
		<link rel="stylesheet" href="{$root_url}lib/prism/prism_okaida_mod.css" />
		<link rel="stylesheet" href="{$root_url}lib/font-awesome-4.1.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="{$current_app_url}public/css/color_scheme/{$color_scheme}.css" />
		<link rel="stylesheet" href="{$current_app_url}public/css/sawhat.css" />
		<script type="text/javascript" src="{$root_url}lib/jquery/jquery.js"></script>
		<script type="text/javascript" src="{$root_url}lib/bootstrap/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="{$root_url}lib/bootstrap/js/jasny-bootstrap.min.js"></script>
		<script type="text/javascript" src="{$root_url}lib/prism/prism.js"></script>
	</head>
	<body>
		<div id="super_wrapper"><!--  TODO : FIX Scrolling -->
			{include file=$content}
		</div>
		<script type="text/javascript" src="{$current_app_url}public/js/sawhat.js"></script>
	</body>
</html>