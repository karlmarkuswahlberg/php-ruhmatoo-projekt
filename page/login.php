<?php
    //loome AB ühenduse
    require_once("functions.php");
    require_once("../classes/User.class.php");
 
	$User = new User($mysqli);

 //kontrollin, kas sessiooni muutuja on olemas 
    if(isset($_SESSION['logged_in_user_id'])){
        header("Location: data.php");
    }

   
    //ERRORid
    $email_error="";
    $password_error="";
	$create_password_error="";
	$firstname_error="";
	$lastname_error="";
	$create_email_error="";
	
    //Muutujad väärtustega
    $email = "";
    $password ="";
    $create_password="";
    $firstname="";
    $lastname="";
    $create_email="";
    
      
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
    //Sisse logimine
        if(isset($_POST["login"])){
            if(empty($_POST["email"])){
                $email_error ="Ei saa olla tühi";
            }else{
                //muutuja puhastamine 
                $email = cleanInput($_POST["email"]);            
            }
            
            if(empty($_POST["password"])){
                $password_error="Ei saa olla tühi";
            }else{
                $password=cleanInput($_POST["password"]);
            }
            //Login sisse 
            if($password_error == "" && $email_error == ""){

                $hash = hash("sha512", $password);
                $login_response = $User->logInUser($email, $hash);
                                         
				if (isset($login_response->success)){
					$_SESSION["user_id"] = $login_response->success->user->id;
					$_SESSION["user_email"] = $login_response->success->user->email;
					
				$_SESSION["login_message"] = $login_response->success->message;
				}						 
                }
            }
            
            
        if(isset($_POST["create"])){
        
            if(empty($_POST["firstname"])){
                $firstname_error="Kohustuslik väli";
            }else{
                $firstname = cleanInput($_POST["firstname"]);            
            }
            if(empty($_POST["lastname"])){
                $lastname_error="Kohustuslik väli";
            }else{
                $lastname = cleanInput($_POST["lastname"]);            
            }
            if(empty($_POST["create_email"])){
                $create_email_error="Kohustuslik väli";
            }else{
                $create_email = cleanInput($_POST["create_email"]);            
            }
            if(empty($_POST["create_password"])){
                $create_password_error="Ei saa olla tühi";
            }else{

                if(strlen($_POST["create_password"]) < 8){
                    $create_password_error="Peab olema vähemalt 8 sümbolit";
                }else{

                    $create_password = cleanInput($_POST["create_password"]);            
            }
                
        }

            if($firstname_error == "" && $lastname_error == "" && $create_email_error == "" && $create_password_error == "" ){
               // echo "Võib kasutajat luua! Kasutajanimi on ".$create_email." ja parool on ".$create_password;
                $hash = hash ("sha512", $create_password);
                
                $response = $User->createUser($create_email, $hash);  
            }
        } //create if end 
    }
    
    function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
    }
    
    
       
?> 

<?php
    //lehe nimi
    $page_title="Logi sisse";

?>
    <br>
	
	<div class="container-fluid">
		

<?php
    require_once("../header.php");
?>
		<p>Tegemist on lehega, kus on võimalik eelregistreerida erinevatele spordisündmustele</p>
		
        <h2>Logi sisse</h2>
		
		<?php if(isset($login_response->error)): ?>
		<p style="color:red;"><?=$login_response->error->message;?></p>
		 <?php endif; ?>
		

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input name="email" type="email" placeholder="E-post" value="<?php echo $email; ?>">* <?php echo $email_error;?> <br><br>
        <input name="password" type="password" placeholder="Parool">* <?php echo $password_error;?> <br><br>
        <input name= "login"type="submit" value="Logi sisse">
        </form>
        

        <h2>Loo kasutaja</h2>

		 
  <?php if(isset($response->success)): ?>
  
  <p style="color:green;"><?=$response->success->message;?><p>
  
  <?php elseif(isset($response->error)): ?>
  
  <p style="color:red;"><?=$response->error->message;?><p>
  
  <?php endif; ?>
		

		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="text" name="firstname" placeholder="Eesnimi" value="<?php echo $firstname; ?>">* <?php echo $firstname_error;?><br><br>
		<input type="text" name="lastname" placeholder="Perenimi" value="<?php echo $lastname; ?>">*<?php echo $lastname_error;?><br><br>
		<input type="email" name="create_email" placeholder="E-post" value="<?php echo $create_email; ?>">*<?php echo $create_email_error;?><br><br>
		<input type="password" name="create_password" placeholder="Parool" value="<?php echo $create_password; ?>">*<?php echo $create_password_error;?><br><br>
		<input type="submit" name="create" value="Loo">
		
		</form>
	<div>
	
<?php require_once("../footer.php"); ?> 

