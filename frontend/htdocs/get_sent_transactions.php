<?php
        // error_reporting(E_ALL);
        // ini_set('display_errors', 1);
        include '../lib/common.php';

        	API::add('Currencies','getCurrencies');
			$query = API::send();

			$currency_count = count($query['Currencies']['getCurrencies']['results'][0]);

			for ($i=0; $i < $currency_count; $i++) { 

				$currency_id = $query['Currencies']['getCurrencies']['results'][0][$i]['id'];
				if ($currency_id == 28) {
						if ($currency_id == 28) {
                                $url = "http://$btc_ip/api/sentTransactions";
                        }elseif ($currency_id == 42) {
                                $url = "http://$ltc_ip/api/sentTransactions";
                        }elseif ($currency_id == 45) {
                                $url = "http://$eth_ip/api/sentTransactions";
                        }elseif ($currency_id == 46) {
                                $url = "http://$xrp_ip/api";
                        }elseif ($currency_id == 47) {
                                $url = "http://$xlm_ip/api";
                        }
				

			

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
          // print_r($result);
          // die();
          $transaction_count = count($result->body->response->data->txs);
          if ($result->status == "success") {
          		for ($j=0; $j < $transaction_count; $j++) { 
          				// print_r(count($result->body->response->data->txs));
          				// echo "<br><br>";
          				// print_r($result->body->response->data->txs[$j]);
          				// echo "<br><br>";
          				 $transaction_id = $result->body->response->data->txs[$j]->txid;
          				 $transaction_date = date("Y-m-d H:m:i",$result->body->response->data->txs[$j]->time);
          				 $transaction_address = $result->body->response->data->txs[$j]->amounts_sent[0]->recipient;
          				 $transaction_amount = $result->body->response->data->txs[$j]->amounts_sent[0]->amount;

          				API::add('Requests','sendTransactionInsert',array($transaction_id));
                  API::add('BitcoinAddresses','getTRXBitcoinAddress',array($transaction_address));
            			$query1 = API::send();
            			// print_r($query1['BitcoinAddresses']['getTRXBitcoinAddress']['results'][0][0]['site_user']);
                  // echo "<br><br>";
            			$transaction_id_check = $query1['Requests']['receivedTransactionIdCheck']['results'][0];
                  $transaction_address_user = $query1['BitcoinAddresses']['getTRXBitcoinAddress']['results'][0][0]['site_user'];

            			if ($transaction_id_check == 0 && $transaction_address_user != 0) {
                    // echo "string";
            					
          						API::add('Requests','receivedTransactionInsert',array($transaction_address_user,$currency_id,$transaction_date,$transaction_amount,$transaction_address,$transaction_id));
            					$query1 = API::send();
            			}

          		}
          }


          }

          }


?>