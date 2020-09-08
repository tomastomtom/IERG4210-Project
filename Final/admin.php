<?php
session_start();

require __DIR__.'/lib/auth.php';
if(!tokenAuth()){
  header('Location: login.php', true, 302);
  exit();
}
require __DIR__.'/lib/db.inc.php';
$res = ierg4210_cat_fetchall();
$res2 = ierg4210_prod_fetchAll(false);
$orders = ierg4210_viewing_orders();
$catOptions = '';
$productsOptions = '';
$ordersTexts = '';
createNonce();

foreach ($res as $value){
    $catOptions .= '<option value="'.$value["catid"].'"> '.$value["name"].' </option>';
}

foreach ($res2 as $value){
    $productsOptions .= '<option value="'.$value["pid"].'"> ' .$value["name"]. ' </option>';
}

foreach ($orders as $key) {
  $ordersTexts .=
  '<tr>
    <td>'
    . $key["oid"] .
    '</td>
    <td>'
    .$key["user"].
    '</td>
    <td>'
    .$key["digest"].
    '</td><td>
    '.$key["salt"].'
    </td>';
    if(!$key["tid"]){
      $ordersTexts .= '<td>not yet</td> </tr>';
    }else{
      $ordersTexts .= '<td>'.$key["tid"].'</td> </tr>';
    }
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Hips'flask --- Admin Panel</title>
</head>

<body>
  <h1>Hips'flask Admin Panel</h1>
  <h2>Welcome back: <?php echo $_SESSION['Token']['email']; ?></h2>
  <a href="login-process.php?action=logout">Click to logout</a> <br />
  <a href="changepw.php">Click to Change Password</a>
  <p>
    Please operate consciously!
  </p>

  <div id="orderHist" style="border-style:solid">
    <h3>Last 50 transection</h3>
    <table>
      <tr>
        <th>Order ID</th>
        <th>User Account</th>
        <th>Digest</th>
        <th>Salt</th>
        <th>Transection ID</th>
      </tr>
      <?php
      echo $ordersTexts;
       ?>
    </table>
  </div>


  <form id="prod_insert" method="POST" action="admin-process.php?action=prod_insert" enctype="multipart/form-data">
    <fieldset>
      <legend> Adding New Product</legend>

      <div>
        <label for="prod_catid1"> Category *</label>
        <select id="prod_catid1" name="catid" required>
          <?php echo $catOptions; ?></select>
      </div>
      <div>
        <label for="prod_subcat1"> Sub-Category *</label>
        <input id="prod_subcat1" type="text" name="subcat" required pattern="^[\w\-\s]+$" />
      </div>
      <div>
        <label for="prod_name1"> Name *</label>
        <input id="prod_name1" type="text" name="name" required pattern="^[\w\-\s]+$" />
      </div>
      <div>
        <label for="prod_price1"> Price *</label>
        <input id="prod_price1" type="text" name="price" required pattern="^\d+\.?\d*$" />
      </div>
      <div>
        <label for="prod_desc1"> Description *</label>
        <textarea name="description" rows="8" cols="80" id="prod_desc1" required></textarea>
      </div>
      <div>
        <label for="prod_image1"> Image * </label>
        <input id="prod_image1" type="file" name="file" required accept="image/jpeg" />
      </div>
      <input type="hidden" name="nonce" value="<?php echo $_SESSION['nonce']; ?>">
      <input type="submit" value="Submit" />
    </fieldset>
  </form>

  <form id="prod_edit" action="admin-process.php?action=prod_edit" method="post" enctype="multipart/form-data">
    <fieldset>
      <legend>Updating The Products</legend>
      <div>
        <label for="prod_pid2">Product to be update * </label>
        <select id="prod_pid2" name="pid" required>
          <?php echo $productsOptions; ?> </select>
      </div>
      <div>
        <label for="prod_catid2"> Updated Category * </label>
        <select id="prod_catid2" name="catid" required>
          <?php echo $catOptions; ?></select>
      </div>
      <div>
        <label for="prod_subcat2"> Updated Sub-Category *  </label>
        <input id="prod_subcat2" type="text" name="subcat" pattern="^[\w\-\s]+$" / required>
      <div>
        <label for="prod_name2"> Updated Name * </label>
        <input id="prod_name2" type="text" name="name" pattern="^[\w\-\s]+$" required/>
      </div>
      <div>
        <label for="prod_price2"> Updated Price * </label>
        <input id="prod_price2" type="text" name="price" pattern="^\d+\.?\d*$" required/>
      </div>
      <div>
        <label for="prod_desc2"> Updated Description  * </label>
        <textarea name="description" rows="8" cols="80" id="prod_desc2" required></textarea>
      </div>
      <div>
        <label for="prod_image2"> Updated Image * </label>
        <input id="prod_image2" type="file" name="file" accept="image/jpeg" / required>
      </div>
      <input type="hidden" name="nonce" value="<?php echo $_SESSION['nonce']; ?>">
      <input type="submit" value="Submit" />
    </fieldset>
  </form>

  <form id="prod_delete" action="admin-process.php?action=prod_delete" method="post">
    <fieldset>
      <legend>Deleting a Product</legend>
      <div>
        <label for="prod_pid3">Product to be deleted * </label>
        <select id="prod_pid3" name="pid" required><?php echo $productsOptions; ?>
        </select>
      </div>
      <input type="hidden" name="nonce" value="<?php echo $_SESSION['nonce']; ?>">
      <input type="submit" value="Submit" />
    </fieldset>
  </form>

  <form id="cat_insert" action="admin-process.php?action=cat_insert" method="post">
    <fieldset>
      <legend>Adding The Categories</legend>
      <div>
        <label for="cat_name2"> Adding Categories Name</label>
        <input id="cat_name2" type="text" name="name" pattern="^[\w\-\s]+$" />
      </div>
      <input type="hidden" name="nonce" value="<?php echo $_SESSION['nonce']; ?>">
      <input type="submit" value="Submit" />
    </fieldset>
  </form>

  <form id="cat_edit" action="admin-process.php?action=cat_edit" method="post">
    <fieldset>
      <legend>Updating The Categories</legend>
      <div>
        <label for="cat_catid2">Categorie to be update * </label>
        <select id="cat_catid2" name="catid" required>
        <?php echo $catOptions; ?></select>
      </div>
      <div>
        <label for="cat_name2"> Updated Category Name* </label>
        <input id="cat_name2" type="text" name="name" pattern="^[\w\-\s]+$" required/>
      </div>
      <input type="hidden" name="nonce" value="<?php echo $_SESSION['nonce']; ?>">
      <input type="submit" value="Submit" />
    </fieldset>
  </form>

  <form id="cat_delete" action="admin-process.php?action=cat_delete" method="post">
    <fieldset>
      <legend>Deleting a Category</legend>
      <div>
        <label for="cat_catid3">Category to be deleted * </label>
        <select id="cat_catid3" name="catid" required><?php echo $catOptions; ?>
        </select>
      </div>
      <input type="hidden" name="nonce" value="<?php echo $_SESSION['nonce']; ?>">
      <input type="submit" value="Submit" />
    </fieldset>
  </form>

</body>

</html>
