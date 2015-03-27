/*
--------------------------------------
---------- CLASSE PARTICULE ----------
--------------------------------------
- représentation des éclats qui apparaissent lors des collisions.
*/
class Particule{  
  private final float GRAVITY = .4;
  
  private final int READY = 0;
  private final int INSKY = 1;
  public int status;
  
  public float powerInit;
  public float x = 0;
  public float y = 0;
  public float x0 = 0;
  public float y0 = 0;
  public float vx = 0;
  public float vy = 0;
  public boolean lines = true;
  
  Particule(){
    status = READY;
  }
  
  void go(float gx, float gy, float power){
    status = INSKY;
    x = gx;
    y = gy;
    x0 = x + 1;
    y0 = y + 1;
    powerInit = power;
    
    float dx = x0 - x;
    float dy = y0 - y;
    float distSQ = dx * dx + dy * dy;
    float dist = sqrt(distSQ);
    dist = (dist < .4) ? .4 : dist;
    float force = powerInit / distSQ;
    float angle = random(50);
    vx += force * cos(angle) / dist;
    vy += force * sin(angle) / dist;
  }
  
  void move(){
    if(status == READY){
      
    }else if(status == INSKY){
      vy += GRAVITY;
      vx *= .9;
      vy *= .9;
      x += vx;
      y += vy;
      if(x > W || x < 0 || y > H){
        status = READY;
      }
    }
  }
  
  void display(){
    if(status == INSKY){
      if(lines){
        line(x, y, x - vx, y - vy);
      }
	  else{
		point(x, y);
      }
    }
  }
}
