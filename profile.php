<?php
    session_start();
	// connect to the database
	
	$id_user = $_SESSION['profile_id_user'];
	
	$con = mysqli_connect('us-cdbr-east-06.cleardb.net', 'b1cf63f2b2a47e', '20c164a2', 'heroku_4c68794ecc831a4');
	
    $get_username = mysqli_query($con, "SELECT username
								FROM users
								WHERE id_user = '$id_user'");
	$username = mysqli_fetch_array($get_username)['username'];
	
	$followers_query = mysqli_query($con, "SELECT follower
											FROM follows
											WHERE following = '$id_user'");
	$followers = mysqli_fetch_assoc($followers_query);
	
	$nr_followers_query = mysqli_query($con, "SELECT count(*) nr
											FROM follows
											WHERE following = '$id_user'");
	$nr_followers = mysqli_fetch_assoc($nr_followers_query);

	$following_query = mysqli_query($con, "SELECT following
											FROM follows
											WHERE follower = '$id_user'");
	$following = mysqli_fetch_assoc($following_query);
	
	$nr_following_query = mysqli_query($con, "SELECT count(*) nr
											FROM follows
											WHERE follower = '$id_user'");
	$nr_following = mysqli_fetch_assoc($nr_following_query);
	
	$crt_id = $_SESSION['id_user'];
	$check_follow_query = mysqli_query($con, "SELECT *
											FROM follows
											WHERE follower = '$crt_id'
											AND following = '$id_user'");
	$check_follow = mysqli_fetch_assoc($check_follow_query);
	
	$posts = mysqli_query($con, "SELECT U.id_user, name, id_img, username, path, upload_date, likes, description
								FROM images I join users U on I.id_user = U.id_user
								WHERE I.id_user = '$id_user'
								AND I.profile = 0
								ORDER BY upload_date desc");
								
	$no_result = 0;
	if (!$row = mysqli_fetch_array($posts)) {
		$no_result = 1;
	} else {
		$posts = mysqli_query($con, "SELECT U.id_user, name, id_img, username, path, upload_date, likes, description
								FROM images I join users U on I.id_user = U.id_user
								WHERE I.id_user = '$id_user'
								AND I.profile = 0
								ORDER BY upload_date desc");
	}
						   
    $profile =mysqli_query($con, "SELECT path FROM images Where id_user = '$id_user' and profile = '1' ");
    $profile_img = mysqli_fetch_array($profile);

    $statusMsg="";

	if(isset($_POST["delete_post"])) {
		$id_img = $_POST['id'];
		$query_delete = "DELETE from images Where  id_user = '$id_user' and id_img = '$id_img'";
		mysqli_query($con, $query_delete);

		header('location: profile.php');
		exit;
	}
	
	if(isset($_POST["delete_comm"])) {
		$id_comm = $_POST['id_comm'];
		$con->query("DELETE from comments WHERE id_comm = '$id_comm'");

		header('location: profile.php');
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

		$_SESSION['profile_id_user'] = $_POST['profile_id_user'];
		header('Location: profile.php#scroll='.$scrollPos);
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
			$_SESSION['profile_id_user'] = $_POST['profile_id_user'];
			header('Location: profile.php');

			$query_delete = "DELETE from likes Where  id_user = '$id_user_logged' and id_img = '$id_img'";
			mysqli_query($con, $query_delete);
			$_SESSION['profile_id_user'] = $_POST['profile_id_user'];
			header('Location: profile.php#scroll='.$scrollPos);
			exit;

		} else {
			$query = "INSERT INTO likes (id_user, id_img)
			VALUES('$id_user_logged', '$id_img')";
			mysqli_query($con, $query);

			$query_update = "UPDATE images set likes = likes + 1 Where id_img = '$id_img'";
			mysqli_query($con, $query_update);
			$_SESSION['profile_id_user'] = $_POST['profile_id_user'];
			header('Location: profile.php#scroll='.$scrollPos);
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
	
	if (isset($_POST['new_post'])) {
		$_SESSION['upload_id_user'] = $_SESSION['id_user'];
		header('Location: upload.php');
		exit;
	}
	
	if (isset($_POST['settings'])) {
		$_SESSION['settings_id_user'] = $_SESSION['id_user'];
		header('Location: settings.php');
		exit;
	}
	
	if (isset($_POST['follow'])) {
		$crt_id2 = $_SESSION['id_user'];
		$id3 = $_POST['following_id'];
		
		$query = "INSERT INTO follows (follower, following)
					VALUES('$crt_id2', '$id3')";
		mysqli_query($con, $query);

		$_SESSION['profile_id_user'] = $id3;
		header('Location: profile.php');
		exit;
	}
	
	if (isset($_POST['unfollow'])) {
		$crt_id2 = $_SESSION['id_user'];
		$id3 = $_POST['following_id'];
	
		$con->query("DELETE
						FROM follows
						WHERE follower = '$crt_id2'
						AND following = '$id3'");

		$_SESSION['profile_id_user'] = $id3;
		header('Location: profile.php');
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
							<button name="unfollow" class="btn btn-secondary" onclick="submit" ><i class="fa fa-eye-slash" aria-hidden="true"></i> Unfollow</button>
					<?php else : ?>
							<button name="follow" class="btn btn-secondary" onclick="submit" ><i class="fa fa-eye" aria-hidden="true"></i> Follow</button>
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
					<a class="nav-link" href="followers.php" src="<?php $_SESSION['followers_id_user'] = $id_user; ?>"><?php echo $nr_followers['nr'] ?> FOLLOWERS</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="following.php" src="<?php $_SESSION['following_id_user'] = $id_user; ?>"><?php echo $nr_following['nr'] ?> FOLLOWING</a>
				</li>
			</ul>
		</div>
	</nav>

<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Modal header</h3>
  </div>
  <div class="modal-body">
    <p>One fine body…</p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-primary">Save changes</button>
  </div>
</div>

<?php if ($no_result == 1) { ?>
	<img class="center" src="./Photos/no_results.jpeg" alt="Photo" style="display: block; margin-left: auto; margin-right: auto; width: 50%; opacity: 0.35; padding: 70px;">
<?php exit;} ?>

<?php while ($row = mysqli_fetch_array($posts)) { ?>
        <?php
        $id_img = $row['id_img'];
        $id_user2 = $row['id_user'];
        $nr_comm = mysqli_query($con, "SELECT COUNT(*) from comments where id_img = '$id_img'");
        $comm =  mysqli_query($con, "SELECT name, username, U.id_user, comm, id_comm, date FROM comments C join users U on C.id_user = U.id_user where id_img = '$id_img'");

        $profile =  mysqli_query($con, "SELECT path FROM images where id_user ='$id_user2' and profile ='1' ");
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
                        <div class="box box-widget">
                            <div class="box-header with-border">
                                <div class="user-block">
									<i class="description", style="margin-left: 0px; margin-top: 0px;"><?php echo $row['upload_date']; ?></i>
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
                                <form action="profile.php" method="post">
								
                                <input type="text" hidden = "true"  name="id" value="<?php echo $row['id_img'] ?>" >
								<?php if($like) : ?>
									<button  class="btn btn-secondary" style="font-size:15px; margin-left:10px" name="post_like" type="submit">Unlike
										<i class="fa fa-thumbs-down">
										</i>
									</button>
									<span class="pull-right text-muted">
										<form action="profile.php" method="post">
											<input type="text" hidden = "true"  name="likers_id_photo" value="<?php echo $row['id_img'] ?>" >
											<input type="text" hidden = "true"  name="profile_id_user" value="<?php echo $row['id_user'] ?>" >
											<?php if($id_user2 == $_SESSION['id_user']) : ?>
												<button class="btn btn-secondary pull-right" name="delete_post" onclick="submit" style="font-size:17px;margin-right:20px ;"><i class="fa fa-trash-o"></i></button>
											<?php endif; ?>
											<button class="btn btn-primary" name="to_likers" onclick="submit" style="font-size:12px; margin-left:10px">
												<?php echo $row['likes']; ?> likes - <?php echo $nr_comments['0']; ?> comments
											</button>
										</form>
									</span>
                                <?php else : ?>
									<button  class="btn btn-secondary" style="font-size:15px; margin-left:10px" name="post_like" type="submit">Like
										<i class="fa fa-thumbs-up">
										</i>
									</button>
									<span class="pull-right text-muted">
										<form action="profile.php" method="post">
											<input type="text" hidden = "true"  name="likers_id_photo" value="<?php echo $row['id_img'] ?>" >
											<input type="text" hidden = "true"  name="profile_id_user" value="<?php echo $row['id_user'] ?>" >
											<?php if($id_user2 == $_SESSION['id_user']) : ?>
												<button class="btn btn-secondary pull-right" name="delete_post" onclick="submit" style="font-size:17px;margin-right:20px ;"><i class="fa fa-trash-o"></i></button>
											<?php endif; ?>
											<button class="btn btn-primary" name="to_likers" onclick="submit" style="font-size:12px; margin-left:10px">
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
												<form action="profile.php" method="post">
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
												<form action="profile.php" method="post">
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
                                <form action="profile.php" method="post">
									<img class="img-responsive img-circle img-sm" src="<?php echo $res['0']; ?>" alt="Alt Text" style="border: 3px solid #dd9;">
                                    <div class="img-push">
										<input id="myInput" name="comment" type="text" class="form-control input-sm" placeholder="Press enter to post comment">
									</div>
                                    <input type="text" hidden = "true"  name="id" value="<?php echo $row['id_img'] ?>" >
									<input type="text" hidden = "true"  name="profile_id_user" value="<?php echo $row['id_user'] ?>" >
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
