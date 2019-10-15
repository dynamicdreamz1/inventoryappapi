<?php

//Test => http://localhost/Project/Script/shopifyorder.php?order_id=1761953546312
//GET order from Shopify using order id

define("SHOPIFY_API_KEY","fa2e6847cffd1b8b0677ce3c04a5e3aa");
define("SHOPIFY_API_PASSWORD","0ce5daff19f7ea5595d4387204d4639a");
define("API_DOMAIN","insectlore-staging.myshopify.com");
define('API_URL', 'https://' . SHOPIFY_API_KEY . ':' . SHOPIFY_API_PASSWORD . '@' . API_DOMAIN . '/');

$myfile = fopen("test.txt", "w") or die("Unable to open file!");

fwrite($myfile, "lineA");

fclose($myfile);

ini_set('display_errors', 1);
ini_set("memory_limit",-1);
set_time_limit(0);

$data = file_get_contents('php://input');
$order = json_decode($data, true);

$order_id=$order['id'];

$myfile = fopen("test.txt", "w") or die("Unable to open file!");

fwrite($myfile, $order_id);

fclose($myfile);



if(isset($order_id)){

    $order_id = trim($order_id);
 
    if(!empty($order_id) || $order_id!==null || $order_id!==" ")
    {
        $url = API_URL . "admin/orders/". $order_id . ".json";
        $ch = curl_init($url);
            curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $json_array = json_decode($result, true);
        if($json_array)
        {      
            if($json_array['order']) {
         
            header('Content-type: application/json');
            $myorder = $json_array['order'];
            $line_items = $myorder['line_items'];

           if(count($line_items)){

                  $total_line_items = count($line_items);

                  for($i=0;$i<$total_line_items;$i++){
                      $variant_id = $line_items[$i]['variant_id'];

                      if($variant_id){
                        $url = API_URL . "admin/variants/". $variant_id . ".json";
                        $ch = curl_init($url);
                              curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
                              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        $result = curl_exec($ch);
                        $json_array = json_decode($result, true);

                        if($json_array){
    
                            if($json_array['variant']) {

                                $variant = $json_array['variant'];
                                $inventory_quantity = $variant['inventory_quantity'];
                                $inventory_quantity = $inventory_quantity -1;

                                $data =  array("variant"=>array("id" => $variant_id,"inventory_quantity"=>$inventory_quantity));

                            $url_ = API_URL . "admin/variants/" . $variant_id . ".json";
                            $ch_ = curl_init($url_);
                            curl_setopt($ch_, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch_, CURLOPT_CUSTOMREQUEST, "PUT");
                            curl_setopt($ch_, CURLOPT_POSTFIELDS,http_build_query($data));
                            
                            $response = curl_exec($ch_);
                            print_r($response);
                            }
                        }
                     }
                  }
              }
          }
    }
}
}
else{
    echo "order_id not found";
}


   




    
   



?>
