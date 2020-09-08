<?php
session_start();
function ierg4210_DB() {
	// connect to the database
	// TODO: change the following path if needed
	// Warning: NEVER put your db in a publicly accessible location
	$db = new PDO('sqlite:/var/www/cart.db');

	// enable foreign key support
	$db->query('PRAGMA foreign_keys = ON;');

	// FETCH_ASSOC:
	// Specifies that the fetch method shall return each row as an
	// array indexed by column name as returned in the corresponding
	// result set. If the result set contains multiple columns with
	// the same name, PDO::FETCH_ASSOC returns only a single value
	// per column name.
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	return $db;
}

function thumbnail($file){
	list($width, $height) = getimagesize($file);
	$ratio = $width / $height;
	$newWidth = 250;
	$newHeight = 250/ $ratio;
	$src = imagecreatefromjpeg($file);
	$dst = imagecreatetruecolor($newWidth, $newHeight);
	imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
	return $dst;
}
function ierg4210_cat_fetchall() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM categories LIMIT 100;");
    if ($q->execute())
        return $q->fetchAll();
}

function ierg4210_cat_fetchone($catid){
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM categories WHERE catid = ? LIMIT 100;");
	$q->bindParam(1, $catid);
	if ($q->execute())
			return $q->fetch(PDO::FETCH_ASSOC);
}

function ierg4210_prod_fetchAll($singleCat, $catid = null){
		global $db;
    $db = ierg4210_DB();
		if ($singleCat == false) {
    $q = $db->prepare("SELECT * FROM products LIMIT 100;");
		}
		else {
		$q = $db->prepare("SELECT * FROM products WHERE catid = ? LIMIT 100;");
		$q->bindParam(1, $catid);
		}
    if ($q->execute())
        return $q->fetchAll();
			}

// Since this form will take file upload, we use the tranditional (simpler) rather than AJAX form submission.
// Therefore, after handling the request (DB insert and file copy), this function then redirects back to admin.html
function ierg4210_prod_insert() {
    // input validation or sanitization

    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    // TODO: complete the rest of the INSERT command
    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = (int) $_POST['catid'];
    if (!preg_match('/^[\w\-\s]+$/', $_POST['name']))
        throw new Exception("invalid-name");
		if (!preg_match('/^[\w\-\s]+$/', $_POST['subcat']))
						throw new Exception("invalid-subcat");
    if (!preg_match('/^[\d\.]+$/', $_POST['price']))
        throw new Exception("invalid-price");
    if (!preg_match('/^[\w\- ]+$/', $_POST['description']))
        throw new Exception("invalid-text");

    // Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
    if ($_FILES["file"]["error"] == 0
        && $_FILES["file"]["type"] == "image/jpeg"
        && mime_content_type($_FILES["file"]["tmp_name"]) == "image/jpeg"
        && $_FILES["file"]["size"] < 5000000) {

        $catid = $_POST["catid"];
        $name = $_POST["name"];
				$subcat = $_POST["subcat"];
        $price = $_POST["price"];
        $desc = $_POST["description"];
        $sql="INSERT INTO products (catid, subCategory, name, price, desc) VALUES (?, ?, ?, ?, ?);";
        $q = $db->prepare($sql);
        $q->bindParam(1, $catid);
        $q->bindParam(2, $subcat);
				$q->bindParam(3, $name);
        $q->bindParam(4, $price);
        $q->bindParam(5, $desc);
        $q->execute();
        $lastId = $db->lastInsertId();

        // Note: Take care of the permission of destination folder (hints: current user is apache)
				$imgPath ="img/product/" . $lastId . ".jpg";
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $imgPath)) {
            // redirect back to original page; you may comment it during debug
						$thumbPath = "img/product/thumbnail/". $lastId . "Thumb.jpg";
						$thumbnail = thumbnail($imgPath);
						imagejpeg($thumbnail, $thumbPath);
						imagedestroy($thumbnail);
            header('Location: admin.php');
            exit();
        }
    }
    // Only an invalid file will result in the execution below
    // To replace the content-type header which was json and output an error message
    header('Content-Type: text/html; charset=utf-8');
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}

// TODO: add other functions here to make the whole application complete
function ierg4210_cat_insert(){
	global $db;
	$db = ierg4210_DB();
	//Server-side checking
	if (!preg_match('/^[\w\-\s]+$/', $_POST['mainCategory']))
			throw new Exception("invalid-main");
	$name = $_POST["name"];
	$main = $_POST["mainCategory"];
	$sql = "INSERT INTO categories (name) VALUES (?, ?);";
	$q = $db->prepare($sql);
	$q->bindParam(1, $name);
	$q->execute();
	header('Location: admin.php');
	exit();
}

function ierg4210_cat_edit(){
	global $db;
	$db = ierg4210_DB();
	if (!preg_match('/^\d*$/', $_POST['catid']))
			throw new Exception("invalid-catid");
	$_POST['catid'] = (int) $_POST['catid'];
	if (!preg_match('/^[\w\-\s]+$/', $_POST['mainCategory']))
			throw new Exception("invalid-main");
	if (!preg_match('/^[\w\-\s]+$/', $_POST['name']))
			throw new Exception("invalid-name");
	$catid = $_POST["catid"];
	$name = $_POST["name"];
	$main = $_POST["mainCategory"];
	$sql = "UPDATE categories SET name = ?, mainCategory = ? WHERE catid = ?;";
	$q = $db->prepare($sql);
	$q->bindParam(1, $name);
	$q->bindParam(2, $main);
	$q->bindParam(3, $catid);
	$q->execute();
	header('Location: admin.php');
	exit();
}
function ierg4210_prod_delete_by_catid($catid){
	$db = ierg4210_DB();
	$sql = "DELETE FROM products WHERE catid = ?;";
	$q = $db->prepare($sql);
	$q->bindParam(1, $catid);
	$q->execute();
	return;
}

function ierg4210_cat_delete(){
	global $db;
	$db = ierg4210_DB();

	if (!preg_match('/^\d*$/', $_POST['catid']))
			throw new Exception("invalid-catid");
	$_POST['catid'] = (int) $_POST['catid'];
	$catid = $_POST['catid'];
	ierg4210_prod_delete_by_catid($catid);
	$sql = "DELETE FROM categories WHERE catid =?;";
	$q = $db->prepare($sql);
	$q->bindParam(1,$catid);
	$q->execute();
	header('Location: admin.php');
	exit();
}

function ierg4210_prod_fetchOne($pid){
		global $db;
    $db = ierg4210_DB();

		$q = $db->prepare("SELECT * FROM products WHERE pid = ?;");
		$q->bindParam(1, $pid);

    if ($q->execute())
        return $q->fetch(PDO::FETCH_ASSOC);
}

function ierg4210_prod_edit(){
	global $db;
	$db = ierg4210_DB();
	if (!preg_match('/^\d*$/', $_POST['pid']))
			throw new Exception("invalid-pid");
	$_POST['pid'] = (int) $_POST['pid'];
	if (!preg_match('/^\d*$/', $_POST['catid']))
			throw new Exception("invalid-catid");
	$_POST['catid'] = (int) $_POST['catid'];
	if (!preg_match('/^[\w\-\s]+$/', $_POST['name']))
			throw new Exception("invalid-productName");
	if (!preg_match('/^[\w\-\s]+$/', $_POST['subcat']))
							throw new Exception("invalid-subcat");
	if (!preg_match('/^[\d\.]+$/', $_POST['price']))
	    throw new Exception("invalid-price");
	if (!preg_match('/^[\w\- ]+$/', $_POST['description']))
	    throw new Exception("invalid-desc");
			if ($_FILES["file"]["error"] == 0
					&& $_FILES["file"]["type"] == "image/jpeg"
					&& mime_content_type($_FILES["file"]["tmp_name"]) == "image/jpeg"
					&& $_FILES["file"]["size"] < 5000000) {

					$catid = $_POST["catid"];
					$name = $_POST["name"];
					$price = $_POST["price"];
					$desc = $_POST["description"];
					$pid = $_POST["pid"];
					$subcat = $_POST["subcat"];
					$sql="UPDATE categories SET catid = ?, subCategory = ?, name = ?, price = ?, desc = ? WHERE pid = ? ;";
					$imgPath = "img/product/" . $pid . ".jpg";
					$q = $db->prepare($sql);
					$q->bindParam(1, $catid);
					$q->bindParam(2, $subcat);
					$q->bindParam(3, $name);
					$q->bindParam(4, $price);
					$q->bindParam(5, $desc);
					$q->bindParam(6,$pid);
					$q->execute();
					if (move_uploaded_file($_FILES["file"]["tmp_name"], $imgPath)) {
						$thumbPath = "img/product/thumbnail/". $pid . "Thumb.jpg";
						$thumbnail = thumbnail($imgPath);
						imagejpeg($thumbnail, $thumbPath);
						imagedestroy($thumbnail);
						header('Location: admin.php');
            exit();
					}
					header('Content-Type: text/html; charset=utf-8');
			    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
			    exit();
				}
}

function ierg4210_prod_delete(){
	global $db;
	$db = ierg4210_DB();
	if (!preg_match('/^\d*$/', $_POST['pid']))
			throw new Exception("invalid-pid");
	$_POST['pid'] = (int) $_POST['pid'];
	$pid = $_POST['pid'];
	$sql = "DELETE FROM products WHERE pid = ?;";
	$q=$db->prepare($sql);
	$q->bindParam(1,$pid);
	$q->execute();
	header('Location: admin.php');
	exit();
}

function ierg4210_prod_fetchOneJSON(){
	if (!preg_match('/^\d*$/', $_REQUEST['pid']))
			throw new Exception("invalid-pid");
	$pid = (int) $_REQUEST['pid'];
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM products WHERE pid = ?;");
	$q->bindParam(1, $pid);
	$q->execute();
	if ($q->execute()){
		$result = $q->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);
	}
	exit();
}

function ierg4210_viewing_orders(){
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM orders ORDER BY oid DESC LIMIT 50;");
	if($q->execute()){
		return $q->fetchAll();
	}

}

?>
