<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...

if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
if(isset($_POST["submit"])) {
	
	$url = 'http://192.168.31.77:1000';
	$data = array('user' => $_SESSION['name'], 'info' => $_POST['info']);
	// use key 'http' even if you send the request to https://...
	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($data)
	    )
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	if ($result === FALSE) { /* Handle error */ 
	var_dump($result);
	}
}		
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<?php
		if($_SESSION['darkmode'] === false)
		{
			echo '<link href="second.css" rel="stylesheet" type="text/css">';
		}else{
			echo '<link href="dark.css" rel="stylesheet" type="text/css">';
		}
		?>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	</head>
  <nav class="navtop">
		<div>
			<h1>Blog Post site</h1>
			<a href="home.php"><i class="fas fa-home-alt"></i>home</a>
			<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
			<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
		</div>
	</nav>



<div class="row">
  <div class="leftcolumn">
    <?php

		$json = file_get_contents("http://192.168.31.77:1000/posts");
		$obj = json_decode($json);
		
		foreach($obj as $post){
			echo '<div class="card"';
			echo " id = "."$post->post_id".">";
			echo "<h5>" ."This post was made by: "  ."$post->post_user". "</h5>" . "<p>". $post->post_info . "</p>";
			echo '</div> ';
		}
	?>
  </div>
  <div class="rightcolumn">
    <div class="card">
      <form method = "post">
	  	<div class="form-input">
        	<input type="info" name="info" placeholder="Type your post "/>
		</div>
		<div class="form-input">
        	<input type="submit" value="Post" class="submit" name ="submit"/>
		</div>
      </form>
    </div>
	<div class="card">
    <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <input class="form-control" type="file" name="uploadfile" value="" />
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit" name="upload">UPLOAD</button>
            </div>
        </form>
		
		
		<?php
		$name = $_SESSION['name'];
		$con = mysqli_connect("db", "user", "password", "appDB");
		if(!empty($_FILES["uploadfile"]["name"])) {
			if (isset($_POST['upload'])) {
				// Get file info 
				$filename = $_FILES["uploadfile"]["name"];
				$tempname = $_FILES["uploadfile"]["tmp_name"];
				$imgContent =  "0x" . bin2hex(file_get_contents($tempname));
				$insert = $con->query("INSERT INTO `images` (`image_id`, `user_id`, `imageData`) VALUES (NULL, '$name', $imgContent);"); 
				
			}
		}
		?>
		</div>
    
	<div class="card">
      <h3>Popular Post</h3>
      <?php 
		// Include the database configuration file  
		$con = mysqli_connect("db", "user", "password", "appDB");
		// Get image data from database 
		$result = $con->query("SELECT * FROM images "); 	
	?>

	<?php if($result->num_rows > 0){ ?> 
		<div class="gallery"> 
			<?php while($row = $result->fetch_assoc()){ 
				$img = ($row['imageData']);
				$sub = substr($test,2);
				echo '<img style="width:100px;height:100px;" src="data:image/jpeg;base64,'.base64_encode($img).' "/>';
				}
				?> 
		</div> 
	<?php }else{ ?> 
		<p class="status error">Image(s) not found...</p> 
	<?php } ?>
		</div>
  </div>
</div>
</body>
</html>