<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Rooms</title>
	<!--
				<link href="/~mmaltar/libs/jquery-ui-1.12.1.custom/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
        <script src="/~mmaltar/libs/jquery-ui-1.12.1.custom/jquery-ui.min.js" type="text/javascript"></script>
!-->

        <link href="../libs/jquery-ui-1.12.1.custom/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
        <script src="../libs/jquery-ui-1.12.1.custom/jquery-ui.min.js" type="text/javascript"></script>
        <script src="../view/logout.js" type="text/javascript"></script>
</head>
<?php if(!isset($_SESSION))	session_start(); ?>

<body>
    <div class="headerDiv">
			<?php
			if (isset($_SESSION['login'])) //prikaži logout ako je user ulogiran
					echo '<div class="logout">' . "Pozdrav, " . $_SESSION['login'] .
							 '.  <br><a href="index.php?rt=users/logout">Logout</a>' . '</div>';
			else
					{?>
						<ul>
								<li><a href="index.php?rt=rooms">Home</a></li>
		            <li><a href="index.php?rt=users">Login</a></li>
		            <li><a href="index.php?rt=users/reg_index">Register</a></li>
		        </ul>
					<?php
				}?>

    </div>
    <div class="contentDiv">
