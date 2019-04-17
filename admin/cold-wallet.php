<?php

 $conn = new mysqli("localhost","root","xchange123","bitexchange_cash");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$update = $_REQUEST['update_status'];
$id = $_REQUEST['id'];
	// echo $update;
if (count($update)) {
	// echo $update;
	// echo "<br>";
	// echo $id;
	// exit;
	$sql1 = "UPDATE trazor_wallets SET status= $update where id =$id";
$result1 = $conn->query($sql1);
if ($conn->query($sql1) == FALSE) {
    echo "Error: " . $sql1 . "<br>" . $conn->error;
}
}


$sql = "SELECT * FROM trazor_wallets";
$result = $conn->query($sql);


?>
<!-- <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
<style>
	/*.path:nth-child(1) {
    display: none;
}
*/
	.path {
    display: none;
}
	#path_cold {
    display: block;
}
input.btn.btn-primary {
    color: #fff;
    background-color: #28a745;
    border-color: #28a745;
    display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    padding: .375rem .75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: .25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
input.btn.btn-danger{
	color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
    display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    padding: .375rem .75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: .25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
section.trazor_div {
    display: none;
}
.box_b {
    
    background-color: #ffffff;
}
table tr.alt {
    background-color: #ffffff;
}
</style>
<body>
	<div class="path" id="path_cold"><a href="index.php">Home</a> /  <a href="index.php?current_url=cold-wallet">Cold Wallet</a> </div>
	<div class="container">
<div class="row">
	<div class="area full_box" id="grid_257">
				<h2>Cold Wallet</h2>
				<div class="box_bar"></div>
				<div class="box_tl"></div>
				<div class="box_tr"></div>
				<div class="box_bl"></div>
				<div class="box_br"></div>
				<div class="t_shadow"></div>
				<div class="r_shadow"></div>
				<div class="b_shadow"></div>
				<div class="l_shadow"></div>
				<div class="box_b"></div>
				<!-- <div class="grid_buttons">
				<div class="button before"></div>
				<button type="button" class="button" data-toggle="modal" data-target="#myModal"><div class="add_new"></div>Open Modal</button> -->
				<!-- <a class="button" href="index.php?action=form;is_tab=1;current_url=dummy" onclick="ajaxGetPage('index.php?action=form;is_tab=1;current_url=dummy','add_trazor',false,''); return false;"><div class="add_new"></div>Add New</a>  -->

				<!-- </div> -->
	<div class="table-responsive">
		<table>
	<tbody>
		<tr class="grid_header">
			
			<th>S.No</th>
					<th>Wallet Name</th>
					<!-- <th>Order Type</th> -->
					<th>Status</th>
		</tr>
	</tbody>
			<tbody>
				<?php
				while($row = $result->fetch_assoc()) {


				?>
			
					<tr class="alt">
						
						<td><?php echo $row["id"]; ?></td>
						<td><?php echo $row["name"]; ?>
						</td>
						<!-- <td><?php //echo $row["trazor_id"]; ?></td> -->
						<!-- <td><?php// echo $row["order_type"];?></td> -->
					
					<td>
						<form method="POST" action="">
							
							<?php 
								if ($row["status"] == 1) {
									echo '<input type="hidden" value="0" name="update_status">';
									echo '<input type="hidden" value="'.$row["id"].'" name="id">';
									echo '<input type="submit" value="Off" class="btn btn-danger" style="margin-bottom: 20px;">';
								}else{
									echo '<input type="hidden" value="1" name="update_status">';
									echo '<input type="hidden" value="'.$row["id"].'" name="id">';
									echo '<input type="submit" value="On" class="btn btn-primary" style="margin-bottom: 20px;">';
								}
								 ?>
				</form>
			</td>
				</tr>
				<?php
			}
				?>
			</tbody>
		</table>
	</div>


</div>
</div>
</div>
<!-- <div class="row">
	<div class="modal fade" id="myModal" role="dialog"> -->
<!-- <div id="add_trazor" class="popup area ui-draggable" style="top: 379.5px; margin-top: -317.5px; left: 768px; margin-left: -384px; display: none;"> -->
	  <!-- <div class="modal-dialog">  -->
    
      <!-- Modal content  -->
      <!-- <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Trazor</h4>
        </div>
        <form name="currencies_add" action="" class="form" method="POST">
        <div class="modal-body">
          
		
			<div class="form-group">
			<label for="currencies_currency">Name </label>
			<input type="text" name="name" value="" id="currencies_currency">
			</div>
			<div class="form-group">
			<label for="currencies_currency">Transation Id </label>
			<input type="text" name="trazor_id" value="" id="currencies_currency">
			</div>
			<div class="form-group">
			<label for="currencies_currency">Status </label>
			<input type="checkbox" name="status" value="" id="currencies_currency">
			</div>
        </div>
        <div class="modal-footer">
        	<button type="submit" class="btn btn-danger">Submit</button>
		
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
      
    </div>
  </div>
  <section class="trazor_div">
	<h2 class="popup_bar">
		<div class="bpath">Add Trazor</div>
		<a class="close" onclick="closePopup(this)"></a>
	</h2>
	<div class="box_bar"></div>
	<div class="box_tl"></div>
	<div class="box_tr"></div>
	<div class="box_bl"></div>
	<div class="box_br"></div>
	<div class="t_shadow"></div>
	<div class="r_shadow"></div>
	<div class="b_shadow"></div>
	<div class="l_shadow"></div>
	<div class="box_b"></div>
	<div class="popup_content">
		
		<form name="currencies_add" action="" class="form" method="POST">
			<label for="currencies_currency">Name </label>
			<input type="text" name="name" value="" id="currencies_currency">
			<label for="currencies_currency">Transation Id </label>
			<input type="text" name="trazor_id" value="" id="currencies_currency">
			<label for="currencies_currency">Status </label>
			<input type="checkbox" name="status" value="" id="currencies_currency">
		</form>
	
	</div>
	<div class="resize"></div>
	</section>
</div>
</div> --> 
</body>