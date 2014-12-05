<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="(TLT)">
		<meta name="author" content="label carrote">
		<meta name="keywords" content="label carrote, battle, jon dalton, touchypunchy, p2b, moustachu, mitch">
		<title>(TLT)</title>
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
				<div class="unitRight">
					<div class="line">
						<span>since: </span>
						<a class="" 
							href="{$batl_current_app_virtual_url}">
							all time
						</a>
						<a class="" 
							href="{$batl_current_app_virtual_url}?date1={$last_month|date_format:"%m/%d/%Y"}">
							1 month
						</a>
						<a class="" 
							href="{$batl_current_app_virtual_url}?date1={$last_week|date_format:"%m/%d/%Y"}">
							1 week
						</a>
						<a class="" 
							href="{$batl_current_app_virtual_url}?date1={$yesterday|date_format:"%m/%d/%Y"}">
							yesterday
						</a>
						<!-- TOFIX -->
						<!-- <a class="" 
							href="{$batl_current_app_virtual_url}?date1={$now|date_format:"%m/%d/%Y"}">
							today
						</a> -->
					</div>
					<div class="unitRight">
						<span>types: </span>
						<a class="" 
							href="{$batl_current_app_virtual_url}?types=CigaretteSmoked,PictureAdded,TextAdded">
							all
						</a>
						<a class="" 
							href="{$batl_current_app_virtual_url}?types=PictureAdded">
							pictures
						</a>
						<a class="" 
							href="{$batl_current_app_virtual_url}?types=TextAdded">
							texts
						</a>
						<a class="" 
							href="{$batl_current_app_virtual_url}?types=CigaretteSmoked">
							cigarettes
						</a>
					</div>
				</div>
			</div>
		</header>
		<div class="line">
			<div class="unit size1of4">
				{include file="section.dashboard.tpl"}
			</div>
			<div class="unit size3of4">
				{include file="section.time.tpl"}
			</div>
		</div>
	</body>
</html>