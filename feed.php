<?php
    session_start();
	// connect to the database
	$con = mysqli_connect('us-cdbr-east-06.cleardb.net', 'b1cf63f2b2a47e', '20c164a2', 'heroku_4c68794ecc831a4');
	
	$id_user_crt = $_SESSION['id_user'];
	// Retrieve posts from the database
	$posts = mysqli_query($con, "SELECT U.id_user, name, id_img, username, path, upload_date, likes, description
								FROM images I join users U ON I.id_user = U.id_user
								WHERE I.profile = 0
								AND U.id_user in (SELECT following FROM follows WHERE follower = '$id_user_crt')
								OR I.profile = 0
								AND U.id_user = '$id_user_crt'
								ORDER BY upload_date desc");
								
	$no_result = 0;
	if (!$row = mysqli_fetch_array($posts)) {
		$no_result = 1;
	} else {
		$posts = mysqli_query($con, "SELECT U.id_user, name, id_img, username, path, upload_date, likes, description
								FROM images I join users U ON I.id_user = U.id_user
								WHERE I.profile = 0
								AND U.id_user in (SELECT following FROM follows WHERE follower = '$id_user_crt')
								OR I.profile = 0
								AND U.id_user = '$id_user_crt'
								ORDER BY upload_date desc");
	}

	if(isset($_POST["delete_comm"])) {
		$id_comm = $_POST['id_comm'];
		$con->query("DELETE from comments WHERE id_comm = '$id_comm'");

		header('location: feed.php');
		exit;
	}

	if (isset($_POST['post_comm'])) {
		$id_img = $_POST['id'];
		$id_user_logged = $_SESSION['id_user'];
		$comment = $_POST['comment'];
		$t = time();
		$data = date("Y-m-d",$t);
		$query = "INSERT INTO comments (id_user, id_img, comm, date)
				  VALUES('$id_user_logged', '$id_img', '$comment', '$data')";
		mysqli_query($con, $query);
		
		$scrollPos = (array_key_exists('scroll', $_GET)) ? $_GET['scroll'] : 0;
		header('Location: feed.php#scroll='.$scrollPos);
		exit;
	}


	if (isset($_POST['post_like'])) {
		$id_img = $_POST['id'];
		$id_user_logged = $_SESSION['id_user'];
		$likes = mysqli_query($con, "SELECT * FROM likes where id_user = '$id_user_logged' and id_img = '$id_img'");
		$like = mysqli_fetch_assoc($likes);
		
		$scrollPos = (array_key_exists('scroll', $_GET)) ? $_GET['scroll'] : 0; 
		
		if ($like) {
			$query = "UPDATE images set likes = likes - 1 Where id_img = '$id_img'";
			mysqli_query($con, $query);
			header('Location: feed.php');

			$query_delete = "DELETE from likes Where  id_user = '$id_user_logged' and id_img = '$id_img'";
			mysqli_query($con, $query_delete);
			header('Location: feed.php#scroll='.$scrollPos);
			exit;

		} else {
			$query = "INSERT INTO likes (id_user, id_img)
			VALUES('$id_user_logged', '$id_img')";
			mysqli_query($con, $query);

			$query_update = "UPDATE images set likes = likes + 1 Where id_img = '$id_img'";
			mysqli_query($con, $query_update);
			header('Location: feed.php#scroll='.$scrollPos);
			exit;
		}
	}
	
	if (isset($_POST['to_profile'])) {
		$_SESSION['profile_id_user'] = $_POST['profile_id_user'];
		header('Location: profile.php');
		exit;
	}
	
	if (isset($_POST['to_likers'])) {
		$_SESSION['likers_id_photo'] = $_POST['likers_id_photo'];
		header('Location: likers.php');
		exit;
	}
	
	if (isset($_POST['search_user'])) {
		$_SESSION['searched_user'] = $_POST['searched_user'];
		header('Location: searched.php');
		exit;
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="feed.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body style="background-image: url(./Photos/z.jpg); background-attachment: fixed; background-position: center; background-repeat: no-repeat; background-size: cover;">

	<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top">
		<div class="navbar-collapse collapse w-100 order-1 order-md-0 dual-collapse2">
			<form class="form-inline mr-auto" action="feed.php" method="post">
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
					<a class="nav-link" src="<?php $_SESSION['profile_id_user'] = $_SESSION['id_user']; $_SESSION['profile_username'] = $_SESSION['username']; ?>" href="profile.php"><i class="fa fa-id-card" aria-hidden="true"></i> PROFILE</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="index.php?logout='1'"><i class="fa fa-sign-out" aria-hidden="true"></i> LOGOUT</a>
				</li>
			</ul>
		</div>
	</nav>

<?php if ($no_result == 1) { ?>
	<img class="center" src="./Photos/no_results.jpeg" alt="Photo" style="display: block; margin-left: auto; margin-right: auto; width: 50%; opacity: 0.35; padding: 70px;">
<?php exit;} ?>

<?php while ($row = mysqli_fetch_array($posts)) { ?>


        <?php
        $id_img = $row['id_img'];
        $id_user = $row['id_user'];
        $nr_comm = mysqli_query($con, "SELECT COUNT(*) from comments where id_img = '$id_img'");
        $comm =  mysqli_query($con, "SELECT name, username, U.id_user, comm, id_comm, date FROM instastalking.comments C join instastalking.users U on C.id_user = U.id_user where id_img = '$id_img'");

        $profile =  mysqli_query($con, "SELECT path FROM images where id_user ='$id_user' and profile ='1' ");
        $res = mysqli_fetch_array($profile);
        $nr_comments =mysqli_fetch_array($nr_comm);


        $id_user_logged = $_SESSION['id_user'];
        $likes = mysqli_query($con, "SELECT * FROM likes where id_user = '$id_user_logged' and id_img = '$id_img'");
        $like = mysqli_fetch_assoc($likes);
         ?>

        <div class="page-content page-container" id="page-content">
            <div class="padding">
                <div class="row container" style="margin-left: auto; margin-right: auto;">
                    <div class="col-md-6" style="margin-left: auto; margin-right: auto;">
                        <div class="box box-widget" >
                            <div class="box-header with-border">
                                <div class="user-block">
									<img class="img-circle" src="<?php echo $res['0']; ?>" alt="User Image" style="border: 3px solid #dd9;">
									<span class="username">
										<form action="feed.php" method="post">
											<input type="text" hidden = "true"  name="profile_id_user" value="<?php echo $row['id_user'] ?>" >
											<button class="btn btn-primary" name="to_profile" onclick="submit" style="font-size:15px; margin-left:10px">
												<?php echo $row['name']; ?>
											</button><i class="description", style="margin-left: 300px; margin-top: -25px;"><?php echo $row['upload_date']; ?></i>
										</form>
									</span>
								</div>

                            </div>
							
                            <div class="box-body">
								<?php if(!empty($row['description'])) : ?>
									<blockquote class="blockquote text-right">
										<footer class="blockquote-footer"><?php echo $row['description']; ?>
										</footer>
									</blockquote>
								<?php endif; ?>
								<img class="img-responsive pad" src="<?php echo $row['path']; ?>" alt="Photo" style="width: 100%">
                                <form action="feed.php" method="post">
								
                                <input type="text" hidden = "true"  name="id" value="<?php echo $row['id_img'] ?>" >
								<?php if($like) : ?>
									<button  class="btn btn-secondary data-remember-position" style="font-size:15px; margin-left:10px" name="post_like" type="submit">Unlike
										<i class="fa fa-thumbs-down">
										</i>
									</button>
									<span class="pull-right text-muted">
										<form action="feed.php" method="post">
											<input type="text" hidden = "true"  name="likers_id_photo" value="<?php echo $row['id_img'] ?>" >
											<button class="btn btn-primary data-remember-position" name="to_likers" onclick="submit" style="font-size:12px; margin-left:10px">
												<?php echo $row['likes']; ?> likes - <?php echo $nr_comments['0']; ?> comments
											</button>
										</form>
									</span>
                                <?php else : ?>
									<button  class="btn btn-secondary data-remember-position" style="font-size:15px; margin-left:10px" name="post_like" type="submit">Like
										<i class="fa fa-thumbs-up">
										</i>
									</button>
									<span class="pull-right text-muted">
										<form action="feed.php" method="post">
											<input type="text" hidden = "true"  name="likers_id_photo" value="<?php echo $row['id_img'] ?>" >
											<button class="btn btn-primary data-remember-position" name="to_likers" onclick="submit" style="font-size:12px; margin-left:10px">
												<?php echo $row['likes']; ?> likes - <?php echo $nr_comments['0']; ?> comments
											</button>
										</form>
									</span>
                                <?php endif; ?>
								</form>
                            </div>


                            <div class="box-footer box-comments" style="max-height: fit-content ; overflow:hidden;">

                                <?php while ($row1 = mysqli_fetch_array($comm) ) { ?>	
									<?php
										$username_comm = $row1['username'];
										$profile =  mysqli_query($con, "SELECT path
																		FROM images I join users U ON I.id_user = U.id_user
																		WHERE U.username = '$username_comm'
																		AND I.profile ='1'");
										$res = mysqli_fetch_array($profile);
									?>

									<div class="box-comment">
										<img class="img-circle img-sm" src="<?php echo $res['0']; ?>" alt="User Image" style="border: 3px solid #dd9;">
										<div class="comment-text">
											<span class="username">
												<form action="feed.php" method="post">
													<input type="text" hidden = "true"  name="profile_id_user" value="<?php echo $row1['id_user'] ?>" >
													<button class="btn btn-light btn-sm" name="to_profile" onclick="submit" style="font-size:15px">
														<?php echo $row1['name']; ?>
													</button>
												</form>
												<span class="text-muted pull-right">
													<?php echo $row1['date']; ?>
												</span>
											</span>
										<?php echo $row1['comm']; ?>
										<?php if($row1['id_user'] == $_SESSION['id_user']) : ?>
											<span class="text-muted pull-right">
												<form action="feed.php" method="post">
													<input type="text" hidden = "true"  name="id_comm" value="<?php echo $row1['id_comm'] ?>" >
													<button class="btn btn-light btn-sm" name="delete_comm" onclick="submit" style="font-size:10px">
														Delete
													</button>
												</form>
											</span>
										<?php endif; ?>
										</div>
									</div>
                                <?php } ?>		
							</div>
							
							<?php
								$usr = $_SESSION['username'] ;
								$profile =  mysqli_query($con, "SELECT path
																FROM images I join users U ON I.id_user = U.id_user
																WHERE U.username = '$usr'
																AND I.profile ='1' ");
								$res = mysqli_fetch_array($profile);
							?>

                            <div class="box-footer">
                                <form action="feed.php" method="post">
									<img class="img-responsive img-circle img-sm" src="<?php echo $res['0']; ?>" alt="Alt Text" style="border: 3px solid #dd9;">
                                    <div class="img-push">
										<input id="myInput" name="comment" type="text" class="form-control input-sm" placeholder="Press enter to post comment">
									</div>
                                    <input type="text" hidden = "true"  name="id" value="<?php echo $row['id_img'] ?>" >
                                    <button name="post_comm" hidden = "true" onclick="submit" >Button</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php } ?>

<script>
var input = document.getElementById("myInput");
input.addEventListener("keyup", function(event) {
  if (event.keyCode === 13) {
   event.preventDefault();
   document.getElementById("myBtn").click();
  }
});
</script>



</body>
</html>
