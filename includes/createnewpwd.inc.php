<?php

require 'dbh.inc.php';

$selector = $_POST["selector"];
$validator = $_POST["validator"];
$newPassword = $_POST["pwd"];
$newPasswordCnf = $_POST["pwdrpt"];



if(empty($selector) || empty($validator) || ctype_xdigit($selector) === false || ctype_xdigit($validator) === false){
    echo '<script type="text/javascript">'; 
    echo 'alert("line 10.. ");'; 
    echo 'window.location = "../resetPassword.php";';
    echo '</script>';
    exit();
}else{
    
    $date = date("U");
    $sql = "select * from pwdrst where pwdrstSelector = ? and pwdrstExpires >= ?;";
    $stmt = mysqli_stmt_init($conn);
    if(mysqli_stmt_prepare($stmt,$sql)){

        mysqli_stmt_bind_param($stmt, "ss" , $selector , $date);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(!$row = mysqli_fetch_assoc($result)){
            echo '<script type="text/javascript">'; 
            echo 'alert("invalid tokens or tokens expired..line 26 ");'; 
            echo 'window.location = "../resetPassword.php";';
            echo '</script>';
            exit();
        } else{
            
            $tokenBin = hex2bin($validator);
            $tokenCheck = password_verify($tokenBin , $row["pwdrstToken"]);
            if($tokenCheck === false){
                
                echo '<script type="text/javascript">'; 
                echo 'alert("invalid tokens ... line 37");'; 
                echo 'window.location = "../resetPassword.php";';
                echo '</script>';
                exit();
                
            } else{
                
                  global $message;
                  global $message_css;

                  $server = "ldap://192.168.67.190";
                  $dn = "cn=admin,dc=testldap";
                  $ldapPort = "12320";
                  $adminPassword = "pass@123";
                  $user = $row["pwdrstEmail"];  

                  error_reporting(0);
                  ldap_connect($server);
                  $con = ldap_connect($server,$ldapPort);
                  ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);

                  // bind anon and find user by uid
                  $user_search = ldap_search($con,"dc=testldap","(uid=$user)");
                  $user_get = ldap_get_entries($con, $user_search);
                  $user_entry = ldap_first_entry($con, $user_search);
                  $user_dn = ldap_get_dn($con, $user_entry);
                  $user_id = $user_get[0]["uid"][0];
                  $user_givenName = $user_get[0]["givenName"][0];
                  $user_search_arry = array( "*", "ou", "uid", "mail", "passwordRetryCount", "passwordhistory" );
                  $user_search_filter = "(uid=$user_id)";
                  $user_search_opt = ldap_search($con,$user_dn,$user_search_filter,$user_search_arry);
                  $user_get_opt = ldap_get_entries($con, $user_search_opt);
                  $passwordRetryCount = $user_get_opt[0]["passwordRetryCount"][0];
                  $passwordhistory = $user_get_opt[0]["passwordhistory"][0];


                  /* Start the testing */
                  if ( $passwordRetryCount == 3 ) {
                    //$message[] = "Error E101 - Your Account is Locked Out!!!";
                    echo '<script type="text/javascript">'; 
                    echo 'alert("Error E101 - Your Account is Locked Out!!!");'; 
                    echo 'window.location = "../resetPassword.php";';
                    echo '</script>';
                    exit();
                    
                  }
                  if (ldap_bind($con, $dn, $adminPassword) === false) {
                   // $message[] = "Error E101 - You donot have admin privillages.";
                    echo '<script type="text/javascript">'; 
                    echo 'alert("Error E101 - You donot have admin privillages.");'; 
                    echo 'window.location = "../resetPassword.php";';
                    echo '</script>';
                    exit();
                  }
                  if ($newPassword != $newPasswordCnf ) {
                   // $message[] = "Error E102 - Your New passwords do not match!";
                    echo '<script type="text/javascript">'; 
                    echo 'alert("Error E102 - Your New passwords do not match!");'; 
                    echo 'window.location = "../resetPassword.php";';
                    echo '</script>';
                    exit();
                  }
                  $encoded_newPassword = "{SHA}" . base64_encode( pack( "H*", sha1( $newPassword ) ) );

           
                  if (!$user_get) {
                    echo '<script type="text/javascript">'; 
                    echo 'alert("Error E202 - Unable to connect to server!");'; 
                    echo 'window.location = "../resetPassword.php";';
                    echo '</script>';
                    exit();
                  }

                  $auth_entry = ldap_first_entry($con, $user_search);
                  $mail_addresses = ldap_get_values($con, $auth_entry, "mail");
                  $given_names = ldap_get_values($con, $auth_entry, "givenName");
                  $password_history = ldap_get_values($con, $auth_entry, "passwordhistory");
                  $mail_address = $mail_addresses[0];
                  $first_name = $given_names[0];

                  /* And Finally, Change the password */
                  $entry = array();
                  $entry["userPassword"] = "$encoded_newPassword";

                  if (ldap_modify($con,$user_dn,$entry) === false){
                    $error = ldap_error($con);
                    $errno = ldap_errno($con);
                    echo '<script type="text/javascript">'; 
                    echo 'alert("Error E203 - Invalid privilages!");'; 
                    echo 'window.location = "../resetPassword.php";';
                    echo '</script>';
                    exit();
                  } else {
                      
                    echo '<script type="text/javascript">'; 
                    echo 'alert("your password has been changed successfully....try logging in again");'; 
                    echo 'window.location = "../resetPassword.php";';
                    echo '</script>';
                    exit();
                  
                  }



            }


    }
        
        
    }  else{
        
            echo "sorry";
    }

}
