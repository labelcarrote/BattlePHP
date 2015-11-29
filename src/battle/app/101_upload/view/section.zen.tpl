{************************************************

 Form Upload 
 
 in :
 - $title
 [for 'form.upload_file.tpl)]
 - $upload_form
 - $upload_form_errors
 - $dat_file_url
 - ($batl_root_url)

************************************************}
<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="\o/">
		<title>{$title} - BattlePHP</title>
		<link rel="stylesheet" href="{$batl_root_url}public/css/reset.css" />
		<link rel="stylesheet" href="{$batl_root_url}lib/bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" href="{$batl_root_url}lib/bootstrap/css/jasny-bootstrap.min.css" />
		<link rel="stylesheet" href="{$batl_root_url}lib/font-awesome-4.1.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="{$batl_current_app_url}public/css/101_upload.css" />
		<script type="text/javascript" src="{$batl_root_url}lib/jquery/jquery.js"></script>
		<script type="text/javascript" src="{$batl_root_url}lib/bootstrap/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="{$batl_root_url}lib/bootstrap/js/jasny-bootstrap.min.js"></script>
		<script type="text/javascript" src="{$batl_current_app_url}public/js/101_upload.js"></script>
	</head>
	<style type="text/css">
	form { background-color: transparent; }
	#dat_file_container {
		padding: 12px;
		background-color: #272727;
	}
	</style>
	<body>
		<div class="content">
			{include file="form.upload_file.tpl" }
		</div>
	</body>
</html>