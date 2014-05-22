{if isset($breadcrumbs)}
	<div class="breadcrumbs">
		<a
			href="{$current_app_virtual_url}"
			{if $breadcrumbs['position'] == 0}
				class="current"
				{if isset($card)}style="color:{$card->color};"{/if}
			{/if}
		>
			{ConfigurationSawhat::SITE_TITLE}
		</a>
		{if count($breadcrumbs['items']) > 0}
			<span class="bigger">&rsaquo;</span>&nbsp;...&nbsp;<span class="bigger">&rsaquo;</span>
			{foreach from=$breadcrumbs['items'] item=breadcrumbs_item}
				<a
					href="{$breadcrumbs_item['url']}"
					{if $breadcrumbs_item@iteration == $breadcrumbs['position']}
						class="current"
						{if isset($card)}style="color:{$card->color};"{/if}
					{/if}
				>
					{$breadcrumbs_item['name']}
				</a>
				{if $breadcrumbs_item@iteration < count($breadcrumbs['items'])}
					<span class="bigger">&rsaquo;</span>
				{/if}
			{/foreach}
		{/if}
	</div>
{/if}