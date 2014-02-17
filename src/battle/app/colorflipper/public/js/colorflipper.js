// -----------------------
// Color Flipper v1
// -----------------------

function ColorFlipper() {
	var that = this;
	this.colors = {};
	
	this.init = function(){
		//that.colors[".content"] = ["ffffff","ff9933"];
		// TODO : load from cookie
		// ...
		that.apply();
	}

	this.save = function (selector, color, background_color){
		that.colors[selector] = [color, background_color];
		// TODO : save to cookie
		// ...
		that.apply();
	}
	
	this.remove = function (selector){
		delete that.colors[selector];
		$(selector).css("color",'').css("background-color",'');
		// TODO : save to cookie
		// ...
		that.apply();
	}

	this.apply = function(){
		for(selector in that.colors){
			if (that.colors.hasOwnProperty(selector)){
				$(selector).css("color",that.colors[selector][0]).css("background-color",that.colors[selector][1]);
			}
		}
	} 

	this.to_css = function(){
		var result = "";
		for(selector in that.colors){
			if (that.colors.hasOwnProperty(selector)){
				var color = (that.colors[selector][0]) ? "color: " + that.colors[selector][0] + ";" : "";
				var bgcolor = (that.colors[selector][1]) ? "background-color: " + that.colors[selector][1] + ";": "";
				result += selector 
					+ " { "+ color + bgcolor +" }<br/>";
			}
		}
		return result;
	}
	
	this.to_table = function(){
		var result = "";
		for(selector in that.colors){
			if (that.colors.hasOwnProperty(selector) && that.colors[selector] != null){
				result += "<tr selector-value='" + selector + "'><td>" + selector
				+ "</td><td>" + that.colors[selector][0]
				+ "</td><td>" + that.colors[selector][0]
				+ "</td><td>" + that.colors[selector][1]
				+ "</td><td>" + that.colors[selector][1]
				+ "</td><td><a href='#' class='deleteButton' selector-value='" + selector + "'>delete</a></td><td><a class='editButton' href='#' selector-value='" + selector + "'>edit</a></td></tr>";
			}
		}
		return result;
	}
	
	this.rgb2hex = function (rgb){
		if(typeof rgb == 'undefined' || rgb === null)
			return "";
		
		rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);
		if(rgb === null)
			return "";

		function hex(x) {
			return ("0" + parseInt(x).toString(16)).slice(-2);
		}
		return hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
	}
	
	// Initialize !
	this.init();
}

// ---- jquery ----

$(window).load(function(){
	// load html 
	$("body").append( 
		'<div class="colorFlipper">\
			<div class="colorFlipperShowButton"></div>\
			<div class="colorFlipperMain hidden">\
				<form class="colorFlipperForm ">\
					<label for="selector">BgColor </label><input type="text" class="inputTextColor" name="bgcolor"/>\
					<label for="selector">Color </label><input type="text" class="inputTextColor" name="color" /><br>\
					<label for="selector">Selector </label><input type="text" class="inputTextSelector" name="selector"/>\
					&nbsp;&nbsp;&nbsp;<a class="button green savecolor" href="#">Save</a>\
					<br>\
					<span class="colorFlipperInfoSpan"></span>\
				</form>\
				<hr/>\
				<table id="colorstable">\
					<thead>\
						<tr>\
						<th>Selector</th>\
						<th>Color</th>\
						<th></th>\
						<th>BgColor</th>\
						<th></th>\
						<th></th>\
						<th></th>\
					  </tr>\
					</thead>\
					<tbody>\
					</tbody>\
				</table>\
				<hr/>\
				<div class="colorscss"></div>\
			</div>\
		</div>\
		'
	);

	// Load Minicolors color pickers
	var colorInputs = $('.inputTextColor');
	colorInputs.minicolors();

	var colorFlipper = new ColorFlipper();
	var enable_mouse_move_up = false;
	
	function refresh_display(){
		// refresh table
		$("#colorstable tbody").html(colorFlipper.to_table());
		// refresh "css"
		$(".colorscss").html(colorFlipper.to_css());
	}
	
	// Toggle Color Flipper
	$('.colorFlipperShowButton').click(function(){
		$('.colorFlipperMain').toggleClass("hidden");
		enable_mouse_move_up = !enable_mouse_move_up;
	});
	
	$("body").on("click",".deleteButton",function(e) {
		e.preventDefault();
		var selector = $(this).attr("selector-value");
		colorFlipper.remove(selector);
		refresh_display();
	});
	
	$("body").on("click",".editButton, #colorstable tr", function(e) {
		e.preventDefault();
		var selector = $(this).attr("selector-value");
		$('input[name="selector"]').val(selector);
		$('input[name="color"]').val("#"+colorFlipper.rgb2hex($(selector).css("color")));
		$('input[name="bgcolor"]').val("#"+colorFlipper.rgb2hex($(selector).css("background-color")));
		return false;
	});
	
	var last_selector = "";

	$("input").keyup(function (e) {
		var selector = $('input[name="selector"]').val();
		if(selector.length < 2 && (selector.substring(0,1) === "." || selector.substring(0,1) === "#"))
			return true;
		
		if(e.which === 9) // tab
			return true;
		
		if (e.which == 13){ // enter
			var color = $('input[name="color"]').val();
			var background = $('input[name="bgcolor"]').val();
			colorFlipper.save(selector,color,background);
			refresh_display();
			return false;
		}
		else{
			if($(this).attr("name") === "selector" && last_selector !== selector){
				$(selector).css("outline-width","2px");
				$(selector).css("outline-color","#e6199b");
				$(selector).css("outline-style","solid");
				$(selector).css("outline-offset","-2px");
				$(selector).css("overflow","auto");
				$(selector).css("box-shadow","inset 0 0 0 2px #e6199b");
				$(last_selector).css("outline-width",'');
				$(last_selector).css("outline-color",'');
				$(last_selector).css("outline-style",'');
				$(last_selector).css("overflow",'');
				$(last_selector).css("box-shadow",'');
				last_selector = selector;
			}
		}
		return false;
	});

	$(".savecolor").click(function() {
		var selector = $('input[name="selector"]').val();
  		var color = $('input[name="color"]').val();
		var background = $('input[name="bgcolor"]').val();
		colorFlipper.save(selector,color,background);
		refresh_display();
	});

	function onMouseMove(e) {
		if(!enable_mouse_move_up)
			return;

		var selected_area = document.elementFromPoint(e.clientX,e.clientY);
    	var color_flipper_area = $(".colorFlipper").first()[0];
        if(!jQuery.contains(color_flipper_area, selected_area)){
				$(last_selector).css("outline-width",'');
				$(last_selector).css("outline-color",'');
				$(last_selector).css("outline-style",'');
				$(last_selector).css("overflow",'');
				$(last_selector).css("box-shadow",'');
				$(selected_area).css("outline-width","2px");
				$(selected_area).css("outline-color","#e6199b");
				$(selected_area).css("outline-style","solid");
				$(selected_area).css("outline-offset","-2px");
				$(selected_area).css("overflow","auto");
				$(selected_area).css("box-shadow","inset 0 0 0 2px #e6199b");
			last_selector = selected_area;
			$(".colorFlipperForm .colorFlipperInfoSpan").html("element="+selected_area.tagName.toLowerCase() + ", class=" + selected_area.className);
		}
    }

	function onMouseUp(e) {
		if(enable_mouse_move_up){
        	var selected_area = document.elementFromPoint(e.clientX,e.clientY);
        	var color_flipper_area = $(".colorFlipper").first();
        	if(!jQuery.contains(color_flipper_area[0], selected_area)){
        		var selector = (selected_area.className ) 
        		? "." + selected_area.className
        		: selected_area.tagName.toLowerCase();

				$('input[name="selector"]').val(selector);
			}
		}
    }
	document.addEventListener("mousemove", onMouseMove, false);
	document.addEventListener("mouseup", onMouseUp, false);
});