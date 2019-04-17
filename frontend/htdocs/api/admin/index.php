<?php 
include 'config/login-check.php';
$_SESSION['url'] = 'dashboard';
include '../config/constants.php';

$conn = new mysqli(HOST,DB_USER,DB_PASS,DB);
$sql = "SELECT * FROM users WHERE status = 1";
$execute = mysqli_query($conn,$sql);

if (isset($_SESSION['success'])) {
    unset($_SESSION['success']);
    header("Refresh:0");
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

    <?php include 'includes/css.php'; ?>

</head>

<body>

    <div id="wrapper">

        <?php include'includes/nav.php'; ?>

         <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">

                    <h1>
                        <?php if(!isset($_GET['code'])){ ?> User Details <?php }else{ ?>
                            All users reffered by <?php echo $_GET['username']; ?>
                            <?php } ?>
                    </h1>

                    <p>-Use this screen to manage all your Exchange users.</p>
                    <p>-Click on a Username to view all the users he/she has reffered.</p>
                    <p>-Add / Edit Bonus points for each user.</p>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?php if(!isset($_GET['code'])){ ?> Registered Users <?php }else{ ?>
                            Users referred by <?php echo $_GET['username']; ?>
                            <?php } ?>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">

                            <?php if(!isset($_GET['code'])){ ?>
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Referral Code</th>
                                        <th>Bonus Point</th>
                                        <th>Referred By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody> 
                                    <?php while($data = mysqli_fetch_array($execute)){ 

                                    
                
                                    ?>
                                    <tr class="odd gradeX">
                                        <td>
                                            <?php echo $data['id']; ?></td>
                                        <td>
                                            <a href="index.php?code=<?php echo $data['referral_code']; ?>&username=<?php echo $data['username']; ?>"> 
                                                <?php echo $data['username']; ?>
                                            </a>
                                                 
                                        </td>
                                        <td>
                                            <?php echo $data['referral_code']; ?></td>
                                        <td>
                                            <?php echo $data['bonous_point']; ?>
                                        </td>
                                        <td class="center">
                                            <?php
                                                
                                                if ($data['referred_by'] != '0') {
                                                    echo $data['referred_by']; 
                                                }else{
                                                    echo '-';
                                                }
                                             
                                            ?>
                                        </td>
                                        <td class="center">
                                            
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#<?php echo $data['id']; ?>bonus">
                                                Edit Bonus
                                            </button>

                                        </td>


                                        <!-- start of edit bonus model -->
                                        <!-- Modal -->
                                        <div id="<?php echo $data['id']; ?>bonus" class="modal fade" role="dialog">
                                          <div class="modal-dialog">

                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <form name="update" action="" method="post">
                                                    <input type="hidden" name="update-bonus" value="1">
                                                    <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                                              <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Edit Bonus Point</h4>
                                              </div>
                                              <div class="modal-body">
                                                
                                                <div class=" ">
                                                    <label for="bonous_point">
                                                        Current Bonus
                                                    </label> 
                                                    <input type="text" id="bonous_point" name="bonous_point" value="<?php echo $data['bonous_point']; ?>" class="form-control">
                                                </div>

                                              </div>
                                              <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">
                                                    Update Bonus
                                                </button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                              </div>
                                              </form>
                                            </div>

                                          </div>
                                        </div>
                                        <!-- end of edit bonus model -->
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php }else{ ?>
                            <?php 
                            $reff_code = $_GET['code'];
                                    $get_referred_sql = "SELECT * FROM users WHERE referred_by = '$reff_code'";
                                    $exe = mysqli_query($conn,$get_referred_sql);
                            
                            ?>

                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead><tr>
                                                        <td>Username</td>
                                                        <td>Referral Code</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                       
                                                        <?php while($ref = mysqli_fetch_array($exe)){ ?>
                                                        <tr>
                                                            <td>
                                                                <?php echo $ref['username']; ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $ref['referral_code']; ?>
                                                            </td>
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                    </table>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-wrapper -->
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <?php include 'includes/js.php'; ?>

</body>

</html>


<?php

if(isset($_POST['update-bonus'])){
    
    include '../config/db.php';
    include 'config/constants.php';

    $conn = DB::connect();

    $id = $_POST['id'];
    $bonus_point = $_POST['bonous_point'];

    $SQL = "UPDATE users SET bonous_point = $bonus_point WHERE id = $id";

    $query = mysqli_query($conn,$SQL);

    if($query){
        $_SESSION['success'] = 'Data updated successfully';
        $login_page = BASE_URL . 'index.php';
        header("Location: $login_page");
        die();
    }else{
        $_SESSION['error'] = 'Unable to update try again';
        $login_page = BASE_URL . 'index.php';
        header("Location: $login_page");
        die();
    }

}
?>