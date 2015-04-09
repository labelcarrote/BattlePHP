<section class="">
	{if isset($event_views)}
		{foreach from=$event_views item=event}
			<div class="event">
			{$event}
			</div>
		{/foreach}
	{/if}
</section>