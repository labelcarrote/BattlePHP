"use strict";
// -----------------------
// Sawhat.js
// -----------------------
if(typeof ace !== 'undefined'){
	var editor = ace.edit("editor");
	editor.setShowPrintMargin(false);
	editor.setOptions({
		minLines: 12,
        maxLines: 120000
    });
    editor.setAutoScrollEditorIntoView();
	editor.resize();
	editor.getSession().setUseWrapMode(true);
	editor.getSession().setWrapLimitRange(0, 124);
	editor.resize();

}
// extends jQuery for selector existence //
jQuery.fn.exists = function () {
    return this.length !== 0;
}
$(document).ready(function(){
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
					'top' : (position.top+$(this).outerHeight()-3)+'px',
					'width' : $(this).outerWidth()
				});
				related_color_picker.hide().removeClass('hidden').slideDown(200);
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
		}
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
			$(".uploadprogress .progress-bar").css("width", percentage + "%")
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
		    $(".uploadprogress .progress-bar").css("width", percentage + "%")
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

		// ---- Auto save WIP
		// latency in milliseconds between editor change and the actual call to server
		var latency = 10000;
		// id of the save_to_server request
		var sts_id = 0;
		editor.getSession().on('change', function(e) {
			clearTimeout(self.sts_id);
			// http://stackoverflow.com/questions/1101668/how-to-use-settimeout-to-invoke-object-itself
			self.sts_id = setTimeout(function(){save_card($("#editor_save"));},latency);
		});
	}
	
	$('#editor_save').click(function(e){
		save_card($(this));
	});

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

	// Double click to edit card
	$(".things").dblclick(function(e){
		e.preventDefault();
		// go to card edit form
		window.location.href = $(this).attr("data-edit-url");
		return false;
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
					element.closest('.banner.loadable').next().html(data.body).hide().removeClass('hidden').slideDown(300);
					element.attr("data-action","unload");
					element.attr('title','close');
					element.find('span.fa').removeClass('fa-chevron-circle-down').addClass('fa-times');
					Prism.highlightAll();
					//load_starred_cards();
				}
			});
		}
		else{
			$(this).closest('.banner.loadable').next().slideUp(200,function(){
				$(this).html('');
			});
			element.attr("data-action","load");
			element.attr('title','load');
			element.find('span.fa').removeClass('fa-times').addClass('fa-chevron-circle-down');
		}
	});

	// Toggle Content Width
	function set_width_mode(init){
		var element = $('#toggle_width');
		var current_mode = (localStorage['width_mode'] != undefined)
			? localStorage['width_mode']
			: 'constraint'
		;
		var width_mode = element.attr('data-width-mode');
		if(current_mode !== width_mode && !init){
			current_mode = width_mode;
			localStorage['width_mode'] = current_mode;
		}
		if(current_mode === 'constraint'){
			$('.content').addClass('width_constraint');
			element.attr({
				'data-width-mode':'stretch',
				'title':'Stretch view'
			});
			element.find('span.fa').removeClass('fa-compress').addClass('fa-expand');
		} else {
			$('.content').removeClass('width_constraint');
			element.attr({
				'data-width-mode':'constraint',
				'title':'Constraint view'
			});
			element.find('span.fa').removeClass('fa-expand').addClass('fa-compress');
		}
	}
	
	$('body').on('click','#toggle_width',function(e){
		e.preventDefault();
		set_width_mode(false);
	});

	set_width_mode(true);

	// SEARCH RESULT RESIZE //
	var banner_min_height = parseInt($('.banner').css('min-height'));
	$('#search_result .banner').each(function(){
		var item_height = $(this).height();
		var item_padding = ($(this).innerHeight() - $(this).height()) / 2;
		var item_margin = ($(this).parent().outerHeight(true) - $(this).parent().innerHeight()) / 2;
		if(item_height % banner_min_height > 0){
			var next_ratio = Math.ceil(item_height/banner_min_height);
			$(this).css('height',((next_ratio*banner_min_height)+(((next_ratio*2)-2)*(item_padding+item_margin)))+'px');
		}
	});
	
	// Starred
	var starred_cards = localStorage['starred_cards'];
	if(typeof starred_cards === 'undefined' || starred_cards === null){
		starred_cards = new Array;
	} else {
		starred_cards = JSON.parse(starred_cards);
	}
	for(var key in starred_cards){
		$('.starred[data-card-name="'+starred_cards[key]+'"]').addClass('fa-star').removeClass('fa-star-o');
	}
	$('.banner').on('click','.starred',function(){
		var is_checked = $(this).hasClass('fa-star');
		var card_name = $(this).attr('data-card-name')
		if(is_checked){
			$(this).addClass('fa-star-o').removeClass('fa-star');
			// remove from storage
			starred_cards.splice(starred_cards.indexOf(card_name),1);
		} else {
			$(this).addClass('fa-star').removeClass('fa-star-o');
			// add in storage
			starred_cards.push(card_name);
		}
		localStorage['starred_cards'] = JSON.stringify(starred_cards);
	});
	function load_starred_cards(){
		var starred_card_container = $('.starred_container');
		if(starred_card_container.exists()){
			if(starred_cards.length !== 0){
				for(var key in starred_cards){
					var card_name = starred_cards[key];
					$.ajax({
						url: card_name+"/as_html/",
						type: 'get',
						dataType: 'json',
						success: function(data) {
							starred_card_container.append('<div class="left size1of2">'+data.loadable_link+'</div>');
						}
					});
				}
			} else {
				starred_card_container.append('<div class="placeholder_message">Click the <span class="fa fa-star-o" title="star icon"></span> icon on any card to bookmark it here.</div>');
			}
		}
	}
	load_starred_cards();
});