
<?php

//require_once __DIR__.'/_header.php';
require_once 'view/_header.php'; ?>

<br>
<div class="login">
<form id="loginForm" method="POST" action="index.php?rt=users/login">
  <input type="text" placeholder="Username" id="username" name="logusername">
  <input type="password" placeholder="Password" id="password" name="logpass" >
    <input type="submit" value="Login">
</form>
</div>

</body>
</html>
<!-- <script src="/~mmaltar/view/login.js" type="text/javascript"></script>  !-->
<script src="../view/login.js" type="text/javascript"></script>
<?php 

//require_once __DIR__.'/_footer.html';
require_once 'view/_footer.html'; ?>
