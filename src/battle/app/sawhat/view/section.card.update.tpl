<div class="content">
	<section class="white" {if $card->exists}id="{$card->name}"{/if}>
		{if $card->exists}<style>{$card->style_definition}</style>{/if}
		<form id="card_edit_form" class="form-inline" method="POST" enctype="multipart/form-data" action="{$current_app_virtual_url}">
			<input type="hidden" name="name" value="{$card->name}"/>
			<fieldset>
				<legend class="banner">
					<h1 class="lighter_text">
						<i>{if $card->exists}Update{else}Create{/if}</i> <a href="{$current_app_virtual_url}{$card->name}" class="white_text">{$card->display_name}</a>
					</h1>
				</legend>
				<div class="padding">
					<!--  Color for headers, links and horizontal bar  -->
					<span class="help-inline">Color : </span>
					<input type="text" class="input-large" name="color" placeholder="ex: #FF9900" value="{if $card->exists}{$card->color}{/if}">
					<div class="color_picker hidden">
						<div class="color_picker_item" data-color="#000"></div>
						<div class="color_picker_item" data-color="#fff"></div>
						<div class="color_picker_item" data-color="#2b2b2b"></div>
						<div class="color_picker_item" data-color="#f90"></div>
						<div class="color_picker_item" data-color="#ff6523"></div>
						<div class="color_picker_item" data-color="#5bc0de"></div>
						<br class="clearer">
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
				</div>
				<!-- TEXT -->
				<div id="editor-mask">
					<div id="editor-container">
					<pre id="editor">{$card->text_code}</pre>
					</div>
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
		<div class="border padding darker">
			<h2>Files</h2>
			<div class="line margintopbottom">
				<div class="unit size1of5">
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
					<div class="uploadprogress">
					<div class="bar"></div>
					<p>Progress</p>
					</div>
				</div>
			</div>
			<div id="files" class="files">
				{include file="element.file_set.tpl" }
				<div class="clearer"></div>
		   </div>
		</div>

		<!-- HISTORY -->
		<div class="padding marginbottom">
			<h2>History</h2>
			<ul>
				{foreach from=$card->history item=old_version}
				<li>
					<span class="left block">{$old_version->name}</span>
					<span class="image_size left block">{$old_version->human_readable_size}</span>
					<a href="#" class="load_card_as_current marginleftright" data-card-name="{$card->name}" data-card-version="{$old_version->name}">Set as current card</a>
					<div class="clearer_left"></div>
				</li>
				{/foreach}
			</ul>
		</div>
	</section>
</div>
<script src="{$root_url}lib/ace/ace.js" type="text/javascript" charset="utf-8"></script>