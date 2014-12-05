{************************************************

 Cigarette Watcher
 in :
 - $cigarette_watcher

************************************************}
<div class="watcher cigarette">
	<p>
		<h2>{$cigarette_watcher->count_since}</h2>
		cigarettes smoked since ...
	</p>
	<br>
	{include file="form.cigarette.tpl"}
	<br>
	<p>
		<strong>[{$cigarette_watcher->type}] watcher</strong>
	</p>
</div>