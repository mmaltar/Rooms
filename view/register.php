
<?php require_once 'view/_header.php';

// require_once __DIR__.'/_header.php';
?>


<br>
<div class="register">
<form id=registerForm method="POST" action="index.php?rt=users/register">
  <input type="text" placeholder="Username" id="username" name="username">
  <input type="email" placeholder="E-Mail" id="mail" name="mail">
  <input type="password" placeholder="Password" id="password" name="pass" >
    <input type="submit" value="Register">
</form>
</div>

</body>
</html>

<!--
<script src="/~mmaltar/view/register.js" type="text/javascript"></script> !-->
<?php //require_once __DIR__.'/_footer.html'; ?>

<script src="../view/register.js" type="text/javascript"></script>
<?php require_once 'view/_footer.html'; ?>
