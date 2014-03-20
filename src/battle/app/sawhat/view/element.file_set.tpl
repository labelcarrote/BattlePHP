<ul>
{foreach from=$files item=file}
    <li>{$file->name} (<a href="{$root_url}{$file->fullname}">see</a>) size : {$file->size}</li>
{/foreach}
</ul>