<?php include('server.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="login.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body style="background-image: url(./Photos/z1.jpg); background-attachment: fixed; background-position: center; background-repeat: no-repeat; background-size: cover;">

	<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top">
		<div class="mx-auto order-0">
			<a class="navbar-brand mx-auto" href="#">InstaStalking <i class="fa fa-dashcube" aria-hidden="true"></i></a>
		</div>
	</nav>

<div class="container" style="background: rgba(255,255,255, 0.5); border-radius: 25px;">
    <div class="row mgTp">
        <form method="post" action="login.php" class="col-md-6 offset-md-3">
        <?php include('errors.php'); ?>
            <h3 class="title">Please sign in</h3>
            <hr class="divisor">
            <div class="form-group">
                <label for="exampleInputEmail1"><i class="fa fa-address-book" aria-hidden="true"></i> Email</label>
                <input name="email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1"><i class="fa fa-unlock-alt" aria-hidden="true"></i> Password</label>
                <input name="password" type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Remember me</label>
            </div>
            <button type="submit" name="login_user" class="btn btn-primary topBtn"><i class="fa fa-sign-in"></i> Sign in</button>
            <p>
			Not yet a member? <a href="register.php">Sign up</a>
		    </p>
        </form>

    </div>
</div>
</body>
</html>

