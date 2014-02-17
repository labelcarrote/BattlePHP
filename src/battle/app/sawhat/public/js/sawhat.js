"use strict";
// -----------------------
// Sawhat.js
// -----------------------
var editor = ace.edit("editor");
editor.getSession().setUseWrapMode(true);
editor.setShowPrintMargin(false);
editor.resize();
//editor.setTheme("ace/theme/monokai");
//editor.getSession().setMode("ace/mode/javascript");

$(window).load(function(){
	
	function send_formdatawithupload(formData){
		var xhr = new XMLHttpRequest();
		var submit_url = $('#card_edit_form').attr("action") + "/api";
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
		    data: $("#card_edit_form").serialize(),
		    success: function(data) {
           		document.location.href = "./";
			}
		});
	});
});