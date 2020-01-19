<?php
    
   
    
?>

    <main>
            <style type="text/css">
            body { font-family: Verdana,Arial,Courier New; font-size: 0.7em; }
            th { text-align: right; padding: 0.8em; }
            #container { text-align: center; width: 500px; margin: 5% auto; }
            .msg_yes { margin: 0 auto; text-align: center; color: green; background: #D4EAD4; border: 1px solid green; border-radius: 10px; margin: 2px; }
            .msg_no { margin: 0 auto; text-align: center; color: red; background: #FFF0F0; border: 1px solid red; border-radius: 10px; margin: 2px; }
            </style>
    	
    	
 			<div id="container">
 			<h2 >Password Reset </h2>
 			
 			<p>An email will be sent to you ...</p>
 	

 			<form  action="includes/resetpwd.inc.php"  method="post">
 			
 			<input type="text" id="login" class="fadeIn second" name="mail" placeholder="Email">
 			
 			<input type= "submit" class="fadeIn fourth" name="resetpwd" value="Send Mail to reset Password">




 			</form>

 			
 			</div>

    
    </main>

<?php
    
    require "footer.php";

?>