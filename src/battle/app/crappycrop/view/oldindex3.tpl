<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="user-scalable=0, width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="description" content="CrappyCrop">
		<meta name="author" content="label carrote">
		<meta name="keywords" content="label carrote, battle, jon dalton, touchypunchy, p2b, moustachu, mitch">
		<title>CrappyCrop</title>
		<script type="text/javascript" src="{$root_url}lib/jquery/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="{$root_url}lib/hammer/jquery.hammer-full.min.js"></script>
		<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.6/jquery.mousewheel.js"></script>
		<style>
		html { 
		    -ms-touch-action: none;
		}
		.content{
			width: 960px;
			margin: auto;
		}
		.crop_container{
			width: 300px;
			height: 300px;
			overflow: hidden;
			position: relative;
			border: 1px solid #dedede;
		}
		.to_crop{
			position: absolute;
			height: auto;
		}
		</style>
		{literal}
		<script type="text/javascript" defer>
			$(function(){

				var img_real_width,
					img_real_height,
					img_current_width,
					img_current_height,
					last_scale = 1,
					zoom_scale_step = 0.2,
					zoom_in_scale_step = 1 - zoom_scale_step,
					zoom_out_scale_step = 1 + zoom_scale_step,
					is_pinching = false;

				function init(){
					// resize image to fit container
					var container = $('.crop_container');
					var img_to_crop = $('.to_crop',container);
					
					// get img dimensions
					var img = new Image();
        			img.src = img_to_crop.attr('src');
	        		img.onload = function () {
	          			img_real_width = img.width;
	          			img_real_height = img.height;
	          			img_current_width = img_real_width;
	          			img_current_height = img_current_height;
	          			var zoom_scale = container.width() / img_real_width;
						zoom(img_to_crop,zoom_scale);
	          		}

					// hammer events
					$('body').hammer({prevent_default: true})
					.on('touch drag pinchin pinchout release doubletap','.crop_container', function(event) {
						handle_hammer_gesture(event,$('.to_crop',this));
					});

					// mousewheel event
					$('.crop_container').on('mousewheel', function(event) {
						handle_mousewheel(event,$('.to_crop',this));
					});
				}

				// ---- event handlers

				function handle_hammer_gesture(event,element) {
					event.gesture.preventDefault();
					event.preventDefault();
					switch(event.type) {
						// reset element on start
						case 'touch':
							y_pos = element.position().left;
							x_pos = element.position().top;
							break;
						// move image on drag
						case 'drag':
							move(element,event.gesture.deltaX,event.gesture.deltaY);
							break;
						case 'pinchin':
						case 'pinchout':
							pinch_zoom(element,event.gesture.scale);
							break;
						case 'release':
							end_gesture(element);
							break;
					}
				};

				function handle_mousewheel(event,element){
					//console.log(event.deltaX, event.deltaY, event.deltaFactor);
				    if(event.deltaY < 0)
				    	zoom_in(element);
				    else
				    	zoom_out(element);
				}

				// ---- helpers 

				function move(element, deltaX, deltaY){
					if(is_pinching)
						return;
					element.css('left', (y_pos + deltaX) + 'px');
					element.css('top', (x_pos + deltaY) + 'px');
				}
				function zoom(element,percent){
					img_current_width = element.width() * percent;
					element.css('width', img_current_width);
				}
				function zoom_in(element){
					img_current_width = element.width() * zoom_in_scale_step;
					element.css('width', img_current_width);
					last_scale = zoom_in_scale_step;
				}
				function zoom_out(element){
					img_current_width = element.width() * zoom_out_scale_step;
					element.css('width', img_current_width);
					last_scale = zoom_out_scale_step;
				}
				function pinch_zoom(element,scale){
					if(last_scale !== scale){
						is_pinching = true;
						element.css('width', img_current_width * scale);
						last_scale = scale;
					}
					//debug("pinch_zoom : " + scale + " " + img_current_width + " " + (img_current_width * scale);
				}
				function end_gesture(element){
					if(is_pinching){
						img_current_width = element.width();
						is_pinching = false;
					}
					//debug("end_gesture : " + img_current_width);
				}

				function debug(txt){
					$("#debug").html(txt);
					console.log(txt);
				}

				// !!!
				init();
			});
		</script>
		{/literal}
	</head>
	<body>
		<div class="content">
			<div class="crop_container">
			<img class="to_crop" alt="" src="{$current_app_url}public/images/lardba.jpg"/>
			</div>
			<p id="debug"></p>
			<!-- <form id="upload_form" action="{$current_app_virtual_url}">
				<img class="cropimage" alt="" src="{$current_app_url}public/images/lardba.jpg" cropwidth="300" cropheight="300"/>
				<div class="results">
					<b>X</b>: <span class="cropX"></span>
					<b>Y</b>: <span class="cropY"></span>
					<b>W</b>: <span class="cropW"></span>
					<b>H</b>: <span class="cropH"></span>
				</div>
				<a class="upload" href="#">Upload</a>
			</form>
			<img class="result" alt="" src="{$current_app_url}public/images/result.jpg"> -->
		</div>
	</body>
</html>