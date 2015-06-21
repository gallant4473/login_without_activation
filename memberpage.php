<?php require('includes/config.php'); 

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: index.php'); } 

//define page title
$title = 'Members Page';

//include header template
require('layout/header.php'); 
?>

<div class="container" style="margin-top:10%;>

	<div class="clearfix">
		 <div class="col-md-3"></div>
	    <div class="col-md-6" style="background:#F5F5F5; margin-top:15px; padding:5%;border-radius:5px;box-shadow: 0px 8px 17px 0px rgba(0, 0, 0, 0.2), 0px 6px 20px 0px rgba(0, 0, 0, 0.19);z-index: 200;">
			
				<h2 style="color:#2E6DA4;">My Account Information </h2>
				<hr>
				<?php $email = $_SESSION['email']; 
				$stmt = $db->prepare('SELECT * FROM members WHERE email = :username' );
			$stmt->execute(array('username' => $email));
			$row = $stmt->fetch();
			?>
				<p style="font-size:16px;"><span class="col-md-4" style="background:lightgrey;padding:5px;padding-left:35px;padding-right:35px;font-size:16px;text-align:center;color:#2E6DA4;border-radius:10px;">Name</span><span style="padding-top:10px;padding-left:35px;"> <?php echo $row['username'];?></span></p><br>
				<p style="font-size:16px;"><span class="col-md-4" style="background:lightgrey;padding:5px;padding-left:35px;padding-right:35px;font-size:16px;text-align:center;color:#2E6DA4; border-radius:10px;">Email</span><span style="padding:10px;padding-left:35px;"> <?php echo $row['email'];?></span></p><br>
				<p style="font-size:16px;"><span class="col-md-4" style="background:lightgrey;padding:5px;padding-left:35px;padding-right:35px;text-align:center;font-size:16px;color:#2E6DA4;border-radius:10px;">DOJ</span><span style="padding:10px;padding-left:35px;"> <?php echo $row['date'];?></span></p><br>
				<hr>
				<div align="right">
				<a class="btn btn-primary btn-lg" href='logout.php' >Logout</a>
				</div>
				

		</div>
		 <div class="col-md-3"></div>
	</div>


</div>

<?php 
//include header template
require('layout/footer.php'); 
?>
