<?php
/**
*	Elo Ranking Helper
*	http://en.wikipedia.org/wiki/Elo_rating_system
*	https://github.com/grossvogel/elo-rankings-php
*	
*/
class EloHelper {	
	
	//const INITIAL_RATING = 1000;

	// K-Factor (Note: usually AROUND 32 for weak players, 16 for masters ... cf. wikipedia...)
	const K_FACTOR = 32;

	// "for each 400 rating points of advantage over the opponent, the chance of winning is magnified ten times in comparison to the opponent's chance of winning."
	// "400 means a 200 difference is a 75% win rate"
	const DIFF = 400;

	// Usage : list($p1nr,$p2nr) = EloHelper::calculate_ratings_after_fight($p1_rating,$p2_rating,$p1_score,$p2_score);
	public static function calculate_ratings_after_fight($p1_rating,$p2_rating,$p1_score,$p2_score){
		// expected ratings
		$expected_p1_rating = 1 / (1 + pow(10,($p2_rating - $p1_rating) / self::DIFF));
		$expected_p2_rating = 1 / (1 + pow(10,($p1_rating - $p2_rating) / self::DIFF));

		$sc1 = 0;
		$sc2 = 0;

		// score result of this fight
		if($p1_score > $p2score)
			$sc1 = 1;
		elseif($p2_score > $p1score)
			$sc2 = 1;
		else{
			$sc1 = 0.5;
			$sc2 = 0.5;
		}

		// updated ratings 
        $p1_new_rating = round($p1_rating + self::K_FACTOR * ($sc1 - $expected_p1_rating));
		$p2_new_rating = round($p2_rating + self::K_FACTOR * ($sc2 - $expected_p2_rating));
                
        echo "{$p1_rating},{$p2_rating},{$p1_score},{$p2_score} => {$expected_p1_rating}, {$expected_p1_rating} => {$p1_new_rating} {$p2_new_rating} !!!!";
		return array($p1_new_rating,$p2_new_rating); 
	}

	public static function k_factor_from_rating($rating){
		// TODO 
	}
}
?>