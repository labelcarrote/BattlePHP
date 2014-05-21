"use strict";
// -----------------------
// Sawhat.js
// -----------------------
if(typeof ace !== 'undefined'){
	var editor = ace.edit("editor");
	editor.getSession().setUseWrapMode(true);
	editor.setShowPrintMargin(false);
	editor.setOptions({
		minLines: 12,
        maxLines: Infinity
    });
    editor.setAutoScrollEditorIntoView();
	editor.resize();
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
				var position = $(this).position();
				related_color_picker.css({
					'left' : position.left+'px',
					'top' : (position.top+$(this).outerHeight()-3)+'px'
				});
				related_color_picker.slideDown(200);
			},
			'blur' : function(){
				related_color_picker.delay(100).slideUp(200);
			}
		});
	});
	$('.color_picker').each(function(){
		var related_input = $(this).prev('input[name="color"]');
		var self = $(this);
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
	$(document).on('click','.files .image_link',function(e){
		e.preventDefault();
		var element = $(this).closest('.files');
		element.find('.image_preview img').attr('src',$(this).attr('href')).focus();
		element.find('.image_preview').fadeIn(200);
	});
	$(document).on('click','.files .image_preview',function(){
		$(this).fadeOut(200);
	});
	
	// ANCHOR LINK SMOOTH SCROLL
	$('a[href^=#]').on('click',function(e) {
		e.preventDefault();
		var target = $(this.hash);
		if(this.hash !== ''){
			// check if id=hash exists or try name=hash instead
			target = target.length ? target : $('[name='+this.hash.slice(1)+']');
			if(target.length){
				$('html,body').animate({
					scrollTop: target.offset().top 
				}, 500);
			}
		} else {
			$('html,body').animate({
				scrollTop: 0
			}, 500);
		}
		
		return false;
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
			var percentage = 0;
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
	if(typeof ace !== 'undefined'){
		var editor = ace.edit("editor");
		editor.commands.addCommand({
		    name: 'myCommand',
		    bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
		    exec: function(editor) {
		    	save_card($("#editor_save"));
		    },
		    readOnly: true // false if this command should not apply in readOnly mode
		});
	}

	function save_card(save_button){
		var btn = save_button,
			editor_console = $("#editor_console");

		$('<input />').attr('type', 'hidden')
			.attr('name', 'card')
			.attr('value', editor.getSession().getValue())
			.appendTo('#card_edit_form');
		$('<input />').attr('type', 'hidden')
		    .attr('name', "submit")
		    .attr('value', "save")
		    .appendTo('#card_edit_form');

		// check format
		var pattern = new RegExp($('input[name="color"]').attr('pattern'));
		if(!$('input[name="color"]').val().match(pattern)){
			editor_console.html('<span class="error">Chosen color is not a valid hexadecimal value.</span>');
		} else {
			// ajax post
			var submit_url = $('#card_edit_form').attr("action");
			$.ajax({
				url: submit_url,
				type: 'post',
				dataType: 'json',
				data: $("#card_edit_form").serialize(),
				beforeSend: function() {
					btn.prop("disabled",true);
					editor_console.html("Saving...");
				},
				success: function(data) {
					if(data.is_saved !== false){
						btn.prop("disabled",false);
						editor_console.html("Last save : " + new Date());
						//document.location.href = data.return_url;
					}
				}
			});
		}
	}

	$('#editor_save').click(function(e){
		save_card($(this));
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
				url: card_name+"/as_html/?show_banner=0",
				type: 'get',
				dataType: 'json',
				success: function(data) {
					element.closest('.banner.loadable').next().html(data.body).slideDown(300);
					element.attr("data-action","unload")
					element.text("CLOSE")
				}
			});
		}
		else{
			$(this).closest('.banner.loadable').next().slideUp(200,function(){
				$(this).html('');
			});
			element.attr("data-action","load");
			element.text("LOAD")
		}
	});

	// Toggle Content Width
	function init_width_mode(){
		var element = $('#toggle_width');
		var width_mode = (localStorage["width_mode"] != undefined)
			? localStorage["width_mode"]
			: element.attr("data-width-mode");
		if(width_mode === "stretch")
			$('.content').removeClass("width_constraint");
		else
			$('.content').addClass("width_constraint");
		localStorage["width_mode"] = width_mode;
		if(typeof ace !== 'undefined'){
			var editor = ace.edit("editor");
			editor.resize();
		}
	}
	$('body').on('click','#toggle_width',function(e){
		e.preventDefault();
		var element = $(this);
		var width_mode = (localStorage["width_mode"] != undefined)
			? localStorage["width_mode"]
			: element.attr("data-width-mode");
		if(width_mode === "stretch"){
			$('.content').addClass("width_constraint");
			width_mode = "constraint";
			element.attr("data-width-mode",width_mode);
		}else{
			$('.content').removeClass("width_constraint");
			width_mode = "stretch";
			element.attr("data-width-mode",width_mode);
		}
		localStorage["width_mode"] = width_mode;
	});

	init_width_mode();

});