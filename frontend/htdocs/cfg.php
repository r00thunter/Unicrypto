<?php
$conn = new mysqli("localhost","root","ghost227#","bitexchange");
 $cur_sql = "SELECT * FROM currencies WHERE is_active='Y'";
        $currency_query = mysqli_query($conn,$cur_sql);

        // $base_ip = "http://18.220.172.39/api/";
          // CHECKING REFErral status 
    // $ch = curl_init("http://18.220.172.39/api/get-settings.php"); 
    // curl_setopt($ch, CURLOPT_HEADER, 0);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $output = curl_exec($ch);      
    // curl_close($ch);
    // $ref_response = json_decode($output);
    // if ($ref_response->is_referral == 1) {
    //     $GLOBALS['REFERRAL'] = true;
    //     $GLOBALS['REFERRAL_BASE_URL'] = $base_ip;
    //     //$GLOBALS['REFERRAL_BASE_URL'] = $ref_response->base_url;
    // }else{
    //    $GLOBALS['REFERRAL'] = false; 
    // }

     $GLOBALS['REFERRAL'] = false;
?>
