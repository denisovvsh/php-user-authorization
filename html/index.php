<?php
session_start();
require_once './settings.php';
require_once './registration.php';
require_once './edit.php';
require_once './login.php';

$sessionHash = !empty($_SESSION['hash']) ? $_SESSION['hash'] : false;

$login = new Login(HOST_DB, USER_NAME_DB, PASS_DB, NAME_DB);
$status = $login->checkSession($sessionHash);

if(!empty($_GET['login'])) $active_status_login = true;
if(!empty($_GET['registration'])) $active_status_registration = true;
if(!empty($_GET['logout'])) $login->logout();
?>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

		<title>Test</title>
	</head>
	<body>
		<div class="container m-auto m-2">
			<div class="col-12 my-2">
				<div class="btn-group" role="group" aria-label="Basic example">
					<a class="btn btn-primary btn-sm <?php if($status) echo 'd-none'; if(!$status && !$active_status_registration) echo 'disabled';?>" href="./index.php?login=1">Login</a>
					<a class="btn btn-primary btn-sm <?php if($status) echo 'd-none'; if(!$status && $active_status_registration) echo 'disabled';?>" href="./index.php?registration=1">Registration</a>
					<a class="btn btn-primary btn-sm <?php if(!$status) echo 'd-none';?>" href="./index.php?logout=1">Logout</a>
				</div>

				<?php
					if(!$status && empty($active_status_registration))
					{
						$login = new Login(HOST_DB, USER_NAME_DB, PASS_DB, NAME_DB);
						if (!$_POST) $login->renderingFormLogin();
						else $login->prepareLogin($_POST);
					}
					if(!$status && !empty($active_status_registration))
					{
						$registration = new Registration(HOST_DB, USER_NAME_DB, PASS_DB, NAME_DB);
						if (!$_POST) $registration->renderingFormRegistration();
						else $registration->prepareRegistration($_POST);
					}

					if($status)
					{
						$login = new Edit(HOST_DB, USER_NAME_DB, PASS_DB, NAME_DB);
						if (!$_POST) $login->renderingEditPage($_SESSION['hash']);
						else $login->prepareEdit($_POST);
					}
				?>
			</div>
		</div>

		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	</body>
</html>