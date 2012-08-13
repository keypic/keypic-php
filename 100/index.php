<?php
include_once('../Keypic.php');
?><!DOCTYPE html>
<html>
 <head>
  <style>
  html,body{margin:0; padding:0; font-family:Verdena,Tahoma,sans-serif;}
  body{background:#FFF;color:#333}
  a img{border:0;}
  div.greybox{background-color: #f7f7f7; border: solid #cccccc; border-width: 1px 1px 1px 1px; color: #333333; padding: 10px; font-size: 13px; -webkit-border-radius: 5px; -moz-border-radius: 5px;}
  table, td, th{border:1px solid orange; padding:10px; cellpadding:10px; cellspacing:10px; border-style:dashed;}
  th{background-color:orange; color:white;}
  </style>
 </head>
 <body dir="ltr">
<b><a href="/demo/">DEMO home</a></b>
<div class="greybox">
 <h1>image banners</h1>
	<table class="table_class">
	 <tr>
	  <th colspan="2">Special contents</th>
	 </tr>
	 <tr>
	  <td>Protected with keypic.com image</td>
	  <td><?php echo Keypic::getImage() ?></td>
	 </tr>
	 <tr>
	  <td>Lead square transparent 1x1 pixel</td>
	  <td><?php echo Keypic::getImage('1x1') ?> also called <a href="http://en.wikipedia.org/wiki/Web_bug">Web bug</a></td>
	 </tr>
   	 <tr>
	  <th colspan="2">Rectangular and pop-up ads</th>
	 </tr>
	 <tr>
	  <td>Large rectangle (336 x 280)</td>
	  <td><?php echo Keypic::getImage('336x280') ?></td>
	 </tr>
	 <tr>
	  <td>Medium Rectangle (300 x 250)</td>
	  <td><?php echo Keypic::getImage('300x250') ?></td>
	 </tr>
	 <tr>
	  <td>Square Pop-Up (250 x 250)</td>
	  <td><?php echo Keypic::getImage('250x250') ?></td>
	 </tr>
	 <tr>
	  <td>Vertical Rectangle (240 x 400)</td>
	  <td><?php echo Keypic::getImage('240x400') ?></td>
	 </tr>
	 <tr>
	  <td>Rectangle (180 x 150)</td>
	  <td><?php echo Keypic::getImage('180x150') ?></td>
	 </tr>
	 <tr>
	  <td>3:1 Rectangle (300 x 100)</td>
	  <td><?php echo Keypic::getImage('300x100') ?></td>
	 </tr>
	 <tr>
	  <td>Pop-under (720 x 300)</td>
	  <td><?php echo Keypic::getImage('720x300') ?></td>
	 </tr>
	 <tr>
	  <td>Banner w/Naw Bar (392 × 72)</td>
	  <td><?php echo Keypic::getImage('392x72') ?></td>
	 </tr>
	 <tr>
	 <th colspan="2">Banner and button ads</th>
	 </tr>
	 <tr>
	  <td>Full Banner (468 x 60)</td>
	  <td><?php echo Keypic::getImage('468x60') ?></td>
	 </tr>
	 <tr>
	  <td>Half Banner (234 x 60)</td>
	  <td><?php echo Keypic::getImage('234x60') ?></td>
	 </tr>
	 <tr>
	  <td>Micro Button (80 x 15)</td>
	  <td><?php echo Keypic::getImage('80x15') ?></td>
	 </tr>
	 <tr>
	  <td>Micro Bar (88 x 31)</td>
	  <td><?php echo Keypic::getImage('88x31') ?></td>
	 </tr>
	 <tr>
	  <td>Button 1 (120 x 90)</td>
	  <td><?php echo Keypic::getImage('120x90') ?></td>
	 </tr>
	 <tr>
	  <td>Button 2 (120 x 60)</td>
	  <td><?php echo Keypic::getImage('120x60') ?></td>
	 </tr>
	 <tr>
	  <td>Vertical Banner (120 x 240)</td>
	  <td><?php echo Keypic::getImage('120x240') ?></td>
	 </tr>
	 <tr>
	  <td>Square Button (125 x 125)</td>
	  <td><?php echo Keypic::getImage('125x125') ?></td>
	 </tr>
	 <tr>
	  <td>Leaderboard (728 x 90)</td>
	  <td><?php echo Keypic::getImage('728x90') ?></td>
	 </tr>
	 <tr>
	 <th colspan="2">"Skyscraper" ads</th>
	 </tr>
	 <tr>
	  <td>Skyscraper (120 x 600)</td>
	  <td><?php echo Keypic::getImage('120x600') ?></td>
	 </tr>
	 <tr>
	  <td>Wide Skyscraper (160 x 600)</td>
	  <td><?php echo Keypic::getImage('160x600') ?></td>
	 </tr>
	 <tr>
	  <td>Half Page Ad (300 x 600)</td>
	  <td><?php echo Keypic::getImage('300x600') ?></td>
	 </tr>
	</table>
</div>
 </body>
</html>
