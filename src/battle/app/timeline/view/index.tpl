<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="(TLT)">
		<meta name="author" content="label carrote">
		<meta name="keywords" content="label carrote, battle, jon dalton, touchypunchy, p2b, moustachu, mitch">
		<title>(TLT) {if isset($section)} - {$section}{/if}</title>
		<link rel="stylesheet" href="{$batl_root_url}public/css/reset.css">
		<link rel="stylesheet" href="{$batl_current_app_url}public/css/timeline.css">
		<link rel="stylesheet" href="{$batl_root_url}lib/font-awesome/4.2.0/css/font-awesome.css">
		<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="{$batl_root_url}lib/jquery/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="{$batl_current_app_url}public/js/timeline.js?var={rand(1, 2000)}"></script>
	</head>
	<body>
		<header class="content">
			<div class="line">
				<div class="unit">
				<h1>(TLT)</h1>
				</div>
				<nav class="unitRight">
					<a class="{if isset($section) && $section === "dashboard"}underline{/if}" href="{$batl_current_app_virtual_url}">Dashboard</a>
					<a class="{if isset($section) && $section === "time"}underline{/if}" href="{$batl_current_app_virtual_url}time">Timeline</a>
				</nav>
			</div>
			<div class="line">
				<div class="unitRight">
					<span>since: </span>
					<a class="" 
						href="{$batl_current_app_virtual_url}{if isset($section) && $section === 'dashboard'}{else}time{/if}">
						all time
					</a>
					<a class="" 
						href="{$batl_current_app_virtual_url}{if isset($section) && $section === 'dashboard'}{else}time{/if}?date1={$last_month|date_format:"%m/%d/%Y"}">
						1 month
					</a>
					<a class="" 
						href="{$batl_current_app_virtual_url}{if isset($section) && $section === 'dashboard'}{else}time{/if}?date1={$last_week|date_format:"%m/%d/%Y"}">
						1 week
					</a>
					<a class="" 
						href="{$batl_current_app_virtual_url}{if isset($section) && $section === 'dashboard'}{else}time{/if}?date1={$yesterday|date_format:"%m/%d/%Y"}">
						yesterday
					</a>
					<!-- TOFIX -->
					<!-- <a class="" 
						href="{$batl_current_app_virtual_url}{if isset($section) && $section === 'dashboard'}{else}time{/if}?date1={$now|date_format:"%m/%d/%Y"}">
						today
					</a> -->
				</div>
			</div>
			<div class="line">
				<div class="unitRight">
					<span>types: </span>
					<a class="" 
						href="{$batl_current_app_virtual_url}{if isset($section) && $section === 'dashboard'}{else}time{/if}?types=CigaretteSmoked,PictureAdded,TextAdded">
						all
					</a>
					<a class="" 
						href="{$batl_current_app_virtual_url}{if isset($section) && $section === 'dashboard'}{else}time{/if}?types=PictureAdded">
						pictures
					</a>
					<a class="" 
						href="{$batl_current_app_virtual_url}{if isset($section) && $section === 'dashboard'}{else}time{/if}?types=TextAdded">
						texts
					</a>
					<a class="" 
						href="{$batl_current_app_virtual_url}{if isset($section) && $section === 'dashboard'}{else}time{/if}?types=CigaretteSmoked">
						cigarettes
					</a>
				</div>
			</div>
		</header>
		{if isset($section) && $section === "dashboard"}
			{include file="section.dashboard.tpl"}
		{else}
			{include file="section.time.tpl"}
		{/if}
	</body>
</html>