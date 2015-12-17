<!DOCTYPE html>
<html>
<head>
	<title>Clock</title>
	<script type="text/javascript">
	function dotime(){
		var clock = document.getElementById("clock"),
			now = new Date();

		clock.innerHTML = getDigitalTime(now.getHours()) 
			+ "<span>h</span>" 
			//+ "<span style='font-family:Lucida Console;'>:</span>" 
			+ getDigitalTime(now.getMinutes())
			+ "<span style='font-size: 62px; padding-left: 6px; padding-right:32px;'>" 
			+ getDigitalTime(now.getSeconds()) 
			+ "</span>";
	}

	function getDigitalTime(digit){
		return (digit < 10) ? "0" + digit : digit;
	}

	window.onload=function(){
		dotime();
		setInterval(dotime,1000);
	}
	</script>
	<style type="text/css">
	body { 
		text-align: center; 
		font-size: 124px;
		font-family: "Courier New";
		position: absolute;
		bottom: 0px;
		right: 0;
		background-color: #2b2b2b;
		color: white;
	}
	</style>
</head>
<body>
	<div id="clock">
	</div>
</body>
</html>