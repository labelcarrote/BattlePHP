"use strict";
// -----------------------
// PartyCul.js
// -----------------------
var PHI = (1+ Math.sqrt(5))/2;// gold

// Colors
var BLACK = "#000000",
	GREY  = 'rgba(32,250,250)',
	RED = 'rgba(250,50,0,1)',//#FF3200
	WHITE = 'rgba(250,250,250,1)',
	RED = '#FF3200',
	RED1 = '#FF6540',
	RED2 = '#FF9980',
	RED3 = '#FFCCBF';



$(window).load(function(){	
	var game = new Game();
});

// -----------------------
// Mouse position object
// -----------------------
function MousePosition(x,y){
	this.x = x;
	this.y = y;
}


function get_mouse_position(e) {
	//http://www.quirksmode.org/js/events_properties.html#position
	var posx = 0,
		posy = 0;

	if(!e) var e = window.event;
	if(e.pageX || e.pageY){
		posx = e.pageX;
		posy = e.pageY;
	}
	else if(e.clientX || e.clientY){
		posx = e.clientX + document.body.scrollLeft
			+ document.documentElement.scrollLeft;
		posy = e.clientY + document.body.scrollTop
			+ document.documentElement.scrollTop;
	}
	// posx and posy contain the mouse position relative to the document
	return new MousePosition(posx,posy);
}

// -----------------------
// Game object
// -----------------------
function Game(){
	var self = this;
	
	// game settings
	var FPS = 60;

	// particules
	var MAXPARTICULES = 1000,
		particules_count = 0,
		particules = [],
		lines = true;
	
	//mouse events
	var last_click_pos = null,
		lastmousex = -1,
		lastmousey = -1,
		lastmousetime,
		mousetravel = 0;

	$('html').mousemove(function(e) {
		var mousex = e.pageX;
		var mousey = e.pageY;
		if (lastmousex > -1)
		 mousetravel += Math.max( Math.abs(mousex-lastmousex), Math.abs(mousey-lastmousey) );
		lastmousex = mousex;
		lastmousey = mousey;
	});
	
	// text properties
	var lineheight = 32;

	this.screen = null;
	this.width  = 960;
	this.height = 690;
	this.status = "";
	
	// layers / canvases 
	this.layer1 = document.getElementById("layer2");
	this.ctx = self.layer1.getContext("2d");

	this.init = function(){
		particules_count = 0;
	  	for(var i = 0; i < MAXPARTICULES; i++){
	    	particules[i] = new Particule(self);
	    	particules_count++;
	  	}

		self.resize();

		// Events / Inputs
		$(document).mousedown(function(e){
			self.on_mouse_down(e);
		});
		
		// Start the game loop
	    setInterval(self.loop, 0);

	    // redraws if window is resized        
		$(window).resize(function() {
			self.resize();
		});
	}

	// LOOP
	this.loop = (function(){
        var loops = 0, skipTicks = 1000 / FPS,
            maxFrameSkip = 10,
            nextGameTick = (new Date).getTime();

        return function(){
          loops = 0;

          while ((new Date).getTime() > nextGameTick) {
            //updateStats.update();
            self.update();
            nextGameTick += skipTicks;
            loops++;
          }
          self.draw();
        };
      })();

	this.update = function (){
		for(var i = 0; i < particules_count; i++)
	    	particules[i].move();
	}

	this.draw = function (){
		self.ctx.fillStyle = BLACK;
		draw_background();
		//draw_status();
		draw_particles();
	}

	function draw_text(text,x,y,color){
		if (typeof text !== 'undefined'){
			var lines = text.split('\n');	
			self.ctx.fillStyle = color;
			for (var i = 0; i < lines.length; i++)
				self.ctx.fillText(lines[i], x + 32, y + i * lineheight);	
		}
	}

	function draw_status(){
		var text = "width="+self.width+" , height="+self.height+", ";
		if(last_click_pos !== null)
			text += "mx="+last_click_pos.x+", my="+last_click_pos.y;
		draw_text(text,12,12,WHITE);
	}

	function draw_background(){
		self.ctx.clearRect(0,0,self.width,self.height);
	}

	function draw_particles(){
		self.ctx.strokeStyle = WHITE;
	  	for(var i = 0; i < particules_count; i++)
	    	particules[i].draw(self.ctx);
	}

	// EVENTS
	this.on_mouse_down = function(e){
		var i = 0,
			nbPartic = 0,
			speed = 62,
			position = get_mouse_position(e);
		
		last_click_pos = position;
			
		while(nbPartic < 100 && i < MAXPARTICULES){
	        if(particules[i].status === 0){//particules[i].READY){
	          particules[i].go(position.x, position.y, speed);
	          nbPartic++;
	        }
	        i++;
		}
	}

	this.resize = function(){
		self.ctx.canvas.width  = window.innerWidth;
		self.ctx.canvas.height = window.innerHeight;
		self.width  = self.ctx.canvas.width;
		self.height = self.ctx.canvas.height;
	}

	this.init();
}

function Particule(game_context){
	var self = this;
	
	var GRAVITY = 0.3,//0.4;
		READY = 0,
  		INSKY = 1;

  	var game = game_context;

  	this.status = READY;
  	this.x = 0;
  	this.y = 0;
  	this.x0 = 0;
  	this.y0 = 0;
  	this.vx = 0;
  	this.vy = 0;
  	this.lastx = 0;
  	this.lasty = 0;

  	this.go = function(gx,gy,power){
  		self.status = INSKY;
	    self.x = gx;
	    self.y = gy;
	    self.lastx = gx;
	    self.lasty = gy;
	    self.x0 = self.x + 1;
	    self.y0 = self.y + 1;
	    
	    var dx = self.x0 - self.x;
	    var dy = self.y0 - self.y;
	    var distSQ = dx * dx + dy * dy;
	    var dist = Math.sqrt(distSQ);
	    dist = (dist < .4) ? .4 : dist;
	    var force = power / distSQ;
	    var angle = Math.floor(Math.random() * 50);
	    self.vx += force * Math.cos(angle) / dist;
	    self.vy += force * Math.sin(angle) / dist;
  	}

  	this.move = function(){
  		if(self.status === INSKY){
			self.vy += GRAVITY;
			self.vx *= .95;
			self.vy *= .95;
			self.x += self.vx;
			self.y += self.vy;
			if(self.x > game.width || self.x < 0 || self.y > game.height)
				self.status = READY;
		}
  	}

  	this.draw = function(ctx){
  		if(self.status === INSKY){
			ctx.beginPath();
		    ctx.moveTo(self.x,self.y);
		    ctx.lineTo(self.x - self.vx, self.y - self.vy);
		    ctx.closePath();
		    ctx.stroke();
		}
  	}
}
