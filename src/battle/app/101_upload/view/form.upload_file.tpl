{************************************************

 Form Upload 

 in :
 - $upload_form
 - $dat_file : DatFile
 - ($batl_root_url)

************************************************}
<form class="upload_form"
	id="upload_form" 
	method="POST" 
	enctype="multipart/form-data"
	action="{$upload_form->action}">
{if $upload_form->mode === "button" }
	<div class="upload-btn_container">
		<div class="upload-btn_title">Upload File</div>
		<input name="dat_file" id="dat_file" type="file" accept='image/jpeg,image/gif,image/png,text/plain' />
		<div class="uploadprogress hidden">
			<div class="bar"></div>
		</div>
	</div>
{else}
	<div id="dat_file_container" >
		{if $dat_file->extension === "txt"}
		<p>A text file ! :q</p>
		{else}
		<img alt="Click here to change file" src="{$dat_file->url}">
		{/if}
		<input name="dat_file" id="dat_file" type="file" accept='image/jpeg,image/gif,image/png,text/plain' />
		<div class="uploadprogress hidden">
			<div class="bar"></div>
			<p></p>
		</div>
	</div>
{/if}
</form>
