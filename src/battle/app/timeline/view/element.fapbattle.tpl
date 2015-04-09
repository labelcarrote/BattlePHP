<div class="line">
	<div class="event__date unit size1of4 fapbattle_border"> 
	{$fapbattle->date|date_format:"%D %T"}
	{include file="btn.delete_event.tpl" event=$fapbattle}
	</div>
	<div class="event__container unit size3of4">
	<!-- http://localhost/dev/git/flipapart/src/battle/flipapart/battle/embeded/?battle_id=541 -->
	<iframe src="{$fapbattle->url}" width="100%" height="550">
		<p>Votre navigateur ne supporte pas l'élément iframe</p>
	</iframe>
	
	</div>
</div>
