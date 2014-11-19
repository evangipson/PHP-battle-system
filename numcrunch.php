<!DOCTYPE HTML> 
<html lang=en>
<head>
<?php 
	//really quick php header requires :-)
	//include "libchart/libchart/classes/libchart.php";
	// gotta randomly generate chart name.  remember
	// to clean this up when we destroy the session.
	//$seedChart = (int)((rand(1,183927)*1.2-9*rand(1,3))*rand(8,12)*.1);
?>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<title> :evngpsn-#crunch </title>

<link rel="shortcut icon" href="favicon.ico" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js" type="text/javascript"></script>
<script src="audio1_html5.js" type="text/javascript"></script>
<script src="scripty.js" type="text/javascript"></script>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:100,300,700,600,400' rel='stylesheet' type='text/css'>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Berkshire+Swash' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="http://www.evangipson.com/min_style.css" media="screen">
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="numStyle.css" >
<?php 
include 'brain.php';
// we need to start the session
// in order to make sure the variables
// are there when we refresh.
if (!isset($_SESSION)) {
	/*if($seedChart!=0) {
		// don't forget to clean up
		// that chart nonsense as well
		$qry = "chart".$seedChart.".png";
		unlink($qry);
		// reset seedChart variable to
		// ensure new filename.
		$seedChart = (int)((rand(1,183927)*1.2-9*rand(1,3))*rand(8,12)*.1);
	}*/
	// start up that php session
	// for grabbing POST variables.
	session_start();
	$thePortable =  'this is session id from index.php: ' .session_id();
	debug_to_console($thePortable);

}
?>
</head>               
<body style="background:white">

<div id="container" style="margin-left:auto;margin-right:auto;padding-bottom:20px;">
    <div id="content" style='margin-top:10%'>
        <div class="post" style="text-align:center">
		
			<?php if(isset($_GET['saved'])) {
				echo 'saved <b>'.$_SESSION["theName"].'</b> the <b>'.$_SESSION["theTitle"].'</b> into the database!<br />';
				echo 'go on, create another!<br /><br />';
			}	?>
			
			<?php if(empty($_POST) || isset($_GET['saved'])):?>
				<h2><a style="font-weight:300;color:black;text-decoration:none;" href="numcrunch.php?fight=true">fight</a></h2>
				<small class="theOr"> or </small>
			<?php endif; ?>
			<?php if(empty($_GET['fought'])): ?>
					<h2><a style="font-weight:300;color:black;text-decoration:none;" href="numcrunch.php?create=true">create<?php if (!empty($_POST)): ?>d<?php endif ?></a></h2>
			<?php else : ?>
				<h2><a style="font-weight:300;color:black;text-decoration:none;" href="numcrunch.php">back home</a></h2>
			<?php endif; ?>
			<div style="margin-top:20px;" class="entry">
				<?php if (!empty($_POST)): 
					if(isset($_POST['save'])) {
						debug_to_console("You\'ve attempted to save a character, by the name of: ".$_SESSION['theName']);
						$dbhost = 'localhost';
						$dbuser = 'sprgrpco_cRoot';
						$dbpass = '$@ndb0x';
						$conn = mysql_connect($dbhost, $dbuser, $dbpass);
						if(! $conn ) {
						  die('Could not connect: ' . mysql_error());
						}
						$pName = $_SESSION['theName'];
						$pType = $_SESSION['selectPiece'];
						$pTitle = $_SESSION['theTitle'];
						$pLvl = $_SESSION['level'];
						$pAtk = $_SESSION['atk'];
						$pDef = $_SESSION['def'];
						$pVit = $_SESSION['hp'];
						$pDex = $_SESSION['dex'];
						$pHp = $_SESSION['realHP'];
						if($pName!="")
						{
							$sql = "INSERT INTO `pieces`(`name`, `type`, `title`, `lvl`, `atk`, `def`, `vit`, `dex`, `hp`) VALUES ('$pName','$pType','$pTitle','$pLvl','$pAtk','$pDef','$pVit','$pDex','$pHp')";
							mysql_select_db('sprgrpco_chess');
							$retval = mysql_query( $sql, $conn );
							if(! $retval ) {
								echo($sql);
								die('Could not update data: ' . mysql_error());
							}
							mysql_close($conn);
							Header('Location: '.$_SERVER['PHP_SELF']."?saved=1");
						}
						else {
							echo 'database error, you shouldn\'t see this.  <a href="numcrunch.php">refresh the page!</a>';
						}
					}
					if(isset($_GET['create']) && isset($_POST['selectPiece'])) {
						$theDebug = "if POST isn\'t empty//if POST[\'selectPiece\']//SET LEVEL : ".$_POST['level'];
						debug_to_console($theDebug);
						$_SESSION['selectPiece']=$_POST['selectPiece'];
						$_SESSION['level']=$_POST['level'];
						$_SESSION['hp']=$_POST['hp'];
						$_SESSION['atk']=$_POST['atk'];
						$_SESSION['def']=$_POST['def'];
						$_SESSION['dex']=$_POST['dex'];
						$_SESSION['realHP']= calcHP((int)htmlspecialchars($_SESSION["level"]),(int)htmlspecialchars($_SESSION["hp"]));
						$_SESSION["theName"] = createName($_SESSION["selectPiece"]);
						$_SESSION["theTitle"] = calcTitle(array($_SESSION["selectPiece"],$_SESSION["atk"],$_SESSION["def"],$_SESSION["hp"],$_SESSION["dex"]));
					}
					// if we are re-running the simulation
					// let's not change anything except the name.
					elseif(isset($_SESSION['level']) && isset($_POST['resim'])) {
						$theDebug = "if POST isn\'t empty//if POST[\'resim\']//LEVEL : ".$_SESSION['level'];
						debug_to_console($theDebug);
						// make a new name!
						$_SESSION['theName'] = createName($_SESSION["selectPiece"]);
					}
					// if we need new stats, let's get 'em!
					elseif(isset($_SESSION['level']) && isset($_POST['restat'])) {
						// must set the seed value
						$theStat = $_SESSION['level'];
						// let the new stats pour in!
						$_SESSION['hp']=randStat();
						$_SESSION['atk']=randStat();
						$_SESSION['def']=randStat();
						$_SESSION['dex']=randStat();
						// gotta recalculate the title as well!
						$_SESSION['theTitle'] = calcTitle(array($_SESSION["selectPiece"],$_SESSION["atk"],$_SESSION["def"],$_SESSION["hp"],$_SESSION["dex"]));
						// make a new name!
						$_SESSION['theName'] = createName($_SESSION["selectPiece"]);
					}
					// why would you want to downlevel a character?  
					elseif (isset($_SESSION['level']) && isset( $_POST['dlvl'] ) ) {
						$_SESSION["level"] = (int)$_SESSION["level"] - 1;
						$_SESSION["hp"] = (int)$_SESSION["hp"] - 1;
						$_SESSION["atk"] = (int)$_SESSION["atk"] - 1;
						$_SESSION["def"] = (int)$_SESSION["def"] - 1;
						$_SESSION["dex"] = (int)$_SESSION["dex"] - 1;
					}
					elseif (isset($_SESSION['level']) && isset( $_POST['ulvl'] ) ) {
						$_SESSION["level"] = (int)$_SESSION["level"] + 1;
						$_SESSION["hp"] = (int)$_SESSION["hp"] + 1;
						$_SESSION["atk"] = (int)$_SESSION["atk"] + 1;
						$_SESSION["def"] = (int)$_SESSION["def"] + 1;
						$_SESSION["dex"] = (int)$_SESSION["dex"] + 1;
					}
					// ------------
					// data output!
					// ------------
					if(isset($_GET['create'])&& isset($_SESSION['selectPiece'])) :	?>
						you've created <b><?php echo $_SESSION["theName"]."</b> the <b>".$_SESSION["theTitle"]; ?></b>.<br />
						he's a <b><?php echo $_SESSION["selectPiece"] ?></b> who specializes in <b><?php echo calcSpec($_SESSION['atk'],$_SESSION['def'],$_SESSION['hp'],$_SESSION['dex']); ?></b>.<br />
						he's level <b><?php echo $_SESSION["level"]; ?></b><br />
						total XP: <b><?php echo calcLevel((int)htmlspecialchars($_SESSION["level"])); ?> </b><br />
						total HP: <b><?php echo $_SESSION['realHP']; ?></b><br />
						attack: <b><?php echo $_SESSION["atk"] ?></b><br />
						defense: <b><?php echo $_SESSION["def"] ?></b><br />
						vitality: <b><?php echo $_SESSION["hp"] ?></b><br />
						dexterity: <b><?php echo $_SESSION["dex"] ?></b><br /><br />
						<b>sample attacks (vs self):</b><br /><p>
						<?php
							//for holding crit info
							//(critTotal is counted up in
							//calcAtk())
							$critTotal=0;
							//hold these in arrays
							$itr = rand(500,2000);
							$atks = array();
							$defs = array();
							for($i=0;$i<$itr;$i++){
								$atks[$i]=calcAtk((int)htmlspecialchars($_SESSION["atk"]),(int)htmlspecialchars($_SESSION["dex"]),(int)htmlspecialchars($_SESSION["level"]));
							 }
							sort($atks);				
								$i=0;
							 foreach($atks as $atkElement) {
								if(++$i === $itr) {
									echo $atkElement;
								}
								else {
									echo $atkElement.", ";
								}
							 }
						?> </p>
						hit percentage : <b><?php
							$runningHit = 0;
							foreach($atks as $atkElement) {
								// if we hit
								if($atkElement>0) {
									$runningHit += 1;
								}
							}
							echo (int)(($runningHit/$itr)*100);
						?>%</b> (<b><?php echo $runningHit ?></b> hits/<b><?php echo $itr ?></b> attempts) <br />
						critical hit percentage : <b><?php
							echo (int)(($critTotal/$itr)*100);
						?>%</b> (<b><?php echo $critTotal ?></b> crits/<b><?php echo $itr ?></b> attempts) <br />
						critical hit damage (average) : <b><?php
							echo (int)($_SESSION['atk'] * (($_SESSION['atk']+$_SESSION['dex'])*.5) *(rand(20,35)*.01));
						?></b><br/>
						base crit multiplier: <b><?php 
							echo number_format((($_SESSION['atk']+$_SESSION['dex']/2)*(rand(15,20)*.01)), 2, '.', '')."x"; 
						?></b><br />
						average attack damage (crits included): <b><?php 
							// damage counters
							$totalAtk = 0;
							$totalAtks = 0;
							foreach($atks as $atkElement) {
								// if we hit
								if($atkElement>0) {
									$totalAtk += $atkElement;
									$totalAtks += 1;
								}
							}
							echo (int)($totalAtk/$totalAtks);
						?></b><br /><br />
						<b>sample defenses (vs self):</b><br /><p>
						<?php
							for($i=0;$i<$itr;$i++){
								$defs[$i]=calcDef(calcAtk((int)htmlspecialchars($_SESSION["atk"]),(int)htmlspecialchars($_SESSION["dex"]),(int)htmlspecialchars($_SESSION["level"])),(int)htmlspecialchars($_SESSION["def"]));
							 }		
							sort($defs);					
								$i=0;
							 foreach($defs as $defElement) {
								if(++$i === $itr) {
									echo $defElement;
								}
								else {
									echo $defElement.", ";
								}
							 }
						?></p>
						perfect blocks : <b><?php
							$runningHit = 0;
							foreach($defs as $defElement) {
								// if we hit
								if($defElement==0) {
									$runningHit += 1;
								}
							}
							echo (int)(($runningHit/$itr)*100);
						?>%</b> (<b><?php echo $runningHit ?></b> for <b><?php echo $itr ?></b>) <br />
						average damage taken per hit: <b><?php 
							// damage counters
							$totalAtk = 0;
							$totalAtks = 0;
							foreach($defs as $defElement) {
								// if we hit
								if($defElement>0) {
									$totalAtk += $defElement;
									$totalAtks += 1;
								}
							}
							echo (int)($totalAtk/$totalAtks);
						echo "</b><br />it would take around <b>";
						// average damage divided by total health.
						echo (int)($_SESSION['realHP']/($totalAtk/$totalAtks));
						?></b> hits to kill you.<br /><br />
						<?php 
							//remove 0s hopefully!
							/*$remove = array(0);
							$result = array_diff($atks, $remove);  
							$result = array_diff($defs, $remove); */     
							//sort(&$defs);
							//graphs
							/*$chart = new LineChart();
							$dataSetA = new XYDataSet();
							$dataSetD = new XYDataSet();
							// for every atk/def, so we 
							// iterate DOUBLE the amount (for
							// 2 sets of data, sort of an "on-the
							// -fly" array! :-]
							for($i=0;$i<$itr*2;$i++) {
								// if we're below half the count
								if($i<$itr) {
									// build the atk point
									$dataSetA->addPoint(new Point("",$atks[$i]));
								}
								// otherwise, we're over halfway
								else {
									// build the def point
									$dataSetD->addPoint(new Point("",$defs[$i-$itr]));
								}
							}
							// combine data sets
							$dataSet = new XYSeriesDataSet();
							$dataSet->addSerie("Atk",$dataSetA);
							$dataSet->addSerie("Def",$dataSetD);
							// set the dataSet into the chart
							$chart->setDataSet($dataSet);
							// title the chart
							$chart->setTitle("Atk/Def Comparison Chart");
							// render it
							$chart->render("tmp/chart".$seedChart.".png");*/
						?>
						<!-- <img alt="Pie chart"  src="tmp/chart<?php //echo $seedChart; ?>.png" style="border: 0;"/> -->
						<br />
						<form action="" method="post">	
							<?php if($_SESSION["level"]>1) : ?> <input type="submit" value="dwnLvl" name="dlvl"><br /> <?php endif; ?>
							<input type="submit" value="upLvl" name="ulvl">
							<input type="submit" value="new stats" name="restat"><br />
							<input type="submit" value="same stats" name="resim"><br />
						</form>
						<form action="numcrunch.php?saved=1" method="post">	
							<input type="submit" value="save character" name="save"><br /> 
						</form>
					<?php elseif(isset($_GET['fought'])): ?>
						<div style="clear:both"></div>
						<?php
							// let's bring in the debug info first
							$debugInfo = $_POST['debugCheck'];
							// now we need session variables for the
							// POSTed information (1st,2nd player)
							if(isset($_POST['firstPlayer']))
							    $_SESSION['firstPlayer'] = $_POST['firstPlayer'];
							if(isset($_POST['secondPlayer']))
							    $_SESSION['secondPlayer'] = $_POST['secondPlayer'];
							// call the database with the name information
							// in the POST variables
							$dbhost = 'localhost';
							$dbuser = 'sprgrpco_cRoot';
							$dbpass = '$@ndb0x';
							$conn = mysql_connect($dbhost, $dbuser, $dbpass);
							if(! $conn ) {
							  die('Could not connect: ' . mysql_error());
							}
							mysql_select_db('sprgrpco_chess');
							$sql = "SELECT * FROM `pieces` WHERE name='".$_SESSION['firstPlayer']."'";
							$res = mysql_query( $sql, $conn );
							if(! $res ) {
								echo($sql);
								die('Could not update data: ' . mysql_error());
							}
							$row = mysql_fetch_row($res);
							// for use later
							$tmpPl1 = $row;
							// second query
							$sql = "SELECT * FROM `pieces` WHERE name='".$_SESSION['secondPlayer']."'";
							$res = mysql_query( $sql, $conn );
							if(! $res ) {
								echo($sql);
								die('Could not update data: ' . mysql_error());
							}
							$row2 = mysql_fetch_row($res);
							// close up that mysql connection
							mysql_close($conn);
							// for use later
							$tmpPl2 = $row2;
							echo "<div class='halfy'>";
								// type
								echo "<h3>the ".$row[1]."</h3>";
								// name 
								echo "<h2>".$row[0]."</h2>";
								// level and title 
								echo "<small>level ";
								if($tmpPl1[3]<$tmpPl2[3]) {
									echo $row[3];
								}
								else {
									echo "<span style='color:green'>".$row[3]." <i class='fa fa-trophy'></i></span>";
								}
								echo "</small>";
								echo "<small>".$row[2]."</small><br />";
								// stats 
								echo "<small>";
								echo "hit points: ";
								if($tmpPl1[8]<$tmpPl2[8]) {
									echo $row[8];
								}
								else {
									echo "<span style='color:green'>".$row[8]." <i class='fa fa-trophy'></i></span>";
								}
								echo "<br />";
								echo "attack: ";
								if($tmpPl1[4]<$tmpPl2[4]) {
									echo $row[4];
								}
								else {
									echo "<span style='color:green'>".$row[4]." <i class='fa fa-trophy'></i></span>";
								}
								echo "<br />";
								echo "defense: ";
								if($tmpPl1[5]<$tmpPl2[5]) {
									echo $row[5];
								}
								else {
									echo "<span style='color:green'>".$row[5]." <i class='fa fa-trophy'></i></span>";
								}
								echo "<br />";
								echo "vitality: ";
								if($tmpPl1[6]<$tmpPl2[6]) {
									echo $row[6];
								}
								else {
									echo "<span style='color:green'>".$row[6]." <i class='fa fa-trophy'></i></span>";
								}					
								echo "<br />";
								echo "dexterity: ";
								if($tmpPl1[7]<$tmpPl2[7]) {
									echo $row[7];
								}
								else {
									echo "<span style='color:green'>".$row[7]." <i class='fa fa-trophy'></i></span>";
								}					
								echo "<br /></small><br />";
							echo "</div>";
							// for funny code
							$row=$row2;
							echo "<div class='halfy' style='padding-bottom:0px;margin-bottom:20px;'>";
								// type
								echo "<h3>the ".$row[1]."</h3>";
								// name 
								echo "<h2>".$row[0]."</h2>";
								// level and title 
								echo "<small>level ";
								if($tmpPl2[3]<$tmpPl1[3]) {
									echo $row[3];
								}
								else {
									echo "<span style='color:green'>".$row[3]." <i class='fa fa-trophy'></i></span>";
								}
								echo "</small>";
								echo "<small>".$row[2]."</small><br />";
								// stats 
								echo "<small>";
								echo "hit points: ";
								if($tmpPl2[8]<$tmpPl1[8]) {
									echo $row[8];
								}
								else {
									echo "<span style='color:green'>".$row[8]." <i class='fa fa-trophy'></i></span>";
								}
								echo "<br />";
								echo "attack: ";
								if($tmpPl2[4]<$tmpPl1[4]) {
									echo $row[4];
								}
								else {
									echo "<span style='color:green'>".$row[4]." <i class='fa fa-trophy'></i></span>";
								}
								echo "<br />";
								echo "defense: ";
								if($tmpPl2[5]<$tmpPl1[5]) {
									echo $row[5];
								}
								else {
									echo "<span style='color:green'>".$row[5]." <i class='fa fa-trophy'></i></span>";
								}
								echo "<br />";
								echo "vitality: ";
								if($tmpPl2[6]<$tmpPl1[6]) {
									echo $row[6];
								}
								else {
									echo "<span style='color:green'>".$row[6]." <i class='fa fa-trophy'></i></span>";
								}					
								echo "<br />";
								echo "dexterity: ";
								if($tmpPl2[7]<$tmpPl1[7]) {
									echo $row[7];
								}
								else {
									echo "<span style='color:green'>".$row[7]." <i class='fa fa-trophy'></i></span>";
								}					
								echo "<br /></small><br />";
							echo "</div>";
							// number of battles to simulate.
							$debugInfo == true ? $outerIter = 1: $outerIter = 1000;
							// to count up victories
							// through iterations
							$victory1=0;
							$victory2=0;
							// let's loop!
							for($r=0;$r<$outerIter;$r++) {
								// reset our variables
								$i=0;
								$y=0;
								$tmpDmg1=$tmpPl1[8];
								$tmpDmg2=$tmpPl2[8];
								$attacked=0;
								if($debugInfo){
									echo "<small style='font-size:22px'>battle ";
									echo $r+1;
									echo "</small></br>";
								}
								while($tmpDmg1>0 && $tmpDmg2>0) {
									++$i;
									if($i%2==1) {
										$y+=1;
										if($debugInfo) {
											echo "<div style='clear:both'></div>";
											echo "<small style='text-decoration:underline'>attack phase ".$y."</small><br />";
										}
									}

									
									if($attacked==0) {
										// let's attack, based on whose turn it is!
										if($debugInfo) {
											echo "<div class='halfy' style='display:table;min-height:150px;background:#ECECEC;'><small style='font-weight:300;padding:5px;display:table-cell;width:100%;height:100%;vertical-align:top'>";
										}
										$attacked=1;
										$theDmg = battleTwo($tmpPl1[4],$tmpPl1[7],$tmpPl1[3],$tmpPl2[5]);
										if($theDmg>0) {
											if($debugInfo)
												echo "<b>".$tmpPl1[0]."</b> did <b>".$theDmg."</b> damage to <b>".$tmpPl2[0]."</b>!";
											$tmpDmg2 -= $theDmg;
											$tmpTmp = $tmpDmg2 + $theDmg;
											if($debugInfo)
												echo "<small>".$tmpPl1[0]." HP: ".$tmpDmg1."<br />".$tmpPl2[0]." HP: ".$tmpTmp."<i class='fa fa-arrow-right'></i><span style='color:red'>".$tmpDmg2."</span></small>";	
										}
										else {
											if($debugInfo)
												echo "<b>".$tmpPl1[0]."</b> missed <b>".$tmpPl2[0]."</b>!";	
										}
									}
									else {
										// let's attack, based on whose turn it is!
										if($debugInfo) {
											echo "<div class='halfy' style='display:table;min-height:150px;background:#757D75;color:#fff  ;'><small style='font-weight:300;padding:5px;color:#fff6e8  ;display:table-cell;width:100%;height:100%;vertical-align:top'>";
										}
										$attacked=0;
										$theDmg = battleTwo($tmpPl2[4],$tmpPl2[7],$tmpPl2[3],$tmpPl1[5]);
										if($theDmg>0) {
											if($debugInfo)
												echo "<b>".$tmpPl2[0]."</b> did <b>".$theDmg."</b> damage to <b>".$tmpPl1[0]."</b>!";
											$tmpDmg1 -= $theDmg;
											$tmpTmp = $tmpDmg1 + $theDmg;
											if($debugInfo)
												echo "<small>".$tmpPl1[0]." HP: ".$tmpTmp."<i class='fa fa-arrow-right'></i><span style='color:black'>".$tmpDmg1."</span><br />".$tmpPl2[0]." HP: ".$tmpDmg2."</small>";	
										}
										else {
											if($debugInfo)
												echo "<b>".$tmpPl2[0]."</b> missed <b>".$tmpPl1[0]."</b>!";	
										}
										
									}
									if($debugInfo==true)
										echo "</small></div>";
								}
								if($tmpDmg2<0) {
									// increment player 1 counter here
									++$victory1;
									if($debugInfo)
										echo "<div style='clear:both;'></div><br / ><small style='background:black;color:white;letter-spacing:.5px;'><b>".$tmpPl1[0]."</b> has won taking ".$y." turns, with ".$tmpDmg1." HP left!</small><br />";
								}
								else {
									// increment player 2 counter here
									++$victory2;
									if($debugInfo)
										echo "<div style='clear:both;'></div><br / ><small style='background:black;color:white;letter-spacing:.5px;'><b>".$tmpPl2[0]."</b> has won taking ".$y." turns, with ".$tmpDmg2." HP left!</small><br />";
								}
							}
							/*$theNewOutput = ($victory1/$outerIter) * 100);
							$thePnewOutput = ($victory2/$outerIter) * 100);*/
							$theNewOutput = number_format((($victory1/$outerIter) * 100), 1, '.', '');
							$thePnewOutput = number_format((($victory2/$outerIter) * 100), 1, '.', '');
							// first player had more wins
							if(($victory1/$outerIter) > ($victory2/$outerIter)) {
								echo "<div class='wholly'><h2>".$tmpPl1[0]." wins!</h2></div>";
								if($debugInfo == false) {
								    echo "<small style='letter-spacing:1px;'><b>".$tmpPl1[0]."</b> has won ".$theNewOutput."% (".$victory1."/".$outerIter.")</small><br />";
								    echo "<small style='letter-spacing:1px;'><b>".$tmpPl2[0]."</b> has lost, only winning ".$thePnewOutput."% (".$victory2."/".$outerIter.")</small><br />";
								}
							}
							// 2nd player won!
							elseif(($victory2/$outerIter) > ($victory1/$outerIter)) {
								echo "<div class='wholly'><h2>".$tmpPl2[0]." wins!</h2></div>";
								if($debugInfo == false) {
								    echo "<small style='letter-spacing:1px;'><b>".$tmpPl2[0]."</b> has won ".$thePnewOutput."% (".$victory2."/".$outerIter.")</small><br />";
								    echo "<small style='letter-spacing:1px;'><b>".$tmpPl1[0]."</b> has lost, only winning ".$theNewOutput."% (".$victory1."/".$outerIter.")</small><br />";
								}
							}
							// they must've tied.
							else {
								echo "<div class='wholly'><h3>no contest</h3></div>";
								if($debugInfo == false) {
								    echo "<small style='letter-spacing:1px;'><b>".$tmpPl1[0]."</b> has won ".$theNewOutput."% (".$victory1."/".$outerIter.")</small><br />";
								    echo "<small style='letter-spacing:1px;'><b>".$tmpPl2[0]."</b> has won ".$thePnewOutput."% (".$victory2."/".$outerIter.")</small><br />";
								}
							}
							echo "<form action='numcrunch.php?fought=1' method='post'>";
							echo "<input type='submit' value='fight again!' name='fight' />";
							echo "</form>";
						?>
					<?php endif; ?>
				<?php else: ?>
					<?php if(isset($_GET['fight'])) : ?>
					
						<form action="numcrunch.php?fought=1" method="post">	
							<?php 
								$dbhost = 'localhost';
								$dbuser = 'sprgrpco_cRoot';
								$dbpass = '$@ndb0x';
								$conn = mysql_connect($dbhost, $dbuser, $dbpass);
								if(! $conn ) {
								  die('Could not connect: ' . mysql_error());
								}
								mysql_select_db('sprgrpco_chess');
								$sql = "SELECT * FROM (SELECT * FROM `pieces` ORDER BY rand()) AS tmp_table ORDER BY 'name'";
								$res = mysql_query( $sql, $conn );
								if(! $res ) {
									echo($sql);
									die('Could not update data: ' . mysql_error());
								}
								echo "<select style='font-size:14px;font-weight:600' name = 'firstPlayer'>";
								while (($row = mysql_fetch_row($res)) != null)
								{									
									echo "<option value = '".$row[0]."'";
									if (++$i == 1)
										echo "selected = 'selected'";
									echo ">".$row[0]." (".$row[1].", lvl ".$row[3].")</option>";
								}
								echo "</select><br />"; 
								echo "<small>vs</small><br />";
								$sql = "SELECT * FROM (SELECT * FROM `pieces` ORDER BY rand()) AS tmp_table ORDER BY 'name'";
								$res = mysql_query( $sql, $conn );
								if(! $res ) {
									echo($sql);
									die('Could not update data: ' . mysql_error());
								}
								echo "<select style='font-size:14px;font-weight:600' name = 'secondPlayer'>";
								while (($row = mysql_fetch_row($res)) != null)
								{
									echo "<option value = '".$row[0]."'";
									if (++$i == 1)
										echo "selected = 'selected'";
									echo ">".$row[0]." (".$row[1].", lvl ".$row[3].")</option>";
								}
								echo "</select>"; 
								mysql_close($conn);
							?>
							<br />
							<!-- Squared THREE -->
							<small style="position:relative;top:-10px;">Debug
								<div class="squaredThree" style="display: inline-block;position: relative;top: 36px;">
									<input type="checkbox" value="None" id="squaredThree" name="debugCheck" />
									<label for="squaredThree"></label>
								</div>
							</small>
							<input type="submit" value="fight!" name='fought' />
						</form>
					<?php elseif(isset($_GET['create'])) : ?>
						<form action="" method="post">	
							<select name="selectPiece">
							<?php
								$theClasses = array("pawn","knight","bishop","rook","king");
								$i=0;
								$theSelected = rand(1,5);
								foreach($theClasses as $class) {
									if(++$i == $theSelected) {
										echo "<option value='".$class."' selected>".ucfirst($class)."</option>";
									}
									else {
										echo "<option value='".$class."'>".ucfirst($class)."</option>";
									}
								}
							?>
							</select> <br />
							<?php $floatingVar = randStat(true); ?>
							<small>lvl</small>
							<input type="text" name="level" placeholder="level" value="<?php echo $floatingVar; ?>"/> <br />
							<?php $floatingVar = randStat(); ?>
							<small>atk</small>
							<input type="text" name="atk" placeholder="attack" value="<?php echo $floatingVar; ?>" /> <br />
							<?php $floatingVar = randStat(); ?>
							<small>def</small>
							<input type="text" name="def" placeholder="defense" value="<?php echo $floatingVar; ?>" /> <br />
							<?php $floatingVar = randStat(); ?>
							<small>vit</small>
							<input type="text" name="hp" placeholder="vitality" value="<?php echo $floatingVar; ?>" /> <br />
							<?php $floatingVar = randStat(); ?>
							<small>dex</small>
							<input type="text" name="dex" placeholder="dexterity" value="<?php echo $floatingVar; ?>" /> <br />
							<input type="submit" value="create!" name="create" />
						</form>
					<?php endif; ?>
				<?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>