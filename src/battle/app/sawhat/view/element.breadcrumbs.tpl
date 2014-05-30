{if isset($breadcrumbs)}
	<div class="breadcrumbs">
		<a
			href="{$current_app_virtual_url}"
			{if $breadcrumbs['position'] == 0}
				class="current"
				{if isset($card)}style="color:{$card->color};"{/if}
			{/if}
			title="{ConfigurationSawhat::SITE_TITLE}"
		><span class="fa fa-home"></span></a>
		{if count($breadcrumbs['items']) > 0}
			<span class="fa fa-angle-right"></span>&nbsp;...&nbsp;<span class="fa fa-angle-right"></span>
			{foreach from=$breadcrumbs['items'] item=breadcrumbs_item}
				<a
					href="{$breadcrumbs_item['url']}"
					{if $breadcrumbs_item@iteration == $breadcrumbs['position']}
						class="current"
						{if isset($card)}style="color:{$card->color};"{/if}
					{/if}
				>{$breadcrumbs_item['name']}</a>
				{if $breadcrumbs_item@iteration < count($breadcrumbs['items'])}
					<span class="fa fa-angle-right"></span>
				{/if}
			{/foreach}
		{/if}
	</div>
{/if}