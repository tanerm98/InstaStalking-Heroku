<?php
    session_start();
	// connect to the database
	
	$id_user = $_SESSION['settings_id_user'];
	
	// variable declaration
	$username = "";
	$email    = "";
	$_SESSION['success'] = "";

	// connect to database
	$db = mysqli_connect('us-cdbr-east-06.cleardb.net', 'b1cf63f2b2a47e', '20c164a2', 'heroku_4c68794ecc831a4');
	
	$get_username = mysqli_query($db, "SELECT username
								FROM users
								WHERE id_user = '$id_user'");
	$username = mysqli_fetch_array($get_username)['username'];
	$profile = mysqli_query($db, "SELECT path FROM images Where id_user = '$id_user' and profile = '1' ");
    $profile_img = mysqli_fetch_array($profile);

	// REGISTER USER
	if (isset($_POST['settings'])) {
		// receive all input values from the form
		$name = mysqli_real_escape_string($db, $_POST['name']);
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$email = mysqli_real_escape_string($db, $_POST['email']);
		$password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
		$password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

		// form validation: ensure that the form is correctly filled

		if (!empty($name)) {
			$query = "UPDATE users
						SET name = '$name'
						WHERE id_user = '$id_user'";
			mysqli_query($db, $query);
		}
		
		if (!empty($username)) {
			$user_check_query = "SELECT * FROM users WHERE username='$username'";
			$result = mysqli_query($db, $user_check_query);
			$user = mysqli_fetch_assoc($result);

			if ($user) {
				if ($user['username'] === $username) {
				} else {
					$query = "UPDATE users
								SET username = '$username'
								WHERE id_user = '$id_user'";
					mysqli_query($db, $query);
				}
			} else {
				$query = "UPDATE users
							SET username = '$username'
							WHERE id_user = '$id_user'";
				mysqli_query($db, $query);
			}
		}
		if (!empty($email)) {
			$user_check_query = "SELECT * FROM users WHERE username='$username'";
			$result = mysqli_query($db, $user_check_query);
			$user = mysqli_fetch_assoc($result);

			if ($user) {
				if ($user['email'] === $email) {
				} else {
					$query = "UPDATE users
								SET email = '$email'
								WHERE id_user = '$id_user'";
					mysqli_query($db, $query);
				}
			} else {
				$query = "UPDATE users
							SET email = '$email'
							WHERE id_user = '$id_user'";
				mysqli_query($db, $query);
			}
		}
		if (!empty($password_1)) {
			if ($password_1 != $password_2) {
			} else {
				$password = md5($password_1);
				$query = "UPDATE users
							SET password = '$password'
							WHERE id_user = '$id_user'";
				mysqli_query($db, $query);
			}
		}
		
		$_SESSION['profile_id_user'] = $id_user;
		header('Location: profile.php');
		exit;
	}
?>

<!DOCTYPE html>
<html lang='en' class=''>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="feed.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
</head>
<body style="background-image: url(./Photos/z.jpg); background-attachment: fixed; background-position: center; background-repeat: no-repeat; background-size: cover;">

<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top">
	<div class="navbar-collapse collapse w-100 order-1 order-md-0 dual-collapse2">
		<form class="form-inline mr-auto" action="profile.php" method="post">
			<input id="myInput" name="searched_user" class="form-control" type="text" placeholder="Search" aria-label="Search">
			<button class="btn btn-mdb-color btn-rounded btn-sm my-0 ml-sm-2" name="search_user" hidden = "true" type="submit">Search</button>
		</form>
	</div>
		<div class="mx-auto order-0">
			<a class="navbar-brand mx-auto" href="feed.php">InstaStalking <i class="fa fa-dashcube" aria-hidden="true"></i></a>
		</div>
	<div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
		<ul class="navbar-nav ml-auto">
			<li class="nav-item">
				<a class="nav-link" href="profile.php" src="<?php $_SESSION['profile_id_user'] = $_SESSION['id_user']; $_SESSION['profile_username'] = $_SESSION['username']; ?>"><i class="fa fa-id-card" aria-hidden="true"></i> PROFILE</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="index.php?logout='1'"><i class="fa fa-sign-out" aria-hidden="true"></i> LOGOUT</a>
			</li>
		</ul>
	</div>
</nav>

	<nav class="navbar navbar-expand-md navbar-light d-flex justify-content-between" style="background-color: #e3f2fd;">
		<div class="d-flex">
			<ul class="navbar-nav ml-auto">
				<li class="nav-item" style="margin-right: 10px;">
					<img class="img-circle img" src="<?php echo $profile_img['0']; ?>" alt=""  style="width: 50px; height: 50px; border: 3px solid #dd9;" >
				</li>
				<li class="nav-item">
					<h4 class="h2-responsive" style="margin-top: 10px;">@<?php echo $username ?></h4>
				</li>
			</ul>
		</div>
		<div class="d-flex">
			<?php if($id_user != $_SESSION['id_user']) : ?>
				<form action="profile.php" method="post" enctype="multipart/form-data">
					<input type="text" hidden = "true"  name="following_id" value="<?php echo $id_user ?>" >
					<?php if($check_follow) : ?>
							<button name="unfollow" class="btn btn-secondary" onclick="submit" >Unfollow</button>
					<?php else : ?>
							<button name="follow" class="btn btn-secondary" onclick="submit" >Follow</button>
					<?php endif; ?>
				</form>
			<?php endif; ?>
			<ul class="navbar-nav ml-auto">
				<li class="nav-item">
					<?php if($id_user == $_SESSION['id_user']) : ?>
						<a class="nav-link" href="upload.php" src="<?php $_SESSION['upload_id_user'] = $_SESSION['profile_id_user']; ?>"><i class="fa fa-plus-square" aria-hidden="true"></i> Create new post</a>
					<?php endif; ?>
				</li>
				<li class="nav-item">
					<?php if($id_user == $_SESSION['id_user']) : ?>
						<a class="nav-link" href="settings.php" src="<?php $_SESSION['settings_id_user'] = $_SESSION['profile_id_user']; ?>"><i class="fa fa-gears" aria-hidden="true"></i> Profile settings</a>
					<?php endif; ?>
				</li>
			</ul>
		</div>
		<div class="d-flex">
			<ul class="navbar-nav ml-auto">
				<li class="nav-item">
					<a class="nav-link" href="followers.php" src="<?php $_SESSION['followers_id_user'] = $_SESSION['profile_id_user']; ?>">FOLLOWERS</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="following.php" src="<?php $_SESSION['following_id_user'] = $_SESSION['profile_id_user']; ?>">FOLLOWING</a>
				</li>
			</ul>
		</div>
	</nav>

<div class="container" style="background: rgba(255,255,255, 0.5); border-radius: 25px; margin-top: 100px;">
<article class="card-body mx-auto" style="max-width: 400px;">
	<h4 class="card-title mt-3 text-center">Change Account Details</h4>

	<form method="post" action="settings.php">
	<div class="form-group input-group">
		<div class="input-group-prepend">
		    <span class="input-group-text"> <i class="fa fa-user"></i> </span>
		 </div>
        <input name="name" class="form-control" placeholder="Full name" type="text">
    </div> <!-- form-group// -->

        <div class="form-group input-group">
		<div class="input-group-prepend">
		    <span class="input-group-text"> <i class="fa fa-user"></i> </span>
		 </div>
        <input name="username" class="form-control" placeholder="Username" type="text">
    </div> <!-- form-group// -->


    <div class="form-group input-group">
    	<div class="input-group-prepend">
		    <span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
		 </div>
        <input name="email" class="form-control" placeholder="Email address" type="email">
    </div> <!-- form-group// -->


    <div class="form-group input-group">
    	<div class="input-group-prepend">
		    <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
		</div>
        <input name="password_1" class="form-control" placeholder="Create password" type="password">
    </div> <!-- form-group// -->


    <div class="form-group input-group">
    	<div class="input-group-prepend">
		    <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
		</div>
        <input name="password_2" class="form-control" placeholder="Repeat password" type="password">
    </div> <!-- form-group// -->



    <div class="form-group">
        <button type="submit" name="settings" class="btn btn-primary btn-block"> Apply New Settings  </button>
    </div> <!-- form-group// -->
</form>
</article>
</div>
<!--container end.//-->


</body></html>
