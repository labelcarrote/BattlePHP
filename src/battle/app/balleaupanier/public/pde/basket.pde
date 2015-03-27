/*
-----------------------------------
---------- CLASSE PANIER ----------
-----------------------------------
- gestion du panier et du score.
- chaque balle teste continuellement si elle passe dans le panier,
  auquel cas le score est mis à jour en fonction des propriétés du tir 
  (supermove etc.)
*/

class Panier {

  final int MAXBUMPHIT = 999;
  int score;
  int scoreNow;
  int bumpHit;
  int x0, y0, x1, y1;
  int diameter;
  int shotScore;
  boolean shot;
  int timerShot;
  boolean superA;
  boolean superS;
  int alleyHoop;
 
  Panier(int px0, int py0, int px1, int py1, int diam) {
    score = 0;
    initScoreNow();
    x0 = px0;
    y0 = py0;
    x1 = px1;
    y1 = py1;
    diameter = diam;
    shotScore = 0;
    shot = false;
    timerShot = 255;
    superA = false;
    superS = false;
    alleyHoop = 0;
  }
 
  private int direction(int px0, int py0, int px1, int py1, int px2, int py2){
    return (px1 - px0) * (py2 - py0) - (px2 - px0) * (py1 - py0);
  }
  
  private boolean surSegment(int px0, int py0, int px1, int py1, int px2, int py2){
    return min(px0, px1) <= px2 && px2 <= max(px0, px1) && min(py0, py1) <= py2 && py2 <= max(py0, py1);  
  }
  
  private boolean intersectionSegment(int px0, int py0, int px1, int py1, int px2, int py2, int px3, int py3){
    int d1 = direction(px2, py2, px3, py3, px0, py0);
    int d2 = direction(px2, py2, px3, py3, px1, py1);
    int d3 = direction(px0, py0, px1, py1, px2, py2);
    int d4 = direction(px0, py0, px1, py1, px3, py3);  
    
    if(((d1 > 0 && d2 < 0) || (d1 < 0 && d2 > 0)) && ((d3 > 0 && d4 < 0) || (d3 < 0 && d4 > 0))){ return true; }
    else if(d1 == 0 && surSegment(px2, py2, px3, py3, px0, py0)){ return true; }
    else if(d2 == 0 && surSegment(px2, py2, px3, py3, px1, py1)){ return true; }
    else if(d3 == 0 && surSegment(px0, py0, px1, py1, px2, py2)){ return true; }
    else if(d4 == 0 && surSegment(px0, py0, px1, py1, px3, py3)){ return true; }
    else { return false; }
  }
  
  
  boolean checkPoint(int px0,int py0, int px1, int py1, boolean isSuperAerial, boolean isSuperSwitch, int aHoop){
    if(intersectionSegment(x0, y0, x1, y1, px0, py0, px1, py1)){
      if(isSuperAerial){
        statAirCombo++;
        if(statAirCombo > statMaxAirCombo){
          statMaxAirCombo = statAirCombo;  
        }
        scoreNow *= 100 * statAirCombo;  
        superA = true;
      }else{
        statAirCombo = 0;
      }
      
      if(isSuperSwitch){
        scoreNow *= 1000;
        superS = true;
      }  
      alleyHoop = aHoop;        
      if(alleyHoop >= 1){
        statAlleyHoop++;
      }
      score += scoreNow + alleyHoop * 999;
      shotScore = scoreNow + alleyHoop * 999;
      shot = true;
      if(statBestShot < scoreNow){
        statBestShot = scoreNow;
      } 
      initScoreNow();     
      return true;
    }  
    return false;
  }
  
  void initScoreNow(){
    scoreNow = 1;
    bumpHit = 0;
  }
  
  void incrBumpHit(){
    bumpHit++;
	if(bumpHit < MAXBUMPHIT){
		scoreNow += 2 * bumpHit;
	}
  }
  
  int getScoreNow(){
    return scoreNow;
  }
  
  /*void addPointToScore(int n){
    scoreNow += n;
  }*/
  
  void move() {}
  
  void display(){    
    textFont(fontA, 40);
    
    // shoot réussi
    if(shot){
      drawFlash();
      if(timerShot > 0){     
        fill(255, timerShot);
        timerShot--;
        text("SHOT! +" + shotScore, 10, 145);        
        // super move
        if(alleyHoop != 0){
          text("ALLEY HOOP! +" + (999 * alleyHoop), 10, 75);  
        }
        if(superA){
          if(statAirCombo>1){
            text("AVALANCHE! x" + statAirCombo * 100, 10, 110);
          }else{
            text("SUP-AIR! x100", 10, 110);
          }
        }
        if(superS){
          text("SWITCH! x1000", 10, 180);
        }
      }else{
        timerShot = 255;
        timerFlash = 255;
        shot = false;
        superA = false;
        superS = false;
        alleyHoop = 0;
      }
    }   
   
    // score
    fill(255, 205);
    text(score, 10, 40);
    
    // panier avec reaction a la musique
    float levelL = sound.songIngameLeftLevel();
    float alphaLeft = (levelL * 500 > 255) ? 255 : levelL * 500;
    fill(255, int(alphaLeft));
    ellipse(x0, y0, diameter, diameter);
    float levelR = sound.songIngameRightLevel();
    float alphaRight = (levelR * 500 > 255) ? 255 : levelR * 500;
    fill(255, int(alphaRight));
    ellipse(x1, y1, diameter, diameter);
    line(x0, y0, x1, y1);
    fill(0);
  }
}
