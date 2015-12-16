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
	action="{$upload_form->action}"
	data-card-name="{$upload_form->card_name}">
	<div class="upload-btn_container">
		<div class="upload-btn_title">Upload File</div>
		<input name="dat_file" id="dat_file" type="file" accept='image/jpeg,image/gif,image/png,application/zip' />
		<div class="uploadprogress hidden">
			<div class="bar"></div>
		</div>
	</div>
</form>
