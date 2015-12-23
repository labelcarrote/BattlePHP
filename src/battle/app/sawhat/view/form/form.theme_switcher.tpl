{************************************************

 Theme Switcher Form 

 in :
 - $color_schemes

************************************************}
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