<?php

/*
* This is the Keypic class you must include
*/
include_once('../Keypic.php');

ini_set('display_errors', 'on'); 
error_reporting(E_ALL & ~E_NOTICE);

$username = $_REQUEST['username'];
$email = $_REQUEST['email'];
$message = $_REQUEST['message'];
$Token = $_REQUEST['Token'];

/*
*	instead of xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx insert your form code here
*	IMPORTANT this code must be secret, don't send it to clients
*/
Keypic::setFormID('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
//Keypic::setDebug(true);

if($_SERVER['REQUEST_METHOD'] ==  "POST")
{
	if($username!=='' && $email!=='' && $message!=='')
	{
		$spam = Keypic::isSpam($Token, $email, $username, $message);
		if(is_numeric($spam))
		{
			echo '<font color="red"> This message has ' . $spam . '% of spam probability</font><br />';
			echo Keypic::getIt() . '<br />';
			echo '<a href="">reload</a>';
			die();
		}
		else
		{
			echo '<font color="red"> There was an error: '. $spam .'</font><br />';
			die();
		}
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
   <form method="post" action="#">
   Username: <br />
   <input type="text" name="username" value="<?php echo $username;  ?>" /> <br />
   Email: <br />
   <input type="text" name="email" value="<?php echo $email;  ?>" /> <br />
   Message: <br />
   <textarea name="message" rows="5" cols="30"><?php echo $message; ?></textarea> <br />
   <input type="hidden" name="Token" value="<?php echo Keypic::getToken($Token, $email, $username, $message); ?>" /> <br />
   <?php echo Keypic::getIt(); ?> <br />
   <input type="submit" value="Send"> <br />
   </form>
   <?php echo $error; ?>
  </div>
 </body>
</html>
