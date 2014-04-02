"use strict";
// -----------------------
// Chartreuse.js
// -----------------------
var PHI = (1 + Math.sqrt(5)) / 2;// gold

// Colors
var BLACK = "#000000";
var GREY  = 'rgba(32,250,250)';
var RED = 'rgba(250,50,0,1)';//#FF3200
var WHITE = 'rgba(250,250,250,1)';
var RED = '#FF3200';
var RED1 = '#FF6540';
var RED2 = '#FF9980';
var RED3 = '#FFCCBF';
var REDS = [RED, RED1, RED2, RED3, WHITE];

// -----------------------
// Chart Value object
// -----------------------
function ChartValue(x, name, value){
	this.x = x;
	this.name = name;
	this.value = value;
}

// -----------------------
// Bar object
// -----------------------
function Bar(x){
	var self = this;
	
	this.x = x;
	this.values = [];
	
	this.add_chart_value = function(chart_value){
		var found = 0;
		for (var i = 0; i < self.values.length; i++){
			if (self.values[i].name === chart_value.name){
				self.values[i].value += chart_value.value;
				found = 1;
			}
		}
		if (found === 0)
			self.values.push(chart_value);
	}
	
	this.get_total_value = function(){
		var sum = 0;
		self.values.map(function(item){
			sum += item.value;
		});
		return sum;
	}
}

// -----------------------
// Chart object
// -----------------------
function Chart(ctx, posx, posy, width, height, name, x_name, y_name, options){
	var self = this;

	this.ctx = ctx;
	this.posx =  posx;
	this.posy =  posy;
	this.width = width;
	this.height =  height;
	this.name = name;
	this.x_name = x_name;
	this.y_name = y_name;
	this.options = options;
	
	this.x_padding = 0;
	this.y_padding = 0;
	this.origin_x = 0;
	this.origin_y = 0;
	this.x_width = 0;
	this.y_width = 0;
	this.bar_width = 0;
	this.unit_height = 0;
	this.values = [];
	this.bars = [];
	
	this.mouse_x = 0;
	this.mouse_y = 0;
	this.must_draw_status = false;
	this.status_message = "";
	
	this.FONT = "Helvetica";
	this.fat_size = 216;
	this.big_size = self.fat_size / PHI;
	this.small_size = self.fat_size / (10 * PHI);
	this.FONT_SMALL = self.small_size + 'px "'+ self.FONT +'"';
	this.FONT_MEDIUM = 'bold 24px "'+ self.FONT +'"';
	this.FONT_BIG    = 'bold '+ self.big_size + 'px "'+ self.FONT +'"';
	this.FONT_FAT    = 'bold '+ self.fat_size + 'px "'+ self.FONT +'"';
	
	this.init = function(){
		self.resize();
		document.addEventListener("mousemove",update_display);
	}
	
	this.add_chart_value = function(chart_value){
		self.values.push(chart_value);
		
		// sorts by x
		self.values.sort(function(a, b){ return a.x > b.x ? 1 : -1; });
		
		// creates and fills bars
		var found = 0;
		for (var i = 0; i < self.bars.length; i++) {
			if (self.bars[i].x === chart_value.x){
				self.bars[i].add_chart_value(chart_value);
				found = 1;
			}
		}
		if (found === 0){
			var new_bar = new Bar(chart_value.x);
			new_bar.add_chart_value(chart_value);
			self.bars.push(new_bar);
		}
		self.resize();
	}
	
	this.clear = function(){
		self.bars = [];
		self.values = [];
	}
	
	this.resize = function(){
		self.x_padding = self.width / 24;
		self.y_padding = Math.floor(self.height / (3 * PHI));
		self.origin_x = self.posx + self.x_padding;
		self.origin_y = self.posy + self.height - self.y_padding;
		self.x_width = self.width - (2 * self.x_padding);
		self.y_width = self.height - (2 * self.y_padding);
		self.bar_width = self.x_width / self.bars.length;
		self.unit_height = 32;
	}
	
	this.draw = function(){
		self.ctx.fillStyle = WHITE;
		self.ctx.strokeStyle = WHITE;
		self.ctx.lineWidth = 2;
		self.ctx.font = self.FONT_MEDIUM;
		
		draw_chart();
		draw_status();
		//draw_border();
		//draw_message();
	}
	
	this.to_string = function(){
		return self.posx +", "+ self.posy +", "+ self.width +", "+ self.height
			+", "+ self.name +", "+ self.x_name +", "+ self.y_name;
	}
	
	// ---- Helpers ----
	
	var update_display = function(e){
		self.mouse_x = e.clientX;
		self.mouse_y = e.clientY;
		self.must_draw_status = (self.mouse_x > self.origin_x && self.mouse_x < self.posx + self.x_width && self.mouse_y > self.posy && self.mouse_y < self.origin_y);
	}
	
	var highest_bar_value = function(array){
		var narray = [];
		for(var i = 0; i < array.length; i++)
		  if(array[i] != null)
			narray.push(array[i].get_total_value());
		return Math.max.apply(Math, narray);
	};
	
	var draw_chart = function(){
		//  x line
		var x = posx;//self.origin_x;
		var y = self.origin_y;
		self.ctx.beginPath();
		self.ctx.moveTo(x,y);
		self.ctx.lineTo(x + self.width,y);
		self.ctx.closePath();
		self.ctx.stroke();
		if(self.x_name !== null)
			self.ctx.fillText(self.x_name, x + self.width - self.x_padding - 12, y + 24);
	
		// values as bars
		x = self.origin_x;
		y = self.origin_y;
		for (var i = 0; i < self.bars.length; i++){
			var total_value = self.bars[i].get_total_value() * self.unit_height;
			x = x + i * self.bar_width;
			y = self.origin_y - total_value;
			for(var j = 0; j < self.bars[i].values.length; j++){
				var value = self.bars[i].values[j].value * self.unit_height;
				self.ctx.fillStyle = REDS[4 - (j % 5)];
				self.ctx.fillRect(x , y, self.bar_width, value);
				if (self.must_draw_status){
					self.status_message = (self.mouse_x > x && self.mouse_x < x + self.bar_width && self.mouse_y > y && self.mouse_y < y + value)
						? self.bars[i].values[j].name +" : "+ self.bars[i].values[j].value
						: self.status_message;
				}
				y = y + value;
			}
			self.ctx.fillStyle = WHITE;
			y = self.origin_y - total_value;
			self.ctx.strokeRect(x , y, self.bar_width, total_value);
			self.ctx.fillText(self.bars[i].x,x + self.bar_width / 2, self.origin_y + 24)
		}
	}
	
	var draw_border = function(){
		self.ctx.strokeRect(self.posx, self.posy, self.width, self.height);
	}
	
	var draw_status = function(){
		if(self.must_draw_status){
			self.ctx.font = self.FONT_MEDIUM;
			self.ctx.fillStyle = BLACK;
			var x = self.ctx.canvas.width / 2;
			var y = self.posy + (self.height / 6);
			self.ctx.fillText(self.status_message, x, y);
		}
	}
	
	var draw_message = function(message){
		self.ctx.font = self.FONT_MEDIUM;
		self.ctx.fillStyle = BLACK;
		if(message === null){
			message = self.width + " x " + self.height;
			if(self.bars.length > 0)
			  message += JSON.stringify(self.bars);	
		}
		var x = self.ctx.canvas.width / 2;
		var y = self.posy + (self.height / 6);
		self.ctx.fillText(message, x, y);
	}
	
	this.init();
}

// -----------------------
// Chart Value object
// -----------------------
function Slice(start_value, end_value, name){
	this.start_value = start_value;
	this.end_value = end_value;
	this.name = name;
}

// -----------------------
// Timeline object
// -----------------------
function Timeline(ctx, posx, posy, width, height, name, x_name, y_name, start_value, end_value, options){
	var self = this;

	this.ctx = ctx;
	this.posx =  posx;
	this.posy =  posy;
	this.width = width;
	this.height =  height;
	this.name = name;
	this.x_name = x_name;
	this.y_name = y_name;
	this.start_value = start_value;
	this.end_value = end_value;
	this.options = options;
	
	this.x_padding = 0;
	this.y_padding = 0;
	this.origin_x = 0;
	this.origin_y = 0;
	this.x_width = 0;
	this.y_width = 0;
	this.unit_height = 0;
	this.unit_width = 0;
	this.slices = [];
	
	this.mouse_x = 0;
	this.mouse_y = 0;
	this.must_draw_status = true;
	this.status_message = "T_T";
	
	this.FONT = "Helvetica";
	this.fat_size = 216;
	this.big_size = self.fat_size / PHI;
	this.small_size = self.fat_size / (10 * PHI);
	this.FONT_SMALL = self.small_size + 'px "'+ self.FONT +'"';
	this.FONT_MEDIUM = 'bold 24px "'+ self.FONT +'"';
	this.FONT_BIG    = 'bold '+ self.big_size + 'px "'+ self.FONT +'"';
	this.FONT_FAT    = 'bold '+ self.fat_size + 'px "'+ self.FONT +'"';
	
	this.init = function(){
		self.resize();
		document.addEventListener("mousemove",update_display);
	}
	
	this.add_slice = function(slice){
		self.slices.push(slice);
		// sorts by start_value
		self.slices.sort(function(a, b){ return a.start_value > b.start_value ? 1 : -1; });
		self.resize();
	}
	
	this.clear = function(){
		self.slices = [];
	}
	
	this.resize = function(){
		self.x_padding = self.width / 24;
		self.y_padding = Math.floor(self.height / (1.5 * PHI));
		self.origin_x = self.posx + self.x_padding;
		self.origin_y = self.posy + self.height - self.y_padding;
		self.x_width = self.width - (2 * self.x_padding);
		self.y_width = self.height - (2 * self.y_padding);
		self.unit_height = 32;
		self.unit_width = self.x_width / ((self.end_value - self.start_value) / 1000);
	}
	
	this.draw = function(){
		self.ctx.fillStyle = WHITE;
		self.ctx.strokeStyle = WHITE;
		self.ctx.lineWidth = 2;
		self.ctx.font = self.FONT_MEDIUM;
		
		draw_timeline();
		draw_status();
		//draw_border();
		//draw_message();
	}
	
	this.to_string = function(){
		return self.posx +", "+ self.posy +", "+ self.width +", "+ self.height
			+", "+ self.name +", "+ self.x_name +", "+ self.y_name;
	}
	
	// ---- Helpers ----
	
	var update_display = function(e){
		self.mouse_x = e.clientX;
		self.mouse_y = e.clientY;
		self.must_draw_status = (self.mouse_x > self.origin_x && self.mouse_x < self.posx + self.x_width && self.mouse_y > self.posy && self.mouse_y < self.origin_y);
	}
	
	var draw_timeline = function(){
		//  x line
		var x = self.origin_x - self.x_padding;
		var y = self.origin_y;
		var slice_width = 0;
		self.ctx.beginPath();
		self.ctx.moveTo(x,y);
		self.ctx.lineTo(x + self.width,y);
		self.ctx.closePath();
		self.ctx.stroke();

		// x name
		/*if(self.x_name !== null)
			self.ctx.fillText(self.x_name, x + self.width - self.x_padding, y - 12);*/

		// hours
		for (var i = 0; i <= 24; i++){
			x = self.origin_x + i * self.unit_width * 3600;
			var y = self.origin_y;
			if(i % 12 === 0){
				self.ctx.beginPath();
				self.ctx.moveTo(x,y - 8);
				self.ctx.lineTo(x,y + 8);
				self.ctx.closePath();
				self.ctx.stroke();
				self.ctx.fillText(i + "H", x, y + 40);
			}else{
				self.ctx.beginPath();
				self.ctx.moveTo(x,y - 4);
				self.ctx.lineTo(x,y + 4);
				self.ctx.closePath();
				self.ctx.stroke();	
			}
		}

		// current time (down / up triangle || vertical bar )
		
		// slices 
		x = self.origin_x;
		y = self.origin_y;
		for (var i = 0; i < self.slices.length; i++){
			self.ctx.fillStyle = WHITE;
			var midnight = new Date();
			midnight.setHours(0,0,0,0);
			x = self.origin_x + Math.round((self.slices[i].start_value - midnight) / 1000) * self.unit_width;
			slice_width = ((self.slices[i].end_value - self.slices[i].start_value) / 1000) * self.unit_width;
			y = self.origin_y - (self.unit_height / 2);
			self.ctx.strokeRect(x , y, slice_width, self.unit_height);
		}
	}
	
	var draw_border = function(){
		self.ctx.strokeRect(self.posx, self.posy, self.width, self.height);
	}
	
	var draw_status = function(){
		if(self.must_draw_status){
			self.ctx.font = self.FONT_MEDIUM;
			self.ctx.fillStyle = RED;
			var x = self.ctx.canvas.width / 2;
			var y = self.posy + (self.height / 6);
			self.ctx.fillText(self.status_message, x, y);
		}
	}
	
	var draw_message = function(message){
		self.ctx.font = self.FONT_MEDIUM;
		self.ctx.fillStyle = BLACK;
		if(message === null){
			message = self.width + " x " + self.height;
			if(self.bars.length > 0)
			  message += JSON.stringify(self.bars); 
		}
		var x = self.ctx.canvas.width / 2;
		var y = self.posy + (self.height / 6);
		self.ctx.fillText(message, x, y);
	}
	
	this.init();
}