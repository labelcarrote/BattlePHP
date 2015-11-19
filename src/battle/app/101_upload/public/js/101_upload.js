// ---- On Load ----
$(window).load(function(){

	// ---- Helpers ----

	// stop event bubbling
	function stop_bubbling(e){
		if (!e) var e = window.event;
		e.cancelBubble = true;
		if (e.stopPropagation) 
			e.stopPropagation();
		e.preventDefault();
	}

	// ------- UPLOAD ---------

	function send_formdatawithupload(formData){
		console.log(formData);
		var xhr = new XMLHttpRequest();
		var submit_url = $('#upload_form').attr("action");
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
		    		$("#dat_file").html(ajaxresult.body);
		    	}
		    	else 
		    		$(".uploadprogress p").html(ajaxresult.errors);
		    }
		    $(".uploadprogress .progress-bar").css("width", percentage + "%")
		}
		xhr.send(formData);
	}

	// upload / attach file form
	$("#dat_file").change(function (e) {
		// Check for the various File API support.
		var is_file_api_supported = (window.File && window.FileReader && window.FileList && window.Blob);
		if (is_file_api_supported){
		  	console.log('Great success! All the File APIs are supported.');
		  	
			var output = [];
		  	var files = e.target.files;
			for (var i = 0, f; f = files[i]; i++) {
				
				output.push('<li><strong>', escape(f.name), '</strong> (', f.type || 'n/a', ') - ',
				    f.size, ' bytes, last modified: ',
					f.lastModifiedDate ? f.lastModifiedDate.toLocaleDateString() : 'n/a',
					'</li>'
				);

				// Only process image files.
				if (!f.type.match('image.*'))
					continue;

				// Closure to capture the file information.
				var reader = new FileReader();
				reader.onload = (function(theFile) {
					return function(e) {
						var form_data = new FormData(),
							data = {
								submit : "upload_file", 
								file : e.target.result,
								file_name : theFile.name
							};
						form_data.append("data", JSON.stringify(data));
					 	send_formdatawithupload(data);
						// Render thumbnail.
						/*var span = document.createElement('span');
						span.innerHTML = ['<img class="thumb" src="', e.target.result,
						'" title="', escape(theFile.name), '"/>'].join('');
						document.getElementById('list').insertBefore(span, null);*/
					};
				})(f);
				reader.readAsDataURL(f);
			}
			console.log(output.join(''));
		} else {
		  	console.log('The File APIs are not fully supported in this browser.');
		}
	});
});