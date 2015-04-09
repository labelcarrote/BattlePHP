/*
-----------------------------------
---------- CLASSE BUMPER ----------
-----------------------------------
*/

class Bumper {
  int value;
  int x, y;
  int diameter;
  boolean bigBump;
  int timer;
  
  int idComment;
  final int NBCOMMENT = 13;
  String tabComment[];
 
  Bumper(int px, int py, int diam, int val) {
    x = px;
    y = py;
    diameter = diam;
    value = val;
    bigBump = false;
    timer = 255;
    idComment = 0;
    tabComment = new String[NBCOMMENT];
    tabComment[0] = "BUMP!";
    tabComment[1] = "BUMP FOR MORE!";
    tabComment[2] = "BUMP DESU!";
    tabComment[3] = "MOTHER NATURE?";
    tabComment[4] = "BUMP THE HARD WAY!";
    tabComment[5] = "BUMP MY RIDE!";
    tabComment[6] = "CHEESU BUMP!";
    tabComment[7] = "SMOOTH BUMP!";
    tabComment[8] = "GEORGOUS BUMP!";
    tabComment[9] = "HOLLY BUMP!";
    tabComment[10] = "FEARSOME BUMP";
    tabComment[11] = "BUMP IT LIKE IT'S HOT!";
    tabComment[12] = "GOD BLESS THE BUMP.";
  }
  void bumpIt(){
    bigBump = true;
  }
  
  void move() {}
  
  void display(){
    if(bigBump){
      textFont(fontB, 30);
      if(timer > 0){     
        fill(255, timer);
        timer -= 5;
        text(tabComment[idComment], 10, 290);    
      }else{
        timer = 255;
        bigBump = false;
        idComment = int(random(NBCOMMENT));
      }
    }
    fill(255, 204);
    ellipse(x, y, diameter, diameter);
    fill(0);
  }
}
