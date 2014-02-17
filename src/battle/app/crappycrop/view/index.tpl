<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="description" content="CrappyCrop">
		<meta name="author" content="label carrote">
		<meta name="keywords" content="label carrote, battle, jon dalton, touchypunchy, p2b, moustachu, mitch">
		<title>CrappyCrop</title>
		<script type="text/javascript" src="{$root_url}lib/jquery/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/hammer.js/1.0.5/hammer.js"></script>
		<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.6/jquery.mousewheel.js"></script>
		<script type="text/javascript" src="{$root_url}lib/cropbox/jquery.cropbox.js"></script>
		<link rel="stylesheet" href="{$root_url}lib/cropbox/jquery.cropbox.css">
		<style>
		html { 
		    -ms-touch-action: none;
		}
		.content{
			width: 960px;
			margin: auto;
		}
		</style>
		{literal}
		<script type="text/javascript" defer>
		$(function(){
		  	$('.cropimage').each( function () {
				var image = $(this),
					cropwidth = image.attr('cropwidth'),
					cropheight = image.attr('cropheight'),
					results = image.next('.results' ),
					x       = $('.cropX', results),
					y       = $('.cropY', results),
					w       = $('.cropW', results),
					h       = $('.cropH', results);

				image.cropbox({width: cropwidth, height: cropheight, showControls: 'auto'})
				.on('cropbox', function(event, results, img){
					x.text(results.cropX);
					y.text(results.cropY);
					w.text(results.cropW);
					h.text(results.cropH);
				});
			});

			$(".upload").click(function (){
				var submit_url = $('#upload_form').attr("action"),
					crop = $('.cropimage').data('cropbox'),
					dataString = JSON.stringify({submit: "save", image: crop.getDataURL()});
				$.post(submit_url, {data : dataString}, upload_callback);
			});

			function upload_callback(obj){
				location.reload(true);
			}
		});
		</script>
		{/literal}
	</head>
	<body>
		<div class="content">
			<form id="upload_form" action="{$current_app_virtual_url}">
				<img class="cropimage" alt="" src="{$current_app_url}public/images/lardba.jpg" cropwidth="300" cropheight="300"/>
				<div class="results">
					<b>X</b>: <span class="cropX"></span>
					<b>Y</b>: <span class="cropY"></span>
					<b>W</b>: <span class="cropW"></span>
					<b>H</b>: <span class="cropH"></span>
				</div>
				<a class="upload" href="#">Upload</a>
			</form>
			<img class="result" alt="" src="{$current_app_url}public/images/result.jpg">
		</div>
	</body>
</html>