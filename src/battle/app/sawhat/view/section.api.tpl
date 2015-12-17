{************************************************

 API Section 
 
************************************************}
<div class="content">
	<section>
		<h1>Sawhat API v0.1</h1>
			<strong>[GET]</strong>
			<ul>
				<li>- Get Card by name : GET <a href="{$batl_current_app_virtual_url}api?m=get_card&amp;name=debug">/sawhat/api?m=get_card&amp;name=debug</a>
					<pre class="code line-numbers  language-javascript"><code>{
	"errors":null,
	"body":{
		"is_private":false,
		"is_recursive":false,
		"color":"#f90",
		"is_light":true,
		"properties":null,
		"last_edit":null,
		"name":"debug",
		"display_name":"debug",
		"files":[],
		"exists":false,
		"history":null,
		"text_code":"",
		"html":"..."
	}
}</code></pre>
				</li>
				<li>- Get Card by name and version : GET <a href="{$batl_current_app_virtual_url}api?m=get_card&amp;name=debug&amp;version=bugs_20151215_1912.txtold">/sawhat/api?m=get_card&amp;name=debug&amp;version=bugs_20151215_1912.txtold"</a>
					<pre class="code line-numbers  language-javascript"><code>{
	"errors":null,
	"body":{
		"is_private":false,
		"is_recursive":false,
		"color":"#f90",
		"is_light":true,
		"properties":null,
		"last_edit":"2015-12-15",
		"name":"debug",
		"display_name":"debug",
		"files":[],
		"exists":false,
		"history":null,
		"text_code":"",
		"html":"..."
	}
}</code></pre>
				<li>- Search Cards (query) : GET <a href="{$batl_current_app_virtual_url}api?m=search&amp;query=carrote">/sawhat/api?m=search&amp;query=carrote</a>
					<pre class="code line-numbers  language-javascript"><code>{
	"errors":null,
	"body":[
		{
			"is_private":false,
			"is_recursive":false,
			"color":"#f90",
			"is_light":true,
			"properties":null,
			"last_edit":"2015-10-12",
			"name":"labelcarrote",
			"display_name":"labelcarrote",
			"files":[
				{
					"name":"13943532f70ed5d711138122825e7d44.png",
					"fullname":"app\/sawhat\/storage\/labelcarrote\/13943532f70ed5d711138122825e7d44.png",
					"size":11724,
					"human_readable_size":"11.45 ko",
					"type":"image"
				}
			],
			"exists":true,
			"history":null,
			"text_code":"*some text or markdown*",
			"html":"..."
		}
	]
}</code></pre>
				</li>
			</ul>

			<strong>[POST]</strong>
			<ul>
				<li>- Save Card : POST <a href="{$batl_current_app_virtual_url}api/">/sawhat/api</a>
					<pre class="code line-numbers  language-javascript"><code>{literal}{
	"submit":"save_card",
	"card_name":"bugs",
	"card_color":"#f90",
	"card_is_private":false,
	"card_txt":"- Fix card colors"
}{/literal}</code></pre>
				</li>
				<li>
					- Upload and attach a file to a Card : POST <a href="{$batl_current_app_virtual_url}api/">/sawhat/api</a>
					<pre class="code line-numbers  language-javascript"><code>{literal}{
	"submit":"add_file_to_card",
	"file":"data:image/jpeg;base64,...",
	"file_name":"lolcat.jpg",
	"card_name":"Lolcats"
}{/literal}</code></pre>
				</li>
			</ul>
	</section>
</div>