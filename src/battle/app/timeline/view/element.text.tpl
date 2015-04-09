<div class="line">
	<div class="event__date unit size1of4 text_border"> 
	{$text->date|date_format:"%D %T"}
	{include file="btn.delete_event.tpl" event=$text}
	</div>
	<div class="event__container unit size3of4">
	<!-- {$text->txt}<br> -->
	{$text->html}
	</div>
</div>