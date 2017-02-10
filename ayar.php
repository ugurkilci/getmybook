<?php
	try{
		$vt = new PDO("mysql:host=localhost;dbname=mybook;charset=utf8;","root","");
	}catch(PDOExeption $ugur){
		echo $ugur->getMessege();
	}
?>