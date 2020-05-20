<?php
	session_start();

	// variable declaration
	$username = "";
	$email    = "";
	$errors = array(); 
	$_SESSION['success'] = "";

	// connect to database
	$db = mysqli_connect('us-cdbr-east-06.cleardb.net', 'b1cf63f2b2a47e', '20c164a2', 'heroku_4c68794ecc831a4');

	// REGISTER USER
	if (isset($_POST['reg_user'])) {
		// receive all input values from the form
		$name = mysqli_real_escape_string($db, $_POST['name']);
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$email = mysqli_real_escape_string($db, $_POST['email']);
		$password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
		$password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

		// form validation: ensure that the form is correctly filled

		if (empty($name)) { array_push($errors, "Name is required"); }
		if (empty($username)) { array_push($errors, "Username is required"); }
		if (empty($email)) { array_push($errors, "Email is required"); }
		if (empty($password_1)) { array_push($errors, "Password is required"); }

		if ($password_1 != $password_2) {
			array_push($errors, "The two passwords do not match");
		}

         $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
         $result = mysqli_query($db, $user_check_query);
         $user = mysqli_fetch_assoc($result);

		if ($user) { // if user exists
			if ($user['username'] === $username) {
				array_push($errors, "Username already exists!");
			}

			if ($user['email'] === $email) {
				array_push($errors, "Email already exists!");
			}
		}

		// register user if there are no errors in the form
		if (count($errors) == 0) {
			$password = md5($password_1);//encrypt the password before saving in the database
			$query = "INSERT INTO users (name, username, email, password)
					  VALUES('$name', '$username', '$email', '$password')";
			mysqli_query($db, $query);
			
			$get_id_user = mysqli_query($db, "SELECT id_user
											FROM users
											WHERE username = '$username'");
			$id_user = mysqli_fetch_array($get_id_user)['id_user'];
	
			$db->query("UPDATE images set profile = 0 where profile = '1' and id_user = '$id_user'");
			$db->query("INSERT into images (id_user, path, upload_date, profile) VALUES ($id_user, './Photos/default_profile.png', NOW(), 1)");
			
			$_SESSION['username'] = $username;
			$_SESSION['success'] = "You are now logged in!";
			header('location: login.php');
		}

	}

	// ... 

	// LOGIN USER
	if (isset($_POST['login_user'])) {
		$email = mysqli_real_escape_string($db, $_POST['email']);
		$password = mysqli_real_escape_string($db, $_POST['password']);

		if (empty($email)) {
			array_push($errors, "Email is required");
		}
		if (empty($password)) {
			array_push($errors, "Password is required");
		}

		if (count($errors) == 0) {
			$password = md5($password);
			$query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
			$results = mysqli_query($db, $query);
            $rez = mysqli_fetch_array($results);

			if (mysqli_num_rows($results) == 1) {
				$_SESSION['username'] = $rez['username'];
				$_SESSION['id_user'] = $rez['id_user'];
				$_SESSION['success'] = "You are now logged in";
				header('location: feed.php');
			}else {
				array_push($errors, "Wrong username/password combination");
			}
		}
	}

?>
