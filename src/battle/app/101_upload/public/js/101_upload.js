// -----------------------
// 101_upload.js
// -----------------------

// ---- On Load ----
$(window).load(function(){

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
			var files = e.target.files;
			for (var i = 0, f; f = files[i]; i++) {
				// Only process image and plain text files.
				if (!f.type.match('image.*') && !f.type.match('text/plain'))
					continue;

				// Closure to capture the file information.
				var reader = new FileReader();
				reader.onload = (function(datFile) {
					var max_file_size = 5242880;// 5Mio
					if(datFile.size > max_file_size){
						alert("DAT FILE TOO BIG, MAX IS " + max_file_size + " BYTES, YOUR FILE IS "+ datFile.size +" BYTES! ROFL XD");
					}else{
						return function(e) {
							var data = {
								submit : "upload_file", 
								file : e.target.result,
								file_name : escape(datFile.name)
							};
							send_formdatawithupload(JSON.stringify(data));
						};
					}
				})(f);
				reader.readAsDataURL(f);
			}
		} 
	});
});