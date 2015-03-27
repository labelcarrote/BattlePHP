/* 
  -------------------------
  ---- Balle Au Panier ----
  -------------------------
  @author P2B, JOSEPH DEAN
*/
import processing.opengl.*;
import ddf.minim.*;
import ddf.minim.signals.*;
import ddf.minim.analysis.*;
import ddf.minim.effects.*;

// game 
final int MAGICSAND = 248; // WARNING : mettre la vraie valeur
final int W = 600;// screen width
final int H = 300;// screen height
String fontName = "Arial-Black-40.vlw";
String fontNameSmall = "Arial-Black-30.vlw";
String fontNameTitle = "Arial-Black-65.vlw";
PFont fontA;
PFont fontB;
PFont fontTitle;
PImage[] backgrounds = new PImage[9];
int timerFlash;
int status = 0;
final int STATUS_TITLE = 0;
final int STATUS_GAME = 1;
final int STATUS_ENDGAME = 2;
final int STATUS_PAUSE = 3;
int lastTime;
int time;
int endGameFrameId;
int r, g, b;// couleur pour les ecrans de titre et de fin de jeu
int br, bg, bb;
int numBonus;// nombre de coup bonus
int nbBasket;// nombre de panier reussi

// son
SoundManager sound;

// mouse
int mouseX0, mouseY0; // position du click
int mouseX1, mouseY1; // position du release
int mouseX2, mouseY2; 
boolean move;
boolean released;
boolean overLeftButton = false;
boolean overRightButton = false;

// keyboard
int action;
final int ACTION_UNFREEZE = 0;
final int ACTION_FREEZE = 1;
int timerAction;
boolean actionBonus;
int timerBonus;
boolean actionRedo;

// gun 
Gun gun;

// balles
final int MAXBALLS = 10;
int numBalls;
int newBallId;
Ball[] balls;
int statMaxHit;// plus grand nombre de hit de bumper pendant un shoot
int statMaxAirCombo;// plus grand combo aerien de la partie
int statAirCombo;// combo actuel
int statBestShot;// meilleur shoot de la partie
int statAlleyHoop;// nombre de alleyhoop de la partie

// panier
Panier pani;

// bumpers
final int MAXBUMPERS = 4;
int numBumpers;
Bumper[] bumpers;

// particules
final int MAXPARTICULES = 1000;
int numParticules;
Particule[] particules;
boolean lines = true;

//-----------------------------------------------
//-------------------- SETUP --------------------
//-----------------------------------------------
void setup(){
  //size(600, 300, OPENGL);//JOGL
  size(600, 300);//Java2D
  //size(W, H, P3D);//Java3D
  
  // son
  sound = new SoundManager(this);
  
  // polices
  fontA = loadFont(fontName);
  fontB = loadFont(fontNameSmall);
  fontTitle = loadFont(fontNameTitle);

  // backgrounds
  backgrounds[0] = loadImage("app/balleaupanier/public/img/bap00.png");
  backgrounds[1] = loadImage("app/balleaupanier/public/img/bap01.png");
  backgrounds[2] = loadImage("app/balleaupanier/public/img/bap02.png");
  backgrounds[3] = loadImage("app/balleaupanier/public/img/bap03.png");
  backgrounds[4] = loadImage("app/balleaupanier/public/img/bap04.png");
  backgrounds[5] = loadImage("app/balleaupanier/public/img/bap05.png");
  backgrounds[6] = loadImage("app/balleaupanier/public/img/bap06.png");
  backgrounds[7] = loadImage("app/balleaupanier/public/img/bap07.png");
  backgrounds[8] = loadImage("app/balleaupanier/public/img/bap00.png");
  
  // init  
  textFont(fontA, 40);
  initTitle();

}

//----------------------------------------------
//-------------------- INIT --------------------
//----------------------------------------------
void initTitle(){
  status = STATUS_TITLE;
  initRGB();
  initParticules();
  endGameFrameId = 500;
  sound.playSong(status);
}

void initEndGame(){
  status = STATUS_ENDGAME;
  endGameFrameId = 0;
  sound.playSong(status);
}

void initGame(){
  status = STATUS_GAME;
  
  // time
  time = 0;
    
  // gun
  gun = new Gun();
  
  // balles
  numBalls = 0;
  newBallId = 0;
  balls = new Ball[MAXBALLS];
  statMaxHit = 0;
  statMaxAirCombo = 0;
  statAirCombo = 0;
  statBestShot = 0;
  statAlleyHoop = 0;
  nbBasket = 0;
  
  // panier
  pani = new Panier(500,100,560,100,20);
  
  // bumpers
  numBumpers = 0;
  bumpers = new Bumper[MAXBUMPERS];
  bumpers[0] = new Bumper(200, 250, 20, 1);
  bumpers[1] = new Bumper(300, 200, 30, 2);
  bumpers[2] = new Bumper(400, 150, 40, 4);
  numBumpers += 3;
  
  // particules
  initParticules();
  
  // mouse
  mouseX0 = 0;
  mouseY0 = 0;
  move = false;
  released = false;
  
  timerAction = 0;
  actionBonus = false;
  timerBonus = 0;
  numBonus = 0;
  actionRedo = false;
  timerFlash = 255;

  // son
  sound.playSong(status);
}

void initRGB(){  
  r = 0;
  g = 70;
  b = 148;
  br = 1;
  bg = 1;
  bb = 1;  
}

void initParticules(){
  numParticules = 0;
  particules = new Particule[MAXPARTICULES];
  for(int i = 0; i < MAXPARTICULES; i++){
    particules[i] = new Particule();
    particules[i].x = random(W);
    particules[i].y = random(H);
    particules[i].vx = random(-1, 1);
    particules[i].vy = random(-1, 1);
    numParticules++;
  }
}

//-----------------------------------------------
//-------------------- MOUSE --------------------
//-----------------------------------------------
void mousePressed(){ 
  move = true; 
  if(status == STATUS_ENDGAME){
    if(overLeftButton){
	  int magicKey = keyCalculate();
      link("http://www.labelcarrote.com/artistes/p2b/processing/bap/index.php?action=submit&score="+pani.score+"&maxhit="+statMaxHit+"&bestshot="+statBestShot+"&aircombo="+statMaxAirCombo+"&alleyhoop="+pani.alleyHoop+"&time="+time+"&key="+magicKey, "_new");
    }else if(overRightButton){
      //link("http://www.processing.org", "_new");
    }
  }
}

void mouseReleased(){ 
  if(status == STATUS_TITLE){
    //initGame();
  }else if(status == STATUS_ENDGAME){
    if(overRightButton){
      initGame();  
    }
  }else if(status == STATUS_GAME){ 
    mouseX1 = mouseX;
    mouseY1 = mouseY;
    move = false; 
    released = true;
  }  
}

void mouseMoved(){ 
  if(status == STATUS_ENDGAME){
    checkButtons(); 
  }
}
  
void mouseDragged() {
  if(status == STATUS_ENDGAME){
    checkButtons(); 
  }
}

void checkButtons() {
  //carre submit
  //rect(65,230, 235, 65);
  if(mouseX > 65 && mouseX < 300 && mouseY > 230 && mouseY < 295){
    overLeftButton = true; 
  //carre retry  
  //rect(350,230, 180, 65);
  }else if(mouseX > 350 && mouseX < 520 && mouseY > 230 && mouseY < 295){
    overRightButton = true; 
  } else {
    overLeftButton = overRightButton = false;
  }
}
//--------------------------------------------------
//-------------------- KEYBOARD --------------------
//--------------------------------------------------
void keyPressed(){
  float x = 300;
  float v = 30;
  if(key == 'd' || key == 'D'){
	sound.playSfxBallBumper(x, v, 0);
    //sound.playSfxBallContact(x, v);
  }else if(key == 'f' || key == 'F'){
	sound.playSfxBallBumper(x, v, 1);
    //sound.playSfxBallPanier(x, v);
  }else if(key == 'g' || key == 'G'){
	sound.playSfxBallBumper(x, v, 2);
    //sound.playSfxBallWallLeft(x, v);
  }else if(key == 'j' || key == 'J'){
    sound.playSfxBallContact(x, v);
  }else if(key == 'k' || key == 'K'){
    sound.playSfxBallPanier(x, v);
  }
  /*else if(key == 'l' || key == 'L'){
    sound.playSfxBallWallLeft(x, v);
  }*/
  else
  if(key == 't' || key == 'T'){//go to title
    initTitle();
  }else if(key == ' '){
    if(status == STATUS_TITLE){//start
      initGame();
    }else if(status == STATUS_GAME){// freeze-unfreeze ball   
      action = (action == ACTION_FREEZE) ? ACTION_UNFREEZE : ACTION_FREEZE;
      timerAction = 255;   
    }
  }else if(key == 'b' || key == 'B'){// use bonus
    if(status == STATUS_GAME){ 
      actionBonus = true;
      if(numBonus > 0){
        timerBonus = 255;
      }
    }else if(status == STATUS_TITLE){
      sound.playSfxBallGround(x, v);
    }
  }else if(key == 'p' || key == 'P'){// pause
    if(status == STATUS_GAME){
      status = STATUS_PAUSE;
    }else if(status == STATUS_PAUSE){
      status = STATUS_GAME; 
    }
  }else if(key == 'n' || key == 'N'){// redo
      if(status == STATUS_TITLE)
        sound.playSfxBallWallRight(x, v);
      else
        actionRedo = true;
  }else if (key == 'r' || key == 'R'){//retry
    initGame();
  }else if (key == 's' || key == 'S'){// sound on/off
    sound.switchState();
  }
}

//----------------------------------------------
//-------------------- DRAW --------------------
//----------------------------------------------

//---- LOOP ----
void draw(){
  if(status == STATUS_TITLE){
    // update title elements
    updateTitle();
    // background
    drawBackGround(); 
    // title
    drawTitle(); 
  }else if(status == STATUS_GAME){    
    // update game elements
    updateGame();
    // background
    drawBackGround();
    // draw game elements
    drawGame(); 
    
  }else if(status == STATUS_PAUSE){
    drawBackGround();
    drawGame(); 
    drawPause();
  }else{      
    drawEndGame();
  }  
}

void drawBackGround(){
  if(status == STATUS_GAME || status == STATUS_PAUSE){    
    image(backgrounds[nbBasket], 0, 0, 600, 300);
  
  }else if(endGameFrameId < 255){
    fill(0, endGameFrameId);
    noStroke();
    rect(0, 0, W, H);
    endGameFrameId += 5;
  
  }else{
    background(0);  
  }
}

void drawFlash(){
  if(timerFlash > 0){     
    fill(60, 0, 140, timerFlash);
    timerFlash -= 10;
    noStroke();
    rect(0, 0, W, H);
  }
}

void changeRGB(){
  //red evolution
  if(r > 255 || r < 0){
    br = -br; 
  }
  r = r + (br * 1);  
  //green evolution
  if(g > 255 || g < 0){
    bg = -bg; 
  }
  g = g + (bg * 2);
  //blue evolution
  if(b > 255 || b < 0){
    bb = -bb; 
  }
  b = b + (bb * 3); 
}

void drawTitle(){ 
  drawParticules();
  changeRGB();
  textFont(fontTitle, 65);
  fill(r, g, b);
  text("Balle Au Panier", 75, 140); 
  textFont(fontTitle, 45); 
  text("Press Space", 170, 210);
}

void drawPause(){
  changeRGB();
  textFont(fontTitle, 65);
  fill(r, g, b);
  text("Pause", 10, 100);    
}

void drawGame(){
  drawTime();
  drawBalls();
  drawGun();
  drawMouse();
  drawPanier();
  drawBumpers();
  drawParticules();
  drawGameInfo();  
}

void drawTime(){
  /*stroke(255,200);
  ellipse(W,H,time,time);
  stroke(255,255);*/
}

void drawGun(){
  gun.display();
}

void drawBalls(){

  if(numBalls > 2){
    balls[numBalls - 3].display();
  }
  
  //ancienne balle
  if(numBalls > 1){
    balls[numBalls - 2].display();
  }
  //nouvelle balle
  if(numBalls != 0){
    balls[numBalls - 1].displaySpecial();
  }
}

void drawMouse(){
  if(mouseX0 != 0 && mouseY0 != 0){
    line(mouseX0, mouseY0, mouseX, mouseY);
  }
}

void drawPanier(){
  pani.display();  
}

void drawBumpers(){
  for (int i = 0; i < numBumpers; i++) {
    bumpers[i].display();
  }  
}

void drawParticules(){
  //changeRGB();
  //stroke(r,g,b, 255);
  stroke(255);
  for(int i = 0; i < numParticules; i++){
    particules[i].display();
  }
}

void drawGameInfo(){
  textFont(fontB, 30);
  fill(255, 204);
  if(numBalls == 8){
	fill(255, 0, 0, 255);
  }
  text("Ball " + (MAXBALLS - numBalls - 2), 475, 230);
  fill(255, 204);
  if(numBonus == 3){
	fill(255, 255);
  }
  text("Bon " + numBonus, 475, 260);
  fill(255, 204);
  if(timerAction > 0){     
    fill(255, timerAction);
    timerAction -= 5;
    if(action == ACTION_FREEZE){
      text("LOCKED", 430, 40);    
    }else{
      text("UNLOCKED", 400, 40);
    }
  }
  if(timerBonus > 0){
    fill(255, timerBonus);
    timerBonus -= 5;
    text("GRAVITY?", 410, 40);
  }else{
    timerBonus = 0;  
  }
  // last ball !
  if(numBalls == 7){
  
  }
  // no more valuable ball !
  else if(numBalls == 8 && !balls[numBalls - 2].isValuable && !balls[numBalls - 1].isValuable && !balls[numBalls - 3].isValuable){
	textFont(fontTitle, 65);
	fill(255, 204);
	text("Game's Over!", 10, 110);
	text("Click It!", 60, 170);
  }
}

void drawEndGame(){
  // fondu vers noir (temporisation)
  if(endGameFrameId < 255){
    drawBackGround();
  }else{
    drawBackGround();
    changeRGB();
    textFont(fontA, 50);
    if(overLeftButton){
      stroke(#666666);
      noFill();
      rect(65, 230, 235, 65);//carre submit
    }
    if(overRightButton){
      stroke(#666666);
      noFill();
      rect(350, 230, 180, 65);//carre retry
    }
    fill(r, g, b);
    text("Score : " + pani.score, 15, 50);  
    text("Best Shot : " + statBestShot, 15, 90);
    text("Max Hits : " + statMaxHit, 15, 130);
    text("Air Combo : " + statMaxAirCombo, 15, 170);
    text("Time : " + time, 15, 210);
    text("Submit!", 75, 280);
    text("Retry!", 360, 280);
    
    // fondu vers texte
    if ((255 <= endGameFrameId) && (endGameFrameId < 510)){
      fill(0, 510 - endGameFrameId);
      noStroke();
      rect(0, 0, W, H);
      endGameFrameId += 2;
    }
  }
}

//------------------------------------------------
//-------------------- UPDATE --------------------
//------------------------------------------------
void updateTitle(){
  
  // creation de particules en reaction a la musique
    float levelL = sound.songTitleLeftLevel();
    float levelR = sound.songTitleRightLevel();
    if(levelL * 500 > 120 || levelR * 500 > 120){
      int i = 0;
      float x = random(-20, W - 50);
      float y = random(-20, H - 50);
      int nbPartic = 0;
      float speed = random(8, 30);
      while(nbPartic < 100 && i < MAXPARTICULES){
        if(particules[i].status == particules[i].READY){
          particules[i].go(x, y, speed);
          nbPartic++;
        }
        i++;
      }
    }
    updateParticules();
}

void updateGame(){
  if(numBalls == MAXBALLS - 1){ 
    initEndGame();
  }else{
    updateTime();
    updateGun();
    updateBalls();
    updateParticules();
  }
}

void updateTime(){
  if(lastTime != second()){
    time++;
    lastTime = second();
  }
}

void updateGun(){
  gun.update();
}

void updateBalls(){  
  // nouvelle balle
   if(released || actionRedo){
     float[] ballInit = gun.getLaunchProperty();
     if(numBalls < MAXBALLS - 1){
       balls[numBalls] = new Ball(ballInit[0], ballInit[1], ballInit[2], ballInit[3], 40, numBalls, balls, pani, bumpers);
       numBalls++;
       pani.initScoreNow();
     }
     if(numBalls >= 2){
       balls[numBalls - 2].isOldGhost = true;
     }
     released = false;
     actionRedo = false;
     mouseX0 = 0;
     mouseY0 = 0;
     action = ACTION_UNFREEZE;
   }
   
   if(numBalls > 2){
     balls[numBalls - 2].collide();
     balls[numBalls - 3].collide();
     if(action != ACTION_FREEZE){
       balls[numBalls - 2].move();
       balls[numBalls - 3].move();
     }
   }else if(numBalls > 1){
     balls[numBalls - 2].collide();
     if(action != ACTION_FREEZE){
       balls[numBalls - 2].move();
     }
   }
   
   if(numBalls != 0){
     balls[numBalls - 1].collide();
     balls[numBalls - 1].move();
   }    
   if(actionBonus){ 
     numBonus = (numBonus > 0) ? numBonus - 1 : 0;
   }
   actionBonus = false;
}

void updateParticules(){
  for(int i = 0; i < numParticules; i++){
    particules[i].move();
  }
}

//------------------------------------------------
//-------------------- UTILS --------------------
//------------------------------------------------

int keyCalculate(){
	//int key = pani.score + statMaxHit + statBestShot + statMaxAirCombo + pani.alleyHoop + time / magicSand;
	int key = (pani.score + statBestShot + time + MAGICSAND) / MAGICSAND;
	return key;
}

void stop()
{
  sound.closeSound();

  super.stop();
}
