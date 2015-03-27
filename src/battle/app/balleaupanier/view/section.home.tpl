<div class='emptysection'></div>
<div id="content">
	<div id="panier_container">
		<canvas data-processing-sources="{$batl_current_app_url}public/pde/panier.pde {$batl_current_app_url}public/pde/particule.pde {$batl_current_app_url}public/pde/soundmanager.pde {$batl_current_app_url}public/pde/gun.pde {$batl_current_app_url}public/pde/bumper.pde {$batl_current_app_url}public/pde/basket.pde {$batl_current_app_url}public/pde/ball.pde"></canvas>
	</div>
</div>	
<div class='section'><b>How to play</b></div>
<div id="content">
	<b>Controls:</b><br/>
	<img src="{$batl_current_app_url}public/img/keyboard_mouse.png" alt=""/><br/>
	'S' = sound on/off<br/>
	'P' = pause/unpause <br/>
	Mouse = throw a new ball<br/>
	'N' = throw a new ball with the same power as the previous one<br/>
	'SPACE' = freeze/unfreeze ghost ball<br/>
	'B' = use bonus<br/>
	'R' = retry<br/>
	<br/>
	<b>Quick Explanation:</b><br/>
	'Balle Au Panier' means 'Basket Ball' in french, that's it.<br/> 
	Now, it's also a bad-looking random-casual-pachinko-pool-game with a stupid score system!<br/>
	My pleasure!<br/><br/>

	1 - Use your mouse (target - click - move - release) to throw a ball in the 'basket'.<br/>
	2 - You have 8 balls to make a big score (and submit it to the the all-time scoreboard).<br/>
	3 - Every 7 times your ball hit one of the 3 bumpers, you win a bonus.<br/>
	4 - Use 'B' to use your bonus and witness an "instant gravity inversion".<br/>
	5 - With 'SPACE', you can freeze the ghost balls (the old balls) and make them act like a bumper.<br/>
	<br/>
	The score mecanism is mostly based on this :<br/>
	- number of bumper's hits<br/>
	- air combos (your ball doesn't hit the ground before the basket)<br/>
	- switchs (lucky perfect shots...)<br/>
	- alley hoops (balls collide before the basket)<br/>
	- etc! Find your own strategy ;)<br/>
	<br/>
	Need more help on how to play? Check the <a href="#manual">manual</a>.
</div>
<div class='section'><b>Download</b></div>
<div id="content">
	<b>Download offline version (requires java runtime) :</b><br/>
	Windows : <a href="http://labelcarrote.free.fr/bap/bap_windows.zip" title="BAP">BAP_windows.zip</a> <br/>
	Mac OS : <a href="http://labelcarrote.free.fr/bap/bap_macosx.zip" title="BAP">BAP_macosx.zip</a> <br/>
	Linux : <a href="http://labelcarrote.free.fr/bap/bap_linux.tar.gz" title="BAP">BAP_linux.tar.gz</a> <br/>
	<br/>
	<b>Download the 'BAP EP':</b><br/>
	<!--You need this mp3 compilation to enjoy the (great) songs of the game wherever you want!<br/> 
	 <a href="http://www.labelcarrote.com/mp3/Joseph Dean/Joseph Dean - BAP EP.zip" title="BAP EP">BAP EP.zip</a><br/> -->
	AVAILABLE SOON !<br/>
	<br/>
	<b>Cover Art:</b><br/><br/>
	<a href="{$batl_current_app_url}public/img/bap_cover.png" title="BAP EP"><img src="{$batl_current_app_url}public/img/bap_cover_thb.png" alt=""/></a><br/>
</div>
<div class='section'><b>Score Board</b></div>
<div id="content">
	<b>All Time 20 Best:</b>
	<br/><br/>
	<?php
		include_once "score.php";
		Score::affichescoreboard();
	?>
	<a name="manual"/>
</div>
<div class='section'><b>Manual</b></div>
<div id="content">
	- Use your mouse (target - click - move - release) on the screen to launch the ball. The point you click on the screen will represent the direction of the ball from the launcher (on the bottom-left corner) while the line you draw represent the power of the shot.<br/>
	<br/>
	<img src="{$batl_current_app_url}public/img/screen_1.png" alt=""/>
	<br/><br/>
	- While your ball is cruising it might touch bumpers, increasing your hit count.<br/>
	<br/>
	<img src="{$batl_current_app_url}public/img/screen_2.png" alt=""/>
	<br/><br/>
	- Each time you get 7 hits you earn 1 bonus (you can't have more than 3 bonus). Press the (B) key to use a bonus.<br/>
	Bonus will make your ball jump when it's on the ground or inverse gravity (?!) when it's in the air.<br/>
	<br/>
	<img src="{$batl_current_app_url}public/img/screen_3.png" alt=""/>
	<br/><br/>
	- Your ball must pass trough the basket bumpers to score, points earned depending on the number of hits and other parameters you'll have to discover by yourself.<br/>
	<br/>
	<img src="{$batl_current_app_url}public/img/screen_4.png" alt=""/>
	<br/><br/>
	- After the ball scored, it stays in play and continue to bounce but become inactive (i.e. it can't score or increase hit count anymore). The ball is orange while active and green while inactive.<br/>
	<br/>
	- You can launch more than one ball, use your mouse to make a new shot or press the (N) key to launch a new ball with the same power and direction than the previous one.<br/>
	<br/>
	- When you throw an other ball the previous one become a "ghost" ball (you can't have more than 2 ghost balls). Ghost balls act exactly like normal ones and can score too when still active (inactive ghost balls have a double border).<br/>
	Nonetheless they have one more feature: the freeze. Press the (SPACE) key and ghost balls will be frozen in time and space, press it again and the balls will move again.<br/>
	<br/>
	<img src="{$batl_current_app_url}public/img/screen_5.png" alt=""/>
	<br/><br/>
	- You have 8 balls to maximize your score. You can press the (R) key at any time to retry. When all your balls have been launched, click the screen to finish and submit your score or play a new game.<br/>
</div>
<div class='section'><b>Credits</b></div>
<div id="content">
	<b>Original Concept:</b><br/> P2B <br/>
	<b>Programming and Design:</b><br/> P2B / Joseph Dean <br/>
	<b>Music and Sound Design:</b><br/> Joseph Dean <br/>
	<br/>
	<b>- LC GAMES 2009 -</b>
</div>
<div class='section'><b>About</b></div>
<div id="content">
	Built with <a href="http://processing.org" title="Processing.org">Processing 1.0</a> and <a href="http://code.compartmental.net/tools/">Minim</a>, a nice little audio library.
	</p>
	Thank you for playing.
</div>
<div class='emptysection'></div>
