<div class="line">
	<div class="event__date unit size1of4 picture_border"> 
	{$picture->date|date_format:"%D %T"}
	{include file="btn.delete_event.tpl" event=$picture}
	</div>
	<div class="event__container unit size3of4 ">
	<a href="{$batl_root_url}{$picture->get_path()}">
	<img src="{$batl_root_url}{$picture->get_path()}"><br>
	</a>
	{$picture->width}x{$picture->height}
	</div>
</div>