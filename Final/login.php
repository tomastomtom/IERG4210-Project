<?php
session_start();
require __DIR__.'/lib/auth.php';
if(tokenAuth()){
  header('Location: admin.php', true, 302);
  exit();
}
createNonce();
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
      <input type="hidden" name="nonce" value="<?php echo $_SESSION['nonce']; ?>">
      <input type="submit" name="submit" value="Submit"></button>
    </fieldset>
  </form>
</body>
</html>
