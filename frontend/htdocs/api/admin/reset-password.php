<?php 
session_start(); 
if(!isset($_SESSION['user_id']) && !isset($_SESSION['token'])) {
    include 'config/constants.php';
    $login_page = BASE_URL . 'login.php';
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

    <title>Blockstreet Referral - Admin Panel For Cryptocurrency Exchange script Referral program.</title>

    <?php include 'includes/css.php'; ?>

</head>

<body>

<?php
$sql1 = "SELECT * FROM settings WHERE status = 1";
$execut = mysqli_query($conn,$sql1);
$ref_data = mysqli_fetch_assoc($execut);
$is_referral = (float) $ref_data['is_referral'];

?>
    <div id="wrapper">

        <?php include'includes/nav.php'; ?>

        <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
  
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Password reset</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" name="login-form" action="" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input required class="form-control" placeholder="New password" name="password" type="password" autofocus>
                                </div>
                                <div class="form-group">
                                    <input required class="form-control" placeholder="Confirm Password" name="confirm_password" type="password" value="">
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button type="submit" class="btn btn-lg btn-success btn-block"> 
                                    Reset Password 
                                </button>
                            </fieldset>
                        </form>
                    </div>

                    </div>
                    <?php if(isset($_SESSION['error'])){ ?>
                <div class="alert alert-danger">
                    <h4><?php echo $_SESSION['error']; ?></h4>
                </div>
                <?php unset($_SESSION['error']); } ?>

                
            </div>
        </div>
    </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <?php include 'includes/js.php'; ?>

    <script type="text/javascript">
        
        function change_status(argument) {
            //
            var urlToLoad = 'update-settings.php?value='+argument;
            $.ajax({
                        type: 'GET',
                        url: urlToLoad,
                        success: function (data) {
                            window.location.reload();
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
        }
    </script>
</body>

</html>

<?php

if(isset($_POST['confirm_password']) && isset($_POST['password'])){
    
    include '../config/db.php';
    include 'config/constants.php';

    $conn = DB::connect();
    $confirm_password = $_POST['confirm_password'];
    $password = $_POST['password'];

    if ($password != $confirm_password) {
        $_SESSION['error'] = "Please doesn't match";
        $login_page = BASE_URL . 'reset-password.php';
        header("Location: $login_page");
        die();
    }
    
    //$sql = "SELECT * FROM admin WHERE email = '$email' AND password = '$password'";
    $sql = "UPDATE admin SET password = '$password' WHERE status = 1";
    $query = mysqli_query($conn,$sql);

    if($query){
        // isset($_SESSION['user_id']) && !isset($_SESSION['token'])) {
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['token'] = $data['password'];
        $_SESSION['success'] = "Password changed successfully";
        $login_page = BASE_URL . 'reset-password.php';
        header("Location: $login_page");
        die();
    }else{
        $_SESSION['error'] = 'Please login to access';
        $login_page = BASE_URL . 'login.php';
        header("Location: $login_page");
        die();
    }
}


?>