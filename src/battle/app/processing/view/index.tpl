<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="Some processing / canvas samples." />
		<meta name="author" content="label carrote"/>
		<meta name="keywords" content="label carrote, battle, jon dalton, touchypunchy, p2b, moustachu, mitch" />
		<title>Canvas / Processing Sand Box</title>
		<link rel="stylesheet" href="{$batl_root_url}public/css/reset.css" />
		<link rel="stylesheet" href="{$batl_current_app_url}public/css/processing.css" />
		<script src="{$batl_root_url}lib/processing/processing-1.4.1.min.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="content">
				<header>
					<div class="maintitle">
						<a href="{$batl_root_url}">
							<h1>Processing Sand Box</h1>
						</a>
					</div>
				</header>
				<br/>
				<section>
					<canvas data-processing-sources="{$batl_current_app_url}public/pde/random_draw_01.pde"></canvas><br/><br/>
					<canvas data-processing-sources="{$batl_current_app_url}public/pde/carrote_a_sketch.pde"></canvas><br/>
				</section>
			</div>
		</div>
	</body>
</html>