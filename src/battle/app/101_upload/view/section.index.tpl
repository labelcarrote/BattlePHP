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
	<body>
		<div class="content">
			<p>
				I'm the index page from BattlePHP's <strong>{$title}</strong> example, which illustrates a typical upload file form :
			</p>
			{include file="form.upload_file.tpl" }
		</div>
	</body>
</html>