"use strict";
// -----------------------
// Pansho.js
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

var s = Snap("#svg");
/*var bigCircle = s.circle(150, 150, 100);*/
var g = s.group();

var chien = Snap.load("app/pansho/public/svg/chienos.svg", function (f) {
    var gr = f.select("g");
    g.append(gr);
    gr.drag();
});

// Sun events
raysAnimation();

// Infinite animation for the sun rays
function raysAnimation(){
	g.stop().animate(
		{ transform: 'r360,400,400'}, // Basic rotation around a point. No frills.
		8000, // Nice slow turning rays
		function(){ 
			g.attr({ transform: 'rotate(0 400 400)'}); // Reset the position of the rays.
			raysAnimation(); // Repeat this animation so it appears infinite.
		}
	);

}