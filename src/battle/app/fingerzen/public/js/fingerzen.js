// -----------------------
// FingerZen.js
// -----------------------

var BLACK = 'rgba(0,0,0,0.1)';
var WHITE = 'rgba(255,255,255,1)';
var GREY  = 'rgba(32,32,32,1)';
var RED = 'rgba(250,50,0,1)';//#FF3200

var FONT_MEDIUM = '24px "Helvetica"';
var FONT_FAT    = '216px "Helvetica"';

$(window).load(function(){

	var finger_zen = null;
	
	// Retrieves some words from server, then load
	$.ajax({url:"fingerzen/home/words", success:function(result){
		finger_zen = new FingerZen(eval(result));
	}});
});

	
// -----------------------
// FingerZen object
// -----------------------
function FingerZen(text){
	var self = this;
	
	this.words = text;
	
	this.screen = null;
	this.status = "";
	this.width  = 960;
	this.height = 690;
	
	// layers / canvases 
	this.layer1 = document.getElementById("zen");
	this.ctx = self.layer1.getContext("2d");
	this.layer2 = document.getElementById("zen1");
	this.ctx1 = self.layer2.getContext("2d");
	
	this.init = function(){
		
		self.resize();
		
		// Set text settings
		self.ctx.textBaseline = "top";
		self.ctx.textAlign = "left";
		self.ctx1.font = FONT_MEDIUM;
		
		self.screen = new GameScreen(self);
		
		// Events / Inputs
		$(document).mousedown(function(e){
			self.screen.on_mouse_down(e);
		});
		
		document.addEventListener("keydown",function(e){
			e = e || event;
			/*if(typeof e.key === 'undefined' || e.key.lastIndexOf("F", 0) === 0)
				return;*/

			/*alert(e.key + " , " + e.keyCode + " , " +e.charCode);
			self.status = e.key + " " + e.keyCode + e.charCode;*/
			if (e.key === "Backspace" || e.key === "Enter" || e.key === "Del" || e.key === "Delete"
				|| e.key === "Space" || e.key === "."){
				e.preventDefault();
				self.screen.push_character(e.key);
			}else{
				var key;
				switch(e.keyCode){
					case 8  : key = "Backspace";break;
					case 37 : key = "Left";break;
					case 38 : key = "Up";break;
					case 39 : key = "Right";break;
					case 40 : key = "Down";break;
				}
				if(typeof key !== 'undefined'){
					e.preventDefault();
					self.screen.push_character(key);
				}
			}
		});
	
		document.addEventListener("keypress",function(e){
			var key = (typeof e.key !== 'undefined')
				? e.key
				: (e.which !== 0) ? String.fromCharCode(e.which) : e.keyCode;
			//write name
			var code = (e.which !== 0) ? e.which : e.keyCode;
			switch (code){
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
			self.screen.push_character(key);
			self.status = "key : " + e.key +" "+ e.keyCode +" "+ e.charCode;
			e.preventDefault();
			return false;
		});
		
		// redraws if window is resized        
		$(window).resize(function() {
			//finger_zen.resize(); INFINITE BPM !!! WHHAAAAAATTT?????
		});	
	}
	
	this.resize = function(){
		self.ctx.canvas.width  = window.innerWidth;
		self.ctx.canvas.height = 900;//window.innerHeight;
		self.ctx1.canvas.width  = window.innerWidth;
		self.ctx1.canvas.height = 900;
		
		self.width  = self.ctx1.canvas.width;
		self.height = self.ctx1.canvas.height;
	}
	
	this.init();
}


// -----------------------
// FingerZen object
// -----------------------
function GameScreen(game_ctx){
	var self = this;
	this.game = game_ctx;
	
	
	var MINUTE_IN_MS = 60000; // 60 seconds * 1000 ms = 60000 ms
	var words_index = 0;
	
	this.text_to_type = "using System;\n"
		+ "class HelloWorld{\n"
		+ "    public static void Main(){\n"
		+ "        Console.WriteLine(\"Hello, world!\");\n"
		+ "    }\n"
		+ "}";
	
	this.text_typed = "";	
	this.current_char_index = 0;

	var date_start_typing = null;
	var date_end_typing = null;
	var time_to_end = 0;
	var end_stats = "";
	var lastTime = new Date().getTime();
	var score = 0;
	
	var lineheight = 32;
	var SCORE_HEIGHT = 28;
	
	this.init = function(){
		// Loops
		setInterval(draw_random_words, 1500);
		setInterval(self.draw, 40);
	}
	
	// Events
	var now = new Date().getTime();
	var laps = new Date();
	this.on_mouse_down = function(e){
		now = new Date().getTime();
		laps = now - self.lastTime;
		self.lastTime = now;
		draw_flash();
	}
	
	this.push_character = function(key){
		if (self.current_char_index === 0)
			self.date_start_typing = new Date();
			
		if (self.current_char_index === self.text_to_type.length){
			self.date_end_typing = new Date();
			self.time_to_end = Math.floor(Math.abs(self.date_end_typing - self.date_start_typing) / 1000);
			var char_per_seconds = self.text_to_type.length / self.time_to_end ;
			self.end_stats = self.time_to_end + "s "+ char_per_seconds + "c/s";
			
			// go to stat screen
			//...
		}
		
		if (key === "Backspace")
			self.text_typed = (self.text_typed.length > 0) ? self.text_typed.slice(0, -1) : self.text_typed;
		else if (key === "Alt" || key === "Control" || key === "Shift" || key === "Tab" || key === "Esc") //TODO : More keys !
			return;
		else if (key === "Enter"){
			if ('\n' === self.text_to_type.charAt(self.current_char_index))
				self.current_char_index = self.current_char_index + 1;
			self.text_typed += "\n";
		}else if (key === "Spacebar"){
			while (' ' === self.text_to_type.charAt(self.current_char_index))
				self.current_char_index = self.current_char_index + 1;	
			self.text_typed += " ";
		}else{
			if (key.charAt(0) === self.text_to_type.charAt(self.current_char_index))
				self.current_char_index = self.current_char_index + 1;
			self.text_typed += key;
		}
		
		//draw_score();
		score++;
		draw_text_somewhere(key, WHITE);
		self.draw();
	}
	
	// ---- DRAW ----

	this.draw = function(){
		draw_fade();
		self.game.ctx1.clearRect(0,0,self.game.width,self.game.height);
		draw_colored_text(self.game.ctx1,self.text_to_type,self.current_char_index, RED, WHITE);
		draw_score();
	}
	
	var draw_flash = function(){
		// white rectangle
		self.game.ctx.fillStyle = WHITE;
		self.game.ctx.fillRect(0, 0, self.game.width, self.game.height);
		// black bpm in the center 
		self.game.ctx.font = FONT_FAT;
		draw_text(self.game.ctx,getCurrentBPM() + " bpm", 60, (self.game.height / 2) - 120, BLACK);
	}

	var draw_fade = function(){
		self.game.ctx.fillStyle = 'rgba(0,0,0,0.1)';
		self.game.ctx.fillRect(0, 0, self.game.width, self.game.height);
	}
	
	var draw_colored_text = function(context,text,index_char_for_other, color, other_color){
		if (typeof text !== 'undefined'){
			var lines = text.split('\n');	
			context.fillStyle = color;
			var k = 0;
			var posx = (self.game.width / 2) - (self.game.ctx1.measureText(lines[0]).width);
			var posy = 60;
			var x = posx;
			var y = posy;
			
			for (var i = 0; i < lines.length; i++){
				for(var j = 0; j <= lines[i].length; ++j){
					if (k === self.current_char_index)
						context.fillStyle = other_color;
					var ch = lines[i].charAt(j);
					context.fillText(ch, x, y + i*lineheight);
					x += context.measureText(ch).width;
					k++;
				}
				x = posx;
			}	
		}
	}
	
	var draw_status = function() {
		draw_text(self.game.ctx,self.game.status, 400, 100, WHITE);
	}
	
	var draw_random_words = function(){
		words_index = (words_index < self.game.words.length - 1) ? words_index + 1 : 0;
		draw_text_somewhere(self.game.words[words_index], WHITE);
	}
	
	var draw_score = function(){
		// background
		self.game.ctx1.fillStyle = WHITE;
		self.game.ctx1.fillRect(0, self.game.height - self.SCORE_HEIGHT, self.game.width, self.game.height);
		// score
		self.game.ctx1.font = FONT_MEDIUM;
		draw_text(self.game.ctx1,"bpm: " + getCurrentBPM(), 8, self.game.height - 32, WHITE);
		draw_text(self.game.ctx1,"#" + score, 900, self.game.height - 32, WHITE);
		draw_text(self.game.ctx1,"score: " + self.end_stats, 150, self.game.height - 32, RED);
	}
	
	var draw_text = function(context,text,x,y,color){
		if (typeof text !== 'undefined'){
			var lines = text.split('\n');	
			context.fillStyle = color;
			for (var i = 0; i < lines.length; i++)
				context.fillText(lines[i], x+32, y + i * lineheight);
		}
	}

	var draw_text_somewhere = function(word,color){
		self.game.ctx.font = FONT_MEDIUM;
		var x = (self.game.width - 200) * Math.random();
		var y = (self.game.height - 24) * Math.random();
		draw_text(self.game.ctx,word,x,y,color);
	}
	
	// ---- Helpers ----
	
	var getCurrentLaps = function(){
		return laps;
	}

	var getBPM = function(ms){
		return Math.floor(MINUTE_IN_MS / ms);
	}

	var getCurrentBPM = function(){
		return getBPM(getCurrentLaps());
	}
	
	this.init();
}