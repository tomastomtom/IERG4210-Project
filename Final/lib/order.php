<?php
session_start();
function userDB(){
	$db = new PDO('sqlite:/var/www/cart.db');
	$db->query('PRAGMA foreign_keys = ON;');
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $db;
}
function insertOrder(){
	error_log($_REQUEST['order']);
  global $db;
  $db = UserDB();
  $tmp = $_REQUEST['order'];
  $json = json_decode($tmp);
  $pidList = $json->pid;
  $amountList = $json->amount;
  $priceList = [];
	$nameList = [];
  foreach ($pidList as $pid){
    $q2 = $db -> prepare('SELECT price FROM products WHERE pid = ?;');
    $q2->bindParam(1,$pid);
    $q2->execute();
    $result = $q2->fetch(PDO::FETCH_ASSOC);
    array_push($priceList, $result['price']);
  }
	foreach ($pidList as $pid) {
		$q3 = $db -> prepare('SELECT name FROM products WHERE pid = ?;');
		$q3->bindParam(1,$pid);
		$q3->execute();
		$result2 = $q3->fetch(PDO::FETCH_ASSOC);
		array_push($nameList, $result2['name']);
	}
  $currency = 'HKD';
  $merchantEmail = 'ierg4210testingmerchant-1@cuhk.edu.hk';
  $salt = mt_rand();
	$pidListImplode = implode(',', $pidList);
	$amountListImplode = implode(',', $amountList);
	$digitPriceList= [];

	foreach ($priceList as $key) {
		array_push($digitPriceList, (float)$key);
	}
	$priceListImplode = implode(',', $digitPriceList);
  $totalPrice = 0;
  for($i = 0; $i<count($amountList);$i++){
    $totalPrice = $totalPrice + ($amountList[$i] * $priceList[$i]);
  }
  $digest = $currency . $merchantEmail . $pidListImplode . $amountListImplode . $priceListImplode.$totalPrice;
	error_log('order.php digest:'. $digest);
  $saltedDigest = hash_hmac('sha256', $digest, $salt);
  $userName = 'Guest';

  $q = $db -> prepare('INSERT INTO orders (digest, salt, user) VALUES (?, ?, ?);');
  $q->bindParam(1, $saltedDigest);
  $q->bindParam(2, $salt);
  $q->bindParam(3, $userName);
  if($q->execute()){
    $lastOrderId = $db->lastInsertId();
		$output->email = utf8_encode($merchantEmail);
		$output->id = utf8_encode($lastOrderId);
		$output->digest = utf8_encode($saltedDigest);
		$output->price = $priceList;

    $outputJson = json_encode($output);
		error_log('OutputJSON:' . $outputJson);
    echo $outputJson;
  }

  exit();
}

 ?>
