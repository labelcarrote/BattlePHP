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
	<title>{$title} - BattlePHP</title>
	{include file="meta.open_graph.tpl"}
	<link rel="stylesheet" href="{$batl_root_url}public/css/reset.css" />
	<link rel="stylesheet" href="{$batl_current_app_url}public/css/101_upload.css" />
</head>
<body>
	<style type="text/css">
	.upload_form{
		padding: 0;
		margin-bottom: 12px;
	}
	</style>
	<div class="content">
		{include file="form.upload_file.tpl"}
		<div id="dat_file_container" >
			{if $dat_file->extension === "txt"}
			<p>A text file ! :q</p>
			{else}
			<div class="image_container">
				<img src="{$dat_file->url}">
			</div>
			{/if}
		</div>
		<footer class="footer">
			<a id="dat_file_date_modified_link" title="Follow this link to view source file" href="{$dat_file->url}">
				{$dat_file->date_modified|date_format:"%d/%m/%Y %T"}
			</a>
		</footer>
	</div>
	<script type="text/javascript" src="{$batl_root_url}lib/jquery/jquery.js"></script>
	<script type="text/javascript" src="{$batl_current_app_url}public/js/101_upload.js"></script>
</body>
</html>