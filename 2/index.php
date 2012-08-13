<?php

/*
* This is the Keypic class you must include
*/
include_once('../Keypic.php');


$email = $_REQUEST['email'];
$password1 = $_REQUEST['password1'];
$password2 = $_REQUEST['password2'];
$Token = $_REQUEST['Token'];

Keypic::setFormID('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
//Keypic::setDebug(true);

if($_SERVER['REQUEST_METHOD'] ==  "POST")
{
	if($email != '' && $password1 != '')
	{
		echo '<font color="red"> ' . Keypic::isSpam($Token, $email) . '% of spam. </font><br />';
		echo Keypic::getImage() . '<br />';
		echo '<a href="">reload</a>';
		exit(0);
	}
	else{$error = '<font color="red">Complete all the fields</font><br />';}
}

?>
<!DOCTYPE html> 
<html> 
 <head>
 <style>
 html,body{margin:0; padding:0; font-family:Verdena,Tahoma,sans-serif;}
 body{background:#FFF;color:#333}
 a img{border:0;}
 div.greybox{background-color: #f7f7f7; border: solid #cccccc; border-width: 1px 1px 1px 1px; color: #333333; padding: 10px; font-size: 13px; -webkit-border-radius: 5px; -moz-border-radius: 5px;}
 </style>
 </head>
 <body dir="ltr">
<b><a href="/demo/">DEMO home</a></b>
<div class="greybox">

<form method="post" action="http://<?php echo $_SERVER["HTTP_HOST"]; ?>/demo/2/">

Email: <br />
<input type="text" name="email" value="<?php echo $email;  ?>" /> <br />
Password: <br />
<input type="password" name="password1" value="<?php echo $password1;  ?>" /> <br />
Password again: <br />
<input type="password" name="password2" value="<?php echo $password2;  ?>" /> <br />
<input type="hidden" name="Token" value="<?php echo Keypic::getToken($Token, $email); ?>" /> <br />
<?php echo Keypic::getImage(); ?> <br />
<input type="submit" value="Send"> <br />
</form>
<?php echo $error; ?>
</div>
 </body>
</html>
