<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="MIRROR:" />
		<meta name="author" content="label carrote"/>
		<meta name="keywords" content="label carrote, battle, jon dalton, touchypunchy, p2b, moustachu, mitch" />
		<title>MIRROR:</title>
		<link rel="stylesheet" href="{$batl_root_url}public/css/reset.css" />
		<link rel="stylesheet" href="{$batl_current_app_url}public/css/mirror.css" />
	</head>
	<body class="whitebg">
		<header>
			<a href="{$batl_root_url}">
				<h1>MIRROR</h1>
			</a>
		</header>
		<form method="GET">
			<select name="app">
				{foreach from=$all_apps item=app}
					<option value="{$app}" {if isset($current_app) && $app == $current_app}selected="selected"{/if}>
						{$app}
					</option>
				{/foreach}
			</select>
			<button type="submit">OK</button>
		</form>
		<section>
			<h2>1. Classes</h2>
			<hr>
			{foreach from=$definitions key=folder item=definition}
				<div class="margintopbottom">
					<h2>{$folder}</h2> 
					<hr>
					<a href="http://yuml.me/diagram/plain/class/{$definition}"><img class="margintopbottom" alt="{$definition}" src="http://yuml.me/diagram/plain/class/{$definition}"/></a>
					<code class="hidden">{$definition}</code>
				</div>
			{/foreach}
		</section>
		<section>
			<h2>2. SQL Tables</h2>
			<hr>
			{foreach from=$sqldefinitions key=folder item=definition}
				<div class="margintopbottom">
					<h2>{$folder}</h2> 
					<hr>
					<a href="http://yuml.me/diagram/plain/class/{$definition}"><img class="margintopbottom" alt="{$definition}" src="http://yuml.me/diagram/plain/class/{$definition}"/></a>
					<code class="hidden">{$definition}</code>
				</div>
			{/foreach}
		</section>
	</body>
</html>