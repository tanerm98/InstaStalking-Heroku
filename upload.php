<?php
    session_start();
	// connect to the database
	
	$id_user = $_SESSION['upload_id_user'];
	
	$con = mysqli_connect('us-cdbr-east-06.cleardb.net', 'b1cf63f2b2a47e', '20c164a2', 'heroku_4c68794ecc831a4');
	
    $get_username = mysqli_query($con, "SELECT username
								FROM users
								WHERE id_user = '$id_user'");
	$username = mysqli_fetch_array($get_username)['username'];
	
	$posts = mysqli_query($con, "SELECT U.id_user, name, id_img, username, path, upload_date, likes
								FROM images I join users U on I.id_user = U.id_user
								WHERE I.id_user = '$id_user'
								AND I.profile = 0
								ORDER BY upload_date desc");
						   
    $profile =mysqli_query($con, "SELECT path FROM images Where id_user = '$id_user' and profile = '1' ");
    $profile_img = mysqli_fetch_array($profile);

    $statusMsg="";

  if(isset($_POST["upload"])) {
     $targetDir = "./Photos/";
     $fileName = basename($_FILES["file"]["name"]);
     $targetFilePath = $targetDir . $fileName;
     $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
    // Allow certain file formats
    $allowTypes = array('jpg','png','jpeg','gif');
	$description = $_POST['description'];
    if(!empty($_FILES["file"]["name"])) {
        if(in_array($fileType, $allowTypes)) {
        // Upload file to server
             if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){
            // Insert image file name into database
                 $insert = $con->query("INSERT into images (id_user, path, upload_date, description) VALUES ($id_user,'./Photos/".$fileName."', NOW(), '$description')");
                if($insert){
                    $statusMsg = "The file ".$fileName. " has been uploaded successfully.";
                } else{
                    $statusMsg = "File upload failed, please try again.";
				}
            } else{
                $statusMsg = "Sorry, there was an error uploading your file.";
            }
        } else{
            $statusMsg = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed to upload.';
        }
    } else{
        $statusMsg = 'Please select a file to upload.';
    }
	header('location: profile.php');
  }

if(isset($_POST["upload_profile"])) {
	$targetDir = "./Photos/";
	$fileName = basename($_FILES["file"]["name"]);
	$targetFilePath = $targetDir . $fileName;
	$fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
	// Allow certain file formats
	$allowTypes = array('jpg','png','jpeg','gif','pdf');
	if(!empty($_FILES["file"]["name"])) {
		if(in_array($fileType, $allowTypes)){
		// Upload file to server
			 if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
			// Insert image file name into database

				$query_delete = "DELETE from images Where  id_user = '$id_user' and profile = '1'";
				mysqli_query($con, $query_delete);

				$insert = $con->query("INSERT into images (id_user, path, upload_date, profile) VALUES ($id_user,'./Photos/".$fileName."', NOW(), 1)");
				if($insert){
					$statusMsg = "The file ".$fileName. " has been uploaded successfully.";
				} else{
					$statusMsg = "File upload failed, please try again.";
				}
			} else{
				$statusMsg = "Sorry, there was an error uploading your file.";
			}
		} else{
			$statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
		}
	} else{
		$statusMsg = 'Please select a file to upload.';
	}

	header('location: profile.php');
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


	<div class="upload_photos_group", style="margin: auto; width: 50%; padding: 50px;">
		<form action="upload.php" method="post" enctype="multipart/form-data">
			<div class="upload_photo_single" style="padding: 50px;">
				<div class="input-group-prepend" style="height: 100px;">
					<button name="upload_profile"class="btn btn-primary btn-lg btn-block" onclick="submit"><i class="fa fa-cloud-upload" aria-hidden="true"></i> Set new profile picture</button>
				</div>
				<div class="custom-file">
					<input type="file" name="file" class="custom-file-input" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
					<label class="custom-file-label" for="inputGroupFile01">Choose photo for profile picture</label>
				</div>
			</div>
		</form>
		<br>
		<form action="upload.php" method="post" enctype="multipart/form-data">
			<div class="upload_photo_single" style="padding: 50px;">
				<div class="input-group-prepend" style="height: 100px;">
					<button name="upload" class="btn btn-primary btn-lg btn-block" onclick="submit" ><i class="fa fa-cloud-upload" aria-hidden="true"></i> Add to news feed</button>
				</div>
				<div class="custom-file">
					<input type="file" name="file" class="custom-file-input" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
					<label class="custom-file-label" for="inputGroupFile01">Choose photo for news feed</label>
					<input type="text" name="description" class="form-control input-sm" placeholder="Enter a photo description">
				</div>
			</div>
		</form>
	</div>

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
