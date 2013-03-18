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
	  <th colspan="2">Rectangular and pop-up ads</th>
	 </tr>
	 <tr>
	  <td>Square Pop-Up (250 x 250)</td>
	  <td><?php echo Keypic::getIt('getImage', '250x250') ?></td>
	 </tr>
	 <tr>
	  <td>Medium Rectangle (300 x 250)</td>
	  <td><?php echo Keypic::getIt('getImage', '300x250') ?></td>
	 </tr>
	 <tr>
	  <td>Large rectangle (336 x 280)</td>
	  <td><?php echo Keypic::getIt('getImage', '336x280') ?></td>
	 </tr>
	 <tr>
	  <td>Pop-under (720 x 300)</td>
	  <td><?php echo Keypic::getIt('getImage', '720x300') ?></td>
	 </tr>
	 <tr>
	 <th colspan="2">Banner and button ads</th>
	 </tr>
	 <tr>
	  <td>Full Banner (468 x 60)</td>
	  <td><?php echo Keypic::getIt('getImage', '468x60') ?></td>
	 </tr>
	 <tr>
	  <td>Half Banner (234 x 60)</td>
	  <td><?php echo Keypic::getIt('getImage', '234x60') ?></td>
	 </tr>
	 <tr>
	  <td>Square Button (125 x 125)</td>
	  <td><?php echo Keypic::getIt('getImage', '125x125') ?></td>
	 </tr>
	 <tr>
	  <td>Leaderboard (728 x 90)</td>
	  <td><?php echo Keypic::getIt('getImage', '728x90') ?></td>
	 </tr>
	 <tr>
	 <th colspan="2">"Skyscraper" ads</th>
	 </tr>
	 <tr>
	  <td>Skyscraper (120 x 600)</td>
	  <td><?php echo Keypic::getIt('getImage', '120x600') ?></td>
	 </tr>
	 <tr>
	  <td>Wide Skyscraper (160 x 600)</td>
	  <td><?php echo Keypic::getIt('getImage', '160x600') ?></td>
	 </tr>
	 <tr>
	  <td>Half Page Ad (300 x 600)</td>
	  <td><?php echo Keypic::getIt('getImage', '300x600') ?></td>
	 </tr>
	</table>
</div>
 </body>
</html>
