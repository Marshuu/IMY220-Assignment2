<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);
	$checkNewFiles = true;

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;	

	$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
	$res = $mysqli->query($query);
	$row = mysqli_fetch_array($res);
	$userid = $row["user_id"];



	//print_r($userimages);

	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Name Surname">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form method='POST' action='login.php' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' multiple='multiple'/><br/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submitUpload' />
									<input type='hidden' value='".$email."' name='loginEmail'/>
									<input type='hidden' value='".$pass."' name='loginPass'/>
								</div>
							  </form>";
							  
					if(isset($_FILES["picToUpload"])||$email && $pass){
						
						echo "<h1>imageGallery</h1>
							<div class='row imageGallery'>";
							$imgQuery = "SELECT filename FROM tbgallery WHERE user_id = $userid" ;
							// echo $imgQuery;
							$res = $mysqli->query($imgQuery);
							while($userimages = mysqli_fetch_array($res))
							{
								// print_r($userimages);
								// echo "$userimages[0]";
								$imgname = $userimages[0];
								// echo"</br>";
								$str = 'gallery/'.$imgname;
								// echo "$str";
								echo'<div class="col-3" style="background-image: url('.$str.')"></div>';
							}
							
							echo "</div>";	
							// header("Refresh:0");	
							// $secondsWait = 1;
							
							// 	echo '<meta http-equiv="refresh" content="'.$secondsWait.'">';
						}
						
						
							
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}


			/******************************New code****************************************/

			//1. upload image check  
			if (isset($_POST["submitUpload"])) 
			{	
			
				
				$target_dir = "gallery/"; 
				$uploadFiles = $_FILES["picToUpload"];
				$numFiles = count($uploadFiles["name"]);    //counts names of all uploaded files
				$target_file ="";

				for ($i=0; $i < $numFiles; $i++) 
				{ 				
					$target_file = $target_dir. basename($uploadFiles["name"][$i]); 
					$ext = pathinfo($target_file, PATHINFO_EXTENSION);
					$allowed = array('jpg', 'jpeg');   

					if (!in_array($ext, $allowed)) 
					{
						echo 	'<div class="alert alert-danger mt-3" role="alert">
									That is not jpg/jpeg !
								</div>';
					}
					else if ( !($uploadFiles["size"][$i]/1024)>1000) {
						echo 	'<div class="alert alert-danger mt-3" role="alert">
									The file is too small !
								</div>';
					}
					else
					{                    
						if(move_uploaded_file($uploadFiles["tmp_name"][$i], $target_file))
						{
							$checkNewFiles = true;
							$filename = basename($uploadFiles["name"][$i]);

							$query = "INSERT INTO tbgallery (user_id, filename) VALUES ('$userid', '$filename');";

							$res = mysqli_query($mysqli, $query) == TRUE;
							// echo"</br> The file ". basename($uploadFiles["name"][$i]) . " has been uploaded.</br>";

						}
					}                    
				}
			}


		?>
	</div>
</body>
</html>