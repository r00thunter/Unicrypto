<?php 
session_start(); 
if(isset($_SESSION['user_id']) && isset($_SESSION['token'])) {
    include 'config/constants.php';
    $login_page = BASE_URL . 'api/admin/index.php';
    header("Location: $login_page");
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Bitexchange Referral - Admin Panel For Cryptocurrency Exchange script Referral program.</title>

    <!-- Bootstrap Core CSS -->
    <link href="public/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="public/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="public/dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="public/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">

               <h3 style="color:#000;">Bitexchange Referral</h3>
              <!--  <img src="public/Logo.png" style="margin-top: 30px;margin-left: 50px;margin-bottom: 10px;"> -->
                <div class="login-panel panel panel-default" style="margin-top: 0px;">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div> 
                    <div class="panel-body">
                        <form role="form" name="login-form" action="" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input required class="form-control" placeholder="E-mail" name="email" type="email" autofocus>
                                </div>
                                <div class="form-group">
                                    <input required class="form-control" placeholder="Password" name="password" type="password" value="">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                    </label>
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button type="submit" class="btn btn-lg btn-success btn-block"> 
                                    Login 
                                </button>
                            </fieldset>

                        </form>
                    </div>
                    </div>


                    <div class="panel">
                    <p style="font-size: medium;">Using this Admin Panel, you can do the following:</p>

                    <p>- View all the users who have registered in your Exchange.</p>
                    <p>- Check the Bonus points earned by each user ( and also edit it )</p>
                    <p>- Check the users reffered by each user.</p>
                    <p>- Edit the Referral Program settings ie. The bonus of referrar & Referral, Value of each bonus in fiat etc.</p>
                    </div>

                    
                    <?php if(isset($_SESSION['error'])){ ?>
                <div class="alert alert-danger">
                    <h4><?php echo $_SESSION['error']; ?></h4>
                </div>
                <?php unset($_SESSION['error']); } ?>

                
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="public/vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="public/vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="public/vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="public/dist/js/sb-admin-2.js"></script>

</body>

</html>


<?php

if(isset($_POST['email']) && isset($_POST['password'])){
    
    include '../config/db.php';
    include 'config/constants.php';

    $conn = DB::connect();
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM admin WHERE email = '$email' AND password = '$password'";
    $query = mysqli_query($conn,$sql);
    $data = mysqli_fetch_assoc($query);

    if($data){
        // isset($_SESSION['user_id']) && !isset($_SESSION['token'])) {
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['token'] = $data['password'];
        $login_page = BASE_URL . 'api/admin/index.php';
        header("Location: $login_page");
        die();
    }else{
        $_SESSION['error'] = 'Please login to access';
        $login_page = BASE_URL . 'api/admin/login.php';
        header("Location: $login_page");
        die();
    }
}


?>