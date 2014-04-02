"use strict";
// -----------------------
// Tomatoro.js
// -----------------------
var PHI = (1+ Math.sqrt(5))/2;// gold

// Colors
var BLACK = "#000000";
var GREY  = 'rgba(32,250,250)';
var RED = 'rgba(250,50,0,1)';//#FF3200
var WHITE = 'rgba(250,250,250,1)';
var RED = '#FF3200';
var RED1 = '#FF6540';
var RED2 = '#FF9980';
var RED3 = '#FFCCBF';

$(window).load(function(){	
	var tomatoro = new TomatoroManager();
	
	document.addEventListener("keydown",function(e){
		if(typeof e.key === 'undefined' || e.key.lastIndexOf("F", 0) === 0)
			return;
		
		if ( e.key === "Backspace" || e.key === "Enter" || e.key === "Del" || e.key === "Delete" || e.key === "."){
			e.preventDefault();
			tomatoro.push_character(e.key);
			//tomatoro.status = e.key + " " +e.keyCode + e.charCode;
		}
	});
	
	document.addEventListener("keypress",function(e){
		var key = (typeof e.key !== 'undefined') ? e.key : (e.which !== 0) ? String.fromCharCode(e.which) : e.keyCode;
		//write name
		var code = (e.which !== 0) ? e.which : e.keyCode;
		switch (code){//e.which) {
			case 8  : key = "Backspace";break;
			case 13 : key = "Enter";break;
			case 9  : key = "Tab";break;
			case 27 : key = "Esc";break;
			case 18 : key = "Alt";break;
			case 17 : key = "Control";break;
			case 16 : key = "Shift";break;
			case 46 : key = (e.keyCode === 0) ? "." : "Del"; break;
			default : break;
		}
		tomatoro.push_character(key);
		//tomatoro.status = "key : " + e.key +" "+ e.keyCode +" "+ e.charCode;
		e.preventDefault();
		return false;
	});
	
	$(document).mousedown(function(e){
		tomatoro.startstop();
	});
	
	// redraws if window is resized        
	$(window).resize(function() {
		tomatoro.init();
	});
});

// -----------------------
// Completed Tomato object
// -----------------------
function CompletedTomato(date,name){
	this.date =  date;
	this.name =  name;
}

// -----------------------
// Tomatoro Manager object
// -----------------------
function TomatoroManager() {
	var self = this;
	
	this.TOMATORO_PERIOD = 1500;//1500;// 25 min = 20 * 60s = 1500s
	this.STARTED = 0;
	this.START_MESSAGE = "START";
	this.CANCEL_MESSAGE = "STOP";
	
	this.start_time = null;
	this.current_time = null;
	this.task_name = "";
	this.status = "";
	
	this.completed_tomatoes = [];
	
	this.timer_height = 0;
	
	this.FONT = "Helvetica";
	this.fat_size = 216;
	this.big_size = (self.fat_size / PHI);
	this.FONT_MEDIUM = 'bold 24px"'+ self.FONT +'"';
	this.FONT_BIG   = 'bold '+ self.big_size + 'px "'+ self.FONT +'"';
	this.FONT_FAT = 'bold '+ self.fat_size + 'px "'+ self.FONT +'"';
	
	// sounds
	this.snd = new Audio("app/tomatoro/public/sounds/kumkum1.mp3"); // buffers automatically when created
	
	// loops
	this.loop1 = null;
	this.loop2 = null;
	this.loop3 = null;
	
	// layers / canvases 
	this.layer1 = document.getElementById("layer1");
	this.ctx1 = layer1.getContext("2d");
	this.layer2 = document.getElementById("layer2");
	this.ctx2 = layer2.getContext("2d");
	
	// chart
	this.chart = null;
	
	this.init = function(){
		self.ctx1.canvas.width  = window.innerWidth;
		self.ctx1.canvas.height = window.innerHeight;
		self.ctx2.canvas.width  = window.innerWidth;
		self.ctx2.canvas.height = window.innerHeight;

		// background
		self.ctx1.fillStyle = RED;
		
		// timer / clock
		self.current_time = seconds_to_time(self.TOMATORO_PERIOD);
		self.timer_height = self.layer1.height/PHI;
		self.ctx2.fillStyle = WHITE;
		self.ctx2.font = self.FONT_FAT;
		self.ctx2.textAlign = 'center';
		
		// chart
		var timeline_height = (self.layer1.height - self.timer_height);
		var chart_width = Math.floor(self.layer1.width / PHI);
		var chart_height = Math.floor(timeline_height / PHI);
		var posx = Math.floor(self.ctx2.canvas.width / 2 - chart_width / 2);
		var posy = Math.floor(self.timer_height + (timeline_height - chart_height )/ 2);
		if (self.chart !== null){
			self.chart.posx = posx;
			self.chart.posy = posy;
			self.chart.width = chart_width;
			self.chart.height = chart_height;
			self.chart.resize();
		}
		else
			self.chart = new Chart(self.ctx2,posx,posy,chart_width,chart_height,"Tomatoro Timeline","t","n",null);
		
		load_tomatoes();
		self.snd.load();
		
		// Loops
		draw_background(); //self.loop3 = setInterval(draw_background, 400);
		self.loop1 =  setInterval(update_clock, 20);
		self.loop2 = setInterval(draw_tomatoro, 40);
	}
	
	this.startstop = function(){
		if(self.STARTED === 0)
			self.start_clock();
		else
			self.stop_clock();
	}
	
	this.start_clock = function(){
		self.start_time = new Date;
		self.STARTED = 1;
		update_clock();
		// stop sound
		self.snd.pause();
		self.snd.load();
	}
	
	this.stop_clock = function(){
		self.STARTED = 0;
		self.current_time = seconds_to_time(self.TOMATORO_PERIOD);
	}
	
	this.push_character = function(key){
		if (key === "Backspace")
			self.task_name = (self.task_name.length > 0) ? self.task_name.slice(0, -1) : self.task_name;
		else if (key === "Alt" || key === "Control" || key === "Shift" || key === "Tab" || key === "Esc") //TODO : More keys !
			return;
		else if (key === "Enter")
			self.startstop();
		else if (key === "Spacebar")
			self.task_name += " ";
		else if (key === "Del" || key === "Delete")
			clear_history();
		else
			self.task_name += key;
	}
	
	// ---- UPDATE ----
	
	var update_clock = function(){
		if(self.STARTED === 1){
			var time_in_seconds = self.TOMATORO_PERIOD - Math.round((new Date() - self.start_time) / 1000);
			if (time_in_seconds >= 0){
				self.current_time = seconds_to_time(time_in_seconds);
			}else{
				self.STARTED = 0;
				save_tomato();
				// play sound
				self.snd.play();
			}
		}
	}
	
	// ---- DRAW ---- 
	
	var draw_background = function(){
		// timer
		self.ctx1.fillStyle = WHITE;
		self.ctx1.fillRect(0,0,self.layer1.width,self.timer_height);
		// timeline 
		self.ctx1.fillStyle = RED;
		self.ctx1.fillRect(0,self.timer_height,self.layer1.width,self.layer1.height);
		//OLD
		//self.i = ((++self.i) % 250);
		//self.ctx1.fillStyle = 'rgba(250,'+self.i+','+self.i+',1)';
		//self.ctx1.fillRect(0,0,self.layer1.width,self.layer1.height);
	}
	
	var draw_tomatoro = function(){
		draw_clock();
		draw_timeline();
		draw_status();
	}
	
	var draw_clock = function(){
		self.ctx2.clearRect(0,0,self.layer1.width,self.layer1.height);
		self.ctx2.fillStyle = RED;
		var x = self.ctx2.canvas.width / 2;
		
		//name
		if (self.task_name !== ""){
			self.ctx2.font = self.FONT_MEDIUM;
			var y = Math.floor(self.layer1.height / (11*PHI));
			self.ctx2.fillText(self.task_name, x, y);
		}
		
		// time
		self.ctx2.font = self.FONT_FAT;
		var y = Math.floor(self.layer1.height / (2*PHI));
		self.ctx2.fillText(self.current_time, x, y);
		
		// message
		self.ctx2.font = self.FONT_BIG;
		y = Math.floor(self.timer_height / PHI + (self.timer_height / 6 * PHI));
		var message = (self.STARTED === 0) ? self.START_MESSAGE : self.CANCEL_MESSAGE;
		self.ctx2.fillText(message, x, y);
	}
	
	var draw_status = function(){
        self.ctx2.fillStyle = BLACK;
		self.ctx2.font = self.FONT_MEDIUM;
		var x = self.ctx2.canvas.width / 2;
		var y = 24;
		self.ctx2.fillText(self.status, x, y);
		
		//x = self.ctx.canvas.width / 2;
		//y = self.timer_height + (timeline_height - self.chart_height )/ 3;
		//var message = self.chart_width + " x " + self.chart_height;
		//if(self.completed_tomatoes.length > 0)
		//	message = JSON.stringify(self.completed_tomatoes);
		//self.ctx.fillText(message, x, y);
    }
	
	var draw_timeline = function(){
		self.chart.draw();
		//draw_status();
	}
	
	// ---- Helpers ----
	
	var add_leading_zero = function(number){
		return (number > 9) ? number : "0" + number;
	}
	
	var seconds_to_time = function(time_in_seconds){
		var minutes = add_leading_zero(Math.floor(time_in_seconds / 60));
		var seconds = add_leading_zero(time_in_seconds % 60);
		return minutes + " : " + seconds;	
	}
	
	var get_formated_date = function (date) {
	    var day = date.getDate();
	    var month = date.getMonth() + 1;
	    return month + '/' + day;
	}
	
	var save_tomato = function (){
		var completed_tomato = new CompletedTomato(get_formated_date(new Date()),self.task_name);
		self.completed_tomatoes.push(completed_tomato);
		self.chart.add_chart_value(new ChartValue(completed_tomato.date,completed_tomato.name, 1));
		
		bake_cookie("tomatoes",self.completed_tomatoes);
	}
	
	var load_tomatoes = function(){
		self.completed_tomatoes = read_cookie("tomatoes");
		if (typeof self.completed_tomatoes !== 'undefined' && self.completed_tomatoes !== null){
			self.chart.clear();
			var completed_tomato = null;
			for (var i = 0; i < self.completed_tomatoes.length; i++) {
				completed_tomato = self.completed_tomatoes[i];
				self.chart.add_chart_value(new ChartValue(completed_tomato.date,completed_tomato.name, 1));
			}	
		}
		else{
			self.completed_tomatoes = [];
		}
	}
	
	var clear_history = function(){
		delete_cookie("tomatoes");
		load_tomatoes();
		self.chart.clear();
	}
	
	// initialize !!
	this.init();
}
