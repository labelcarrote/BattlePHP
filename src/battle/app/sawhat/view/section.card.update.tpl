{************************************************

 Update Card Section 
 
 in :
 - $card : Card
 
************************************************}
<div class="content">
	{include file="element.header.tpl"}
	<section class="white" {if $card->exists}id="{$card->name}"{/if}>
		{if $card->exists}<style>{$card->style_definition}</style>{/if}
		<legend class="banner">
			<h1 class="{if $card->is_light}lighter_text{else}darker_text{/if}">
				<strong>{if $card->exists}Edit{else}Create{/if}</strong> <a href="{$batl_current_app_virtual_url}{$card->name}" class="{if $card->is_light}white_text{else}black_text{/if}">{$card->display_name}</a>
			</h1>
		</legend>
		<!-- TEXT EDITOR -->
		<style>
		.toolbar{
			float: right;
			width: 250px;
			min-height: 50px;
			padding: 12px;
			background-color: #f0f0f0;
		}
		.editor-container{
			background-color: #f0f0f0;/* pink;*/
			margin-right: 250px;
		}
		</style>
		<div>	
			<div class="toolbar">	
				<div>
					<a href="#" class="btn-sawhat-default btn_save_card">Save</a>
				</div>
				<div>
					<a class="btn-sawhat-default" href="#">
						Edit History
					</a>
				</div>
				<div>
					<a class="btn-sawhat-default" href="{$batl_current_app_virtual_url}{$card->name}">View</a>
				</div>
				<div>
				 	{include file="form.upload_file_button.tpl"}
				</div>
				<hr>
				<div>
					<a class="btn-sawhat-default" href="#">
						New Card [WIP Modal?]
					</a>
				</div>
				<!-- <div>
					<a class="btn-sawhat-default">
						Find Card
					</a>
				</div> -->
			</div>
			<div class="editor-container" >
				<form id="card_edit_form" method="POST" enctype="multipart/form-data" action="{$batl_current_app_virtual_url}">
					<input type="hidden" name="card_name" value="{$card->name}"/>
						<div class="form-inline">
							<div class="padding form-group">
								<label for="card_color" class="control-label">Color</label>
								<input type="text" class="form-control" id="card_color" name="card_color" placeholder="ex: #FF9900" value="{if $card->exists}{$card->color}{else}#ff9900{/if}" pattern="^#[a-fA-F0-9]{literal}{{/literal}3,6{literal}}{/literal}$">
								<div class="color_picker hidden auto_clear">
									{foreach from=$palette item=color}
										<div class="color_picker_item" data-color="#{$color['color']}" title="{$color['name']}"></div>
									{/foreach}
								</div>
							</div>
							<div class="padding form-group">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="card_is_private" {if $card->is_private}checked{/if}> Is Private ?
									</label>
								</div>
							</div>
							<br>
						</div>
						<div >
							<div id="editor-mask">
								<div id="editor-container">
								<pre id="editor">{$card->text_code}</pre>
								</div>
							</div>
						</div>
				</form>
			</div>
	
			<!-- SUBMIT -->
			<div class="padding darker">
				<span id="editor_console" class="paddingleft"></span>
			</div>

	
			<!-- FILES -->
			<div class="padding">
				<h2>Files</h2>
				<div id="files" class="files auto_clear">
					{include file="element.file_set.tpl" }
				</div>
				<!--
				<div class="line margintopbottom">
					
					 <div class="unit size1of5">
						<form id="addfileform" class="">
							<input type="hidden" name="name" value="{$card->name}">
							<div class="fileinput fileinput-new" data-provides="fileinput">
								<span class="btn btn-default btn-file">
									<span class="fileinput-new">Attach / Upload File</span>
									<span class="fileinput-exists">Attach / Upload File</span>
									<input name="file" id="file" type="file" data-card-name="{$card->name}">
								</span>
							</div>
						</form>   
					</div> 
					<div class="unit size4of5">
						<div class="uploadprogress">
						<div class="progress-bar"></div>
						<p>Progress</p>
						</div>
					</div>
				</div>
				-->
			</div>
		</div>

		<!-- HISTORY -->
		<div class="padding marginbottom darker">
			<h2>History</h2>
			<ul>
				{foreach from=$card->history item=old_version}
				<li class="auto_clear">
					<span class="left block">{$old_version->name}</span>
					<span class="image_size left block">{$old_version->human_readable_size}</span>
					<a href="#" 
						class="load_card_as_current marginleftright" 
						data-card-url="{$batl_current_app_virtual_url}api/?m=get_card&amp;name={$card->name}&amp;version={$old_version->name}" 
						data-card-name="{$card->name}" 
						data-card-version="{$old_version->name}">
						Set as current card
					</a>
				</li>
				{/foreach}
			</ul>
		</div>
	</section>
</div>
{include file="element.footer.tpl"}
<script src="{$batl_root_url}lib/ace/ace.js" type="text/javascript" charset="utf-8"></script>