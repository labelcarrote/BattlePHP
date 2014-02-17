{if $card}
<div class="content">
    <section class="size1of1">
    {include file="element.card.tpl"}
    </section>
    <div class="margin">
        <a class="btn btn-large btn-block" href="{$root_url}sawhat/{$card->name}/edit">Edit</a>
    </div>
    <br>
</div>
{else}
<div class="content line">
    {foreach from=$cards item=card}
    <section class="unit size1of3">
        {include file="element.card.tpl"}
    </section>
    {/foreach}
</div>
{/if}