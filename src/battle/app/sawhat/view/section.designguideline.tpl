{************************************************

 Design Guideline Section
 
 in :
 - $card : Card
 
************************************************}
<div class="content" style="background-color: aliceblue">
	<h1>Design Guide</h1>

	<div class="banner">
		<h2>Colors</h2>
	</div>
	<p>- Colors from theme : 
	</p>
	<div class="banner">
		<h2>Typography</h2>
	</div>
	<p class="paddingtop">
	Paragraph.
	Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
	tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
	quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
	consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
	cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
	proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
	</p>
	<ul>
		<li>List Element 1 : Pear</li>
		<li>List Element 2 : Banana</li>
		<li>List Element 3 : Mango</li>
	</ul>
	<h1>Titre en h1.</h1>
	<h2>Titre en h2.</h2>
	<h3>Titre en h3.</h3>
	<h4>Titre en h4.</h4>
	<h5>Titre en h5.</h5>
	<h6>Titre en h6.</h6>
	<div class="banner">
		<h2>Icons</h2>
	</div>
	<div class="paddingtopbottom">
		<span class="fa fa-home"></span>
		<span class="fa fa-search"></span>
		<span class="fa fa-lock"></span>
		<span class="fa fa-pencil"></span>
		<span class="fa fa-star"></span>
		<span class="fa fa-star-o"></span>
		<span class="fa fa-circle-thin"></span>
		<span class="fa fa-caret-right"></span>
		<span class="fa fa-angle-right"></span>
		<span class="fa fa-sign-out fa-fw"></span>
		<!-- 
		<span class="fa fa-circle-thin fa-stack-2x"></span>
		<span class="fa fa-star fa-stack-1x"></span> 
		-->
		<br>
		<span class="lighter_text fa fa-home"></span>
		<span class="lighter_text fa fa-search"></span>
		<span class="lighter_text fa fa-lock"></span>
		<span class="lighter_text fa fa-pencil"></span>
		<span class="lighter_text fa fa-star"></span>
		<span class="lighter_text fa fa-star-o"></span>
		<span class="lighter_text fa fa-circle-thin"></span>
		<span class="lighter_text fa fa-caret-right"></span>
		<span class="lighter_text fa fa-angle-right"></span>
		<span class="lighter_text fa fa-sign-out fa-fw"></span>
		<br>
		<span class="darker_text fa fa-home"></span>
		<span class="darker_text fa fa-search"></span>
		<span class="darker_text fa fa-lock"></span>
		<span class="darker_text fa fa-pencil"></span>
		<span class="darker_text fa fa-star"></span>
		<span class="darker_text fa fa-star-o"></span>
		<span class="darker_text fa fa-circle-thin"></span>
		<span class="darker_text fa fa-caret-right"></span>
		<span class="darker_text fa fa-angle-right"></span>
		<span class="darker_text fa fa-sign-out fa-fw"></span>
	</div>
	
	<div class="banner">
		<h2>Buttons</h2>
	</div>
	<div class="paddingtop">
	<button>A Simple Button</button>
	</div>
	<div class="paddingtop">
		<a class="btn-sawhat-default" href="#">A .btn-sawhat-default Link...</a>
	</div>
	<div class="paddingtop">
		<button class="btn-sawhat-default" href="#">A .btn-sawhat-default Button...</button>
	</div>
	<div class="paddingtopbottom">
	- File Upload Button : form.upload_file_button.tpl<br>
	{include file="form/form.upload_file_button.tpl"}
	</div>

	<div class="banner">
		<h2>Other Components</h2>
	</div>
	<div class="paddingtop">
	- search form : form/form.search.tpl<br>
	{include file="form/form.search.tpl"}
	</div>
	<div class="paddingtop">
	- theme switcher form : form/form.theme_switcher.tpl<br>
	{include file="form/form.theme_switcher.tpl"}
	</div>
</div>