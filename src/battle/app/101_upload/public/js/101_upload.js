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
		if (window.File && window.FileReader && window.FileList && window.Blob) {
		  	console.log('Great success! All the File APIs are supported.');
		  	var files = e.target.files;
		  	// files is a FileList of File objects. List some properties.
			var output = [];
			for (var i = 0, f; f = files[i]; i++) {
			  output.push('<li><strong>', escape(f.name), '</strong> (', f.type || 'n/a', ') - ',
			              f.size, ' bytes, last modified: ',
			              f.lastModifiedDate ? f.lastModifiedDate.toLocaleDateString() : 'n/a',
			              '</li>');
			}
			console.log(output.join(''));
			/*document.getElementById('list').innerHTML = '<ul>' + output.join('') + '</ul>';*/
			 // Loop through the FileList and render image files as thumbnails.
			for (var i = 0, f; f = files[i]; i++) {
				// Only process image files.
				if (!f.type.match('image.*')) {
					continue;
				}

				var reader = new FileReader();

				// Closure to capture the file information.
				reader.onload = (function(theFile) {
					return function(e) {
						var data = new FormData();/*$('#upload_form')[0]);*//*{ data : {
							name : "addfile", 
							file : e.target.result, 
							submit : "addfile"
							}};*/ //
						var data_upload = {
							name : "upload_file", 
							file : e.target.result,
							file_name : theFile.name,
							submit : "upload_file"
						};

						//var file = e.target.result;//datform_data.files[0];
				    	//data.append("file", file);
						/*data.append("name", "addfile");*/
						/*data.append("submit", "upload_file");*/
						data.append("data", JSON.stringify(data_upload));
						console.log(data);
						console.log(JSON.stringify(data));
					 	send_formdatawithupload(data);
						// Render thumbnail.
						/*var span = document.createElement('span');
						span.innerHTML = ['<img class="thumb" src="', e.target.result,
						'" title="', escape(theFile.name), '"/>'].join('');
						document.getElementById('list').insertBefore(span, null);*/
					};
				})(f);

				// Read in the image file as a data URL.
				reader.readAsDataURL(f);
			}
		} else {
		  	console.log('The File APIs are not fully supported in this browser.');

		}
		//var data = new FormData(document.getElementById("addfileform"));
		/*var data = new FormData($("#addfileform,.add_file_form")[0]);*/
		/*var datform = $("#upload_form");
		var datform_data = datform[0];
		var data = new FormData(datform_data);
		var file = datform_data.files[0];
    	data.append("file", file);
		data.append("name", "addfile");
		data.append("submit", "addfile");
	 	send_formdatawithupload(JSON.stringify(data));*/
	});

});