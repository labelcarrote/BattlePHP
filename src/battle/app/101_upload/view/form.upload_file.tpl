{************************************************

 Form Upload 

 in :
 - $upload_form
 - $upload_form_errors
 - $dat_file_url
 - ($batl_root_url)

************************************************}
<style type="text/css">
#dat_file_container{
	position: relative;
	display: inline-block;
	text-align: center;
	max-width: 100%;
}
#dat_file_container > input {
    position: absolute;
    top: 0px;
    right: 0px;
    margin: 0px;
    opacity: 0;
    font-size: 23px;
    height: 100%;
    width: 100%;
    direction: ltr;
    cursor: pointer;
}
.uploadprogress{
	background-color: yellow;
	position:absolute;
	top: 0;
	/*bottom: 0;*/
}
</style>
<form class="upload_form"
	id="upload_form" 
	method="POST" 
	enctype="multipart/form-data"
	action="{$upload_form->action}">
	<div id="dat_file_container" >
		<img class="constrained" src="{$dat_file_url}">
		<input name="dat_file" id="dat_file" type="file" accept='image/jpeg,image/gif,image/png' />
		<div class="uploadprogress hidden">
			<div class="bar"></div>
			<p></p>
		</div>
	</div>
	{if isset($upload_form_errors)}
	<div class="">
	{foreach from=$upload_form_errors item="e"}
	<span class="help-block">{$e}</span>
	{/foreach}
	</div>
	{/if}
</form>