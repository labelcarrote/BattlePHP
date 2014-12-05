{************************************************

 Picture Form
 in :
 - $picture_watcher

************************************************}
<form method="POST" action="{$batl_current_app_virtual_url}" enctype="multipart/form-data">
	<input type="hidden" name="type" value="picture"/>
	<input name="picture" type="file" accept='image/gif,image/jpeg,image/png'/>
	<button type="submit" name="submit" value="upload_picture">Upload Picture</button>
</form>