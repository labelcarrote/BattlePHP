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
		var xhr = new XMLHttpRequest(),
			submit_url = $('#upload_form').attr("action");
		$(".uploadprogress").toggleClass("hidden");
		xhr.open("POST",submit_url,true);
		xhr.upload.onprogress = function(event){
			var percentage = Math.floor(event.loaded / event.total * 100),
				message = (percentage === 100) ? "Completing upload..." : percentage + "%";
			$(".uploadprogress p").html(message);
			$(".uploadprogress .bar").css("width", percentage + "%")
		}
		xhr.onload = function(oEvent){
			var result = "",
				percentage = 0;
		    if (xhr.status != 200){
		    	$(".uploadprogress p").html("Error " + xhr.status + " occurred uploading your file.");
		    }else{
		    	var ajaxresult = JSON.parse(this.response);
		    	if(ajaxresult.errors !== null){
		    		$(".uploadprogress p").html(ajaxresult.errors);	
		    	}else{
		    		$(".uploadprogress p").html("100%");
		    		$(".uploadprogress").toggleClass("hidden");
		    		$("#dat_file_container img").attr("src",ajaxresult.body.dat_file_url);
		    		$("#dat_file_date_modified_link").attr("href",ajaxresult.body.dat_file_url);
		    		$("#dat_file_date_modified_link").html(ajaxresult.body.dat_file_date_modified);
		    	}
		    }
		    $(".uploadprogress .bar").css("width", percentage + "%");
		}
		xhr.send(formData);
	}

	// upload / attach file form
	$("#dat_file").change(function (e) {
		// Check for the various File API support.
		var is_file_api_supported = (window.File && window.FileReader && window.FileList && window.Blob);
		if (!is_file_api_supported){
		  	console.log('The File APIs are not fully supported in this browser.');
		} else {
			var output = [],
				files = e.target.files;
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
				reader.onload = (function(datFile) {
					var max_file_size = 5242880;
					if(datFile.size > max_file_size){// 5Mio
						alert("DAT FILE TOO BIG, MAX IS " + max_file_size + " BYTES ");
					}else{
						return function(e) {
							var form_data = new FormData(),
								data = {
									submit : "upload_file", 
									file : e.target.result,
									file_name : escape(datFile.name)
								};
							form_data.append("data", JSON.stringify(data));
						 	send_formdatawithupload(form_data);
						};
					}
				})(f);
				reader.readAsDataURL(f);
			}
			console.log(output.join(''));
		} 
	});
});