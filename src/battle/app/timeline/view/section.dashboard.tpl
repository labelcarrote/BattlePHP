<section class="">
	{if isset($cigarette_watcher)}
		{include file=$cigarette_watcher->tpl_name}
	{/if}
	{if isset($picture_watcher)}
		{include file=$picture_watcher->tpl_name}
	{/if}
	{if isset($text_watcher)}
		{include file=$text_watcher->tpl_name}
	{/if}
	{if isset($fapbattle_watcher)}
		{include file=$fapbattle_watcher->tpl_name}
	{/if}
</section>