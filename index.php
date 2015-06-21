<?php require('includes/config.php'); 

//if logged in redirect to members page
if( $user->is_logged_in() ){ header('Location: memberpage.php'); } 

//if form has been submitted process it
if(isset($_POST['submit'])){

	//very basic validation
	if(strlen($_POST['username']) < 3){
		$error[] = 'Username is too short.';
	} else {
		$stmt = $db->prepare('SELECT username FROM members WHERE username = :username');
		$stmt->execute(array(':username' => $_POST['username']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if(!empty($row['username'])){
			$error[] = 'Username provided is already in use.';
		}
			
	}

	if(strlen($_POST['password']) < 3){
		$error[] = 'Password is too short.';
	}

	if(strlen($_POST['passwordConfirm']) < 3){
		$error[] = 'Confirm password is too short.';
	}

	if($_POST['password'] != $_POST['passwordConfirm']){
		$error[] = 'Passwords do not match.';
	}

	//email validation
	if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
	    $error[] = 'Please enter a valid email address';
	} else {
		$stmt = $db->prepare('SELECT email FROM members WHERE email = :email');
		$stmt->execute(array(':email' => $_POST['email']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if(!empty($row['email'])){
			$error[] = 'Email provided is already in use.';
		}
			
	}


	//if no errors have been created carry on
	if(!isset($error)){

		//hash the password
		$hashedpassword = $user->password_hash($_POST['password'], PASSWORD_BCRYPT);

		//create the activasion code
		$activasion = md5(uniqid(rand(),true));

		try {

			//insert into database with a prepared statement
			$stmt = $db->prepare('INSERT INTO members (username,password,email,date) VALUES (:username, :password, :email, :date)');
			$stmt->execute(array(
				':username' => $_POST['username'],
				':password' => $hashedpassword,
				':email' => $_POST['email'],
				':date' => $_POST['date']
			));
			$id = $db->lastInsertId('memberID');
			
			//redirect to index page
			header('Location: index.php?action=joined');
			exit;

		//else catch the exception and show the error.
		} catch(PDOException $e) {
		    $error[] = $e->getMessage();
		}

	}

}

//check if already logged in move to home page
if( $user->is_logged_in() ){ header('Location: index.php'); } 

//process login form if submitted
if(isset($_POST['login'])){

	$username1 = $_POST['email1'];
	$password1 = $_POST['password1'];
	$date1 = $_POST['date1'];
	if($user->login($username1,$password1,$date1)){ 
		$_SESSION['email'] = $username1;
		header('Location: memberpage.php');
		exit;
	
	} else {
		$error1[] = 'Wrong email or password or date of join.';
	}

}//end if submit


//define page title
$title = 'Demo';

//include header template
require('layout/header.php'); 
?>


<div class="container" style="margin-top:10%; ">

	<div class="clearfix">
		<div class="col-md-1"></div>
		<div class="col-md-5" style="">
				<div class="col-md-1"></div>
				<div class="col-md-8" style="background:#F5F5F5; margin-top:15px; padding:5%;border-radius:5px;box-shadow: 0px 8px 17px 0px rgba(0, 0, 0, 0.2), 0px 6px 20px 0px rgba(0, 0, 0, 0.19);z-index: 200;">
					<form role="form" method="post" action="" autocomplete="off">
						<h2 style="color:#2E6DA4;">Create Account</h2>
						<br />
						<?php
						//check for any errors
						if(isset($error)){
							foreach($error as $error){
								echo '<p class="bg-danger">'.$error.'</p>';
							}
						}
		
						//if action is joined show sucess
						if(isset($_GET['action']) && $_GET['action'] == 'joined'){
							echo "<h4 class='bg-success'>Registration successfull.</h4>";
						}
						?>
		
						<div class="form-group">
							<input type="text" name="username" id="username" class="form-control input-lg" placeholder="Name" value="<?php if(isset($error)){ echo $_POST['username']; } ?>" tabindex="1" required>
						</div>
						<div class="form-group">
							<input type="email" name="email" id="email" class="form-control input-lg" placeholder="Email Address" value="<?php if(isset($error)){ echo $_POST['email']; } ?>" tabindex="2" required>
						</div>
						<div class="form-group">
							<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" tabindex="3" required>
						</div>
						<div class="form-group">
							<input type="password" name="passwordConfirm" id="passwordConfirm" class="form-control input-lg" placeholder="Retype Password" tabindex="4" required>
						</div>
						<div class="form-group">
							<input class="form-control input-md" type="text" name="date" placeholder="Date of join" id="datepicker1" required>
						</div>
						<div class=""><input type="submit" name="submit" value="Create" class="btn btn-primary btn-lg" tabindex="5"></div>
						
					</form>
				</div>
				<div class="col-md-3"></div>
		</div>
		<div class="col-md-5" >
				<div class="col-md-3"></div>
				<div class="col-md-8" style="background:#F5F5F5;margin-top:15px; padding:5%;border-radius:5px;box-shadow: 0px 8px 17px 0px rgba(0, 0, 0, 0.2), 0px 6px 20px 0px rgba(0, 0, 0, 0.19);z-index: 200;">
					<form role="form" method="post" action="" autocomplete="off">
						<h2 style="color:#2E6DA4;">Sign In</h2>
							<br />
						<?php
						//check for any errors
						if(isset($error1)){
							foreach($error1 as $error1){
								echo '<p class="bg-danger">'.$error1.'</p>';
							}
						}
		
						if(isset($_GET['action'])){
		
							//check the action
							switch ($_GET['action']) {
								case 'active':
									echo "<h4 class='bg-success'>Your account is now active you may now log in.</h4>";
									break;
								case 'reset':
									echo "<h4 class='bg-success'>Please check your inbox for a reset link.</h4>";
									break;
								case 'resetAccount':
									echo "<h4 class='bg-success'>Password changed, you may now login.</h4>";
									break;
							}
		
						}
		
						
						?>
		
						<div class="form-group">
							<input type="email" name="email1" id="email1" class="form-control input-lg" placeholder="Email" value="<?php if(isset($error)){ echo $_POST['email1']; } ?>" tabindex="1" required>
						</div>
		
						<div class="form-group">
							<input type="password" name="password1" id="password1" class="form-control input-lg" placeholder="Password" tabindex="3" required>
						</div>
						<div class="form-group">
							<input class="form-control input-md" type="text" name="date1" placeholder="Date of join" id="datepicker" required>
						</div>
						<div class=""><input type="submit" name="login" value="Login" class="btn btn-primary  btn-lg" tabindex="5"><span style="padding-left:10px;"><a href='reset.php' >Forgot your Password?</a></span>
						</div>
					</form>
				</div>
				<div class="col-md-1"></div>
		</div>
		<div class="col-md-1"></div>
	</div>

</div>

<script>

  $(function() {
    $( "#datepicker" ).datepicker();
  });
  $(function() {
    $( "#datepicker1" ).datepicker();
  });
  </script>
<?php 
//include header template
require('layout/footer.php'); 
?>
