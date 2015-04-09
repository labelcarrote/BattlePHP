{************************************************

 Text Form
 in :
 - $text_watcher

************************************************}
<form method="POST" action="{$batl_current_app_virtual_url}">
	<input type="hidden" name="type" value="text"/>
	<textarea name="txt" placeholder="Text..."></textarea><br>
	<button type="submit" name="submit" value="add_text">Save</button>
</form>