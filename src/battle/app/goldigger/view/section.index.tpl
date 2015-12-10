{************************************************

 Form Upload 
 
 in :
 - $title
 - $goldrate : array ["rate" => 1024, "last-update" : ...]

************************************************}
<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="Get the current gold rate from quandl.com">
		<title>{$title} - BattlePHP</title>
		<link rel="stylesheet" href="{$batl_root_url}public/css/reset.css" />
		<link rel="stylesheet" href="{$batl_current_app_url}public/css/goldigger.css" />
	</head>
	<body>
		<div class="content">
			<p>
			The current Gold Rate (from quandl.com json API) is : <br><br>

			- Client Side (direct js call to quandl json API) :<br><br>
			<span class="gold-rate">loading rate</span> (<span class="gold-rate-last-update">loading...</span>) <span class="ping"></span><br><br>

			- Server Side (from php server side, which call quandl json API and cache it?) :<br><br>
			<span class="">{$rate['rate']}</span> (<span class="">{$rate['last_update']}</span>) <span>- from cache</span>
			</p>
			<br><br>
			<p>
				<strong>Goldigger JSON API :</strong><br><br>
				<a href="{$batl_current_app_virtual_url}api/rate">GET /api/rate</a> : get current rate from cache as json<br><br>
				<a href="{$batl_current_app_virtual_url}api/refresh_rate">GET /api/refresh_rate</a> : refresh rate and update cache
			</p>
		</div>
		<script type="text/javascript" src="{$batl_root_url}lib/jquery/jquery.js"></script>
		<script type="text/javascript" src="{$batl_current_app_url}public/js/goldigger.js"></script>
	</body>
</html>