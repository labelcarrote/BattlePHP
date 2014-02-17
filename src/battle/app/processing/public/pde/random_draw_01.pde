/* @author P2B
  - Random Draw 01 -
  click to draw random ellipses.
*/

int x,y,h,w;
int r,g,b;
int bool,br,bg,bb;

void setup()
{
  x=0;
  y=100;
  h=10;
  w=10;
  r=0;
  g=70;
  b=148;
  bool=1;
  br=1;
  bg=1;
  bb=1;
  
  size(400,400);
  background(255,255,255);
  smooth();
  noStroke();
}

void draw()
{
  //red evolution
  if(r>255 ||r<0){
    br = -br; 
  }
  r = r + (br * 1);
  
  //green evolution
  if(g>255 ||g<0){
    bg = -bg; 
  }
  g = g + (bg * 2);
  
  //blue evolution
  if(b>255||b<0){
    bb = -bb; 
  }
  b = b + (bb * 3);
  
  //heigth evolution
  if(h>62 || h<2){
    bool = -bool;  
  }
  h = h + bool*1;  
  
  //width evolution
  if(w>62 || w <2){
    bool = -bool;  
  }
  w = w + bool*1;
  
  //draw
  fill(r,g,b); 
  if(mousePressed ==true){
    ellipse(mouseX,mouseY,h,w);
  }
}
  
