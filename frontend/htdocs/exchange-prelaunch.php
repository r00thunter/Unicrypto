<?php
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);

$servername = "localhost";
$database = "bitexchange_cash";
$username = "root";
$password = "xchange123";

$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection

// if (!$conn) {
//       die("MainConnection failed: " . mysqli_connect_error());
// }else{
// 		echo "Connected successfully";	
// }
		$sql = 'SELECT * FROM `site_users` ORDER BY `id`  DESC LIMIT 0 , 1';
		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
        // print_r($row);
			$pre_user_id = $row['user'];
			$pre_user_firstname = $row['first_name'];
			$pre_user_lastname = $row['last_name'];
			$pre_user_email = $row['email'];
			$pre_user_phone = $row['phone'];
			$pre_user_pass = $row['pass'];

			// $length = 10;
   //  		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
   //  		$charactersLength = strlen($characters);
   //  		$randomString = '';
   //  		for ($i = 0; $i < $length; $i++) {
   //      		$randomString .= $characters[rand(0, $charactersLength - 1)];
   //  		}
    		// $pre_user_referral_code = "j35p0h5myl".$pre_user_id;
    		$pre_user_referral_code = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 10);


			$servername1 = "localhost";
			$database1 = "bitexchange_kyc";
			$username1 = "root";
			$password1 = "xchange123";


			$conn1 = mysqli_connect($servername1, $username1, $password1, $database1);

// Check connection

			// if (!$conn1) {
   //    			die("Connection failed: " . mysqli_connect_error());
			// }else{
			// 	echo "Connected successfully2";	
			// }

			$sql1 = "INSERT INTO `users` (`id`, `first_name`, `last_name`, `avatar`, `phone`, `country`, `city`, `address`, `postal_code`, `twitter_profile`, `linkedin_profile`, `email`, `password`, `referral_code`, `referral_count`, `completion`, `approval`, `remember_token`, `created_at`, `updated_at`, `exchange_site_user`, `exchange_site_user_status`) VALUES (NULL, '".$pre_user_firstname."', '".$pre_user_lastname."', NULL, '".$pre_user_phone."', NULL, NULL, NULL, NULL, NULL, NULL, '".$pre_user_email."', '".$pre_user_pass."', '".$pre_user_referral_code."', '0', '0', 'PENDING', NULL, NULL, NULL, '".$pre_user_id."', '0')";


			if (mysqli_query($conn1, $sql1)) {
    			// echo "New record created successfully";
    					$sql2 = 'SELECT * FROM `users` ORDER BY `id`  DESC LIMIT 0 , 1';
						$result = mysqli_query($conn1, $sql2);

						if (mysqli_num_rows($result) > 0) {
							while($row1 = mysqli_fetch_assoc($result)) {
								$pre_user_id1 = $row1['id'];

							$sql3 = "INSERT INTO `user_preferences` (`id`, `user_id`, `E2F`, `ESMS`, `id_proof_type`, `id_proof`, `address_proof_type`, `address_proof`, `id_card`, `contact_sms`, `contact_email`, `created_at`, `updated_at`) VALUES (NULL, '".$pre_user_id1."', '0', '0', 'PASSPORT', NULL, 'PASSPORT', NULL, NULL, '0', '0', NULL, NULL)";
							if (mysqli_query($conn1, $sql3)) {
							}
							}	
						}
			} else {
    			// echo  "Error: " . $sql1 . "<br>" . mysqli_error($conn1);
			}
    	
    	}
    	}else{
    		 // echo "0 results";
    	}

?>
