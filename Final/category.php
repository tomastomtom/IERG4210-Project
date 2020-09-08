<?php
require __DIR__.'/lib/db.inc.php';

$pageCatid = (int)$_REQUEST['catid'];

$res = ierg4210_cat_fetchall();
$catList= '';
foreach ($res as $value){
  $catList .=
'<li><a href="category.php?catid='.$value['catid'].'" class="tabListLinks">'.strtoupper($value['name']).'</a></li>';
  }
$res2 = ierg4210_prod_fetchAll(true, $pageCatid);
$count_res2 = count($res2);
$i = 1;
$productsList= '<div id="productsList">';

foreach ($res2 as $value){
  if ($i%3 == 1) {
    $productsList .= '<div class="listRow">
      <div class="listItem listItemL">
        <a href="productPage.php?pid='.$value["pid"].'"><div class="imageContainer">
          <img src="img/product/thumbnail/'.$value["pid"]. 'Thumb.jpg"  alt="' .$value["name"].  'Thumbnail">
        </div>
          <p class = "productType">'
          .$value["subCategory"].
          '</p>
          <p class="productName">'
            .$value["name"].
          '</p>
          <p class="price">
            $'.$value["price"].
          '</p></a>
          <button class="button addToCart" data-pid= "'.$value["pid"].'">Add To Cart</button>
        </div>';
  }
  elseif ($i%3 == 2) {
    $productsList .=
      '<div class="listItem">
        <a href="productPage.php?pid='.$value["pid"].'"><div class="imageContainer">
          <img src="img/product/thumbnail/'.$value["pid"].'Thumb.jpg"  alt=" '.$value["name"].' Thumbnail">
        </div>
        <p class = "productType">'
        .$value["subCategory"].
        '</p>
          <p class="productName">'
            .$value["name"].
          '</p>
          <p class="price">
            $' .$value["price"].
          '</p></a>
          <button class="button addToCart" data-pid = "'.$value["pid"].'">Add To Cart</button>
        </div>';
  }
  else {
    $productsList .=
          '<div class="listItem listItemR">
            <a href="productPage.php?pid='.$value["pid"].'"><div class="imageContainer">
              <img src="img/product/thumbnail/'.$value["pid"].'Thumb.jpg"  alt=" '.$value["name"].' Thumbnail">
            </div>
            <p class = "productType">'
            .$value["subCategory"].
            '</p>
              <p class="productName">'
                .$value["name"].
              '</p>
              <p class="price">
                $'.$value["price"].
              '</p></a>
              <button class="button addToCart" data-pid = "'.$value["pid"].'">Add To Cart</button>
            </div></div>';
  }
  $i++;
}

if ($count_res2%3 == 2) {
  $productsList .=
  '<div class="listItemNull listItemR"> </div>';
}

$productsList .= '</div>';
 ?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Whiskey - Hips'flask</title>
  <link rel="stylesheet" href="css/main.css" type="text/css" />
</head>

<body>

  <header>
    <nav id="mainHeader">

      <span id="shopIcon">Hips'flask</span>

      <span id="tabList">
        <ul>
          <?php echo $catList; ?>
        </ul>
      </span>
      <div id="shoppingCart" class="icons">
        <img class="headerIcon" src="img/icon/shopping-cart.svg" alt="The shopping cart" />
        <div id="cartList">
          <div id="itemContainer">

          </div>
          <div class="subTotal">
            <span id="subTotalText">Sub-total:</span>
            <span id="subTotalAmount"></span>
          </div>
          <form id="cartForm" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" onsubmit="return cartSubmit(event)">
            <div id="checkOut">
              <button class="button" type="submit" value="Submit" form="cartForm">Proceed to checkout</button>
            </div>
            <input type="hidden" name="cmd" value="_cart" />
            <input type="hidden" name="upload" value="1" />
            <input type="hidden" name="business" value="ierg4210testingmerchant-1@cuhk.edu.hk" />
            <input type="hidden" name="currency_code" value="HKD" />
            <input type="hidden" name="charset" value="utf-8" />
            <input type="hidden" name="custom" value="0" />
            <input type="hidden" name="invoice" value="0" />
          </form>
        </div>
      </div>

      <div id="personal" class="icons">
        <img class="headerIcon" src="img/icon/avatar.svg" alt="The personal information" />
      </div>
    </nav>
    <div id="navbar">
      <a href="index.php">Home</a> / Whiskey
    </div>
  </header>
  <div id="Intro">
    <h1>
      <?php echo $res[$pageCatid - 1]["name"]; ?>
    </h1>
    <p>
      Purchase your favorite
      <?php echo $res[$pageCatid - 1]["name"]; ?> here at <b>Hips'flask</b>! At your service, from <b>Hips'flask</b>.
    </p>
  </div>
  //
  <?php echo $productsList; ?>
  <footer>
    <h5>
      Hips'flask Ltd. Est. 2018.
    </h5>
    <p>
      Everyone deserves of <b>Hips'flask</b>.
    </p>
  </footer>

  <script type="text/javascript" src="js/main.js"></script>
  <script type="text/javascript" src="js/cart.js"></script>
</body>

</html>
