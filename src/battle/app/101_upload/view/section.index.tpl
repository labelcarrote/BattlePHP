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
		<meta name="description" content="\o/">
		<title>{$title} - BattlePHP</title>
		<link rel="stylesheet" href="{$batl_root_url}public/css/reset.css" />
		<link rel="stylesheet" href="{$batl_current_app_url}public/css/101_upload.css" />
		<script type="text/javascript" src="{$batl_root_url}lib/jquery/jquery.js"></script>
		<script type="text/javascript" src="{$batl_current_app_url}public/js/101_upload.js"></script>
	</head>
	<body>
		<div class="content">
			<p class="description">
				I'm the index page from BattlePHP's <strong>{$title}</strong>, a simple upload file form example, in javascript and php.<br>
				<strong>Usage :</strong> Click on the picture or drag-n-drop a picture on it to replace the current one with any <strong>.jpg, .png or .gif</strong> file that weighs less than <strong>&lt; {$upload_form->max_file_size_human_readable}.</strong>
			</p>
			{include file="form.upload_file.tpl" }
			<div style="padding-top: 12px;">
			<a style="color:gray;" href="?mode=zen">mode ZEN</a>
			</div>
		</div>
	</body>
</html>