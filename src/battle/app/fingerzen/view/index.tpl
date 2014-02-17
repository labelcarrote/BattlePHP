<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="Some finger input fun." />
		<meta name="author" content="label carrote"/>
		<meta name="keywords" content="label carrote, battle, jon dalton, touchypunchy, p2b, moustachu, mitch" />
		<title>FINGER ZEN</title>
		<link rel="stylesheet" href="{$root_url}public/css/reset.css" />
		<link rel="stylesheet" href="{$current_app_url}public/css/fingerzen.css" />
		<script type="text/javascript" src="{$root_url}lib/jquery/jquery.js"></script>
		<script type="text/javascript" src="{$current_app_url}public/js/fingerzen.js"></script>
	</head>
	<body>
		<header>
      <div class="content line">
    			<div class="unit maintitle">
    				<a href="{$root_url}">
    					<h1>FINGER ZEN</h1>
    				</a>
    			</div>
            </div>
		</header>
        <section>
        	<!-- Note : To display the soft keyboard for canvas on mobile, just add : contenteditable="true" -->
			<canvas contenteditable="true" id="zen"></canvas>
			<canvas contenteditable="true" id="zen1"></canvas>
			<canvas contenteditable="true" id="zen2"></canvas>
        </section>
	</body>
</html>