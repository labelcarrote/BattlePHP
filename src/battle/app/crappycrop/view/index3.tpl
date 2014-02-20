<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="user-scalable=0, width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="description" content="CrappyCrop">
		<meta name="author" content="label carrote">
		<meta name="keywords" content="label carrote, battle, jon, potiron, dalton, touchypunchy, p2b, moustachu, mitch">
		<title>CrappyCrop / PROTOCROP</title>
		<link href='http://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="{$root_url}lib/jquery/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="{$root_url}lib/hammer/jquery.hammer-full.min.js"></script>
		<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
		<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.6/jquery.mousewheel.js"></script>
		<style>
		/*html{ 
		    -ms-touch-action: none;
		}*/
		body{
			background-repeat:repeat;
			font: 16px/1.5 'Raleway', Arial, Verdana, sans-serif;
		}
		.left{ float: left; }
		.right{ float: right; }
		.clear{ clear: both; }
		.content{
			max-width: 960px;
			margin: auto;
			text-align: center;
			margin-top: 24px;
		}
		.content img{
			width: 100%;
			height: auto;
		}
		#upload_form a{
			font: 16px/1.5  Arial, Verdana, sans-serif !important;
		}
		.crop_container{
			-ms-touch-action: none;
			touch-action: none;
			width: 100%;
			height: 500px;
			overflow: hidden;
			position: relative;
			border: 1px solid #dedede;
			background-color: #000;
		}
		.to_crop{
			position: absolute;
			height: auto;
		}
		</style>
		{literal}
		<script type="text/javascript" >
			$(function(){
				// options 
				var zoom_scale_step = 0.1;
				
				var container,
					img_to_crop,
					img_real_width,
					img_real_height,
					img_current_width,
					img_current_height,
					y_pos,
					x_pos,
					last_scale = 1,
					zoom_scale_step = 0.1,
					zoom_in_scale_step = 1 - zoom_scale_step,
					zoom_out_scale_step = 1 + zoom_scale_step,
					is_pinching = false;

				function init(){
					container = $('.crop_container');
					img_to_crop = $('img',container);
					
					// get image dimensions
					var img = new Image();
        			img.src = img_to_crop.attr('src');
	        		img.onload = function () {
	          			img_real_width = img.width;
	          			img_real_height = img.height;
	          			img_current_width = img_real_width;
          				img_current_height = img_real_height;
						
						// resize image to fit container
	          			fit_in(img_to_crop);
	          		}

					// hammer events
					$('body').hammer()
					.on('touch drag pinch pinchin pinchout release doubletap','.crop_container', function(event) {
						handle_hammer_gesture(event,img_to_crop);
					});

					// mousewheel event
					$('.crop_container').on('mousewheel', function(event) {
						event.preventDefault();
						handle_mousewheel(event,img_to_crop);
					});

					// fit in / fit out
					$('.fit_in').on('click', function(event){
						fit_in(img_to_crop);
					});
					$('.fit_out').on('click', function(event){
						fit_out(img_to_crop);
					});
				}

				// ---- event handlers

				function handle_hammer_gesture(event,element) {
					event.gesture.preventDefault();
					event.preventDefault();
					switch(event.type) {
						case 'touch':
							x_pos = element.position().left;
							y_pos = element.position().top;
							break;
						case 'drag':
							move(element,event.gesture.deltaX,event.gesture.deltaY);
							break;
						case 'pinch':
							if(!is_pinching){
								x_pos = element.position().left + (img_current_width / 2);
								y_pos = element.position().top + (img_current_height / 2);
							}
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
					zoom(element, (event.deltaY < 0) ? zoom_in_scale_step : zoom_out_scale_step);
				}

				// ---- helpers 

				function fit_in(element){
          			x_pos = (container.width() / 2);
          			y_pos = (container.height() / 2);
					var zoom_scale = (img_real_width / img_real_height > container.width() / container.height())
						? container.width() / img_current_width
						: container.height() / img_current_height;
					zoom(img_to_crop,zoom_scale);
				}

				function fit_out(element){
          			x_pos = (container.width() / 2);
          			y_pos = (container.height() / 2);
					var zoom_scale = (img_real_width / img_real_height > container.width() / container.height()) 
						? container.height() / img_current_height
						: container.width() / img_current_width;
					zoom(img_to_crop,zoom_scale);
				}

				function move(element, deltaX, deltaY){
					if(is_pinching)
						return;
					element.css({
						left: (x_pos + deltaX) + 'px',
						top: (y_pos + deltaY) + 'px' 
					});
				}

				function zoom(element,scale){
					img_current_width = element.width() * scale;
					img_current_height = element.height() * scale;

					if(img_current_height > 64 && img_current_width > 64){
						element.css({
							width: img_current_width,
							left: (x_pos - (img_current_width / 2)) + 'px',
							top: (y_pos - (img_current_height / 2)) + 'px'
						});
						last_scale = scale;
					}
				}

				function pinch_zoom(element,scale){
					if(last_scale !== scale){
						element.css({
							width: img_current_width * scale,
							left: (x_pos - ((img_current_width * scale) / 2)) + 'px',
							top: (y_pos - ((img_current_height * scale) / 2)) + 'px'
						});
						is_pinching = true;
						last_scale = scale;
					}
				}

				function end_gesture(element){
					if(is_pinching){
						img_current_width = element.width();
						img_current_height = element.height();
						is_pinching = false;
					}
					x_pos = element.position().left + (img_current_width / 2);
					y_pos = element.position().top + (img_current_height / 2);
				}

				function debug(txt){
					$("#debug").html(txt);
					console.log(txt);
				}

				// ---- canvas image / upload 

				function getDataURL() {
        			var canvas = document.createElement('canvas'), 
        				ctx = canvas.getContext('2d');

        			var scale = img_current_width / img_real_width;
        			canvas.width = ((container.width() - img_current_width) / scale) + img_real_width;
        			canvas.height = ((container.height() - img_current_height) / scale) + img_real_height;
        			
        			// set background
        			ctx.fillStyle = '#000'; 
					ctx.fillRect (0, 0, canvas.width, canvas.height); 

					// draw image
        			var img = img_to_crop.get(0), // img
	        			sx = 0, // source x
	        			sy = 0, // source y
	        			sw = img_real_width, // source w 
	        			sh = img_real_height, // source h
	        			dx = img_to_crop.position().left / scale, // destination x
	        			dy = img_to_crop.position().top / scale, // destination y
	        			dw = (img_real_width / canvas.width) * img_current_width, // destination w
	        			dh = (img_real_height / canvas.height) * img_current_height // destination h
        			ctx.drawImage(img,sx,sy,sw,sh,dx,dy,sw,sh);

        			return canvas.toDataURL();
      			}

				$(".upload").click(function (){
					var submit_url = $('#upload_form').attr("action"),
						dataString = JSON.stringify({submit: "save", image: getDataURL()});
					$.post(submit_url, {data : dataString}, upload_callback);
				});

				function upload_callback(obj){
					location.reload(true);
				}

				// ---- !!!
				init();
			});
		</script>
		{/literal}
	</head>
	<body>
		<div class="content">
			<div>
				<div class="left"><strong>PROTOCROP</strong></div>
				<div class="right"><a href="#">FORK / DOWNLOAD</a></div>
				<div class="clear"></div>
			</div>
			<form id="upload_form" action="{$current_app_virtual_url}">
				<div>
					<a class="fit_in" href="#">FIT IN</a>
					<a class="fit_out" href="#">FIT OUT</a>
					<a class="upload" href="#">UPLOAD CROP</a>
				</div>
				<div class="crop_container">
					<img class="to_crop" alt="" src="{$current_app_url}public/images/lechat.jpg"/>
				</div>
			</form>
			<div>
				<p>
					<i class="fa fa-caret-down fa-lg"></i>
					&nbsp;&nbsp;&nbsp;&nbsp;RESULT / CROPPED PICTURE&nbsp;&nbsp;&nbsp;&nbsp;
					<i class="fa fa-caret-down fa-lg"></i>
				</p>
			</div>
			<a href="{$current_app_url}public/images/result.jpg">
				<img class="result" alt="" src="{$current_app_url}public/images/result.jpg">
			</a>
			<p id="debug"></p>
			<p>Label Carrote 2014</p>
		</div>
	</body>
</html>