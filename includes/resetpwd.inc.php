<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'dbh.inc.php';

if(isset($_POST['resetpwd'])){

	$email = $_POST['mail'];
	if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
		echo '<script type="text/javascript">'; 
		echo 'alert("Enter valid E-mail .... ");'; 
		echo 'window.location = "../resetPassword.php";';
		echo '</script>';
	

	} else{


		/*$sql ="select * from userData where email=?;";
		$stmt = mysqli_stmt_init($conn);

		if(mysqli_stmt_prepare($stmt, $sql)){

			mysqli_stmt_bind_param($stmt , "s" , $email);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			$rows = mysqli_stmt_num_rows($stmt);
			*/
            $ldapHost = "ldap://192.168.67.190";
            $ldapPort = "12320";
            $ldapUser ="cn=admin,dc=testldap";
            $ldapPswd ="pass@123";

            $ldapLink =ldap_connect($ldapHost, $ldapPort)
                or die("Can't establish LDAP connection");

            if (ldap_set_option($ldapLink,LDAP_OPT_PROTOCOL_VERSION,3))
            {
                echo "Using LDAP v3 ";
            }else{
                echo "Failed to set version to protocol 3";
            }


            if(ldap_bind($ldapLink,$ldapUser,$ldapPswd)){
                
                $filter = "(uid=$email)";
                $res = ldap_search($ldapLink,"dc = testldap",$filter);
                $entries = ldap_get_entries($ldapLink, $res);
               
                
                if($entries['count'] != 1){
				echo '<script type="text/javascript">'; 
				echo 'alert("User not registered.. ");'; 
				echo 'window.location = "../resetPassword.php";';
				echo '</script>';
				exit();
			}else{

		$selector = bin2hex(random_bytes(8));
		$token = random_bytes(32);

		$url = "localhost:80/includes/newpwd.inc.php?selector=" . $selector . "&validator=" . bin2hex($token);
 
		$expires = date("U")+1800;

		

		//for deleting extra tokens if exists

		$sql ="delete from pwdrst where pwdrstEmail=?;";
		$stmt = mysqli_stmt_init($conn);

		if(mysqli_stmt_prepare($stmt, $sql)){

			mysqli_stmt_bind_param($stmt , "s" , $email);
			mysqli_stmt_execute($stmt);

		}else{

			echo '<script type="text/javascript">'; 
			echo 'alert("Database Error .... ");'; 
			echo 'window.location = "../resetPassword.php";';
			echo '</script>';
			exit();

		}

		//for inserting token data into database

		$sql = "insert into pwdrst (pwdrstEmail,pwdrstSelector,pwdrstToken,pwdrstExpires) values (?,?,?,?) ;";
		$stmt = mysqli_stmt_init($conn);

		if(mysqli_stmt_prepare($stmt, $sql)){
			$htoken = password_hash($token, PASSWORD_DEFAULT);
			mysqli_stmt_bind_param($stmt , "ssss" , $email , $selector , $htoken , $expires);
			mysqli_stmt_execute($stmt);

			mysqli_stmt_close($stmt);
			mysqli_close($conn);


		}else{

			echo '<script type="text/javascript">'; 
			echo 'alert("Database Error .... ");'; 
			echo 'window.location = "../resetPassword.php";';
			echo '</script>';
			exit();

		}

		// sending the mail

		

		require '../PHPMailer/src/Exception.php';
		require '../PHPMailer/src/PHPMailer.php';
		require '../PHPMailer/src/SMTP.php';
		$mail = new PHPMailer(true);

		try{

			$mail->isSMTP(); 
		    $mail->Host       = 'smtp.gmail.com';                    
		    $mail->SMTPAuth   = true;                                   
		    $mail->Username   = 'brahma.joy.jb@gmail.com';                     
		    $mail->Password   = 'joy708927';                              
		   
		    $mail->SMTPSecure = 'ssl';												         

		    $mail->Port       = '465'; 
		    

		    //Recipients
		    $mail->setFrom('tester@gmail.com');
		    
		    

		    // Attachments
		    $mail->AddAddress($email);   

		    // Content
		    $mail->isHTML(true);                                  
		    $mail->Subject = 'Password Reset for Login';
		    $mail->Body    = 'We received a request for password reset. If this doesnot concern you please 
		    				  ignore. Click on the following link or paste in a WEB_BROWSER(Chrome or Firefox)
		    				  to reset your password.<p>Here is your password reset link :</br> 
		    				  <a href = "'. $url .'"> '. $url .' </a>
		    				  

		    				  </p>';
		    

		    $mail->send();
		    echo '<script type="text/javascript">'; 
			echo 'alert("Mail Sent successfully ");'; 
			echo 'window.location = "../index.php";';
			echo '</script>';
			exit();
		    

		}catch(Exception $e){

			echo '<script type="text/javascript">'; 
			echo 'alert("Mailer error ");'; 
			echo 'window.location = "../resetPassword.php";';
			echo '</script>';
			exit();

		}





	}

}

}
}else{
	header("Location: ../index.php");
}
