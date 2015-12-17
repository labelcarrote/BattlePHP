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
	<div id="dat_file_container" >
		<img alt="Click here to change picture" src="{$dat_file->url}">
		<input name="dat_file" id="dat_file" type="file" accept='image/jpeg,image/gif,image/png' />
		<div class="uploadprogress hidden">
			<div class="bar"></div>
			<p></p>
		</div>
	</div>
</form>
