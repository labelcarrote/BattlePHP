<div class="line">
	<div class="event__date unit size1of4 cigarette_border"> 
	{$cigarette->date|date_format:"%D %T"}
	{include file="btn.delete_event.tpl" event=$cigarette}
	</div>
	<div class="event__container unit size3of4">
	Cigarette {$cigarette->excuse}
	</div>
</div>