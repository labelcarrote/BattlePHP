{************************************************

 Form Upload 
 
 in :
 - $title
 - $dat_file : DatFile
 [for 'form.upload_file.tpl)]
 - $upload_form
 - $upload_form_errors
 - ($batl_root_url)

************************************************}
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<meta name="description" content="\o/">
	<title>{$title} - BattlePHP</title>
	<link rel="stylesheet" href="{$batl_root_url}public/css/reset.css" />
	<link rel="stylesheet" href="{$batl_current_app_url}public/css/101_upload.css" />
</head>
<style type="text/css">
form { 
	max-width: 100%;
	display: inline-block;
	background-color: #272727; 
	padding: 12px;
	margin: 12px;
}
</style>
<body>
	<div class="content">
		{include file="form.upload_file.tpl"}
		<div>
			<a id="dat_file_date_modified_link" title="Follow this link to view source image" href="{$dat_file->url}">
				{$dat_file->date_modified|date_format:"%d/%m/%Y %T"}
			</a>
		</div>
	</div>
	<script type="text/javascript" src="{$batl_root_url}lib/jquery/jquery.js"></script>
	<script type="text/javascript" src="{$batl_current_app_url}public/js/101_upload.js"></script>
</body>
</html>