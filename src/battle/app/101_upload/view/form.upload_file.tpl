{************************************************

 Form Upload 
 in :
 - $upload_form
 - $upload_form_errors

************************************************}
<form class="form-horizontal"
	id="upload_form" 
	method="POST" 
	enctype="multipart/form-data"
	action="{$upload_form->action}">
	<div class="form-group nomargin {if isset($upload_form_errors)}has-error{/if}">
		<div class="fileinput fileinput-new noborder" data-provides="fileinput" data-ratio="1">

			<div class="fileinput__menu centered2">
				<span class="help-block">
					- Max file size : {$upload_form->max_file_size_human_readable}<br>
					- Allowed Extensions : 
				</span>
				<span class="btn btn-file btn-default">
					<span class="fileinput-new">btn.choose_picture</span>
					<span class="fileinput-exists">btn.change_picture</span>
					<input name="dat_file" id="dat_file" type="file" accept='image/jpeg,image/gif,image/png' />
				</span>
				<!-- <button class="btn btn-primary marginleft upload_btn"
					value="{$upload_form->submit_action_name}" title="btn.upload">btn.upload</button > -->
				<div class="fileinput-preview" style="width: 100%;">
					<img class="constrained" src="{$batl_root_url}{$upload_form->get_file_url()}">
					<a href="{$batl_root_url}{$upload_form->get_file_url()}">Download File</a>
				</div>
			</div>
		</div>
		{if isset($upload_form_errors)}
		<div class="">
			{foreach from=$upload_form_errors item="e"}
			<span class="help-block">{$e}</span>
			{/foreach}
		</div>
		{/if}
	</div>

	<div class="uploadprogress hidden">
		<div class="progress-bar"></div>
		<p></p>
	</div>
</form>
