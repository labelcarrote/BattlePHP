{************************************************

 Cigarette Form
 in :
 - $cigarette_watcher

************************************************}
<form method="POST" action="{$batl_current_app_virtual_url}">
	<input type="hidden" name="type" value="cigarette"/>
	<input type="text" name="excuse" placeholder="Excuse..." value=""/>
	<button type="submit" name="submit" value="smoked_cigarette">+1 Cigarette</button>
</form>