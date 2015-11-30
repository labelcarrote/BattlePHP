{************************************************

 Form Upload 
 
 in :
 - $title
 [for 'form.upload_file.tpl)]
 - $upload_form
 - $upload_form_errors
 - $dat_file : DatFile
 - ($batl_root_url)

************************************************}
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,user-scalable=no">
	<meta name="description" content="\o/">
	<title>{$title} - BattlePHP</title>
	<link rel="stylesheet" href="{$batl_root_url}public/css/reset.css" />
	<link rel="stylesheet" href="{$batl_current_app_url}public/css/101_upload.css" />
</head>
<style type="text/css">
html, body, a{
	color: gray;
}
form { background-color: transparent; }
#dat_file_container {
	padding: 12px;
	background-color: #272727;
}
</style>
<body>
	<div class="content">
		{include file="form.upload_file.tpl" }
		<a href="{$dat_file->url}">{$dat_file->date_modified|date_format:"%d/%m/%Y %T"}</a>
	</div>
	<script type="text/javascript" src="{$batl_root_url}lib/jquery/jquery.js"></script>
	<script type="text/javascript" src="{$batl_current_app_url}public/js/101_upload.js"></script>
</body>
</html>