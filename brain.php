<?php
// this function will write output
// to the console, and takes in some
// data, as either array or variable.
function debug_to_console( $data ) {
    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data ) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";
    echo $output;
}
// this function will generate
// a 'ripple-random' effect to 
// populate the piece's stats.
//if(!isset($theStat)) 
$theStat=0;
$theNewTotal=0;
$theCounter = 0;
$levelMax = 75;
$levelMin = 70;
function randStat($init=false) {
	global $theStat;
	global $theNewTotal;
	global $randStat;
	global $theCounter;
	global $levelMax,$levelMin;
	++$theCounter;	
	// let's initialize this function!
	if($init==true) {
		//init
		$theStat = rand($levelMin,$levelMax);
		$theNewTotal=$theStat+20;
		$retString = "theNewTotal set at (init==true): ".$theNewTotal;
		debug_to_console($retString);
		return $theStat;
	}
	// we already ran init
	else {
		// we've already run this and we're generating
		// new stats. (magical code)
		if($theCounter==1 && isset($_SESSION['level'])) {
			$theStat=(int)$_SESSION['level'];
			$theNewTotal=$theStat+20;
		}		
		// generate the initial takeaway
		$tmpItr = rand(1,8);
		do {
			if(++$tmpItr<10) {
				$takeAway = rand($theStat*.15,rand($theStat*.4,$theStat*.55));
			}
			elseif(++$tmpItr<30) {
				$takeAway = rand($theStat*.1,$theStat*.3);
			}
			else {
				$takeAway = $theNewTotal;
			}
		}while($takeAway > $theNewTotal);
		
		// we gotta fill out the stats!
		if($theCounter==5) {
			$theCounter = 0;
			$retString = "theNewTotal is now (init==false): ". $theNewTotal . "->" . ($theNewTotal-$takeAway) ;
			$theNewTotal -= $takeAway;
			debug_to_console($retString);
			return $theNewTotal+$takeAway;
		}
		// take it away, take it away
		// take it away, now.
		$retString = "theNewTotal is now (init==false): ". $theNewTotal . "->" . ($theNewTotal-$takeAway) ;
		$theNewTotal -= $takeAway;
		debug_to_console($retString);
		debug_to_console("LEVEL: ".$theStat);
		return $takeAway;
	}
}
// this function will be called
// to create a name based on
// which piece is coming in, x.
function createName($x) {
	// set up the syllable arrays.
	$firstSyllables = array(
			"Mon",
			"Fay",
			"Shi",
			"Gar",
			"Bli",
			"Tem",
			"Scar",
			"Qo",
			"Tar",
			"Mlip",
			"Munk",
			"Qi",
			"Qhi",
			"Phi",
			"Sar",
			"Ral",
			"Sal",
			"Var"
		);
	$secondSyllables = array(
			"malo",
			"zak",
			"abo",
			"won",
			"al",
			"ap",
			"la",
			"phe",
			"ia",
			"fa",
			"ep",
			"el",
			"iil",
			"yl"
		);
	$lastSyllables = array(
			"shi",
			"lm",
			"us",
			"le",
			"ir",
			"lax",
			"for",
			"eam",
			"im",
			"lak"
		);
	// we'll have 1-4 syllables.
	$numSyllables = rand(1,30)>2 ? rand(2,4) : rand(1,5);
	$theName = "";
	// build then name
	for($i=1;$i<$numSyllables;$i++) {
		if($i==1) {
			$theName = $firstSyllables[array_rand($firstSyllables)];
		}
		elseif($i==2) {
			if($numSyllables > 2) {
				$theName .= $secondSyllables[array_rand($secondSyllables)];
			}
			else {
				$theName .= $lastSyllables[array_rand($lastSyllables)];
			}
		}
		else {
				$theName .= $lastSyllables[array_rand($lastSyllables)];
		}
	}
	return $theName;
}
// this function will generate an
// array of appropriate titles based 
// on class, taking in an array $x.
function genClassName($x,$tmpIndex) {
	// we need to fill/return this array.
	// like a milkman, but with data. :-)
	$possibleNames = array();
	// class switch
	if($x == "pawn") {
		// stat switch
		switch($tmpIndex) {
			// attack
			case 1: 
				$possibleNames = array(
					"Soldier"
				);
				break;
			// defense
			case 2:  
				$possibleNames = array(
					"Armored"
				);
				break;
			// vitality
			case 3:  
				$possibleNames = array(
					"Alchemist"
				);
				break;
			// dexterity
			case 4:  
				$possibleNames = array(
					"Corsair"
				);
				break;
		}	
	}
	elseif($x == "knight") {
		// stat switch
		switch($tmpIndex) {
			// attack
			case 1: 
				$possibleNames = array(
					"Assassin"
				);
				break;
			// defense
			case 2:  
				$possibleNames = array(
					"Battlespy"
				);
				break;
			// vitality
			case 3:  
				$possibleNames = array(
					"Mystic"
				);
				break;
			// dexterity
			case 4:  
				$possibleNames = array(
					"Burglar"
				);
				break;
		}
	}
	elseif($x == "bishop") {
		// stat switch
		switch($tmpIndex) {
			// attack
			case 1: 
				$possibleNames = array(
					"Warmage"
				);
				break;
			// defense
			case 2:  
				$possibleNames = array(
					"Battlepriest"
				);
				break;
			// vitality
			case 3:  
				$possibleNames = array(
					"Priest"
				);
				break;
			// dexterity
			case 4:  
				$possibleNames = array(
					"Illusionist"
				);
				break;
		}	
	}
	elseif($x == "rook") {
		// stat switch
		switch($tmpIndex) {
			// attack
			case 1: 
				$possibleNames = array(
					"Death Knight"
				);
				break;
			// defense
			case 2:  
				$possibleNames = array(
					"Inquisitor"
				);
				break;
			// vitality
			case 3:  
				$possibleNames = array(
					"Antipaladin"
				);
				break;
			// dexterity
			case 4:  
				$possibleNames = array(
					"Templar"
				);
				break;
		}	
	}
	elseif($x == "king") {
		// stat switch
		switch($tmpIndex) {
			// attack
			case 1: 
				$possibleNames = array(
					"Weapon Master"
				);
				break;
			// defense
			case 2:  
				$possibleNames = array(
					"Tactician"
				);
				break;
			// vitality
			case 3:  
				$possibleNames = array(
					"Warlord"
				);
				break;
			// dexterity
			case 4:  
				$possibleNames = array(
					"Grand Marshal"
				);
				break;
		}
	}
	return $possibleNames[array_rand($possibleNames)];
}
// this function will tell us
// which "class" the piece is in
// determined by it's stats,
// which comes in the form of an
// array, x.
$tempBig = 0;
$tempInd = 0;
$theGoodStat = 0;
function calcTitle($x) {
	global $tempInd;
	global $tempBig;
	
	// find the maxima
	for($i=1;$i<5;$i++) {
		//print_r($x,$i,$x[$i]);
		if($x[$i]>$tempBig) {
			$tempBig = $x[$i];
			$tempInd = $i;
		}
		elseif($x[$i]==$tempBig) {
			//if we are equal to it.
			// "multiclass"
		}
	}
			
	return genClassName($x[0],$tempInd);
}
// this function will calculate
// (in plain english) what our
// character is good at.
function calcSpec($atk,$def,$hp,$dex) {
	if(max(array($atk,$def,$hp,$dex))==$atk) {
		return "<span style='color:red'>attack</span>";
	}
	elseif(max(array($atk,$def,$hp,$dex))==$def) {
		return "<span style='color:blue'>defense</span>";
	}
	elseif(max(array($atk,$def,$hp,$dex))==$hp) {
		return "<span style='color:green'>vitality</span>";
	}
	return "<span style='color:orange'>dexterity</span>";
}
// this function will calculate
// the total amount of xp required
// to earn a level, x.
function calcLevel($x) {
	$runningTotal = 0;
	for($i=1;$i<=$x;$i++) { 
		$runningTotal += sqrt($i) * 100;
	}
	return (int)$runningTotal;
}
// this function will calculate
// the total amount of hp at a certain
// level, x using vitality, y.
function calcHP($x,$y) {
	$runningHp = ($x*sqrt($x)) + ($y * (rand(6,9)*.1+1.0)) + ($y*4);
	return (int)$runningHp;
}
// this function calculates a
// generic attack using the variables
// got from the POST. (using atk, dex, and level)
function calcAtk($x,$y,$z) {
	global $critTotal;
	if(rand(1,100)>(95-($y*.3))) { 
		//critical hit!
		$runningAtk = $x * (($x+$y)/2)*(rand(20,35)*.01);
		//$runningAtk = $x * ((rand(4,8)*.1 + 1.0));
		$critTotal += 1;
		/*echo "(crit!) ";*/
	}
	else {
		if(($y*rand(8,12)*.01)+rand(1,rand(7,9))>rand(4,5)) {
			$runningAtk = ($x*(rand(65,85)*.01))+ rand(($z*.3),($z*.7)) ;
		}
		else {
			$runningAtk =  0;
		}
	}
	if($runningAtk <= 0) {
		//miss!
		return 0;
	}
	else {
		return (int)$runningAtk;	
	}
}
// this function calculates an
// attack, then the damage
// mitigation totals, based on
// an attack (x) and defense (y)
// sidenote: use calcAtk for attacks.
function calcDef($x,$y) {
	$runningDmg = rand(1,10) < 2 ? ($y*(rand(2,5)*.1)) : (rand(4,8)*.1) * ($y*(rand(2,5)*.1));
	if(rand(1,100) < (4+($y*(rand(3,6)*.1)))) {
		//lol crit block
		$runningDmg = 999999; 
	}
	if($x - $runningDmg < 1) {
		/*return "blocked!";*/
		return 0;
	}
	else {
		/*return "hit for ".(int)($x - $runningDmg)."!"; */
		return (int)($x - $runningDmg);
	}
}

// this function will "fight" two enemies,
// coming in the form of x & y.
// notes: x will be attacking y.
// x1=atk1,x2=dex1,x3=lvl1,y=def2
function battleTwo($x1,$x2,$x3,$y) {
	return calcDef(calcAtk($x1,$x2,$x3),$y);
}

?>