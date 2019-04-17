<?php
        // error_reporting(E_ALL);
        // ini_set('display_errors', 1);
        // include '../lib/common.php';
        // print_r($_SESSION);
        // die();
        API::add('Currencies','getCurrencies');
        API::add('User','getInfo',array($_SESSION['session_id']));
        $query = API::send();
        $user_id = $query['User']['getInfo']['results'][0]['id'];
        // print_r($query['Currencies']['getCurrencies']['results'][0]);
        // echo "<br><br>";
        // echo count($query['Currencies']['getCurrencies']['results'][0]);
        $currency_count = count($query['Currencies']['getCurrencies']['results'][0]);
        // echo "<br><br>";
        for ($i=0; $i < $currency_count; $i++) { 
        		$currency_id = $query['Currencies']['getCurrencies']['results'][0][$i]['id'];
        		// echo $currency_id;
        		// echo "<br><br>";
        		if ($currency_id == 28 || $currency_id == 42) {
        			
        		API::add('BitcoinAddresses','getCurrentUser',array($currency_id));
        		API::add('Requests','getUserBalance',array($currency_id));
            	$query1 = API::send();
            	$from_address = $query1['BitcoinAddresses']['getCurrentUser']['results'][0][0]['address'];
            	$user_balance = $query1['Requests']['getUserBalance']['results'][0][0]['balance'];
            	// echo $from_address;
            	// print_r($user_balance);
            	// echo "<br><br>";
            	if ($from_address) {
            			
            			if ($currency_id == 28) {
                                $url = "http://$btc_ip/api/balance";
                        }elseif ($currency_id == 42) {
                                $url = "http://$ltc_ip/api/balance";
                        }elseif ($currency_id == 45) {
                                $url = "http://$eth_ip/api/".$from_address;
                        }elseif ($currency_id == 46) {
                                $url = "http://$xrp_ip/api/".$from_address;
                        }elseif ($currency_id == 47) {
                                $url = "http://$xlm_ip/api/".$from_address;
                        }

                        // echo "nilashns";echo "<br><br>";
                        $params = array(
                              'address' => $from_address, 
                          );

                        if ($currency_id == 28 || $currency_id == 42) {
                        		$ch = curl_init();
            		 			$payload = json_encode($params);
								curl_setopt($ch, CURLOPT_URL,$url);
								curl_setopt($ch, CURLOPT_POST, 1);
								curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);
                        			curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

								// Receive server response ...
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

								$result = curl_exec($ch);

								curl_close ($ch);
								$result = json_decode($result);
								// print_r($result);
								// echo "<br><br>";
								if ($result->status == "success") {
										 $available_balance = $result->body->response->data->available_balance;
										API::add('Requests','getUserBalanceTrans',array($currency_id));
            							$query1 = API::send();
            							// echo "<br><br>";
            							// print_r($query1['Requests']['getUserBalanceTrans']['results'][0]['balance']);
            							$getUserBalanceTrans = $query1['Requests']['getUserBalanceTrans']['results'][0]['balance'];
            							if ($getUserBalanceTrans) {
            								if ($available_balance == $getUserBalanceTrans) {
            								
            								}elseif ($getUserBalanceTrans < $available_balance) {
            									$balance = $getUserBalanceTrans + $available_balance;
            									$user_balance = $user_balance + $available_balance;
            									API::add('Requests','getUserBalanceTrans',array($currency_id,$balance));
            									API::add('Requests','updatetUserBalance',array($currency_id,$user_balance));
            									$query1 = API::send();
            								
            								}elseif ($getUserBalanceTrans > $available_balance) {
            									$balance = $getUserBalanceTrans + $available_balance;
            									$user_balance = $user_balance + $available_balance;
            									API::add('Requests','getUserBalanceTrans',array($currency_id,$balance));
            									API::add('Requests','updatetUserBalance',array($currency_id,$balance));
            									$query1 = API::send();
            								}
            							}else{
												API::add('Requests','insertUserBalanceTrans',array($currency_id,$available_balance));
          										$query1 = API::send();
            									// print_r($query1);	
            							}
            							
								}

        						// echo "<br><br>";
                        }

                        if ($currency_id == 45 || $currency_id == 46 || $currency_id == 47) {

                                $ch = curl_init($url);

                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                                  
                                # Return response instead of printing.
                                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
          
                                # Send request.
                                $result = curl_exec($ch);
          
                                curl_close($ch);
          
                                if ($is_error) {
                                    unset($is_error);          
                                }

                                $result = json_decode($result);
                                if ($result->status == "success") {

                                     $available_balance = $result->body->balance;
                                        API::add('Requests','getUserBalanceTrans',array($currency_id));
                                        $query1 = API::send();
                                        // echo "<br><br>";
                                        // print_r($query1['Requests']['getUserBalanceTrans']['results'][0]['balance']);
                                        $getUserBalanceTrans = $query1['Requests']['getUserBalanceTrans']['results'][0]['balance'];
                                        if ($getUserBalanceTrans) {
                                            if ($available_balance == $getUserBalanceTrans) {
                                            
                                            }elseif ($getUserBalanceTrans < $available_balance) {
                                                $balance = $getUserBalanceTrans + $available_balance;
                                                $user_balance = $user_balance + $available_balance;
                                                API::add('Requests','getUserBalanceTrans',array($currency_id,$balance));
                                                API::add('Requests','updatetUserBalance',array($currency_id,$user_balance));
                                                $query1 = API::send();
                                            
                                            }elseif ($getUserBalanceTrans > $available_balance) {
                                                $balance = $getUserBalanceTrans + $available_balance;
                                                $user_balance = $user_balance + $available_balance;
                                                API::add('Requests','getUserBalanceTrans',array($currency_id,$balance));
                                                API::add('Requests','updatetUserBalance',array($currency_id,$balance));
                                                $query1 = API::send();
                                            }
                                        }else{
                                                API::add('Requests','insertUserBalanceTrans',array($currency_id,$available_balance));
                                                $query1 = API::send();
                                                // print_r($query1);    
                                        }


                                }
                        }
            					
            	}
        		}

        	// echo "<br><br>";
        }

?>
