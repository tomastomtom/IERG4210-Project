<?php

$salt = mt_rand();
$digest = "HKDierg4210testingmerchant@cuhk.edu.hk41150.0150";
$saltedPassword = hash_hmac('sha256', $digest, 480108696);


?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Login of Hips'flask</title>
</head>

<body>
  <h1>Login to Hips'flask Admin Panel</h1>
  <form class="" action="login-process.php?action=adminLogin" method="post">
    <fieldset>
      <div>
        <label for="email">email:</label>
      </div>
      <div>
        <input type="text" name="email" id="email" required autofocus pattern="^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$">
      </div>
      <div>
        <label for="password">Password: </label>
      </div>
      <div>
        <input type="password" name="password" id="password" required pattern="^[\w@#$%\^\-\s\&\*]+$">
      </div>
      <input type="submit" name="submit" value="Submit"></button>
    </fieldset>
  </form>
</body>
</html>

<?php echo ("Salt: " .$salt. "<br /> Salted Password: ".$saltedPassword); ?>
