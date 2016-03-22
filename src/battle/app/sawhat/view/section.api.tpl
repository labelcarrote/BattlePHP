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

{**
swagger: '2.0'
info:
  title: Sawhat
  description: Sawhat wiki's json API 
  version: 1.0.0
host: labelcarrote.com
schemes:
  - http
basePath: /battle/sawhat/api
produces:
  - application/json
paths:
  /sawhat/api?m=get_card&name={card_name}:
    get:
      summary: Card
      description: |
        The get_card endpoint returns information about the *Uber* products
        offered at a given location. The response includes the display name
        and other details about each product, and lists the products in the
        proper display order.
      parameters:
        - name: card_name
          in: path
          description: Card name
          required: true
          type: string
          format: string
      tags:
        - Card
      responses:
        '200':
          description: A Card
          schema:
            $ref: '#/definitions/Card'
        default:
          description: Unexpected error
          schema:
            $ref: '#/definitions/Error'
definitions:
  Card:
    type: object
    properties:
      is_private:
        type: boolean
        description: 'Is the card private?'
      is_recursive:
        type: boolean
        description: 'Is the card recursive?'
      color:
        type: string
        description: Display name of product.
      is_light:
        type: boolean
        description: ''
      properties:
        type: string
        description: ''
      last_edit:
        type: string
        description: ''
      name:
        type: string
        description: ''
      display_name:
        type: string
        description: ''
      files:
        type: string
        description: ''
      exists:
        type: boolean
        description: ''
      history:
        type: string
        description: ''
      text_code:
        type: string
        description: ''
      html:
        type: string
        description: ''
  Error:
    type: object
    properties:
      code:
        type: integer
        format: int32
      message:
        type: string
      fields:
        type: string


**}