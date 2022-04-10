<?php
/*
functions
---------------------------------------------
0. xss_clean($data)
1. get_product_by_id($id=null)
2. get_product_by_variant_id($arr)
3. convert_to_parent($measurement,$measurement_unit_id)
4. rows_count($table,$field = '*',$where = '')
5. get_configurations()
6. get_balance($id)
7. get_bonus($id)
8. get_wallet_balance($id)
9. update_wallet_balance($balance,$id)
10. add_wallet_transaction($order_id="",$id,$type,$amount,$message,$status = 1)
11. update_order_item_status($order_item_ids,$order_id,$status)
12. validate_promo_code($user_id,$promo_code,$total)
13. get_settings($variable,$is_json = false)
14. send_order_update_notification($uid,$title,$message,$type)
15. send_notification_to_delivery_boy($uid,$title,$message,$type,$order_id)
16. get_promo_details($promo_code)
17. store_return_request($user_id,$order_id,$order_item_id)
18. get_role($id)
19. get_permissions($id)
20. add_delivery_boy_commission($id,$type,$amount,$message,$status = "SUCCESS")
21. store_delivery_boy_notification($delivery_boy_id,$order_id,$title,$message,$type)
22. is_item_available_in_cart($user_id,$product_variant_id)
23. time_slot_config()
24. is_address_exists($id="",$user_id="")
25. is_user_or_dboy_exists($type,$type_id)
26. get_user_or_delivery_boy_balance($type,$type_id)
27. store_withdrawal_request($type, $type_id, $amount, $message)
28. debit_balance($type, $type_id, $new_balance)
29. is_records_exists($type, $type_id,$offset,$limit)
30. get_product_id_by_variant_id($product_variant_id)
31. update_delivery_boy_wallet_balance($balance, $id)
32. low_stock_count($low_stock_limit)
33. sold_out_count()
34. is_product_available($product_id)
35. is_product_added_as_favorite($user_id, $product_id)
36. validate_email($email)
37. update_forgot_password_code($email,$code)
38. validate_code($code)
39. get_user($code)
40. update_password($code,$password_hash)
41. is_return_request_exists($user_id, $order_item_id)
42. get_last_inserted_id($table)
43. is_product_cancellable($order_item_id)
44. is_default_address_exists($user_id)
44. get_data($fields=[], $where,$table)
45. update_order_status($id,$status,$delivery_boy_id=0)
46. verify_paystack_transaction($reference, $email, $amount)
47. get_variant_id_by_product_id($product_id)
48. get_order_item_by_order_id($id)
49. add_wallet_balance($order_id, $user_id, $amount, $type,$message)
50. send_notification_to_admin($id, $title, $message, $type, $order_id)
51. add_seller_wallet_transaction($order_id = "",$order_item_id, $seller_id, $type, $amount, $message = 'Used against Order Placement', $status = 1)

*/
include_once('crud.php');
require_once('firebase.php');
require_once('push.php');
require_once('functions.php');


$fn = new functions;
class custom_functions
{
    protected $db;
    function __construct()
    {
        $this->db = new Database();
        $this->db->connect();
    }


    function xss_clean_array($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = $this->xss_clean($value);
            }
        } else {
            $array = $this->xss_clean($array);
        }
        return $array;
    }

    function xss_clean($data)
    {
        $data = trim($data);
        // Fix &entity\n;
        $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }
    function get_pincode_id_by_pincode($pincode)
    {
        $sql = "SELECT id from pincodes where pincode = " . $pincode;
        $this->db->sql($sql);
        $res = $this->db->getResult();

        if (!empty($res)) {
            return $res;
        }
    }
    function get_product_by_id($id = null)
    {
        if (!empty($id)) {
            $sql = "SELECT * FROM products WHERE id=" . $id;
        } else {
            $sql = "SELECT * FROM products";
        }
        $this->db->sql($sql);
        $res = $this->db->getResult();
        $product = array();
        $i = 1;
        foreach ($res as $row) {
            $sql = "SELECT *,(SELECT short_code FROM unit u WHERE u.id=pv.measurement_unit_id) as measurement_unit_name,(SELECT short_code FROM unit u WHERE u.id=pv.stock_unit_id) as stock_unit_name FROM product_variant pv WHERE pv.product_id=" . $row['id'];
            $this->db->sql($sql);
            $product[$i] = $row;
            $product[$i]['variant'] = $this->db->getResult();
            $i++;
        }
        if (!empty($product)) {
            return $product;
        }
    }
    function get_product_by_variant_id($arr)
    {
        $arr = stripslashes($arr);
        if (!empty($arr)) {
            $arr = json_decode($arr, 1);
            $i = 0;
            foreach ($arr as $id) {
                $sql = "SELECT *,pv.id,pv.type as product_type,(SELECT t.title FROM taxes t WHERE t.id=p.tax_id) as tax_title,(SELECT t.percentage FROM taxes t WHERE t.id=p.tax_id) as tax_percentage,(SELECT short_code FROM unit u WHERE u.id=pv.measurement_unit_id) as measurement_unit_name,(SELECT short_code FROM unit u WHERE u.id=pv.stock_unit_id) as stock_unit_name FROM product_variant pv JOIN products p ON pv.product_id=p.id WHERE pv.id=" . $id;
                $this->db->sql($sql);
                $res[$i] = $this->db->getResult()[0];
                $i++;
            }
            if (!empty($res)) {
                return $res;
            }
        }
    }
    function get_product_by_variant_id2($value)
    {
        // $arr = stripslashes($arr);
        // if (!empty($arr)) {
        // $arr = json_decode($arr, 1);
        // $i = 0;
        // foreach ($arr as $id) {
        $sql = "SELECT *,pv.id,(SELECT t.title FROM taxes t WHERE t.id=p.tax_id) as tax_title,(SELECT t.percentage FROM taxes t WHERE t.id=p.tax_id) as tax_percentage,(SELECT short_code FROM unit u WHERE u.id=pv.measurement_unit_id) as measurement_unit_name,(SELECT short_code FROM unit u WHERE u.id=pv.stock_unit_id) as stock_unit_name FROM product_variant pv JOIN products p ON pv.product_id=p.id WHERE pv.id=" . $value;
        $this->db->sql($sql);
        $res = $this->db->getResult()[0];

        // }
        if (!empty($res)) {
            return $res;
        }
        // }
    }

    function convert_to_parent($measurement, $measurement_unit_id)
    {
        $sql = "SELECT * FROM unit WHERE id=" . $measurement_unit_id;
        $this->db->sql($sql);
        $unit = $this->db->getResult();
        if (!empty($unit[0]['parent_id'])) {
            $stock = $measurement / $unit[0]['conversion'];
        } else {
            $stock = ($measurement) * $unit[0]['conversion'];
        }
        return $stock;
    }
    function rows_count($table, $field = '*', $where = '')
    {
        // Total count
        if (!empty($where)) $where = "Where " . $where;
        $sql = "SELECT COUNT(" . $field . ") as total FROM " . $table . " " . $where;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        foreach ($res as $row)
            return ($row['total'] != "") ? $row['total'] : 0;
    }

    public function get_configurations()
    {
        $sql = "SELECT value FROM settings WHERE `variable`='system_timezone'";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return json_decode($res[0]['value'], true);
        } else {
            return false;
        }
    }
    public function get_balance($id)
    {
        $sql = "SELECT balance FROM delivery_boys WHERE id=" . $id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res[0]['balance'];
        } else {
            return false;
        }
    }
    public function get_bonus($id)
    {
        $sql = "SELECT bonus FROM delivery_boys WHERE id=" . $id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res[0]['bonus'];
        } else {
            return false;
        }
    }
    public function get_wallet_balance($id, $table_name)
    {
        $sql = "SELECT balance FROM $table_name WHERE id=" . $id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res[0]['balance'];
        } else {
            return 0;
        }
    }
    public function update_wallet_balance($balance, $id, $table_name)
    {
        $data = array(
            'balance' => $balance
        );
        $this->db->update($table_name, $data, 'id=' . $id);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return true;
        } else {
            return false;
        }
    }

    public function add_wallet_transaction($order_id = "", $order_item_id = "", $id = "", $type, $amount, $message = 'Used against Order Placement', $table_name, $status = 1)
    {
        if ($table_name == 'seller_wallet_transactions') {
            $data = array(
                'order_id' => $order_id,
                'order_item_id' => $order_item_id,
                'seller_id' => $id,
                'type' => $type,
                'amount' => $amount,
                'message' => $message,
                'status' => $status
            );
        } else if ($table_name == 'wallet_transactions') {
            $data = array(
                'order_id' => $order_id,
                'order_item_id' => $order_item_id,
                'user_id' => $id,
                'type' => $type,
                'amount' => $amount,
                'message' => $message,
                'status' => $status
            );
        } else {
            $data = array(
                'order_id' => $order_id,
                'order_item_id' => $order_item_id,
                'user_id' => $id,
                'type' => $type,
                'amount' => $amount,
                'message' => $message,
                'status' => $status
            );
        }
        if ($this->db->insert($table_name, $data)) {
            if ($table_name == 'users') {
                $result = $this->send_order_update_notification($id, "Wallet Transaction", $message, 'wallet_transaction', 0);
            }
            $data1 = $this->db->getResult();
            return $data1[0];
        } else {
            return false;
        }
        // print_r($result);
    }

    public function update_order_item_status($order_item_id, $order_id, $status)
    {
        $data = array('update_order_item_status' => '1', 'order_item_id' => $order_item_id, 'status' => $status, 'order_id' => $order_id, 'ajaxCall' => 1);
        // print_r($data);

        $jwt_token = generate_token();

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                "Authorization: Bearer $jwt_token"
            ]
        );
        curl_setopt($ch, CURLOPT_URL, DOMAIN_URL . "api-firebase/order-process.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $response = curl_exec($ch);
        $header_info = curl_getinfo($ch, CURLINFO_HEADER_OUT);
        curl_close($ch);
        return $response;
    }

    public function validate_promo_code($user_id, $promo_code, $total)
    {
        $sql = "select * from promo_codes where promo_code='" . $promo_code . "'";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (empty($res)) {
            $response['error'] = true;
            $response['message'] = "Invalid promo code.";
            return $response;
            exit();
        }
        if ($res[0]['status'] == 0) {
            $response['error'] = true;
            $response['message'] = "This promo code is either expired / invalid.";
            return $response;
            exit();
        }

        $sql = "select id from users where id='" . $user_id . "'";
        $this->db->sql($sql);
        $res_user = $this->db->getResult();
        if (empty($res_user)) {
            $response['error'] = true;
            $response['message'] = "Invalid user data.";
            return $response;
            exit();
        }

        $start_date = $res[0]['start_date'];
        $end_date = $res[0]['end_date'];
        $date = date('Y-m-d h:i:s a');

        if ($date < $start_date) {
            $response['error'] = true;
            $response['message'] = "This promo code can't be used before " . date('d-m-Y', strtotime($start_date)) . "";
            return $response;
            exit();
        }
        if ($date > $end_date) {
            $response['error'] = true;
            $response['message'] = "This promo code can't be used after " . date('d-m-Y', strtotime($end_date)) . "";
            return $response;
            exit();
        }
        if ($total < $res[0]['minimum_order_amount']) {
            $response['error'] = true;
            $response['message'] = "This promo code is applicable only for order amount greater than or equal to " . $res[0]['minimum_order_amount'] . "";
            return $response;
            exit();
        }
        //check how many users have used this promo code and no of users used this promo code crossed max users or not
        $sql = "select id from orders where promo_code='" . $promo_code . "' GROUP BY user_id";
        $this->db->sql($sql);
        $res_order = $this->db->numRows();

        if ($res_order >= $res[0]['no_of_users']) {
            $response['error'] = true;
            $response['message'] = "This promo code is applicable only for first " . $res[0]['no_of_users'] . " users.";
            return $response;
            exit();
        }
        //check how many times user have used this promo code and count crossed max limit or not
        if ($res[0]['repeat_usage'] == 1) {
            $sql = "select id from orders where user_id=" . $user_id . " and promo_code='" . $promo_code . "'";
            $this->db->sql($sql);
            $total_usage = $this->db->numRows();
            if ($total_usage >= $res[0]['no_of_repeat_usage']) {
                $response['error'] = true;
                $response['message'] = "This promo code is applicable only for " . $res[0]['no_of_repeat_usage'] . " times.";
                return $response;
                exit();
            }
        }
        //check if repeat usage is not allowed and user have already used this promo code 
        if ($res[0]['repeat_usage'] == 0) {
            $sql = "select id from orders where user_id=" . $user_id . " and promo_code='" . $promo_code . "'";
            $this->db->sql($sql);
            $total_usage = $this->db->numRows();
            if ($total_usage >= 1) {
                $response['error'] = true;
                $response['message'] = "This promo code is applicable only for 1 time.";
                return $response;
                exit();
            }
        }
        if ($res[0]['discount_type'] == 'percentage') {
            $percentage = $res[0]['discount'];
            $discount = $total / 100 * $percentage;
            if ($discount > $res[0]['max_discount_amount']) {
                $discount = $res[0]['max_discount_amount'];
            }
        } else {
            $discount = $res[0]['discount'];
        }
        $discounted_amount = $total - $discount;
        $response['error'] = false;
        $response['message'] = "promo code applied successfully.";
        $response['promo_code'] = $promo_code;
        $response['promo_code_message'] = $res[0]['message'];
        $response['total'] = $total;
        $response['discount'] = "$discount";
        $response['discounted_amount'] = "$discounted_amount";
        return $response;
        exit();
    }
    public function get_settings($variable, $is_json = false)
    {
        if ($variable == 'logo' || $variable == 'Logo') {
            $sql = "select value from `settings` where variable='Logo' OR variable='logo'";
        } else {
            $sql = "SELECT value FROM `settings` WHERE `variable`='$variable'";
        }

        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res) && isset($res[0]['value'])) {
            if ($is_json)
                return json_decode($res[0]['value'], true);
            else
                return $res[0]['value'];
        } else {
            return false;
        }
    }
    public function send_order_update_notification($uid, $title, $message, $type, $id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //hecking the required params 
            //creating a new push
            /*dynamically getting the domain of the app*/
            $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $url .= $_SERVER['SERVER_NAME'];
            $url .= $_SERVER['REQUEST_URI'];
            $server_url = dirname($url) . '/';

            $push = null;
            //first check if the push has an image with it
            //if the push don't have an image give null in place of image
            $push = new Push(
                $title,
                $message,
                null,
                $type,
                $id
            );
            //getting the push from push object
            $mPushNotification = $push->getPush();

            //getting the token from database object
            $sql = "SELECT fcm_id FROM users WHERE id = '" . $uid . "'";
            $this->db->sql($sql);
            $res = $this->db->getResult();
            $token = array();
            foreach ($res as $row) {
                array_push($token, $row['fcm_id']);
            }

            //creating firebase class object 
            $firebase = new Firebase();

            //sending push notification and displaying result 
            $firebase->send($token, $mPushNotification);
            $response['error'] = false;
            $response['message'] = "Successfully Send";
        } else {
            $response['error'] = true;
            $response['message'] = 'Invalid request';
        }
    }
    public function send_notification_to_delivery_boy($delivery_boy_id, $title, $message, $type, $order_id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //hecking the required params 
            //creating a new push
            /*dynamically getting the domain of the app*/
            $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $url .= $_SERVER['SERVER_NAME'];
            $url .= $_SERVER['REQUEST_URI'];
            $server_url = dirname($url) . '/';

            $push = null;
            //first check if the push has an image with it
            //if the push don't have an image give null in place of image
            $push = new Push(
                $title,
                $message,
                null,
                $type,
                $order_id
            );
            //getting the push from push object
            $m_push_notification = $push->getPush();

            //getting the token from database object
            $sql = "SELECT fcm_id FROM delivery_boys WHERE id = '" . $delivery_boy_id . "'";
            $this->db->sql($sql);
            $res = $this->db->getResult();
            $token = array();
            foreach ($res as $row) {
                array_push($token, $row['fcm_id']);
            }

            //creating firebase class object 
            $firebase = new Firebase();

            //sending push notification and displaying result 
            $firebase->send($token, $m_push_notification);
            $response['error'] = false;
            $response['message'] = "Successfully Send";
            //print_r(json_encode($response));
        } else {
            $response['error'] = true;
            $response['message'] = 'Invalid request';
            // print_r(json_encode($response));
        }
    }
    public function get_promo_details($promo_code)
    {
        $sql = "SELECT * FROM `promo_codes` WHERE `promo_code`='$promo_code'";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }
    public function store_return_request($user_id, $order_id, $order_item_id)
    {
        $sql = "select product_variant_id from order_items where id=" . $order_item_id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        $pv_id = $res[0]['product_variant_id'];
        $sql = "select product_id from product_variant where id=" . $pv_id;
        $this->db->sql($sql);
        $res = $this->db->getResult();

        $data = array(
            'user_id' => $user_id,
            'order_id' => $order_id,
            'order_item_id' => $order_item_id,
            'product_id' => $res[0]['product_id'],
            'product_variant_id' => $pv_id
        );
        $this->db->insert('return_requests', $data);
        return $this->db->getResult()[0];
    }
    public function get_role($id)
    {
        $sql = "SELECT role FROM admin WHERE id=" . $id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res) && isset($res[0]['role'])) {
            return $res[0]['role'];
        } else {
            return 0;
        }
    }
    public function get_permissions($id)
    {
        $sql = "SELECT permissions FROM admin WHERE id=" . $id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res) && isset($res[0]['permissions'])) {
            return json_decode($res[0]['permissions'], true);
        } else {
            return 0;
        }
    }

    public function add_delivery_boy_commission($id, $type, $amount, $message, $status = "SUCCESS")
    {
        $balance = $this->get_balance($id);
        $data = array(
            'delivery_boy_id' => $id,
            'type' => $type,
            'opening_balance' => $balance,
            'closing_balance' => $balance + $amount,
            'amount' => $amount,
            'message' => $message,
            'status' => $status
        );
        $this->db->insert('fund_transfers', $data);
        $result = $this->db->getResult()[0];
        return (!empty($result)) ? $result : "0";
    }

    public function store_delivery_boy_notification($delivery_boy_id, $order_item_id, $title, $message, $type)
    {
        $data = array(
            'delivery_boy_id' => $delivery_boy_id,
            'order_item_id' => $order_item_id,
            'title' => $title,
            'message' => $message,
            'type' => $type
        );
        $this->db->insert('delivery_boy_notifications', $data);
        return $this->db->getResult()[0];
    }
    public function is_item_available_in_user_cart($user_id, $product_variant_id = "")
    {
        $sql = "SELECT id FROM cart WHERE user_id=" . $user_id;
        $sql .= !empty($product_variant_id) ? " AND product_variant_id=" . $product_variant_id : "";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    }
    public function is_item_available($product_id, $product_variant_id)
    {
        $sql = "SELECT id FROM product_variant WHERE product_id=" . $product_id . " AND id=" . $product_variant_id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            $sql = "SELECT id FROM products WHERE id=$product_id";
            $this->db->sql($sql);
            $res = $this->db->getResult();
            if (!empty($res)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    public function time_slot_config()
    {
        $sql = "SELECT value FROM settings WHERE `variable`='time_slot_config'";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return json_decode($res[0]['value'], true);
        } else {
            return false;
        }
    }

    public function is_address_exists($id = "", $user_id = "")
    {
        $sql = "SELECT id FROM user_addresses WHERE ";
        $sql .= !empty($id) ? "id=$id" : "user_id=$user_id";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function is_user_or_dboy_exists($type, $type_id)
    {
        // $type1 = $type == 'user' ? 'users' : 'delivery_boys';
        $sql = "SELECT id FROM $type WHERE id=" . $type_id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function get_user_or_delivery_boy_balance($type, $type_id)
    {
        // $type1 = $type == 'user' ? 'users' : 'delivery_boys';
        $sql = "SELECT balance FROM $type WHERE id=" . $type_id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res[0]['balance'];
        } else {
            return false;
        }
    }
    public function store_withdrawal_request($type, $type_id, $amount, $message)
    {

        $data = array(
            'type' => $type,
            'type_id' => $type_id,
            'amount' => $amount,
            'message' => $message,
        );
        if ($this->db->insert('withdrawal_requests', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function debit_balance($type, $type_id, $new_balance)
    {
        // $type1 = $type == 'user' ? 'users' : 'delivery_boys';
        $sql = "UPDATE $type SET balance=" . $new_balance . " WHERE id=" . $type_id;
        if ($this->db->sql($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function is_records_exists($type, $type_id, $offset, $limit)
    {
        $offset = empty($offset) ? 0 : $offset;
        $sql = "SELECT * FROM withdrawal_requests WHERE `type`= '" . $type . "' AND `type_id` = " . $type_id . " ORDER BY date_created DESC";
        $sql .= !empty($limit) ? " LIMIT $offset,$limit" : "";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        return $res;
    }

    public function get_product_id_by_variant_id($product_variant_id)
    {
        $sql = "SELECT product_id FROM product_variant WHERE `id`= " . $product_variant_id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res[0]['product_id'];
        } else {
            return false;
        }
    }
    public function get_variant_id_by_product_id($product_id)
    {
        $sql = "SELECT id FROM product_variant WHERE `product_id`= " . $product_id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        return $res[0]['id'];
    }

    public function update_delivery_boy_wallet_balance($balance, $id)
    {
        $data = array(
            'balance' => $balance
        );
        if ($this->db->update('delivery_boys', $data, 'id=' . $id))
            return true;
        else
            return false;
    }

    function low_stock_count($low_stock_limit)
    {
        $sql = "SELECT COUNT(id) as total FROM product_variant WHERE stock < $low_stock_limit AND serve_for='Available'";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        foreach ($res as $row)
            return $row['total'];
    }
    function low_stock_count1($low_stock_limit, $id)
    {
        $sql = "SELECT COUNT(pv.id) as total FROM `product_variant` pv JOIN products p ON p.id=pv.product_id WHERE pv.stock < $low_stock_limit AND pv.serve_for='Available' AND p.seller_id=$id";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        foreach ($res as $row)
            return $row['total'];
    }

    function sold_out_count()
    {
        $sql = "SELECT COUNT(id) as total FROM product_variant WHERE  serve_for='Sold Out'";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        foreach ($res as $row)
            return $row['total'];
    }
    function sold_out_count1($id)
    {
        $sql1 = "SELECT COUNT(pv.id) as total FROM product_variant pv JOIN products p ON p.id=pv.product_id WHERE pv.serve_for='Sold Out' AND p.seller_id=$id";
        $this->db->sql($sql1);
        $res = $this->db->getResult();
        foreach ($res as $row)
            return $row['total'];
    }

    public function is_product_set_as_rating($product_id)
    {
        // $sql = "select product_rating from category "
        $sql = "SELECT p.id,c.name FROM `products` p join category c on c.id=p.category_id where p.id=$product_id and c.product_rating=1";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function is_user_exists($user_id)
    {
        $sql = "SELECT id FROM users WHERE id=" . $user_id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function is_product_available($product_id)
    {
        $sql = "SELECT id FROM products WHERE id=" . $product_id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function is_product_added_as_favorite($user_id, $product_id)
    {
        $sql = "SELECT id FROM favorites WHERE product_id=" . $product_id . " AND user_id=" . $user_id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function validate_email($email, $table = '')
    {
        if ($table == 'seller') {
            $sql = "SELECT email FROM `seller` WHERE email='" . $email . "'";
        } else {

            $sql = "SELECT email FROM `admin` WHERE email='" . $email . "'";
        }
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res[0]['email'];
        } else {
            return 0;
        }
    }

    public function update_forgot_password_code($email, $code, $table = '')
    {
        if ($table == 'seller') {
            $sql = "UPDATE seller set forgot_password_code = '" . $code . "' WHERE email='" . $email . "'";
        } else {
            $sql = "UPDATE admin set forgot_password_code = '" . $code . "' WHERE email='" . $email . "'";
        }
        if ($this->db->sql($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function validate_code($code, $table = '')
    {
        if ($table == 'seller') {
            $sql = "SELECT forgot_password_code FROM `seller` WHERE forgot_password_code='" . $code . "'";
        } else {
            $sql = "SELECT forgot_password_code FROM `admin` WHERE forgot_password_code='" . $code . "'";
        }
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function get_user($code, $table = '')
    {
        if ($table == 'seller') {
            $sql = "SELECT name,email FROM `seller` WHERE forgot_password_code='" . $code . "'";
        } else {
            $sql = "SELECT username,email FROM `admin` WHERE forgot_password_code='" . $code . "'";
        }
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res;
        } else {
            return 0;
        }
    }

    public function update_password($code, $password_hash, $table = '')
    {
        if ($table == 'seller') {
            $sql = "UPDATE seller set password = '" . $password_hash . "' WHERE forgot_password_code='" . $code . "'";
        } else {
            $sql = "UPDATE admin set password = '" . $password_hash . "' WHERE forgot_password_code='" . $code . "'";
        }
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res;
        } else {
            return 0;
        }
    }

    public function is_return_request_exists($user_id, $order_item_id)
    {
        $sql = "SELECT id FROM return_requests WHERE user_id = '" . $user_id . "' AND order_item_id = '" . $order_item_id . "'";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function get_last_inserted_id($table)
    {
        $sql = "SELECT MAX(id) as id FROM $table";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res[0]['id'];
        } else {
            return 0;
        }
    }

    public function is_product_cancellable($order_item_id)
    {
        $sql = "SELECT product_variant_id,active_status FROM order_items WHERE id = " . $order_item_id;
        $this->db->sql($sql);
        $result = $this->db->getResult();
        $sql = "SELECT p.cancelable_status,p.till_status FROM products p JOIN product_variant pv ON p.id=pv.product_id WHERE pv.id=" . $result[0]['product_variant_id'];
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if ($res[0]['cancelable_status'] == 1) {
            if ($res[0]['till_status'] == 'received' && ($result[0]['active_status'] != 'awaiting_payment' &&  $result[0]['active_status'] != 'received')) {
                $response['error'] = true;
                $response['till_status_error'] = true;
                $response['cancellable_status_error'] = false;
                $response['message'] = 'Sorry this item is only cancelable till status ' . $res[0]['till_status'] . '!';
            } elseif ($res[0]['till_status'] == 'processed' && ($result[0]['active_status'] != 'awaiting_payment' &&  $result[0]['active_status'] != 'received' && $result[0]['active_status'] != 'processed')) {
                $response['error'] = true;
                $response['till_status_error'] = true;
                $response['cancellable_status_error'] = false;
                $response['message'] = 'Sorry this item is only cancelable till status ' . $res[0]['till_status'] . '!';
            } elseif ($res[0]['till_status'] == 'shipped' && ($result[0]['active_status'] != 'awaiting_payment' && $result[0]['active_status'] != 'received' && $result[0]['active_status'] != 'processed' && $result[0]['active_status'] != 'shipped')) {
                $response['error'] = true;
                $response['till_status_error'] = true;
                $response['cancellable_status_error'] = false;
                $response['message'] = 'Sorry this item is only cancelable till status ' . $res[0]['till_status'] . '!';
            } else {
                $response['error'] = false;
                $response['till_status_error'] = false;
                $response['cancellable_status_error'] = false;
                $response['message'] = 'Item Cancellation criteria matched!';
            }
        } else {
            $response['error'] = true;
            $response['cancellable_status_error'] = true;
            $response['till_status_error'] = true;
            $response['message'] = 'Sorry this item is not cancelable!';
        }
        return $response;
    }

    public function is_product_returnable($order_item_id)
    {
        $sql = "SELECT product_variant_id FROM order_items WHERE id = " . $order_item_id;
        $this->db->sql($sql);
        $result = $this->db->getResult();

        $sql = "SELECT p.return_status FROM products p JOIN product_variant pv ON p.id=pv.product_id WHERE pv.id=" . $result[0]['product_variant_id'];
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if ($res[0]['return_status'] == 1) {
            $response['error'] = false;
            $response['return_status_error'] = false;
            $response['message'] = 'Item return criteria matched!';
        } else {
            $response['error'] = true;
            $response['return_status_error'] = true;
            $response['message'] = 'Sorry this item is not returnable!';
        }

        return $response;
    }

    public function remove_other_addresses_from_default($user_id)
    {
        $sql = "UPDATE user_addresses SET is_default = 0 WHERE user_id = " . $user_id;
        $this->db->sql($sql);
    }

    public function verifyTransaction($data)
    {
        global $paypalUrl;

        $req = 'cmd=_notify-validate';
        foreach ($data as $key => $value) {
            $value = urlencode(stripslashes($value));
            $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value); // IPN fix
            $req .= "&$key=$value";
        }
        $ch = curl_init($paypalUrl);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        $res = curl_exec($ch);

        if (!$res) {
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: [$errno] $errstr");
        }

        $info = curl_getinfo($ch);

        // Check the http response
        $httpCode = $info['http_code'];
        if ($httpCode != 200) {
            throw new Exception("PayPal responded with http code $httpCode");
        }

        curl_close($ch);

        return $res === 'VERIFIED';
    }
    public function checkTxnid($txnid)
    {
        $txnid = $this->db->escapeString($txnid);
        $sql = 'SELECT * FROM `payments` WHERE txnid = \'' . $txnid . '\'';
        $result = $this->db->getResult();
        return !$this->db->numRows();;
    }
    public function get_data($columns = [], $where, $table)
    {
        $sql = "select ";
        if (!empty($columns)) {
            $columns = implode(",", $columns);
            $sql .= " $columns from ";
        } else {
            $sql .= " * from ";
        }
        $sql .= " `$table` WHERE $where";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        return $res;
    }
    public function update_order_status($id, $order_item_id, $status, $delivery_boy_id = 0)
    {
        $data = array('update_order_status' => '1', 'order_id' => $id, 'status' => $status, 'order_item_id' => $order_item_id, 'delivery_boy_id' => $delivery_boy_id, 'ajaxCall' => 1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, DOMAIN_URL . "api-firebase/order-process.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function verify_paystack_transaction($reference, $email, $amount)
    {
        $payment_methods = $this->get_settings('payment_methods', true);
        //The parameter after verify/ is the transaction reference to be verified
        $url = 'https://api.paystack.co/transaction/verify/' . $reference;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Authorization: Bearer ' . $payment_methods['paystack_secret_key']
            ]
        );

        //send request
        $request = curl_exec($ch);
        //close connection
        curl_close($ch);
        //declare an array that will contain the result 
        $result = array();

        if ($request) {
            $result = json_decode($request, true);
        }

        if ($result['status'] == true) {

            if (array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success')) {
                if ($result['data']['customer']['email'] == $email && $result['data']['amount'] == $amount) {
                    $response['error'] = false;
                    $response['message'] = "Transaction verified successfully.";
                    $response['status'] = $result['data']['status'];
                } else {
                    $response['error'] = true;
                    $response['message'] = "Transaction verified but does not belong to specified customer or invalid amount sent.";
                    $response['status'] = $result['data']['status'];
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Transaction was unsuccessful. try again";
                $response['status'] = $result['data']['status'];
            }
        } else {
            $response['error'] = true;
            $response['message'] = "Could not initiate verification. " . $result['message'];
            $response['status'] = "failed";
        }
        return $response;
    }
    public function get_payment_methods()
    {
        $sql = "SELECT value FROM settings WHERE `variable`='payment_methods'";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return json_decode($res[0]['value'], true);
        } else {
            return false;
        }
    }
    public function get_order_item_by_order_id($id)
    {
        $sql = "SELECT * FROM `order_items` where order_id=$id";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }
    public function add_wallet_balance($order_id, $user_id, $amount, $type, $message)
    {
        $data = array('add_wallet_balance' => '1', 'user_id' => $user_id, 'order_id' => $order_id, 'amount' => $amount, 'type' => $type, 'message' => $message, 'ajaxCall' => 1);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, DOMAIN_URL . "api-firebase/get-user-transactions.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function send_notification_to_admin($title, $message, $type, $order_id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            /*dynamically getting the domain of the app*/
            $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $url .= $_SERVER['SERVER_NAME'];
            $url .= $_SERVER['REQUEST_URI'];
            $server_url = dirname($url) . '/';
            $push = null;
            $push = new Push(
                $title,
                $message,
                "",
                $type,
                $order_id
            );
            $m_push_notification = $push->getPush();
            $sql = "SELECT fcm_id FROM admin";
            $this->db->sql($sql);
            $res = $this->db->getResult();
            $token = array();
            foreach ($res as $row) {
                array_push($token, $row['fcm_id']);
            }
            //creating firebase class object 
            $firebase = new Firebase();
            //sending push notification and displaying result 
            $firebase->send($token, $m_push_notification);
            $response['error'] = false;
            $response['message'] = "Successfully Send";
            //print_r(json_encode($response));
        } else {
            $response['error'] = true;
            $response['message'] = 'Invalid request';
        }
    }

    public function add_seller_wallet_transaction($order_id = "", $order_item_id, $seller_id, $type, $amount, $message = 'Used against Order Placement', $status = 1)
    {
        // `order_id`, `order_item_id`, `seller_id`, `type`, `amount`, `message`, `status`
        $data = array(
            'order_id' => $order_id,
            'order_item_id' => $order_item_id,
            'seller_id' => $seller_id,
            'type' => $type,
            'amount' => $amount,
            'message' => $message,
            'status' => $status
        );
        $this->db->insert('seller_wallet_transactions', $data);
        $data1 = $this->db->getResult();
        // $result = $this->send_order_update_notification($seller_id, "Wallet Transaction", $message, 'wallet_transaction', 0);
        // print_r($result);
        return $data1[0];
    }

    function replaceArrayKeys($array)
    {
        $replacedKeys = str_replace('-', '_', array_keys($array));
        return array_combine($replacedKeys, $array);
    }

    public function validate_image($file, $is_image = true)
    {
        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($finfo, $file['tmp_name']);
        } else if (function_exists('mime_content_type')) {
            $type = mime_content_type($file['tmp_name']);
        } else {
            $type = $file['type'];
        }
        $type = strtolower($type);
        if ($is_image == false) {
            if (!in_array($type, array('text/plain'))) {
                return true;
            } else {
                return false;
            }
        } else if ($is_image == true) {
            if (!in_array($type, array('image/jpg', 'image/jpeg', 'image/gif', 'image/png'))) {
                return true;
            } else {
                return false;
            }
        } else {
            if (!in_array($type, array('image/jpg', 'image/jpeg', 'image/gif', 'image/png'))) {
                return true;
            } else {
                return false;
            }
        }
    }
    public function validate_other_images($tmp_name, $type)
    {
        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type1 = finfo_file($finfo, $tmp_name);
        } else if (function_exists('mime_content_type')) {
            $type1 = mime_content_type($tmp_name);
        } else {
            $type1 = $type;
        }
        if (!in_array($type1, array('image/jpg', 'image/jpeg', 'image/gif', 'image/png'))) {
            return true;
        } else {
            return false;
        }
    }

    public function is_order_item_cancelled($order_item_id)
    {
        $sql = "SELECT COUNT(id) as cancelled FROM `order_items` WHERE id=" . $order_item_id . " && (active_status LIKE '%cancelled%' OR active_status LIKE '%returned%')";
        $this->db->sql($sql);
        $res_cancelled = $this->db->getResult();
        if ($res_cancelled[0]['cancelled'] > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function is_order_item_returned($active_status, $postStatus)
    {
        if ($active_status != 'delivered' && $postStatus == 'returned') {
            return true;
        } else {
            return false;
        }
    }
    public function cancel_order_item($id, $order_item_id)
    {
        // $sql = 'SELECT oi.`id` as order_item_id,oi.`product_variant_id`,oi.`quantity`,pv.`product_id`,pv.`type`,pv.`stock`,pv.`stock_unit_id`,pv.`measurement`,pv.`measurement_unit_id` FROM `order_items` oi join `product_variant` pv on pv.id = oi.product_variant_id WHERE oi.`id`=' . $order_item_id;
        // $this->db->sql($sql);
        // $res_oi = $this->db->getResult();
        $res_order = $this->get_data($columns = ['final_total', 'total', 'user_id', 'payment_method', 'wallet_balance', 'delivery_charge', 'tax_amount', 'status', 'area_id', 'promo_discount'], 'id=' . $id, 'orders');
        $sql = 'SELECT oi.*,oi.`product_variant_id`,oi.`quantity`,oi.`discounted_price`,oi.`price`,pv.`product_id`,pv.`type`,pv.`stock`,pv.`stock_unit_id`,pv.`measurement`,pv.`measurement_unit_id` FROM `order_items` oi join `product_variant` pv on pv.id = oi.product_variant_id WHERE oi.`id`=' . $order_item_id;
        $this->db->sql($sql);
        $res_oi = $this->db->getResult();
        $price = ($res_oi[0]['discounted_price'] == 0) ? ($res_oi[0]['price'] * $res_oi[0]['quantity']) + ($res_oi[0]['tax_amount'] * $res_oi[0]['quantity'])  : ($res_oi[0]['discounted_price'] * $res_oi[0]['quantity'])  + ($res_oi[0]['tax_amount'] * $res_oi[0]['quantity']);
        $total = $res_order[0]['total'];
        $delivery_charge = $res_order[0]['delivery_charge'];
        $final_total = $res_order[0]['final_total'];
        if ($total - $price >= 0) {
            $sql_total = "update orders set total=$total-$price where id=" . $id;
            $this->db->sql($sql_total);
        }
        $min_amount = $this->get_data($columns = ['minimum_free_delivery_order_amount', 'delivery_charges'], "id=" . $res_order[0]['area_id'], 'area');
        $res_total = $this->get_data($columns = ['total'], "id=" . $id, 'orders');
        $total = $res_total[0]['total'];
        if ($res_order[0]['wallet_balance'] != 0 && $res_order[0]['wallet_balance'] > $res_oi[0]['sub_total'] && strtolower($res_order[0]['payment_method']) == 'cod') {
            $price = $final_total = 0;
        } else {
            if ($total < $min_amount[0]['minimum_free_delivery_order_amount']) { // $config['min_amount'] = Minimum Amount for Free Delivery
                if ($delivery_charge == 0) {
                    $dchrg = $min_amount[0]['delivery_charges'];
                    $sql_delivery_chrg = "update orders set delivery_charge=$dchrg where id=" . $id;
                    $this->db->sql($sql_delivery_chrg);
                    $sql_final_total = "update orders set final_total=$final_total-$price+$dchrg where id=" . $id;
                } else {
                    $sql_final_total = "update orders set final_total=$final_total-$price where id=" . $id;
                }
                $this->db->sql($sql_final_total);
            } else {
                $sql_final_total = "update orders set final_total=$final_total-$price where id=" . $id;
            }
        }

        if ($res_order[0]['wallet_balance'] != 0  && strtolower($res_order[0]['payment_method']) == 'wallet') {
            $sql_final_total = "update orders set final_total=0 where id=" . $id;
        }
        if ($this->db->sql($sql_final_total)) {
            if (strtolower($res_order[0]['payment_method']) != 'cod') {
                /* update user's wallet */
                $user_id = $res_order[0]['user_id'];
                $res_order[0]['tax_amount'] . "total" . $res_order[0]['total'] . "d_charge" . $res_order[0]['delivery_charge'];
                $total_amount = ($res_oi[0]['sub_total'] + $res_order[0]['delivery_charge']) - $res_order[0]['promo_discount'];
                $user_wallet_balance = $this->get_wallet_balance($user_id, 'users');
                $new_balance = $user_wallet_balance + $total_amount;
                $this->update_wallet_balance($new_balance, $user_id, 'users');
                $wallet_txn_id = $this->add_wallet_transaction($id, $order_item_id, $user_id, 'credit', $total_amount, 'Balance credited against item cancellation...', 'wallet_transactions');
            } else {
                if ($res_order[0]['wallet_balance'] != 0) {
                    $user_id = $res_order[0]['user_id'];
                    $user_wallet_balance = $this->get_wallet_balance($user_id, 'users');
                    // $new_balance = ($user_wallet_balance + $res_order[0]['wallet_balance']);
                    $res_oi[0]['sub_total'];
                    $new_balance = ($user_wallet_balance + $res_oi[0]['sub_total']);
                    $this->update_wallet_balance($new_balance, $user_id, 'users');
                    $wallet_txn_id = $this->add_wallet_transaction($id, $order_item_id, $user_id, 'credit', $res_oi[0]['sub_total'], 'Balance credited against item cancellation!!', 'wallet_transactions');
                }
            }
            if ($res_oi[0]['type'] == 'packet') {
                $sql = "UPDATE product_variant SET stock = stock + " . $res_oi[0]['quantity'] . " WHERE id='" . $res_oi[0]['product_variant_id'] . "'";
                $this->db->sql($sql);
                $sql = "select stock from product_variant where id=" . $res_oi[0]['product_variant_id'];
                $this->db->sql($sql);
                $res_stock = $this->db->getResult();
                if ($res_stock[0]['stock'] > 0) {
                    $sql = "UPDATE product_variant set serve_for='Available' WHERE id='" . $res_oi[0]['product_variant_id'] . "'";
                    $this->db->sql($sql);
                }
            } else {
                /* When product type is loose */
                if ($res_oi[0]['measurement_unit_id'] != $res_oi[0]['stock_unit_id']) {
                    $stock = $this->convert_to_parent($res_oi[0]['measurement'], $res_oi[0]['measurement_unit_id']);
                    $stock = $stock * $res_oi[0]['quantity'];
                    $sql = "UPDATE product_variant SET stock = stock + " . $stock . " WHERE product_id='" . $res_oi[0]['product_id'] . "'" .  " AND id='" . $res_oi[0]['product_variant_id'] . "'";
                    $this->db->sql($sql);
                } else {
                    $stock = $res_oi[0]['measurement'] * $res_oi[0]['quantity'];
                    $sql = "UPDATE product_variant SET stock = stock + " . $stock . " WHERE product_id='" . $res_oi[0]['product_id'] . "'" .  " AND id='" . $res_oi[0]['product_variant_id'] . "'";
                    $this->db->sql($sql);
                }
            }
            if ($total == 0) {
                $sql = "update orders set delivery_charge=0,tax_amount=0,tax_percentage=0,final_total=0 where id=" . $id;
                if ($this->db->sql($sql)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function get_user_address($address_id)
    {
        $address_data = $this->get_data($columns = ['mobile', 'latitude', 'longitude', 'address', 'pincode_id', 'area_id', 'landmark', 'state', 'country'], "id=" . $address_id, 'user_addresses');
        if ($address_data[0]['pincode_id'] == "" || $address_data[0]['area_id'] == "") {
            return false;
        }
        if (!empty($address_data)) {
            $area = $this->get_data($columns = ['name'], 'id=' . $address_data[0]['area_id'], 'area');
            $sql = "SELECT a.*,c.name as city_name,p.pincode FROM `area` a LEFT JOIN pincodes p on p.id=a.pincode_id LEFT JOIN cities c on c.id=a.city_id where a.id= " . $address_data[0]['area_id'];
            $this->db->sql($sql);
            $res_city = $this->db->getResult();
            $user_address = $address_data[0]['address'] . "," . $address_data[0]['landmark'] . "," . $res_city[0]['city_name'] . "," . $area[0]['name'] . "," . $address_data[0]['state'] . "," . $address_data[0]['country'] . "," . "Pincode:" . $res_city[0]['pincode'];
            $order_data = array('user_address' => $user_address, 'mobile' => $address_data[0]['mobile'], 'latitude' => $address_data[0]['latitude'], 'longitude' => $address_data[0]['longitude'], 'pincode_id' => $address_data[0]['pincode_id'], 'area_id' => $address_data[0]['area_id']);
            return $order_data;
        } else {

            return false;
        }
    }
    public function send_notification_to_seller($sid, $title, $message, $type, $id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //hecking the required params 
            //creating a new push
            /*dynamically getting the domain of the app*/
            $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $url .= $_SERVER['SERVER_NAME'];
            $url .= $_SERVER['REQUEST_URI'];
            $server_url = dirname($url) . '/';

            $push = null;
            //first check if the push has an image with it
            //if the push don't have an image give null in place of image
            $push = new Push(
                $title,
                $message,
                null,
                $type,
                $id
            );
            //getting the push from push object
            $mPushNotification = $push->getPush();

            //getting the token from database object
            $sql = "SELECT fcm_id FROM seller WHERE id = '" . $sid . "'";
            $this->db->sql($sql);
            $res = $this->db->getResult();
            $token = array();
            foreach ($res as $row) {
                array_push($token, $row['fcm_id']);
            }

            //creating firebase class object 
            $firebase = new Firebase();

            //sending push notification and displaying result 
            $firebase->send($token, $mPushNotification);
            $response['error'] = false;
            $response['message'] = "Successfully Send";
        } else {
            $response['error'] = true;
            $response['message'] = 'Invalid request';
        }
    }
    public function check_for_return_request($product_id = 0, $order_id = 0)
    {
        if (!empty($product_id)) {
            $sql_i = "SELECT id,status FROM `return_requests` where product_id=" . $product_id;
            $this->db->sql($sql_i);
            $return_res = $this->db->getResult();
            $status = array();
            if (!empty($return_res)) {
                foreach ($return_res as $row) {
                    if ($row['status'] == 0) {
                        array_push($status, "1");
                    } else {
                        array_push($status, "2");
                    }
                }
                if (in_array("1", $status)) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        } else {
            $sql_i = "SELECT id,status FROM `return_requests` where order_id=" . $order_id;
            $this->db->sql($sql_i);
            $return_res = $this->db->getResult();
            $status = array();
            if (!empty($return_res)) {
                foreach ($return_res as $row) {
                    if ($row['status'] == 0) {
                        array_push($status, "1");
                    } else {
                        array_push($status, "2");
                    }
                }
                if (in_array("1", $status)) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }

    public function delete_product($product_id)
    {
        if ($this->check_for_return_request($product_id, 0)) {

            $sql = "SELECT * FROM `sections` where find_in_set($product_id,product_ids)";
            $this->db->sql($sql);
            $section = $this->db->getResult();
            foreach ($section as $row) {
                $product_ids = explode(',', $row['product_ids']);
                if (($key = array_search($product_id, $product_ids)) !== false) {
                    unset($product_ids[$key]);
                }
                
                if (!empty($product_ids)) {
                    $product_ids = implode(',', $product_ids);
                    
                    $sql = "UPDATE `sections` SET `product_ids` = '$product_ids' WHERE id=" . $row['id'];
                    $this->db->sql($sql);
                } else {
                    $sql = "DELETE FROM sections WHERE id=" . $row['id'];
                    $this->db->sql($sql);
                }
            }

            $sql_query = "DELETE FROM product_variant WHERE product_id=" . $product_id;
            $this->db->sql($sql_query);
            $sql_query = "DELETE FROM cart WHERE product_id = " . $product_id;
            $this->db->sql($sql_query);

            $sql = "SELECT count(id) as total from product_variant where product_id=" . $product_id;
            $this->db->sql($sql);
            $total = $this->db->getResult();
            // get image file from menu table
            if ($total[0]['total'] == 0) {
                $sql_query = "SELECT image FROM products WHERE id =" . $product_id;
                $this->db->sql($sql_query);
                $res = $this->db->getResult();
                unlink("../" . $res[0]['image']);

                $sql_query = "SELECT other_images FROM products WHERE id =" . $product_id;
                $this->db->sql($sql_query);
                $res = $this->db->getResult();
                if (!empty($res[0]['other_images'])) {
                    $other_images = json_decode($res[0]['other_images']);
                    foreach ($other_images as $other_image) {
                        unlink("../" . $other_image);
                    }
                }

                $sql_query = "DELETE FROM products WHERE id =" . $product_id;
                if ($this->db->sql($sql_query)) {
                    $sql_query = "DELETE FROM favorites WHERE product_id = " . $product_id;
                    $this->db->sql($sql_query);
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }
    public function get_seller_permission($seller_id, $permission)
    {
        $sql = "SELECT " . $permission . " from seller where id=$seller_id";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            if ($res[0][$permission] == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function get_seller_balance($seller_id)
    {
        $sql = "SELECT balance FROM seller WHERE id=" . $seller_id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res[0]['balance'];
        } else {
            return false;
        }
    }


    public function delete_order($order_id)
    {
        if ($this->check_for_return_request(0, $order_id)) {
            $sql_query = "DELETE FROM orders WHERE id =" . $order_id;
            if ($this->db->sql($sql_query)) {
                $sql = "DELETE FROM order_items WHERE order_id =" . $order_id;
                $this->db->sql($sql);
                $sql_return = "DELETE FROM return_requests WHERE order_id =" . $order_id;
                $this->db->sql($sql_return);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function delete_order_item($order_item_id)
    {

        $sql_i = "SELECT id,status,order_id FROM `return_requests` where order_item_id=" . $order_item_id;
        $this->db->sql($sql_i);
        $return_res = $this->db->getResult();
        if (!empty($return_res)) {
            if ($return_res[0]['status'] == 1) {
                $sql = "DELETE FROM order_items WHERE id =" . $order_item_id;
                $this->db->sql($sql);
                $sql_return = "DELETE FROM return_requests WHERE order_item_id =" . $order_item_id;
                $this->db->sql($sql_return);
                $sql_i = "SELECT id FROM `order_items` where order_id=" . $return_res[0]['order_id'];
                $this->db->sql($sql_i);
                $res_order = $this->db->getResult();
                if (empty($res_order)) {
                    $this->delete_order($return_res[0]['order_id']);
                }
                return true;
            } else {
                return false;
            }
        } else {
            $sql_i = "SELECT order_id FROM `order_items` where id=" . $order_item_id;
            $this->db->sql($sql_i);
            $res_order_id = $this->db->getResult();
            $sql = "DELETE FROM order_items WHERE id =" . $order_item_id;
            if ($this->db->sql($sql)) {
                $sql_i = "SELECT id FROM `order_items` where order_id=" . $res_order_id[0]['order_id'];
                $this->db->sql($sql_i);
                $res_order = $this->db->getResult();
                if (empty($res_order)) {
                    $this->delete_order($res_order_id[0]['order_id']);
                }
                return true;
            } else {
                return false;
            }
        }
    }

    public function select_top_sellers()
    {
        $sql = "SELECT SUM(oi.sub_total) as total,oi.seller_id,s.name as seller_name,s.store_name FROM `order_items` oi JOIN seller s on s.id=oi.seller_id where oi.active_status='delivered' GROUP BY oi.seller_id ORDER BY `total` DESC LIMIT 0,5";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }
    public function select_top_categories()
    {
        $sql = "SELECT pv.product_id,pv.id,p.name as p_name,p.category_id,p.seller_id,c.name as cat_name, pv.measurement,oi.product_name,oi.variant_name,SUM(oi.sub_total) as total FROM `order_items` oi join `product_variant` pv ON oi.product_variant_id=pv.id join products p ON pv.product_id=p.id join unit u on pv.measurement_unit_id=u.id JOIN category c ON p.category_id=c.id WHERE oi.date_added > DATE_SUB(NOW(), INTERVAL 1 MONTH) AND oi.active_status='delivered' GROUP BY p.category_id ORDER BY total desc LIMIT 0, 5";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res;
        } else {
            return false;
        }
    }

    public function set_timezone($config)
    {
        $result = false;
        if (isset($config['system_timezone']) && isset($config['system_timezone_gmt']) && $config['system_timezone_gmt'] != "" && $config['system_timezone'] != "") {
            date_default_timezone_set($config['system_timezone']);
            $this->db->sql("SET `time_zone` = '" . $config['system_timezone_gmt'] . "'");
            $result = true;
        } else {
            date_default_timezone_set('Asia/Kolkata');
            $this->db->sql("SET `time_zone` = '+05:30'");
            $result = true;
        }
        return $result;
    }

    public function delete_other_images($pid, $i, $seller_id = "0")
    {
        if ($seller_id > 0) {
            $sql = "SELECT other_images FROM products WHERE id = $pid and seller_id = $seller_id";
            $this->db->sql($sql);
            $res = $this->db->getResult();
            if (empty($res)) {
                return 2;
            }
        }
        $sql = "SELECT other_images FROM products WHERE id =" . $pid;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        foreach ($res as $row)
            $other_images = $row['other_images']; /*get other images json array*/
        $other_images = json_decode($other_images); /*decode from json to array*/
        if ($seller_id > 0) {
            unlink("../../" . $other_images[$i]); /*remove the image from the folder*/
        } else {
            unlink("../" . $other_images[$i]); /*remove the image from the folder*/
        }
        unset($other_images[$i]); /*remove image from the array*/
        $other_images = json_encode(array_values($other_images)); /*convert back to JSON */

        /*update the table*/
        $sql = "UPDATE `products` set `other_images`='" . $other_images . "' where id=" . $pid;
        if ($this->db->sql($sql))
            return 1;
        else
            return 0;
    }

    public function delete_variant($v_id)
    {
        $sql = "SELECT id FROM product_variant WHERE id=" . $v_id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            $sql = "DELETE FROM product_variant WHERE id=" . $v_id;
            if ($this->db->sql($sql)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function get_seller_address($seller_id)
    {
        $res_seller = $this->get_data($columns = ['name', 'mobile', 'latitude', 'longitude', 'state', 'street', 'pincode_id', 'city_id'], "id=" . $seller_id, 'seller');

        $res_pincode = $this->get_data($columns = ['pincode'], "id=" . $res_seller[0]['pincode_id'], 'pincodes');
        $res_city = $this->get_data($columns = ['name'], "id=" . $res_seller[0]['city_id'], 'cities');
        $city_name = (!empty($res_city[0]['name'])) ? $res_city[0]['name'] . " - " : "";
        $state = (!empty($res_seller[0]['state'])) ? $res_seller[0]['state'] . ", " : "";
        $street = (!empty($res_seller[0]['street'])) ? $res_seller[0]['street'] . ", " : "";
        $pincode = (!empty($res_seller[0]['pincode_id'])) ? $city_name . $res_pincode[0]['pincode'] : "";
        $seller_address = $state  . $street . $pincode;
        if (!empty($seller_address)) {
            return $seller_address;
        } else {
            return false;
        }
    }
}
// $this->db->disconnect();
