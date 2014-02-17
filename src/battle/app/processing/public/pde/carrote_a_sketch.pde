
//drawing point 
int xp,yp;
int oldxp, oldyp;

//buttons
int b_size;

//drawing area
int x1,y1,x2,y2,x3,y3,x4,y4;

//words
int xt,yt;
String title;

int r,g,b;
int br,bg,bb;

void setup()
{
  xp = 248;  
  yp = 248;
  oldxp = xp;
  oldyp = yp;
  
  //drawing area
  x1 = 50;
  y1 = 55;
  x2 = 445;
  y2 = 50;
  x3 = 450;
  y3 = 295;
  x4 = 55;
  y4 = 300; 
 
  // buttons
  b_size = 50;
  
  // words
  xt = 180;
  yt = 350;
  title = "Carrote-a-Sketch";
  
  size(500,400);
  drawBackground();
  
}

void draw()
{
  //point / line
  stroke(0);
  point(xp,yp);
  //line(oldxp,oldyp,xp,yp);
  noStroke();
}

void drawBackground()
{
  smooth();
  noStroke();
  
  // background 
  //background(240,30,0);
  background(#ff9900);
  
  // buttons
  fill(200);
  ellipse(50,350,b_size+7,b_size+7);
  ellipse(450,350,b_size+7,b_size+7);
  fill(255);
  ellipse(50,350,b_size,b_size);
  ellipse(450,350,b_size,b_size);
  
  // drawing area
  fill(155);
  stroke(200);
  
  beginShape();
  //top left
  vertex(x1,y1);
  bezierVertex(x1, y1, x1, y1-5, x1+5, y1-5);
  //top right
  vertex(x2,y2);
  bezierVertex(x2, y2, x2+5, y2, x2+5, y2+5);
  //bottom right
  vertex(x3,y3);
  bezierVertex(x3, y3, x3, y3+5, x3-5, y3+5); 
  //bottom left
  vertex(x4,y4);
  bezierVertex(x4, y4, x4-5, y4, x4-5, y4-5);
  vertex(x1,y1);
  endShape( );
  
  //title
  fill(#000000);
  PFont f = loadFont("Univers45.vlw");
  textFont(f, 20);
  text(title, xt, yt);
  textFont(f, 16);
  text("use d,f,j,k to carrote a sketch!", xt-26, yt+20);
}

void keyPressed()
{
  if( key == 'd' || key == 'D') {
    if(xp > x1){
      oldxp = xp;
      xp--;
    }
  }
  if( key == 'f' || key == 'F') {
    if(xp < x2){
      oldxp = xp;
      xp++;
    }
  }
  if( key == 'j' || key == 'J') {
    if(yp > y1){
      oldyp = yp;
      yp--;
    }
  }
  if( key == 'k' || key == 'K') {
    if(yp < y3){
      oldyp = yp;
      yp++;
    }
  }
}
  
