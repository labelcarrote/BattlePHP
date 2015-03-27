// SoundManager
//import ddf.minim.*;
//import ddf.minim.analysis.*;

public class SoundManager{

  private Minim minim;
  private AudioPlayer songInGame;
  private AudioPlayer songEndGame;
  private AudioPlayer songTitle;
  private AudioSample[] sfxBallBumper = new AudioSample[3];
  private AudioSample sfxBallContact;
  private AudioSample sfxBallPanier;
  private AudioSample sfxBallWall;
  private AudioSample sfxBallGround;
  
  private int[] sfxTimer = new int[8];
  private int[] sfxTimerBumper = new int[3];
  private int sfxTimerPanier;
  private int sfxTimerContact;
  private int sfxTimerGround;
  private int sfxTimerWallLeft;
  private int sfxTimerWallRight;
  private float sfxPan;
  private float sfxGain;
  private boolean soundOn;
  private int status = 0;
  
  public SoundManager(PApplet pap){
      minim = new Minim(pap);
      songInGame = minim.loadFile("app/balleaupanier/public/sounds/ingame.mp3");
      songEndGame = minim.loadFile("app/balleaupanier/public/sounds/endgame.mp3");
      songTitle = minim.loadFile("app/balleaupanier/public/sounds/endgame.mp3");
      sfxBallBumper[0] = minim.loadFile("app/balleaupanier/public/sounds/ball01.mp3");
      sfxBallBumper[1] = minim.loadFile("app/balleaupanier/public/sounds/ball02.mp3");
      sfxBallBumper[2] = minim.loadFile("app/balleaupanier/public/sounds/ball03.mp3");
      sfxBallContact = minim.loadFile("app/balleaupanier/public/sounds/ball04.mp3");
      sfxBallPanier = minim.loadFile("app/balleaupanier/public/sounds/ball05.mp3");
      sfxBallWall = minim.loadFile("app/balleaupanier/public/sounds/ball06.mp3");
      sfxBallGround = minim.loadFile("app/balleaupanier/public/sounds/ball07.mp3");
      
      sfxTimerBumper[0] = 0;
      sfxTimerBumper[1] = 1;
      sfxTimerBumper[2] = 2;
      sfxTimerPanier = 3;
      sfxTimerContact = 4;
      sfxTimerGround = 5;
      sfxTimerWallLeft = 6;
      sfxTimerWallRight = 7;
      
      soundOn = true;
  }
  
  public void switchState(){
    if(songInGame.isPlaying() || songTitle.isPlaying() || songEndGame.isPlaying()){
      songInGame.pause();
      songEndGame.pause();
      songTitle.pause();
      soundOn = false;
    }else{
      if(status == STATUS_GAME || status == STATUS_PAUSE){
        soundOn = true;
        songInGame.loop();
      }else if(status == STATUS_ENDGAME){
        soundOn = true;
        songEndGame.loop();
      }else if(status == STATUS_TITLE){
        soundOn = true;
        songTitle.loop();
      }
    }
  }
  
  public void playSong(int status){
    this.status = status;
    if(soundOn){
      switch(status) {
          case STATUS_TITLE: 
            songEndGame.pause();
            songInGame.pause();
            songTitle.loop();
            break;
          case STATUS_GAME: 
            songTitle.pause();
            songEndGame.pause();
            songInGame.loop();
            break;
          case STATUS_ENDGAME:
            songInGame.pause();
            songTitle.pause();
            songEndGame.loop();
            break;
          case STATUS_PAUSE: 
            break;
          default:
           break;
        }
    }
  }
  private void playSfx(float x, float v, int numTimer, AudioSample sfxSample){
    sfxSample.play();
    if(millis() > sfxTimer[numTimer]) {
          if(v < 30) {
            //this.sfxGain = (v - 20);
            this.sfxGain = (log(v) - 4) * 10 / v;
          }else{
            this.sfxGain = 0;
          }
          
          // mappage du pan
          this.sfxPan = map(x, 0, 600, -0.75, 0.75);

          //sfxSample.setGain(this.sfxGain);
          //sfxSample.setPan(this.sfxPan);
          //sfxSample.trigger();
          sfxTimer[numTimer] = millis() + 100;
        }
  }

  public void playSfxBallContact(float x, float v){
      this.playSfx(x, v, sfxTimerContact, sfxBallContact);
  }  
  public void playSfxBallPanier(float x, float v){
    this.playSfx(x, v, sfxTimerPanier, sfxBallPanier);
  }  
  public void playSfxBallWallLeft(float x, float v){
    this.playSfx(x, v, sfxTimerWallLeft, sfxBallWall);
  }  
  public void playSfxBallWallRight(float x, float v){
      this.playSfx(x, v, sfxTimerWallRight, sfxBallWall);
  }    
  public void playSfxBallGround(float x, float v){
      this.playSfx(x, v, sfxTimerGround, sfxBallGround);
  }  
  public void playSfxBallBumper(float x, float v, int idBumper){
      // verifier idBumper est valide
      this.playSfx(x, v, sfxTimerBumper[idBumper], sfxBallBumper[idBumper]);
  }  
  
  public float songTitleLeftLevel() {
    return 0;
    //return songTitle.left.level();
  }
  public float songTitleRightLevel() {
    return 0;
    //return songTitle.right.level();
  }
  
  public float songIngameLeftLevel() {
    return 0;
    //return songInGame.left.level();
  }
  public float songIngameRightLevel() {
    return 0;
    //return songInGame.right.level();
  }

  public void closeSound(){
	songInGame.close();
	songTitle.close();
	songEndGame.close();
	sfxBallBumper[0].close();
	sfxBallBumper[1].close();
	sfxBallBumper[2].close();
	sfxBallContact.close();
	sfxBallPanier.close();
	sfxBallWall.close();
	sfxBallGround.close();
	minim.stop();
  }
}
