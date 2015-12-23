{************************************************

 Footer Element 
 
 in :
 - $color_schemes
 
************************************************}
<footer>
	<div class="content auto_clear">
		<div class="left">
			<span class="fa fa-caret-right"></span>&nbsp;<a href="{$batl_current_app_virtual_url}all_cards">See all cards</a>
			<br>
			<span class="fa fa-caret-right"></span>&nbsp;<a href="{$batl_current_app_virtual_url}starred">See starred cards</a>
			<br>
			<span class="fa fa-caret-right"></span>&nbsp;<a href="{$batl_current_app_virtual_url}help">Help</a>
		</div>
		<div class="right">
			<div class="marginbottom">
				{include file="form/form.search.tpl"}
			</div>
			{if count($color_schemes) > 1}
			<form method="POST" class="form-inline">
				<div class="form-group align_right">
					<label for="style_changer" class="control-label">Color theme</label>
					<select class="form-control" id="style_changer" name="style_changer">
						{foreach from=$color_schemes item=color_scheme}
							<option value="{$color_scheme->name}" {if $color_scheme->is_default}selected="selected"{/if}>{$color_scheme->name}</option>
						{/foreach}
					</select>
				</div>
			</form>
			{/if}
		</div>
	</div>
</footer>