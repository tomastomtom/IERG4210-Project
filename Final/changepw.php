<?php
session_start();
require __DIR__.'/lib/auth.php';
if(!tokenAuth()){
  header('Location: login.php', true, 302);
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
  <h1>Changing your password</h1>
  <form class="" action="login-process.php?action=changePassword" method="post">
    <fieldset>
      <div>
        <label for="oldPassword">Old Password:</label>
      </div>
      <div>
        <input type="password" name="oldPassword" id="oldPassword" required autofocus pattern="^[\w@#$%\^\-\s\&\*]+$">
      </div>
      <div>
        <label for="newPassword">New Password: </label>
      </div>
      <div>
        <input type="password" name="newPassword" id="newPassword" required pattern="^[\w@#$%\^\-\s\&\*]+$">
      </div>
      <div>
        <label for="validate">Type your new password again: </label>
      </div>
      <div>
        <input type="password" name="validate" id="validate" required pattern="^[\w@#$%\^\-\s\&\*]+$">
      </div>
      <input type="submit" name="submit" value="Submit"></button>
    </fieldset>
  </form>
</body>

</html>
