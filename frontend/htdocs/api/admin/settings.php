<?php 
include 'config/login-check.php';
$_SESSION['url'] = 'settings';

include '../config/constants.php';

$conn = new mysqli(HOST,DB_USER,DB_PASS,DB);
$sql = "SELECT * FROM settings WHERE status = 1";
$execute = mysqli_query($conn,$sql);

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

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <br>
                    <div class="col-lg-6">
                        
                        <div class="col-lg-3">
                            <label class="switch">
                              <?php if($is_referral == 1){ ?>
                                <p style="margin-right: 42px;margin-left: 69px;margin-top: 5px;">
    Disable  </p> <input onclick="change_status(0)" value="0" type="checkbox" checked> <?php } ?>
                              <?php if($is_referral != 1){ ?>
                                <p style="margin-right: 42px;margin-left: 69px;margin-top: 5px;">
    Enable </p> <input onclick="change_status(1)" value="1" type="checkbox"> <?php } ?>
                              <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    
                    
                    <h1 class="page-header"></h1>
                    <p style="font-size: medium;">Use this page to Edit the Referral program settings.</p>

                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Referral Amount 
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>Registration Bonus</th>
                                        <th>Extra Registration Bonus.</th>
                                        <th>Referral Bonus</th>
                                        <th>USD value of Bonus points.</th>
                                        <th>BTC value of Bonus points.</th>
                                        <th>LTC value of Bonus points.</th>
                                        <th>BCH value of Bonus points.</th>
                                        <th>ZEC value of Bonus points.</th>
                                        <th>ETH value of Bonus points.</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php while($data = mysqli_fetch_array($execute)){ ?>
                                    <tr class="odd gradeX">
                                        <td>
                                            <?php echo $data['default_bonus']; ?></td>
                                        <td>
                                            <?php echo $data['referred_bonus']; ?></td>
                                        <td>
                                            <?php echo $data['referrar_earn']; ?>
                                        </td>
                                        <td class="center">
                                            <?php echo $data['one_point_value']; ?>
                                        </td>
                                        <td class="center">
                                            <?php echo $data['BTC']; ?>
                                        </td>
                                        <td class="center">
                                            <?php echo $data['LTC']; ?>
                                        </td>
                                        <td class="center">
                                            <?php echo $data['BCH']; ?>
                                        </td>
                                        <td class="center">
                                            <?php echo $data['ZEC']; ?>
                                        </td>
                                        <td class="center">
                                            <?php echo $data['ETH']; ?>
                                        </td>
                                        <td class="center">
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">Edit</button>

                                        <!-- Modal -->
                                        <div id="myModal" class="modal fade" role="dialog">
                                          <div class="modal-dialog">

                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <form name="update" action="" method="post">
                                              <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Edit referral settings</h4>
                                              </div>
                                              <div class="modal-body">

                                                <div class=" ">
                                                    <label for="default_bonus">Bonus earned by each Registered User.</label> 
                                                    <input type="text" id="default_bonus" name="default_bonus" value="<?php echo $data['default_bonus']; ?>" class="form-control">
                                                </div>

                                                <div class=" " style="margin-top: 10px;">
                                                    <label for="referred_bonus">
                                                    Extra Bonus earned by each Registered User ( If he uses a Refferal code by other user ).</label> 
                                                    <input type="text" id="referred_bonus" name="referred_bonus" value="<?php echo $data['referred_bonus']; ?>" class="form-control">
                                                </div>

                                                <div class=" " style="margin-top: 10px;">
                                                    <label for="referrar_earn">Referral Bonus earned by a User</label> 
                                                    <input type="text" id="referrar_earn" name="referrar_earn" value="<?php echo $data['referrar_earn']; ?>" class="form-control">
                                                </div>

                                                <div class=" " style="margin-top: 10px;">
                                                    <label for="one_point_value">USD value equal for each Bonus point.</label> 
                                                    <input type="text" id="one_point_value" name="one_point_value" value="<?php echo $data['one_point_value']; ?>" class="form-control">
                                                </div>

                                                <div class=" " style="margin-top: 10px;">
                                                    <label for="BTC">BTC value equal for each Bonus point.</label> 
                                                    <input type="text" id="BTC" name="BTC" value="<?php echo $data['BTC']; ?>" class="form-control">
                                                </div>

                                        

                                                <div class=" " style="margin-top: 10px;">
                                                    <label for="LTC">LTC value equal for each Bonus point.</label> 
                                                    <input type="text" id="LTC" name="LTC" value="<?php echo $data['LTC']; ?>" class="form-control">
                                                </div>

                                                <div class=" " style="margin-top: 10px;">
                                                    <label for="BCH">BCH value equal for each Bonus point.</label> 
                                                    <input type="text" id="BCH" name="BCH" value="<?php echo $data['BCH']; ?>" class="form-control">
                                                </div>

                                                <div class=" " style="margin-top: 10px;">
                                                    <label for="ZEC">ZEC value equal for each Bonus point.</label> 
                                                    <input type="text" id="ZEC" name="ZEC" value="<?php echo $data['ZEC']; ?>" class="form-control">
                                                </div>

                                                <div class=" " style="margin-top: 10px;">
                                                    <label for="ETH">ETH value equal for each Bonus point.</label> 
                                                    <input type="text" id="ETH" name="ETH" value="<?php echo $data['ETH']; ?>" class="form-control">
                                                </div>

                                              </div>
                                              <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Update</button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                              </div>
                                              </form>
                                            </div>

                                          </div>
                                        </div>

                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
if(isset($_POST['default_bonus'])){
    
    include '../config/db.php';
    include 'config/constants.php';

    $conn = DB::connect();

    $default_bonus = $_POST['default_bonus'];
    $referred_bonus = $_POST['referred_bonus'];
    $referrar_earn = $_POST['referrar_earn'];
    $one_point_value = $_POST['one_point_value'];

    $BTC = $_POST['BTC'];
    $LTC = $_POST['LTC'];
    $BCH = $_POST['BCH'];
    $ZEC = $_POST['ZEC'];
    $ETH = $_POST['ETH'];

    $sql = "UPDATE settings SET default_bonus = $default_bonus , referred_bonus = $referred_bonus , referrar_earn = $referrar_earn , one_point_value = $one_point_value , BTC = $BTC , LTC = $LTC, BCH = $BCH, ZEC = $ZEC , ETH = $ETH WHERE status = 1";

    $query = mysqli_query($conn,$sql);

    if($query){
        $_SESSION['success'] = 'Data updated successfully';
        $login_page = BASE_URL . 'settings.php';
        header("Location: $login_page");
        die();
    }else{
        $_SESSION['error'] = 'Unable to update try again';
        $login_page = BASE_URL . 'settings.php';
        header("Location: $login_page");
        die();
    }
}
?>