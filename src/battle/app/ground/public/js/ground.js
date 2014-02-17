// -----------------------
// Ground.js v0.2
// -----------------------

// -----------------------
// Vote object
// -----------------------
function GroundElement(name,index,src,is_background,is_dir,path){
	this.name = name;
	this.index = index;
	this.src = src;
	this.is_background = is_background;
	this.is_dir = is_dir;
	this.path = path;
}

// -----------------------
// Ground File Explorer
// -----------------------
// ---- Usage :
// Explorer mode:
// - get current folder 
// - display elements (files & folder) in current folder
// - get & change focused element
// - keyboard :
// left & right : next previous image
// down : show current element
// up : go to parent folder

// Fullscreen mode :
// - left & right arrow : next previous image
// - up : show explorer
// - down (info?displaymode?)
function GroundFileExplorer() {
	var self = this;
	
	this.status = "";
	this.action_submit_url = '';
	this.current_folder = '';
	this.current_folder_elements = [];
	this.focused_element_index = 0;
	this.focused_element = null;
	this.explorer_is_visible = false;
	this.centered_img = $(".centered_img");

	this.init = function(){
		$("body").css('height', window.height);
  		self.centered_img = $(".centered_img");
  		self.action_submit_url = $('.ground_api_form').attr("action");
  		self.init_folder();
		
  		// ---- event handling ----

  		$("html").click(function (e) {
		//$(".container").click(function (e) {
  		//$('body').on('click', '.container', function(e){
	    	stop_bubbling(e);
		    if(self.explorer_is_visible)
		    	self.hide_explorer();
		    else
		    	self.show_explorer();
		});
  		$('body').on('click', '.img_container', function(e){
	    	stop_bubbling(e);
			self.open_element(parseInt($("img",this).attr("data-index")));
		});
		$('body').on('click', '.parent_folder', function(e){
	    	stop_bubbling(e);
	    	if($(".parent_folder").hasClass("root_folder"))
				self.hide_explorer();
			else
				server_get_parent_folder_elements();
		});
		
		$('body').on('click', '.go_to_previous', function(e){
	    	stop_bubbling(e);
			self.previous();
		});
		$('body').on('click', '.go_to_next', function(e){
	    	stop_bubbling(e);
			self.next();
		});

		$('body').on('mouseover', '.img_container', function(e){
			$(this).toggleClass("shadow");
		});
		$('body').on('mouseout', '.img_container', function(e){
			$(this).toggleClass("shadow");
		});

		document.addEventListener("keydown",function(e){
			e = e || window.event;

			// left
			if(e.keyCode === 37){ 
				self.previous();
			}
			// right
			else if(e.keyCode === 39){ 
				self.next();
			}
			// up
			else if(e.keyCode === 38){ 
				if(self.explorer_is_visible){
					if($(".parent_folder").hasClass("root_folder"))
						self.hide_explorer();
					else
						server_get_parent_folder_elements();
				}else{
					self.show_explorer();
				}
			}
			// down
			else if(e.keyCode === 40){
				self.explorer_is_visible &&	self.open_current_element(true);
			}

			stop_bubbling(e);
		});

		if(self.explorer_is_visible)
			self.refresh_explorer_focused_element();
		else
			self.open_current_element(false);
	}

	// ---- public methods ----

	this.init_folder = function(){
		// retrieve folder's elements
		var images = $(".img_container img").map(function(){
			return new GroundElement(
				$(this).attr("alt"),
				parseInt($(this).attr("data-index")) + 1,
				$(this).attr("src"),
				$(this).hasClass("bg"),
				$(this).hasClass("dir"),
				$(this).attr("data-path")
			);
		});
  		self.current_folder_elements = images.get();
  		self.current_folder = $(".current_folder").attr("data-path");
  		self.focused_element_index = parseInt($(".img_container img:not('.dir'):first").attr("data-index"));
  		if(isNaN(self.focused_element_index))
  			self.focused_element_index = 0;
  		self.refresh_explorer_focused_element();
	}

	this.open_element = function(index){
		self.focused_element_index = index;
		self.open_current_element(true);
	}

	this.next = function(){
		self.focused_element_index = (self.focused_element_index < self.current_folder_elements.length - 1)
			? self.focused_element_index + 1
			: 0;

		if(self.explorer_is_visible)
			self.refresh_explorer_focused_element();
		else
			self.open_current_element(false);
	}

	this.previous = function(){
		self.focused_element_index = (self.focused_element_index > 0)
			? self.focused_element_index - 1
			: self.current_folder_elements.length - 1;

		if(self.explorer_is_visible)
			self.refresh_explorer_focused_element();
		else
			self.open_current_element(false);
	}

	this.open_current_element = function(loadfolder){
		var element = self.current_folder_elements[self.focused_element_index];
		if(element.is_dir && loadfolder === true){
			server_get_folder_elements();
		}else{
			if(element.is_background){
				self.centered_img.addClass("hidden");
				$("html").css('background-image', 'url(' + element.src + ')');
			}else{
				self.centered_img.attr('src', element.src);
				self.centered_img.removeClass("hidden");
				$("html").css('background-image', 'none');
			}
			$("#bottombar .element_name").html(
				 element.name
				);
			$("#bottombar .element_index").html(
				'<a class="left go_to_previous marginleftright" href="#">Previous</a> '
				+ '<span class="left element_index_value">' + element.index +'/'+ self.current_folder_elements.length +'</span>'
				+ '<a class="left go_to_next marginleftright" href="#">Next</a>');
			self.hide_explorer();
		}
	}

	this.refresh_explorer_focused_element = function(){
		$("#explorer .img_container").removeClass("focused");
		$("#explorer .img_container img[data-index="+self.focused_element_index+"]").parent().addClass("focused");
	}

	this.hide_explorer = function(){
		self.explorer_is_visible = false;
		$("#explorer").addClass("hidden");
	}

	this.show_explorer = function(){
		self.explorer_is_visible = true;
		$("#explorer").removeClass("hidden");
		self.refresh_explorer_focused_element();
	}

	// ---- private methods ----
	
	var server_get_parent_folder_elements = function(){
		var to_send = {submit:'get_parent_folder', path: self.current_folder};
		var dataString = JSON.stringify(to_send);
		$.post(self.action_submit_url, { data : dataString },server_get_folder_elements_callback);
		// TODO : show "beach ball"
		$('#explorer .folder_title').html("<h3>Loading Folder...</h3>");
	}

	var server_get_folder_elements = function(){
		var element = self.current_folder_elements[self.focused_element_index];
		var to_send = {submit:'get_folder', path: element.path};
		var dataString = JSON.stringify(to_send);
		$.post(self.action_submit_url, { data : dataString },server_get_folder_elements_callback);
		// TODO : show "beach ball"
		$('#explorer .folder_title').html("<h3>Loading Folder...</h3>");
	}

	var server_get_folder_elements_callback = function(obj){
		// TODO : hide "beach ball"
		var res = JSON.parse(obj);
		$('#explorer').html(res.body);
  		self.init_folder();
	}

	// stop event bubbling
	var stop_bubbling = function (e){
		if (!e) var e = window.event;
		e.cancelBubble = true;
		if (e.stopPropagation) 
			e.stopPropagation();
		e.preventDefault();
	}

	// ! 
	this.init();
}

$(window).load(function(){
	var ground = new GroundFileExplorer();
});