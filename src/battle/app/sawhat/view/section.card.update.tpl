<div class="content line">
	<section class="unit size1of1 white">
		<form id="card_edit_form" class="form-inline" method="POST" enctype="multipart/form-data" action="{$current_app_virtual_url}">
			<input type="hidden" name="name" value="{$card->name}"/>
			<fieldset>
				<legend class="banner" {if $card->exists}style="background-color:{$card->color}"{/if}>
					<h1 class="lighter_text">
						<i>{if $card->exists}Update{else}Create{/if}</i> <a href="{$current_app_virtual_url}{$card->name}" class="white_text">{$card->display_name}</a>
					</h1>
				</legend>
	
				<!--  Color for headers, links and horizontal bar  -->
				<span class="help-inline">Color : </span>
				<input type="text"class="input-large" name="color" placeholder="ex: #FF9900" value="{if $card->exists}{$card->color}{/if}">
				<div class="color_picker hidden">
					<div class="color_picker_item" data-color="#000"></div>
					<div class="color_picker_item" data-color="#fff"></div>
					<div class="color_picker_item" data-color="#2b2b2b"></div>
					<div class="color_picker_item" data-color="#f90"></div>
					<div class="color_picker_item" data-color="#ff6523"></div>
					<div class="color_picker_item" data-color="#5bc0de"></div>
					<br>
					<div class="color_picker_item" data-color="#7cfc00"></div>
					<div class="color_picker_item" data-color="#1497a2"></div>
					<div class="color_picker_item" data-color="#ffd801"></div>
					<div class="color_picker_item" data-color="#ff2b08"></div>
					<div class="color_picker_item" data-color="#00ccff"></div>
					<div class="color_picker_item" data-color="#ff69a6"></div>
					<div class="clearer"></div>
				</div>
			  
			  <!-- Private Card ? -->
			  <label class="checkbox">
				 <input type="checkbox" name="is_private" {if $card->is_private}checked{/if}> Is Private ?
			  </label>
	
			  <!-- TEXT -->
			  <div id="editor_container" class="margintop">
				 <pre id="editor">{$card->text_code}</pre>
			  </div>
			  <!-- 
			  <textarea class="hidden" type="text" name="card"></textarea>
			   -->
		   </fieldset>
	    </form>
	
	    <!-- SUBMIT -->
	    <div class="padding darker">
		   <button id="editor_save" class="btn btn-primary">Save</button>
		   <a class="btn btn-secondary" href="{$current_app_virtual_url}{$card->name}">Cancel</a>
	    </div>
	
	    <!-- FILES -->
	    <div class="border padding darker marginbottom">
		   <div class="line">
			  <div class="unit line size1of5 margintopbottom">
				 <form id="addfileform" class="">
					<input type="hidden" name="name" value="{$card->name}">
					<div class="fileupload fileupload-new" data-provides="fileupload">
					    <span class="btn btn-file">
						   <span class="fileupload-new">Attach / Upload File</span>
						   <span class="fileupload-exists">Attach / Upload File</span>
						   <input name="file" id="file" type="file">
					    </span>
					</div>
				 </form>   
			  </div>
			  <div class="unit size4of5">
				 <div class="uploadprogress margintopbottom">
					<div class="bar"></div>
					<p>Progress</p>
				 </div>
			  </div>
		   </div>
		   <div id="files">
			  <ul>
				 {foreach from=$card->files item=file}
				 <li>@{$file->name} (<a style="color:{$card->color}" href="{$root_url}{$file->fullname}" alt="{$file->name}">see</a>) size : {$file->size}</li>
				 {/foreach}
			  </ul>
		   </div>
	    </div>
	</section>
</div>
<script src="{$root_url}lib/ace/ace.js" type="text/javascript" charset="utf-8"></script>