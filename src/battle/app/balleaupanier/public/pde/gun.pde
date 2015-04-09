/*
---------------------------------
---------- CLASSE GUN ----------
---------------------------------
*/
class Gun {
  final int GUN_SIZE = 100; //taille au repos
  
  // Spring simulation constants (merci Daniel Shiffman)
  final float M = 0.8;   // Mass
  final float K = 0.2;   // Spring constant
  final float D = 0.92;  // Damping
  
  // Spring simulation variables
  float ps = 100.0; // Position
  float vs = 0.0;  // Velocity
  float as = 0;    // Acceleration
  float f = 0;     // Force
  
  float gunAngleInit;
  float gunAngle;
  float gunX, gunY;
  float gunX0, gunY0; // position du pivot 
  float gunX1, gunY1; // position du bout
  float oldGunX, oldGunY; //position du bout du ghost
  float subdis, oldSubdis;
  
  Gun(){
    gunX0 = 0;
    gunY0 = 250;
    oldGunX = gunX0;
    oldGunY = gunY0;
  }  
  
  float[] getLaunchProperty(){
     float[] speed = new float[4];
     speed[0] = gunX0 - (GUN_SIZE - subdis) * gunX;
     speed[1] = gunY0 - (GUN_SIZE - subdis) * gunY;
     speed[2] = (-(subdis) * cos(gunAngleInit)) / 4.5;
     speed[3] = (-(subdis) * sin(gunAngleInit)) / 4.5;
     return speed;
  }
  
  float[] getLastLaunchProperty(){
     float[] speed = new float[4];
     speed[0] = gunX0 - (GUN_SIZE - oldSubdis) * gunX;
     speed[1] = gunY0 - (GUN_SIZE - oldSubdis) * gunY;
     speed[2] = (-(oldSubdis) * cos(gunAngle)) / 4.5;
     speed[3] = (-(oldSubdis) * sin(gunAngle)) / 4.5;
     return speed;
  }
  
  void update(){
    if(move){//drag
      if(mouseX0 == 0 && mouseY0 == 0) {
        mouseX0 = mouseX;
        mouseY0 = mouseY;
        mouseX2 = mouseX0;
        mouseY2 = mouseY0;
        gunAngleInit = atan2(gunY0 - mouseY0, gunX0 - mouseX0);
      }else{
        subdis = dist(mouseX, mouseY, mouseX0, mouseY0); //distance entre le point du click et le point actuel
        subdis = (subdis > GUN_SIZE - 10) ? GUN_SIZE - 10 : subdis;
        ps = GUN_SIZE - subdis; // on tire le gun   
      }
    }else if(actionRedo){
      subdis = oldSubdis;
      ps = GUN_SIZE - oldSubdis;
    }else{//drop
      // faire pointer le gun dans la direction de la souris
      gunAngle = atan2(gunY0 - mouseY, gunX0 - mouseX);   
      f = -K * (ps - GUN_SIZE);
      as = f / M;       
      vs = D * (vs + as);
      ps = ps + vs;
      if(abs(vs) < 0.1) {
        vs = 0.0;
      }
    }
  
    if(released || actionRedo){
      oldGunX = gunX0 - gunX * ps * 100;
      oldGunY = gunY0 - gunY * ps * 100;
      oldSubdis = subdis;  
    }
    gunX = cos(gunAngle);
    gunY = sin(gunAngle);
    gunX1 = gunX0 - gunX * ps;
    gunY1 = gunY0 - gunY * ps;
  }
  
  void display(){
    // ghost line
    stroke(#666666);
    line(gunX0, gunY0, oldGunX, oldGunY);
  
    // gun
    stroke(255);
    line(gunX0, gunY0, gunX1, gunY1);
  
    // carré orange 
    stroke(#FF9900);
    rect(mouseX2 - 1, mouseY2 - 1, 4, 4);
  
    // carré vert
    stroke(#7CFC00);
    rect(mouseX1 - 1, mouseY1 - 1, 4, 4);

    //info gun
    stroke(255);
    textFont(fontB, 30);
    fill(255, 204);
    text("Pow " + (int)subdis, 475, 290);
    fill(0);
  }
}
