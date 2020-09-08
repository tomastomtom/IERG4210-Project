<?php
session_start();

function createNonce(){
	$_SESSION['nonce'] = uniqid(mt_rand(),true);
}
function validateNonce(){
	if (isset($_POST["nonce"]) && $_POST["nonce"] === $_SESSION["nonce"]) {
		return TRUE;
	}else{
		return FALSE;
	}
}

function userDB(){
	$db = new PDO('sqlite:/var/www/cart.db');
	$db->query('PRAGMA foreign_keys = ON;');
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	return $db;
}

function adminLogin(){
	$emailRegex = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
  if(empty($_POST['email']) || empty($_POST['password']) || !preg_match("/^[\w@#$%\^\-\s\&\*]+$/",$_POST['password'] ) || !preg_match($emailRegex , $_POST['email'] ))
  throw new Exception("Wrong Credentials in regex check");
	if(validateNonce() === FALSE){
			throw new Exception('You may under CSRF attack!');
	}
	$login_success = 0;
	$email = $_POST['email'];
	$password = $_POST['password'];
	global $db;
	$db = UserDB();
	$q = $db->prepare("SELECT * FROM account WHERE flag = 1 ;");
	$q->execute();
	$loginInfo = $q->fetchAll();
	foreach ($loginInfo as $account) {
		$salt  = $account['salt'];
		$saltedPassword  = hash_hmac('sha256', $password, $salt);
		if($email == $account['email'] && $saltedPassword == $account['password']){
			$login_success = 1;
			$exp = time() + 3600 * 24*3;
			$token = array ( 'email' =>$email, 'exp' => $exp, 'key' => hash_hmac('sha1', $exp.$password, $salt));
			setcookie('TokenCookie', json_encode($token), $exp,'','',false,true);
			$_SESSION['Token'] = $token;
			break;
		}
	}
	if($login_success){
    session_regenerate_id();
		header('Location: admin.php', true, 302);
		exit();
	}
	else {
		throw new Exception('Wrong Credentials');
	}
}

function tokenAuth(){
  if(!empty($_SESSION['Token'])){
  return $_SESSION['Token']['email'];
 }
if(!empty($_COOKIE['TokenCookie'])){
  if($token = json_decode(stripslashes($_COOKIE['TokenCookie']),true)){
    if (time() > $token['exp']){
      return false;
    }
    $db = userDB();
    $q = $db -> prepare('SELECT * FROM account WHERE email = ?');
    $q->bindParam(1, $token['email']);
    $q->execute();
    if($res = $q->fetch(PDO::FETCH_ASSOC)){
      $realKey = hash_hmac('sha1', $token['exp'].$res['password'], $res['salt']);
      if ($realKey == $token ['key']){
        $_SESSION['Token'] = $token;
        return $token['email'];
      }
    }
  }
  }
  return false;
}

function logout(){
  $_SESSION = array();
  session_destroy();
  session_regenerate_id();
  if (isset($_COOKIE['TokenCookie'])){
    setcookie('TokenCookie', '', time() - (3600 *24*3) );
  }
  header('Location: login.php', true, 302);
  exit();
}

function changePassword(){
	if(validateNonce() === FALSE){
			throw new Exception('You may under CSRF attack!');
	}
  $token = json_decode(stripslashes($_COOKIE['TokenCookie']),true);
  $email = $token['email'];
  global $db;
  $db = UserDB();
  $q1 = $db->prepare("SELECT * FROM account WHERE email = ?;");
  $q1->bindParam(1,$email);
  $q1->execute();
  $res = $q1->fetch(PDO::FETCH_ASSOC);
  $oldPassword = hash_hmac('sha256',$_POST['oldPassword'], $res['salt']);

  if($oldPassword != $res['password']){
    throw new Exception("Incorrect old password! Inputted pw:" . $oldPassword. " Salt". $res['salt']);
  }

  if(empty($_POST['oldPassword'])||empty($_POST['newPassword'])||empty($_POST['validate'])||!preg_match("/^[\w@#$%\^\-\s\&\*]+$/",$_POST['oldPassword'] )||!preg_match("/^[\w@#$%\^\-\s\&\*]+$/",$_POST['newPassword'] )||!preg_match("/^[\w@#$%\^\-\s\&\*]+$/",$_POST['validate'] )){
    throw new Exception("Wrong Input of Info");
  }



  if ($_POST['newPassword'] != $_POST['validate']){
    throw new Exception("Incorrect New Password");
  }

  $salt = mt_rand();
  $saltedPassword = hash_hmac('sha256',$_POST['newPassword'] , $salt);
	$q2 = $db->prepare("UPDATE account SET password = ? , salt = ? WHERE email = ?;");
  $q2->bindParam(1, $saltedPassword);
  $q2->bindParam(2,$salt);
  $q2->bindParam(3,$email);
  if ($q2->execute()) {
    logout();
  }
}
 ?>
