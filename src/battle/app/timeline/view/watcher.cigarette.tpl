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
	<button class="watcher__more_button">+</button>
	<div class="watcher__form hidden">
	{include file="form.cigarette.tpl"}
	</div>
</div>