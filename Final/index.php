<?php
require __DIR__.'/lib/db.inc.php';
$res = ierg4210_cat_fetchall();
$catList= '';

foreach ($res as $value){
  $catList .=
'<li><a href="category.php?catid='.$value['catid'].'" class="tabListLinks">'.strtoupper($value['name']).'</a></li>';
  }

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
        <img class="headerIcon" src="img/icon/avatar.svg" alt="The personal information"/>
      </div>
    </nav>
    <div id="navbar">
      Home
    </div>
  </header>
  <div id="Intro">
    <h1>Hips'flask</h1>
    <p>
      A dedicated drinking flask for Hipsters. We are <b>Hips'flask</b>. Hipster should enjoy good alcohol. Please pick one of the above category to let us introduce you the beauty of alcohol. Fill with the service of <b>Hips'flask</b>.
    </p>
  </div>
            <footer>
              <h5>
                Hips'flask Ltd. Est. 2018.
              </h5>
              <p>
                Everyone deserves of <b>Hips'flask</b>.
              </p>
            </footer>

            <script type="text/javascript" src="js/cart.js"></script>
            <script type="text/javascript" src="js/main.js"></script>

</body>

</html>
