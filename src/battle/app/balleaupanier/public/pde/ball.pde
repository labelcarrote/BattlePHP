/*
---------------------------------
---------- CLASSE BALL ----------
---------------------------------

TODO :
- Virer les references vers le panier (variable + param du constructeur) vu que ca ne sert apparemment à rien (cf SoundManager sound)
*/

class Ball {
  
  final int MAXALLEYHOOP = 99;
  final float GRAVITY = 0.26;
  final float K = 0.3;
  float x, y, oldX, oldY;
  float diameter;
  final float VMAX = 25;
  float vx;
  float vy;
  float v;
  int id;
  boolean isValuable;
  boolean isOldGhost; // if is the oldest ball
  Ball[] others;
  Bumper[] bumpers;
  boolean[] isBumping;
  boolean isSuperBumping;
  boolean isSuperAerial;
  boolean isSuperSwitch;
  int hit;
  int superBumpHit;
  int alleyHoop;
  Panier pan;
  float highestPos;
  
  int i;
  int ballR;
  int ballG;
  int ballB;
  
  Ball(float xin, float yin, float vxin, float vyin, float din, int idin, Ball[] oin, Panier panier, Bumper[] bumps){
    this.init(xin, yin, vxin, vyin, din, idin, oin, panier, bumps);
  }
 
  Ball init(float xin, float yin, float vxin, float vyin, float din, int idin, Ball[] oin, Panier panier, Bumper[] bumps){
    x = xin;
    y = yin;
    oldX = xin;
    oldY = yin;
    vx = vxin;
    vy = vyin;
    v = mag(vx, vy);
    diameter = din;
    id = idin;
    isValuable = true;
    isOldGhost = false;
    others = oin;  
    pan = panier;
    bumpers = bumps;
    isBumping = new boolean[numBumpers];
    
    for(int i = 0; i < numBumpers; i++){
      isBumping[i] = false;
    }
    
    isSuperBumping = false;
    isSuperAerial = true;
    isSuperSwitch = true;
    hit = 0;
    superBumpHit = 0;
    alleyHoop = 0;
    highestPos = 0;
    
    ballR = 255;
    ballG = 255;
    ballB = 255;
    
    return this;
  }
  
  void collide() {
    
    // entre balles
    for (int i = id + 1; i < numBalls; i++) {
      float dx = others[i].x - x;
      float dy = others[i].y - y;
      float distance = sqrt(dx * dx + dy * dy);
      float minDist = others[i].diameter / 2 + diameter / 2;
      if(distance < minDist){
        float angle = atan2(dy, dx);
        float targetX = x + cos(angle) * minDist;
        float targetY = y + sin(angle) * minDist;
        float ax = K * (targetX - others[i].x);
        float ay = K * (targetY - others[i].y);
        if(action != ACTION_FREEZE){
          vx -= ax;
          vy -= ay;
        }
        if((abs(others[i].vx) < VMAX) && (abs(others[i].vy) < VMAX)){
          others[i].vx += ax;
          others[i].vy += ay;
        }
        float speed = (ax + ay) / 2;
        goParticules((x + others[i].x) / 2, (y + others[i].y) / 2, speed * 50);
        if(alleyHoop < MAXALLEYHOOP){
			alleyHoop++;
		}
        others[i].alleyHoop++;
        
        
        // son
        if(!others[i].isOldGhost) 
          sound.playSfxBallContact(x,v);
      }
      
    }       
    
    // avec bumpers
    for (int i = 0; i < numBumpers; i++) {
      float dx = bumpers[i].x - x;
      float dy = bumpers[i].y - y;
      float distance = sqrt(dx * dx + dy * dy);
      float minDist = bumpers[i].diameter / 2 + diameter / 2;
      if (distance < minDist) {
        float angle = atan2(dy, dx);
        float targetX = x + cos(angle) * minDist;
        float targetY = y + sin(angle) * minDist;
        float ax = K * (targetX - bumpers[i].x);
        float ay = K * (targetY - bumpers[i].y);
        float speed = (ax + ay) / 2;
        if((abs(vx) < VMAX) && (abs(vy) < VMAX)){//limitation de vitesse
          vx -= ax;
          vy -= ay;
        }
        if(isBumping[i] == false){
          if(isValuable){
            pani.incrBumpHit();
            hit++;
            if(hit > statMaxHit){
              statMaxHit = hit;
            }
            if(superBumpHit < 6){
              superBumpHit++;
              goParticules((x + bumpers[i].x) / 2, (y + bumpers[i].y) / 2, speed * 50);
            }else{
              // super bump
              isSuperBumping = true;
              superBumpHit = 0;
              bumpers[i].bumpIt();
              // bonus
              numBonus = (numBonus < 3) ? numBonus + 1 : numBonus;
              goParticules((x + bumpers[i].x) / 2, (y + bumpers[i].y) / 2, speed * 50);
            }
          }
          
          // jouer le son du bumper i
          if(!isOldGhost) 
            sound.playSfxBallBumper(x, v, i);

          isBumping[i] = true;
        }
      }else{
        isBumping[i] = false;
      }
    } 
 
    // avec panier
    float dx = pan.x0 - x;
    float dy = pan.y0 - y;
    float distance = sqrt(dx * dx + dy * dy);
    float minDist = pan.diameter / 2 + diameter / 2;
    if(distance < minDist){
      float angle = atan2(dy, dx);
      float targetX = x + cos(angle) * minDist;
      float targetY = y + sin(angle) * minDist;
      float ax = K *(targetX - pan.x0);
      float ay = K * (targetY - pan.y0);
      if((abs(vx) < VMAX) && (abs(vy) < VMAX)){ //limitation de vitesse
        vx -= ax;
        vy -= ay;
      }
      // son
      if(action != ACTION_FREEZE){
        if(!isOldGhost) 
          sound.playSfxBallPanier(x, v);
      }
    }
    
    dx = pan.x1 - x;
    dy = pan.y1 - y;
    distance = sqrt(dx * dx + dy * dy);
    minDist = pan.diameter / 2 + diameter / 2;
    if(distance < minDist){
      float angle = atan2(dy, dx);
      float targetX = x + cos(angle) * minDist;
      float targetY = y + sin(angle) * minDist;
      float ax = K * (targetX - pan.x1);
      float ay = K * (targetY - pan.y1);
      if((abs(vx) < 35) && (abs(vy) < 35)){ //limitation de vitesse
        vx -= ax;
        vy -= ay;
      }
      // son
      if(action != ACTION_FREEZE){
        if(!isOldGhost)
          sound.playSfxBallPanier(x, v);
      }
      
      isSuperSwitch = false;
    } 
  }
  
  void move() {
    oldX = x;
    oldY = y;
    boolean isJump = false;
    if(actionBonus && numBonus > 0){
      isJump = true;
    }
    if(isJump && y == H - diameter / 2){
      vy =- 9;
      isJump = false;
    }else if(isJump && vy != 0){
      vy *= -1.5;
      x += 3 * vx; 
      y += 3 * vy; 
    }
    x += vx ;
    y += vy;
    v = mag(vx, vy);
    if(isSuperBumping){
      x += 3 * vx; 
      y += 3 * vy; 
      isSuperBumping = false;
    }
	// mur droite
    if(x + diameter / 2 > W) {
      x = W - diameter / 2;
      vx *= -0.9;
      if(!isOldGhost)
        sound.playSfxBallWallRight(x, v);
    }
	// mur gauche
	else if(x - diameter / 2 < 0) {
      x = diameter / 2;
      vx *= -0.9;
      if(!isOldGhost)
        sound.playSfxBallWallLeft(x, v);
    }
	// sol
    if(y + diameter / 2 > H) {
      y = H - diameter / 2;
      vy *= -0.9; 
      isSuperAerial = false;
      alleyHoop = 0;
    }
    if(y == H - diameter / 2 && int(vy) != 0) {
      if(!isOldGhost)
        sound.playSfxBallGround(x, v);
    }
    if(vy < 0){
      if(highestPos > y){
        highestPos = y;
      }
    }else{
      if(y > 0){
        highestPos = 0;
      }
    }
    
    if(isValuable){
      boolean isPoint = pani.checkPoint(int(x), int(y), int(oldX), int(oldY), isSuperAerial, isSuperSwitch, alleyHoop);
      if(isPoint){ 
        isValuable = false;
        nbBasket++;
      }
    }
    vy += GRAVITY;
  }
  
  void goParticules(float x, float y, float speed){
    int i = 0;
    int nbPartic = 0;
    speed = max(20, speed);
    while(nbPartic < hit && i < MAXPARTICULES){
      if(particules[i].status == particules[i].READY){
        particules[i].go(x, y, speed);
        nbPartic++;
      }
      i++;
    }
  }
  
  void display(){
    //dessiner triangle si en dehors écran
    if(y + diameter < 0){
      if(y > highestPos / 2){
        stroke(#FF3300);
      }else{
        stroke(255);
      }
      line(x, 0, x - 5, 10);
      line(x, 0, x + 5, 10);
      line(x - 5, 10, x + 5, 10);
    }else{
      stroke(255);
      fill(255, 60);
      ellipse(x, y, diameter, diameter);
      if(!isValuable){
        ellipse(x, y, diameter - 10, diameter - 10);
      }
      //vecteur vitesse
      line(x, y, x + 5 * vx, y + 5 * vy);
    }
  }
  
  void displaySpecial(){
    // balle
    if (y + diameter < 0){
      if(y > highestPos / 2){
        stroke(#FF3300);
      }else{
        stroke(255);  
      }
      line(x, 0, x - 5, 10);
      line(x, 0, x + 5, 10);
      line(x - 5, 10, x + 5, 10);
    }else{
      if(isValuable){
        fill(#ff9900);
      }else{
        fill(#7CFC00);
      }
      noStroke();
      ellipse(x, y, diameter, diameter);
    }
    displayInfo();
  }

  void displayInfo(){  
    //info sur la balle
    textFont(fontB, 30);
    fill(255, 204);
    text("Hit " + hit, 475, 200);
    fill(0);
    textFont(fontA, 40);
  }
}
