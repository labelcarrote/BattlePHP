"use strict";
// -----------------------
// Sawhat.js
// -----------------------
if(typeof ace !== 'undefined'){
	var editor = ace.edit("editor");
	editor.getSession().setUseWrapMode(true);
	editor.setShowPrintMargin(false);
	editor.resize();
	//editor.setTheme("ace/theme/monokai");
	//editor.getSession().setMode("ace/mode/javascript");
}

$(document).ready(function(){
	// BANNER AUTO TEXT COLOR //
	/* @todo
	 * set as prototype
	 */
	$('.banner').each(function(){
		var color = $(this).css('background-color');
		var rgb = color.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		// calculate luminance
		// from http://stackoverflow.com/questions/596216/formula-to-determine-brightness-of-rgb-color
		var r = parseInt(rgb[1]);
		var g = parseInt(rgb[2]);
		var b = parseInt(rgb[3]);
		var luminance = (r+r+b+g+g+g)/6
		//alert(luminance);
		if(luminance > 190){
			$(this).find('.lighter_text').removeClass('lighter_text').addClass('darker_text');
			$(this).find('.white_text').removeClass('white_text').addClass('black_text');
		}
	});
	
	// COLOR PICKER //
	/* @todo
	 * set as prototype
	 */
	$('input[name="color"]').each(function(){
		var related_color_picker = $(this).next('.color_picker');
		$(this).on({
			'focus' : function(){
				related_color_picker.slideDown(200);
			},
			'blur' : function(){
				related_color_picker.delay(100).slideUp(200);
			}
		});
	});
	$('.color_picker').each(function(){
		var related_input = $(this).prev('input[name="color"]');
		var position = related_input.position();
		var self = $(this);
		$(this).css({
			'left' : position.left+'px',
			'top' : (position.top+related_input.outerHeight()-3)+'px'
		});
		$(this).find('.color_picker_item').each(function(){
			var color = $(this).attr('data-color');
			$(this).css('background-color',color);
			$(this).on('click',function(){
				related_input.val(color);
				self.slideUp(200);
			});
		});
	});
	
	// IMAGE PREVIEW //
	$('.files').on('click','.image_link',function(e){
		e.preventDefault();
		$(this).closest('.files').find('.image_preview img').attr('src',$(this).attr('href')).focus();
		$(this).closest('.files').find('.image_preview').fadeIn(200);
	});
	$('.files').on('click','.image_preview',function(){
		$(this).fadeOut(200);
	});
})

$(window).load(function(){
	function send_formdatawithupload(formData){
		var xhr = new XMLHttpRequest();
		var submit_url = $('#card_edit_form').attr("action") + "api";
		xhr.open("POST",submit_url,true);
		xhr.upload.onprogress = function(event){
			var percentage = Math.floor(event.loaded / event.total * 100);
			if(percentage === 100)
				$(".uploadprogress p").html( "Completing upload...");
			else
				$(".uploadprogress p").html(percentage + "%");
			$(".uploadprogress .bar").css("width", percentage + "%")
		}
		xhr.onload = function(oEvent){
			var result = "";
		    if (xhr.status != 200){
		    	$(".uploadprogress p").html("Error " + xhr.status + " occurred uploading your file.");
		    }else{
		    	var ajaxresult = JSON.parse(this.response);
		    	if(ajaxresult.errors === null){
		    		$(".uploadprogress p").html("Completed!");
		    		$("#files").html(ajaxresult.body);
		    	}
		    	else 
		    		$(".uploadprogress p").html(ajaxresult.errors);
		    }
		    $(".uploadprogress .bar").css("width", percentage + "%")
		}
		xhr.send(formData);
	}

	// addfile forms (not working in wp7)
	$("#file").change(function () {
		var data = new FormData(document.getElementById("addfileform"));
		data.append("submit", "addfile");
	 	send_formdatawithupload(data);
	});

	// Card Edit Form Submission
	$('#editor_save').click(function(e){
		$('<input />').attr('type', 'hidden')
			.attr('name', 'card')
			.attr('value', editor.getSession().getValue())
			.appendTo('#card_edit_form');
		$('<input />').attr('type', 'hidden')
		    .attr('name', "submit")
		    .attr('value', "save")
		    .appendTo('#card_edit_form');

		// ajax post
		var submit_url = $('#card_edit_form').attr("action");
		$.ajax({
			url: submit_url,
			type: 'post',
			dataType: 'json',
			data: $("#card_edit_form").serialize(),
			success: function(data) {
				if(data.is_saved !== false){
					document.location.href = data.return_url;
				}
			}
		});
	});

	// Card Edit : Set As Current
	$('body').on('click','.load_card_as_current',function(e){
		//card to load 
		var element = $(this);
		var card_name = $(this).attr("data-card-name");
		var card_version = $(this).attr("data-card-version");
		$.ajax({
			url: "as_code?card_version="+card_version,
			type: 'get',
			dataType: 'json',
			success: function(data) {
				var editor = ace.edit("editor");
				editor.setValue(data.body);
			}
		});
	});

	// Load Card Dynamically
	$('body').on('click','.load_card',function(e){
		//card to load 
		var card_action = $(this).attr("data-action");
		var element = $(this);
		if(card_action === "load"){
			var card_name = $(this).attr("data-card-name");
			$.ajax({
				url: card_name+"/as_html",
				type: 'get',
				dataType: 'json',
				success: function(data) {
					element.after(data.body);
					element.attr("data-action","unload")
					element.text("( unload )")
				}
			});
		}
		else{
			$(this).next().remove();
			element.attr("data-action","load");
			element.text("( load )")
		}
	});

	// Toggle Content Width
	$('body').on('click','#toggle_width',function(e){
		$('.content').toggleClass("width_constraint");
	});

});