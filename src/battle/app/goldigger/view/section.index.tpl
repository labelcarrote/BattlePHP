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
			The current gold rate from quandl.com is : <br><br>

			- Client Side (direct call to quantl json api) :<br><br>
			Gold Rate is [<span class="gold-rate">loading rate</span>] (<span class="gold-rate-last-update">loading...</span>) <span class="ping"></span>.<br><br>

			- [WIP] Server Side (from php server side, which call quantl json api and cached it?) :<br><br>
			Gold Rate is [<span class="">RATE</span>] (<span class="">DATETIME</span>).
			</p>
		</div>
		<script type="text/javascript" src="{$batl_root_url}lib/jquery/jquery.js"></script>
		<script type="text/javascript" src="{$batl_current_app_url}public/js/goldigger.js"></script>
	</body>
</html>