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
		// $output->email = utf8_encode($merchantEmail);
		// $output->id = utf8_encode($lastOrderId);
		// $output->digest = utf8_encode($saltedDigest);
		$output->code = utf8_encode("var newForm = document.createElement('form');newForm.setAttribute('id', 'newCartList');newForm.setAttribute('action', 'https://www.sandbox.paypal.com/cgi-bin/webscr'); newForm.setAttribute('method', 'post');var cmd = document.createElement('input');cmd.setAttribute('type', 'hidden');cmd.setAttribute('name', 'cmd');cmd.setAttribute('value', '_cart');newForm.appendChild(cmd);var upload = document.createElement('input');upload.setAttribute('type', 'hidden');upload.setAttribute('name', 'upload');upload.setAttribute('value', '1');newForm.appendChild(upload);var business = document.createElement('input');business.setAttribute('type', 'hidden');business.setAttribute('name', 'business');business.setAttribute('value', ".$merchantEmail.");newForm.appendChild(business);var currency_code = document.createElement('input');currency_code.setAttribute('type', 'hidden');currency_code.setAttribute('name', 'currency_code');currency_code.setAttribute('value', 'HKD');newForm.appendChild(currency_code);var charset = document.createElement('input');charset.setAttribute('type', 'hidden');charset.setAttribute('name', 'charset');charset.setAttribute('value', 'utf-8');newForm.appendChild(charset);var custom = document.createElement('input');custom.setAttribute('type', 'hidden');custom.setAttribute('name', 'custom');custom.setAttribute('value', ".$saltedDigest.");newForm.appendChild(custom);var invoice = document.createElement('input');invoice.setAttribute('type', 'hidden');invoice.setAttribute('name', 'invoice');invoice.setAttribute('value', ".$lastOrderId.");newForm.appendChild(invoice);");



			for($i=1;$i<=count($pidList);$i++){
				$output->code .= utf8_encode("var itemName = document.createElement('input');itemName.setAttribute('type', 'hidden');itemName.setAttribute('name', 'item_name_' + ".$i.");itemName.setAttribute('value', ".$nameList[$i-1].");newForm.appendChild(itemName);var itemNumber = document.createElement('input');itemNumber.setAttribute('type', 'hidden');itemNumber.setAttribute('name', 'item_number_' + ".$i.");itemNumber.setAttribute('value', ".$pidList[$i-1].");newForm.appendChild(itemNumber);var itemPrice = document.createElement('input');itemPrice.setAttribute('type', 'hidden');itemPrice.setAttribute('name', 'amount_' + ".$i.");itemPrice.setAttribute('value', ".$priceList[$i-1].");newForm.appendChild(itemPrice);var itemQuantity = document.createElement('input');itemQuantity.setAttribute('type', 'hidden');itemQuantity.setAttribute('name', 'quantity_'+".$i.");itemQuantity.setAttribute('value',".$amountList[$i-1].");newForm.appendChild(itemQuantity);");
			}
			$output->code .=utf8_encode("localStorage.clear();document.body.appendChild(newForm);newForm.submit();");

    $outputJson = json_encode($output,  JSON_UNESCAPED_SLASHES);
    echo $outputJson;
  }

  exit();
}

 ?>
