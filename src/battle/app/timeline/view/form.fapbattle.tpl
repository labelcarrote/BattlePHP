{************************************************

 FAPBattle Form
 in :
 - $fapbattle_watcher

************************************************}
<form method="POST" action="{$batl_current_app_virtual_url}">
	<input type="hidden" name="type" value="fapbattle"/>
	<textarea name="url" placeholder="FAP Battle URL..."></textarea><br>
	<button type="submit" name="submit" value="add_fapbattle">Save</button>
</form>