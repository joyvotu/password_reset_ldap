
    


            
       
        <main> 
        <?php 
        $selector = $_GET["selector"];
        $validator = $_GET["validator"];
        ?>
        <style type="text/css">
        body { font-family: Verdana,Arial,Courier New; font-size: 0.7em; }
        th { text-align: right; padding: 0.8em; }
        #container { text-align: center; width: 500px; margin: 5% auto; }
        .msg_yes { margin: 0 auto; text-align: center; color: green; background: #D4EAD4; border: 1px solid green; border-radius: 10px; margin: 2px; }
        .msg_no { margin: 0 auto; text-align: center; color: red; background: #FFF0F0; border: 1px solid red; border-radius: 10px; margin: 2px; }
        </style>
        <div id="container">   
        <p>Your new password must be 8 characters long or longer and have at least:<br/>
        one capital letter, one lowercase letter, &amp; one number.<br/>
        You must use a new password.</p>
       
        <form action="createnewpwd.inc.php" name="passwordChange" method="post">
        <input type="hidden" name="selector" value="<?php echo $selector?>">
        <input type="hidden" name="validator" value="<?php echo $validator?>">
            <div>
                <input type="password" name="pwd" placeholder="Enter a new password....">
            </div>
            <div>
                <input type="password" name="pwdrpt" placeholder="Repeat new password....">
            </div>  
        <button type="submit" name="resetpwd" > Reset Password</button>
        </div>
            
        
        </main>
            
        </form>
        
            
