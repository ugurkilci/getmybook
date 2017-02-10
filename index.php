<?php
	include("ayar.php"); // config
	$p 	= @$_GET["p"]; // page
	$code = @$_GET["code"]; // code
	$email = @$_GET["email"]; // mail
?>
<!DOCTYPE html>
<html lang="EN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	
	<title>Get My Book</title>
	
	<meta property="og:title" content="Get My Book" >
	<meta property="og:url" content="http://getmybook.16mb.com/">
	<meta property="og:description" content="Personalized eBook site. You need to enter the code to read the book.">
	<meta property="og:keywords" content="get my book, get my book published, get my bookmarks, get my book reviewed, get my book on amazon, get my book on google play">
	<meta property="og:image" content="img/logo.png">
	<meta name="author" content="Uğur KILCI">

	<meta name="twitter:card" content="@ugur2nd">
	<meta name="twitter:url" content="<?php echo "http://".$_SERVER['HTTP_HOST']."".$_SERVER['REQUEST_URI']; ?>">
	<meta name="twitter:title" content="Get My Book">
	<meta name="twitter:description" content="Personalized eBook site. You need to enter the code to read the book.">
	<meta name="twitter:image" content="img/logo.png">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="360">
	<meta name="viewport" content="width=device-width">
	<meta name="robots" content="index,follow">

	<link rel="stylesheet" type="text/css" href="css/normalize.css?v=<?php echo time(); ?>/">
	<link rel="stylesheet" type="text/css" href="css/genel.css?v=<?php echo time(); ?>/">

	<link rel="dns-prefetch" href="https://google-analytics.com/">
	<link rel="dns-prefetch" href="https://www.google-analytics.com/">
	<link rel="dns-prefetch" href="https://fonts.googleapis.com/">
	<link rel="dns-prefetch" href="https://pbs.twimg.com/">

	<link rel="shortcut icon" href="img/ico.ico">
</head>
<body>
	<center>
		<strong>GET MY BOOK</strong>
		<a href="/" class="menu" title="Home">HOME</a>
		<a href="?p=add" class="menu" title="Add">ADD</a>
		<a href="?p=help" class="menu" title="Help">HELP</a>
	<br /><br />
	<?php

		switch ($p) {
			case 'confirm':
				$select = $vt->prepare("SELECT * FROM texts WHERE mail=?");
				$select->execute(array($email));
				$selectx = $select->fetch(PDO::FETCH_ASSOC);

				if ($selectx["mail"] == $email) {
					header("REFRESH:0;URL=?p=read&code=".$code);
				}else{
					$update = $vt->prepare("UPDATE texts SET onay=? WHERE code=?");
					$update->execute(array(1,$code));

					if ($update) {
						echo'Verification successful :)';
						header("REFRESH:2;URL=?p=read&code=".$code);
					}else{
						echo "aWe met with a wrong problem! :S";
					}
				}
				break;

			case 'read':
				
				$cek = $vt->prepare("SELECT * FROM texts WHERE code =:code");

				$cek->execute(array('code'=>$code));
				$saydirma = $cek->rowCount();
				 
				if($saydirma >0){
					$select = $vt->prepare("SELECT * FROM texts WHERE code=?");
					$select->execute(array($code));
					$selectx = $select->fetch(PDO::FETCH_ASSOC);

					if ($selectx["onay"] == 0) {
						if ($_POST) {
							$mail = $_POST["mail"]; // your mail
							
							if (empty($mail)) {
								echo "Please!";
							}else{
								function mailkont($mail){
									if (filter_var($mail, FILTER_VALIDATE_EMAIL)){
										return 1;
									}else{
										return 0; 
									}
								}
								
								$kontrol_et = mailkont($mail); // 1.maili kontrol ettik

								if($kontrol_et == "1"){
									$update = $vt->prepare("UPDATE texts SET mail=? WHERE code=?");
									$update->execute(array($mail,$code));

									if ($update) {
										mail($mail, "Confirm - Get My Book", 'Thank you for using GET MY BOOK. :) - Your Confirm Link: http://getmybook.16mb.com/?p=confirm&code='.$code.'&email='.$mail."/");
										echo "Send to your mail! :)";
									}else{
										echo "We met with a wrong problem! :S";
									}
								}
							}
						}else{
							echo '
							Please enter your e-mail address and confirm<br />that you have received the book.<br /><br />
							<form action="" method="post">
								<strong>Your E-Mail:</strong>
								<input type="text" name="mail" placeholder="Your E-Mail" />
								<input type="submit" value="Confirm Now!" />
							</form>';
						}
						
					}else{
						echo '<strong>Code:</strong> '.$code.'<p>'.$selectx["text"].'</p>';
					}
				}else{
					echo 'No such code available! :(';
				}
				break;

			case 'help':
				echo '
					<h1>HELP</h1>
					<p>
					Personalized eBook site. You need to enter the code to read the book.<br /><br />
					To add a new book, click [ADD]. Do not lose the code after finishing and sending the book.<br />Otherwise you will have lost the book you wrote.<br />You can use html codes in your book.<br /><br />
					Enter the code to read a book [HOME].<br />
					If you enter the correct code your book will be opened.<br />If you enter the wrong code, you will not be able to access the book.
					<br /><br />
					twitter@ugur2nd<br />
					It is a <strong>Uğur KILCI</strong> product.
					</p>
				';
				break;

			case 'add':
				$securitycode = md5(rand(111111,99999));

				function Code($b = 6) {
					$a = 'ABCDEFGK123456789lmnopzfLMNOPQRSTUVWXYZ';
					return substr(md5(sha1(str_shuffle($a))), 0, $b);
				}
				$code = Code();

				if($_POST){
					$text 	= $_POST["text"];
					$secode 	= $_POST["secode"];
					
					if ($secode = $securitycode) {
						if (empty($text)) {
							echo "Please do not leave blank!";
						}else{
							$add = $vt->prepare("INSERT INTO texts SET text=?, code=?");
							$add->execute(array($text, $code));
							if ($add) {
								echo '
								It was loaded successfully. :)<br /><br />
								<strong>TEXT CODE:</strong>
								<input type="text" class="text" value="'. $code .'"/><br /><br />
								<a href="?p=add" class="menutwo">RE-ADD</a>';
							}else{
								echo ":(";
							}
						}
					}else{
						echo "Oh, there's something wrong!";
					}
				}else{
					echo '
						<h1>ADD</h1>
						<form action="" method="post">
							<textarea name="text" placeholder="Your text"></textarea><br />
							<input type="hidden" value="'.$securitycode.'" name="secode" />
							<input type="submit" value="Send"/>
						</form>
					';
				}
				break;
			
			default:
				$securitycode = md5(rand(111111,99999));

				if($_POST){
					$codee 	= $_POST["code"]; // code
					$secode 	= $_POST["secode"]; // secode
					
					if ($secode = $securitycode) {
						if (empty($codee)) {
							echo "Please do not leave blank!";
						}else{
							$cek = $vt->prepare("SELECT * FROM texts WHERE code =:code ");

							$cek->execute(array('code'=>$codee));
							$saydirma = $cek->rowCount();
							 
							if($saydirma >0){
								header("REFRESH:0;URL=?p=read&code=".$codee);
							}else{
								echo 'No such code available! :(';
							}
						}
					}else{
						echo "Oh, there's something wrong!";
					}
				}else{
					$select = $vt->prepare("SELECT * FROM texts ORDER BY id DESC");
					$select->execute();
					$selectx = $select->fetch(PDO::FETCH_ASSOC);
					echo '
						<h1>SEND TO CODE</h1>
						<form action="" method="post">
							<input type="text" name="code" placeholder="Code" />
							<input type="hidden" name="secode" value="'. $securitycode .'" />
							<input type="submit" value="Send"/>
						</form>
						<p><small>A total of <strong>'. $selectx["id"] .' books</strong> have been written.</small></p>
					';
				}
				break;
		}

	?>
	</center>
</body>
</html>