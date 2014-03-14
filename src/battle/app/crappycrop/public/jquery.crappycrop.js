/*! 
 * jQuery CrappyCrop plugin v0.1 - JQuery + Hammer Crop Plugin 
 * https://github.com/labelcarrote/CrappyCrop
 */
(function ($){

	$.CrappyCrop = function(element, options){
		var self = this;

		// Access to jQuery and DOM versions of element
		self.$element = $(element);
		self.element = element;

		// Add a reverse reference to the DOM object
		self.$element.data("CrappyCrop", self);

		// options 
		$.CrappyCrop.defaultOptions = {};
		var zoom_scale_step = 0.1,
			output_img_quality = 0.92;
					
		var container,
			img_to_crop,
			img_real_width,
			img_real_height,
			img_current_width,
			img_current_height,
			y_pos,
			x_pos,
			last_scale = 1,
			zoom_in_scale_step = 1 + zoom_scale_step,
			zoom_out_scale_step = 1 - zoom_scale_step,
			is_pinching = false;

		self.init = function(){
			self.options = $.extend({},$.CrappyCrop.defaultOptions, options);
			
			container = self.$element;
			img_to_crop = $('img',container);

			// get image dimensions
			var img = new Image();
			img.onload = function () {
				img_real_width = img.width;
				img_real_height = img.height;
				img_current_width = img_real_width;
				img_current_height = img_real_height;
				
				// resize image to fit container
				img_to_crop.toggleClass("hidden");
				self.fit_in();
			}
			img.src = img_to_crop.attr('src');

			// hammer events
			container.hammer().on('touch drag pinch pinchin pinchout release doubletap',
				container,
				eventHandlers.handle_hammer_gesture);

			// mousewheel event
			container.on('mousewheel', eventHandlers.handle_mousewheel);
		};

		// ---- event handlers

		var eventHandlers = {
			handle_hammer_gesture : function (event){
				event.gesture.preventDefault();
				event.preventDefault();
				switch(event.type) {
					case 'touch':
						x_pos = img_to_crop.position().left;
						y_pos = img_to_crop.position().top;
						break;
					case 'drag':
						move(event.gesture.deltaX,event.gesture.deltaY);
						break;
					case 'pinch':
						if(!is_pinching){
							x_pos = img_to_crop.position().left + (img_current_width / 2);
							y_pos = img_to_crop.position().top + (img_current_height / 2);
						}
						break;
					case 'pinchin':
					case 'pinchout':
						pinch_zoom(event.gesture.scale);
						break;
					case 'release':
						end_gesture();
						break;
				}
			},

			handle_mousewheel : function (event){
				event.preventDefault();
				zoom((event.deltaY < 0) ? zoom_in_scale_step : zoom_out_scale_step);
			}
		}

		// ---- public methods 

		// resize image to fit in container
		self.fit_in = function(){
			var container_width = container.width(),
				container_height = container.height();
			x_pos = (container_width / 2);
			y_pos = (container_height / 2);
			var zoom_scale = (img_real_width / img_real_height > container_width / container_height)
				? container_width / img_current_width
				: container_height / img_current_height;
			zoom(zoom_scale);
		}

		// resize image to fill container (aka "fit out")
		self.fit_out = function(){
			var container_width = container.width(),
				container_height = container.height();
			x_pos = (container_width / 2);
			y_pos = (container_height / 2);
			var zoom_scale = (img_real_width / img_real_height > container_width / container_height) 
				? container_height / img_current_height
				: container_width / img_current_width;
			zoom(zoom_scale);
		}

		// zoom_in / zoom_out
		self.zoom_in = function(){ 
			zoom(zoom_in_scale_step);
		}
		self.zoom_out = function(){ 
			zoom(zoom_out_scale_step); 
		}

		// get cropped image (canvas generated)
		self.get_data_url = function() {
			var canvas = document.createElement('canvas'), 
				ctx = canvas.getContext('2d');

			var scale = img_current_width / img_real_width;
			canvas.width = ((container.width() - img_current_width) / scale) + img_real_width;
			canvas.height = ((container.height() - img_current_height) / scale) + img_real_height;
			
			// set background
			ctx.fillStyle = '#000'; 
			ctx.fillRect (0, 0, canvas.width, canvas.height); 

			// draw image
			var img = img_to_crop.get(0), // img
				sx = 0, // source x
				sy = 0, // source y
				sw = img_real_width, // source w 
				sh = img_real_height, // source h
				dx = img_to_crop.position().left / scale, // destination x
				dy = img_to_crop.position().top / scale, // destination y
				dw = img_real_width, // destination w
				dh = img_real_height; // destination h
			ctx.drawImage(img,sx,sy,sw,sh,dx,dy,dw,dh);

			var img_src = img_to_crop.attr('src');
			var mime_type = (string_ends_with(img_src,"jpg") || string_ends_with(img_src,"jpeg"))
				? "image/jpeg"
				: "image/png";

			return canvas.toDataURL(mime_type,output_img_quality);
		}

		self.get_crop_data = function(){
			var scale = img_current_width / img_real_width;
			var final_width = ((container.width() - img_current_width) / scale) + img_real_width,
				final_height = ((container.height() - img_current_height) / scale) + img_real_height;
			return {
				fw : final_width, // final image width 
				fh : final_height, // final image height
				sx : 0, // source x
				sy : 0, // source y
				sw : img_real_width, // source w 
				sh : img_real_height, // source h
				dx : img_to_crop.position().left / scale, // destination x
				dy : img_to_crop.position().top / scale, // destination y
				dw : img_real_width, // destination w
				dh : img_real_height // destination h
			};
		}

		// ---- helpers / private methods

		function move(deltaX, deltaY){
			if(is_pinching)
				return;
			img_to_crop.css({
				left: (x_pos + deltaX) + 'px',
				top: (y_pos + deltaY) + 'px' 
			});
		}

		function zoom(scale){
			img_current_width = img_to_crop.width() * scale;
			img_current_height = img_to_crop.height() * scale;

			if(img_current_height > 64 && img_current_width > 64){
				img_to_crop.css({
					width: img_current_width,
					left: (x_pos - (img_current_width / 2)) + 'px',
					top: (y_pos - (img_current_height / 2)) + 'px'
				});
				last_scale = scale;
			}
		}

		function pinch_zoom(scale){
			if(last_scale !== scale){
				img_to_crop.css({
					width: img_current_width * scale,
					left: (x_pos - ((img_current_width * scale) / 2)) + 'px',
					top: (y_pos - ((img_current_height * scale) / 2)) + 'px'
				});
				is_pinching = true;
				last_scale = scale;
			}
		}

		function end_gesture(){
			if(is_pinching){
				img_current_width = img_to_crop.width();
				img_current_height = img_to_crop.height();
				is_pinching = false;
			}
			x_pos = img_to_crop.position().left + (img_current_width / 2);
			y_pos = img_to_crop.position().top + (img_current_height / 2);
		}

		function string_ends_with(str, suffix) {
		    return str.toLowerCase().indexOf(suffix.toLowerCase(), str.length - suffix.length) > -1;
		}

		// !!!
		self.init();
	};
	
	$.fn.CrappyCrop = function(options){
		return this.each(function(){
			new $.CrappyCrop(this, options);
		});
	};
	
	// This function breaks the chain, but returns
	// the CrappyCrop if it has been attached to the object.
	$.fn.getCrappyCrop = function(){
		return this.data("CrappyCrop");
	};

}(jQuery));