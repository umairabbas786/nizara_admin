<?php
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Access-Control-Allow-Origin: *');


include('../../includes/crud.php');
include('../../includes/custom-functions.php');
include('verify-token.php');
$fn = new custom_functions();
$function = new functions;
$db = new Database();
$db->connect();

$config = $fn->get_configurations();
$low_stock_limit = isset($config['low-stock-limit']) && (!empty($config['low-stock-limit'])) ? $config['low-stock-limit'] : 0;

$time_slot_config = $fn->time_slot_config();
if (isset($config['system_timezone']) && isset($config['system_timezone_gmt'])) {
    date_default_timezone_set($config['system_timezone']);
    $db->sql("SET `time_zone` = '" . $config['system_timezone_gmt'] . "'");
} else {
    date_default_timezone_set('Asia/Kolkata');
    $db->sql("SET `time_zone` = '+05:30'");
}
include('../../includes/variables.php');

/* 
-------------------------------------------
APIs for Seller
-------------------------------------------
1. login
2. get_categories
3. get_subcategories
4. get_products
5. get_financial_statistics
6. update_seller_fcm_id
7. get_seller_transactions
8. get_orders
9. update_order_status
10. add_products
11. update_products
12. delete_products
13. get_seller_by_id
14. get_taxes
15. get_units
16. get_pincodes
17. delete_other_images
18. delete_variant
20. send_request
21. get_requests
22. update_seller_profile

-------------------------------------------

-------------------------------------------

*/

// if (!verify_token()) {
//     return false;
// }


if (!isset($_POST['accesskey'])  || trim($_POST['accesskey']) != $access_key) {
    $response['error'] = true;
    $response['message'] = "No Accsess key found!";
    print_r(json_encode($response));
    return false;
    exit();
}
/* 
---------------------------------------------------------------------------------------------------------
*/

/*
1.login
    accesskey:90336
    login:1
    mobile:9876543210
    password:12345678
    fcm_id:YOUR_FCM_ID  // {optional}
*/
if (isset($_POST['login']) && !empty($_POST['login']) == 1) {

    if (empty($_POST['mobile'])) {
        $response['error'] = true;
        $response['message'] = "Mobile should be filled!";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['password'])) {
        $response['error'] = true;
        $response['message'] = "Password should be filled!";
        print_r(json_encode($response));
        return false;
    }

    $mobile = $db->escapeString($fn->xss_clean($_POST['mobile']));
    $password = md5($db->escapeString($fn->xss_clean($_POST['password'])));

    $sql = "SELECT * FROM seller WHERE mobile = '$mobile' AND password = '$password'";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    $rows = $tempRow = array();
    if ($num == 1) {
        if ($res[0]['status'] == 7) {
            $response['error'] = true;
            $response['message'] = "It seems your acount was removed by super admin please contact him to restore the account!";
        } else if ($res[0]['status'] == 2) {
            $response['error'] = true;
            $response['message'] = "Your account is not approved by Super Admin. Please wait for approval!";
        } else {
            $user_id = $res[0]['id'];
            $fcm_id = (isset($_POST['fcm_id']) && !empty($_POST['fcm_id'])) ? $db->escapeString($fn->xss_clean($_POST['fcm_id'])) : "";
            if (!empty($fcm_id)) {
                $sql1 = "update seller set `fcm_id` ='$fcm_id' where id = '" . $user_id . "'";
                $db->sql($sql1);
                $db->sql($sql);
                $res = $db->getResult();
            }
            $res[0]['fcm_id'] = !empty($res[0]['fcm_id'])  ? $res[0]['fcm_id'] : "";
            $res[0]['national_identity_card'] = !empty($res[0]['national_identity_card'])  ?  DOMAIN_URL . 'upload/seller/' . $res[0]['national_identity_card'] : "";
            $res[0]['address_proof'] = !empty($res[0]['address_proof']) ?  DOMAIN_URL . 'upload/seller/' . $res[0]['address_proof'] : "";
            $res[0]['logo'] = (!empty($res[0]['logo'])) ? DOMAIN_URL . 'upload/seller/' . $res[0]['logo'] : "";

            $response['error'] = false;
            $response['message'] = "Login Successfully";
            $response['data'] = $res;
        }
    } else {
        if ($res[0]['mobile'] != $mobile) {
            $response['error'] = true;
            $response['message'] = "Phone Number is not registered!";
        }
        if ($res[0]['mobile'] != $mobile && $res[0]['password'] != $password) {
            $response['error'] = true;
            $response['message'] = "Invalid number or password, Try again.";
        }
    }
    print_r(json_encode($response));
    return false;
}

/* 
---------------------------------------------------------------------------------------------------------
*/

/*
2.get_categories
    accesskey:90336
    seller_id:9
    get_categories: 1
    offset:0           // {optional}
    limit:10           // {optional}
    sort:id            // {optional}
    order:asc/desc     // {optional}    
    search:Beverages   // {optional}
*/
if (isset($_POST['get_categories']) && !empty($_POST['get_categories'] == 1)) {

    if (empty($_POST['seller_id'])) {
        $response['error'] = true;
        $response['message'] = "Seller id can not be empty!";
        print_r(json_encode($response));
        return false;
    }

    $offset = (isset($_POST['offset']) && !empty($fn->xss_clean($_POST['offset'])) && is_numeric($_POST['offset'])) ? $db->escapeString($fn->xss_clean($_POST['offset'])) : 0;
    $limit = (isset($_POST['limit']) && !empty($fn->xss_clean($_POST['limit'])) && is_numeric($_POST['limit'])) ? $db->escapeString($fn->xss_clean($_POST['limit'])) : 10;
    $sort = (isset($_POST['sort']) && !empty($fn->xss_clean($_POST['sort']))) ? $db->escapeString($fn->xss_clean($_POST['sort'])) : 'row_order';
    $order = (isset($_POST['order']) && !empty($fn->xss_clean($_POST['order']))) ? $db->escapeString($fn->xss_clean($_POST['order'])) : 'ASC';
    $seller_id = (isset($_POST['seller_id']) && !empty($fn->xss_clean($_POST['seller_id'])) && is_numeric($_POST['seller_id'])) ? $db->escapeString($fn->xss_clean($_POST['seller_id'])) : "";

    $where = "";
    if (isset($_POST['search']) && !empty($_POST['search'])) {
        $search = $db->escapeString($fn->xss_clean($_POST['search']));
        $where .= "AND `id` like '%" . $search . "%' OR `name` like '%" . $search . "%' OR `subtitle` like '%" . $search . "%'";
    }
    $sql_query = "SELECT categories FROM seller where id=$seller_id ";
    $db->sql($sql_query);
    $res1 = $db->getResult();
    $category_ids = $res1[0]['categories'];

    $sql = "SELECT count(id) as total FROM category where id IN($category_ids)" . $where .  "ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . ", " . $limit;
    $db->sql($sql);
    $total = $db->getResult();

    $sql_query = "SELECT * FROM category where id IN($category_ids)" . $where .  "ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . ", " . $limit;
    $db->sql($sql_query);
    $res = $db->getResult();
    foreach ($total as $row) {
        $total = $row['total'];
    }
    if (!empty($res)) {
        for ($i = 0; $i < count($res); $i++) {
            $res[$i]['image'] = (!empty($res[$i]['image'])) ? DOMAIN_URL  . $res[$i]['image'] : "";
            $res[$i]['web_image'] = (!empty($res[$i]['web_image'])) ? DOMAIN_URL . $res[$i]['web_image'] : "";
            $tmp = [];
        }
        foreach ($res as $r) {
            $r['childs'] = [];
            $db->sql("SELECT * FROM subcategory WHERE category_id = '" . $r['id'] . "' ORDER BY id DESC");
            $childs = $db->getResult();
            $temp = array('id' => "0", 'category_id' => "0", 'name' => "Select SubCategory", 'slug' => "", 'subtitle' => "", 'image' => "");
            if (!empty($childs)) {
                for ($i = 0; $i < count($childs); $i++) {
                    $childs[$i]['image'] = (!empty($childs[$i]['image'])) ? DOMAIN_URL  . $childs[$i]['image'] : '';
                    $r['childs'][$i] = (array)$childs[$i];
                }
                array_unshift($r['childs'], $temp);
            }
            $tmp[] = $r;
        }
        $res = $tmp;
        $response['error'] = false;
        $response['message'] = "Categories retrived successfully";
        $response['total'] = $total;
        $response['data'] = $res;
    } else {
        $response['error'] = true;
        $response['message'] = "No data found!";
    }
    print_r(json_encode($response));
    return false;
}

/* 
---------------------------------------------------------------------------------------------------------
*/

/*
3.get_subcategories
    accesskey:90336
    seller_id:1
    get_subcategories:1
    category_id:29      // {optional}
    subcategory_id:114  // {optional}
    offset:0            // {optional}
    limit:10            // {optional}
    sort:id             // {optional}
    order:asc/desc      // {optional}
*/
if (isset($_POST['get_subcategories']) && !empty($_POST['get_subcategories'] == 1)) {
    if (empty($_POST['seller_id'])) {
        $response['error'] = true;
        $response['message'] = "Seller ID should be filled!";
        print_r(json_encode($response));
        return false;
    }

    $offset = (isset($_POST['offset']) && !empty($fn->xss_clean($_POST['offset'])) && is_numeric($_POST['offset'])) ? $db->escapeString($fn->xss_clean($_POST['offset'])) : 0;
    $limit = (isset($_POST['limit']) && !empty($fn->xss_clean($_POST['limit'])) && is_numeric($_POST['limit'])) ? $db->escapeString($fn->xss_clean($_POST['limit'])) : 10;
    $sort = (isset($_POST['sort']) && !empty($fn->xss_clean($_POST['sort']))) ? $db->escapeString($fn->xss_clean($_POST['sort'])) : 'id';
    $order = (isset($_POST['order']) && !empty($fn->xss_clean($_POST['order']))) ? $db->escapeString($fn->xss_clean($_POST['order'])) : 'DESC';
    $seller_id = (isset($_POST['seller_id']) && !empty($fn->xss_clean($_POST['seller_id'])) && is_numeric($_POST['seller_id'])) ? $db->escapeString($fn->xss_clean($_POST['seller_id'])) : "";
    $category_id = (isset($_POST['category_id']) && !empty($_POST['category_id'])) ? $db->escapeString($fn->xss_clean($_POST['category_id'])) : "";
    $subcategory_id = (isset($_POST['subcategory_id'])) && !empty($_POST['subcategory_id']) ? $db->escapeString($fn->xss_clean($_POST['subcategory_id'])) : "";

    $where = "";
    if (!empty($_POST['category_id'])) {
        $where .= " AND c.id=" . $category_id;
    }
    if (!empty($_POST['subcategory_id'])) {
        $where .= " AND sc.id=" . $subcategory_id;
    }
    $sql = "SELECT count(sc.id) as total FROM `subcategory` sc left join category c on sc.category_id=c.id  
    left JOIN seller s on FIND_IN_SET(c.id, s.categories) > 0 WHERE s.id = $seller_id " . $where;
    $db->sql($sql);
    $total = $db->getResult();

    $sql = "SELECT sc.*,c.name as category_name FROM `subcategory` sc left join category c on sc.category_id=c.id  
    left JOIN seller s on FIND_IN_SET(c.id, s.categories) > 0 WHERE s.id = $seller_id " . $where . " ORDER BY sc.$sort $order LIMIT $offset,$limit ";
    $db->sql($sql);
    $res1 = $db->getResult();

    if (!empty($res1)) {
        for ($i = 0; $i < count($res1); $i++) {
            $res1[$i]['image'] = (!empty($res1[$i]['image'])) ? DOMAIN_URL . '' . $res1[$i]['image'] : '';
        }
        $response['error'] = false;
        $response['message'] = "Sub Categories retrieved successfully";
        $response['total'] = $total[0]['total'];
        $response['data'] = $res1;
    } else {
        $response['error'] = true;
        $response['message'] = "No data found!";
    }
    print_r(json_encode($response));
    return false;
}

/* 
---------------------------------------------------------------------------------------------------------
*/

/*
4.get_products
    accesskey:90336
    get_products:1
    seller_id:1
    filter:low_stock | out_stock // {optional}
    product_id:119      // {optional}
    category_id:119     // {optional}
    subcategory_id:119  // {optional}
    limit:10            // {optional}
    offset:0            // {optional}
    search:value        // {optional}
    slug:popcorn-3      // {optional}
    sort:new / old / high / low  // {optional}
*/
if (isset($_POST['get_products']) && !empty($_POST['get_products'] == 1)) {
    if (empty($_POST['seller_id'])) {
        $response['error'] = true;
        $response['message'] = "Seller ID should be filled!";
        print_r(json_encode($response));
        return false;
    }
    $offset = (isset($_POST['offset']) && !empty($fn->xss_clean($_POST['offset'])) && is_numeric($_POST['offset'])) ? $db->escapeString($fn->xss_clean($_POST['offset'])) : 0;
    $limit = (isset($_POST['limit']) && !empty($fn->xss_clean($_POST['limit'])) && is_numeric($_POST['limit'])) ? $db->escapeString($fn->xss_clean($_POST['limit'])) : 10;

    $sort = (isset($_POST['sort']) && !empty($fn->xss_clean($_POST['sort']))) ? $db->escapeString($fn->xss_clean($_POST['sort'])) : 'new';

    $seller_id = (isset($_POST['seller_id']) && !empty($fn->xss_clean($_POST['seller_id'])) && is_numeric($_POST['seller_id'])) ? $db->escapeString($fn->xss_clean($_POST['seller_id'])) : "";
    $category_id = (isset($_POST['category_id']) && !empty($_POST['category_id'])) ? $db->escapeString($fn->xss_clean($_POST['category_id'])) : "";
    $filter = (isset($_POST['filter']) && !empty($_POST['filter'])) ? $db->escapeString($fn->xss_clean($_POST['filter'])) : '';
    $product_id = (isset($_POST['product_id']) && !empty($_POST['product_id'])) ? $db->escapeString($fn->xss_clean($_POST['product_id'])) : "";
    $subcategory_id = (isset($_POST['subcategory_id']) && $_POST['subcategory_id'] != "") ? $db->escapeString($fn->xss_clean($_POST['subcategory_id'])) : "0";

    $where = "";
    if ($sort == 'new') {
        $sort = 'date_added DESC';
        $price = 'MIN(price)';
        $price_sort = ' pv.price ASC';
    } elseif ($sort == 'old') {
        $sort = 'date_added ASC';
        $price = 'MIN(price)';
        $price_sort = ' pv.price ASC';
    } elseif ($sort == 'high') {
        $sort = ' price DESC';
        $price = 'MAX(price)';
        $price_sort = ' pv.price DESC';
    } elseif ($sort == 'low') {
        $sort = ' price ASC';
        $price = 'MIN(price)';
        $price_sort = ' pv.price ASC';
    } else {
        $sort = ' p.row_order ASC';
        $price = 'MIN(price)';
        $price_sort = ' pv.price ASC';
    }

    $join = $out_stock = "";
    if ($filter == "out_stock") {
        $join = " join product_variant pv ON pv.product_id=p.id ";
        $where .= " AND pv.serve_for = 'Sold Out'";
        $out_stock = " AND pv.serve_for = 'Sold Out'";
    }
    if ($filter == "low_stock") {
        $join = " join product_variant pv ON pv.product_id=p.id ";
        $where .=  " AND pv.stock < $low_stock_limit AND pv.serve_for = 'Available'";
    }

    if (isset($_POST['product_id']) && !empty($_POST['product_id']) && is_numeric($_POST['product_id'])) {
        $where .= " AND p.`id` = " . $product_id;
    }

    if (isset($_POST['category_id']) && !empty($_POST['category_id']) && is_numeric($_POST['category_id'])) {
        $where .=  " AND p.`category_id`=" . $category_id;
    }
    if (isset($_POST['subcategory_id']) && $_POST['subcategory_id'] != "" && is_numeric($_POST['subcategory_id'])) {
        $where .= " AND p.`subcategory_id`=" . $subcategory_id;
    }

    if (isset($_POST['slug']) && !empty($_POST['slug'])) {
        $slug = $db->escapeString($fn->xss_clean($_POST['slug']));
        $where .= " AND p.`slug` =  '$slug' ";
    }
    if (isset($_POST['search']) && !empty($_POST['search'])) {
        $search = $db->escapeString($fn->xss_clean($_POST['search']));
        $where .= " AND (p.`id` like '%" . $search . "%' OR p.`name` like '%" . $search . "%' OR p.`category_id` like '%" . $search . "%' OR p.`subcategory_id` like '%" . $search . "%' OR p.`slug` like '%" . $search . "%' OR p.`description` like '%" . $search . "%')";
    }
    $sql = "SELECT count(p.id) as total FROM products p JOIN seller s ON s.id=p.seller_id $join where p.seller_id = $seller_id  AND s.status = 1" . $where;
    $db->sql($sql);
    $total = $db->getResult();

    $sql = "SELECT p.*,s.name as seller_name ,s.status as seller_status,(SELECT " . $price . " FROM product_variant pv WHERE pv.product_id=p.id) as price FROM products p JOIN seller s ON s.id=p.seller_id $join where p.seller_id=$seller_id AND s.status = 1 $where ORDER BY $sort LIMIT $offset,$limit ";
    $db->sql($sql);
    $res = $db->getResult();
    $product = array();
    $i = 0;

    foreach ($res as $row) {
        $sql = "SELECT *,(SELECT short_code FROM unit u WHERE u.id=pv.measurement_unit_id) as measurement_unit_name,(SELECT short_code FROM unit u WHERE u.id=pv.stock_unit_id) as stock_unit_name FROM product_variant pv WHERE pv.product_id=" . $row['id'] . " $out_stock";
        $db->sql($sql);
        $variants = $db->getResult();
        if (empty($variants)) {
            continue;
        }
        $row['other_images'] = json_decode($row['other_images'], 1);
        $row['other_images'] = empty($row['other_images']) ? array() : $row['other_images'];
        for ($j = 0; $j < count($row['other_images']); $j++) {
            $row['other_images'][$j] = DOMAIN_URL . $row['other_images'][$j];
        }
        $row['image'] = DOMAIN_URL . $row['image'];
        if ($row['tax_id'] == 0) {
            $row['tax_title'] = "";
            $row['tax_percentage'] = "0";
        } else {
            $t_id = $row['tax_id'];
            $sql_tax = "select * from taxes where id= $t_id";
            $db->sql($sql_tax);
            $res_tax1 = $db->getresult();
            foreach ($res_tax1 as $tax1) {
                $row['tax_title'] = (!empty($tax1['title'])) ? $tax1['title'] : "";
                $row['tax_percentage'] =  (!empty($tax1['percentage'])) ? $tax1['percentage'] : "0";
            }
        }
        // [ 0=included, 1=excluded, 2=all ]
        if ($row['type'] == 'excluded') {
            $row['delivery_places'] = "1";
        } else  if ($row['type'] == 'included') {
            $row['delivery_places'] = "0";
        } else  if ($row['type'] == 'all') {
            $row['delivery_places'] = "2";
        } else {
            $row['delivery_places'] = "";
        }
        $row['type'] = $variants[0]['type'];

        $product[$i] = $row;
        for ($k = 0; $k < count($variants); $k++) {
            // if ($variants[$k]['stock'] <= 0 && $variants[$k]['serve_for'] = 'Sold Out') {
            //     $variants[$k]['serve_for'] = 'Sold Out';
            // } else {
            //     $variants[$k]['serve_for'] = 'Available';
            // }
            if ($variants[$k]['stock'] <= 0) {
                $variants[$k]['serve_for'] = 'Sold Out';
            } else {
                $variants[$k]['serve_for'] = $variants[$k]['serve_for'];
            }
        }
        $product[$i]['variants'] = $variants;
        $i++;
    }
    if (!empty($product)) {
        $response['error'] = false;
        $response['message'] = "Products retrieved successfully";
        $response['total'] = $total[0]['total'];
        $response['data'] = $product;
    } else {
        $response['error'] = true;
        $response['message'] = "No data found!";
    }
    print_r(json_encode($response));
    return false;
}

/* 
---------------------------------------------------------------------------------------------------------
*/

/*
5.get_financial_statistics
    accesskey:90336
    get_financial_statistics:1
    seller_id:1
*/
if (isset($_POST['get_financial_statistics']) && !empty($_POST['get_financial_statistics'] == 1)) {
    if (empty($_POST['seller_id'])) {
        $response['error'] = true;
        $response['message'] = "Seller ID should be filled!";
        print_r(json_encode($response));
        return false;
    }

    $seller_id = (isset($_POST['seller_id']) && !empty($fn->xss_clean($_POST['seller_id'])) && is_numeric($_POST['seller_id'])) ? $db->escapeString($fn->xss_clean($_POST['seller_id'])) : "";
    $low_stock_limit = isset($config['low-stock-limit']) && (!empty($config['low-stock-limit'])) ? $config['low-stock-limit'] : 0;

    $total_orders = $total_products = $total_sold_out_products = $total_low_stock_count = 0;

    $sql = "SELECT categories FROM seller WHERE id = " . $seller_id;
    $db->sql($sql);
    $res = $db->getResult();
    $category_ids = explode(',', $res[0]['categories']);
    $category_id = implode(',', $category_ids);

    $total_orders = $fn->rows_count('order_items', 'distinct(order_id)', "seller_id = $seller_id");

    $total_products = $fn->rows_count('products', '*', "seller_id= $seller_id AND category_id IN($category_id)");
    $total_sold_out_products = $fn->sold_out_count1($seller_id);
    $total_low_stock_count = $fn->low_stock_count1($seller_id, $low_stock_limit);

    $year = date("Y");
    $curdate = date('Y-m-d');

    //    echo $sql = "SELECT DATE(date_added) AS order_date,(SELECT SUM(sub_total) FROM `order_items` WHERE seller_id=$seller_id and active_status='delivered') AS total_sale  FROM orders WHERE YEAR(date_added) = $year AND DATE(date_added) < $curdate 
    //     AND `active_status`='delivered' GROUP BY DATE(date_added) ORDER BY DATE(date_added) DESC ";
    $sql1 = "SELECT SUM(sub_total) AS total_sale,DATE(date_added) AS order_date FROM order_items WHERE YEAR(date_added) = '$year' AND DATE(date_added)<='$curdate' AND seller_id=$seller_id  and active_status = 'delivered' GROUP BY DATE(date_added) ORDER BY DATE(date_added) DESC  LIMIT 0,7";
    // echo $sql1;
    $db->sql($sql1);
    $result_order = $db->getResult();
    $total_sales = array_column($result_order, "total_sale");

    $response['error'] = false;
    $response['total_orders'] = $total_orders;
    $response['total_products'] = $total_products;
    $response['total_sold_out_products'] = $total_sold_out_products;
    $response['total_low_stock_count'] = $total_low_stock_count;
    $response['balance'] = $fn->get_seller_balance($seller_id);
    $response['currency'] = $fn->get_settings('currency');
    $response['total_sale'] = (!empty($result_order)) ? strval(array_sum($total_sales)) : "0";

    print_r(json_encode($response));
}
/* 
---------------------------------------------------------------------------------------------------------
*/

/*
6.update_seller_fcm_id
    accesskey:90336
    update_seller_fcm_id:1
    seller_id:1  
    fcm_id:YOUR_FCM_ID
*/
if (isset($_POST['update_seller_fcm_id']) && !empty($_POST['update_seller_fcm_id'] == 1)) {
    if (empty($_POST['fcm_id']) || empty($_POST['seller_id'])) {
        $response['error'] = true;
        $response['message'] = "Please pass all fields.";
        print_r(json_encode($response));
        return false;
    }
    $seller_id = $db->escapeString($fn->xss_clean($_POST['seller_id']));
    $fcm_id = $db->escapeString($fn->xss_clean($_POST['fcm_id']));

    if (isset($_POST['fcm_id']) && !empty($_POST['fcm_id'])) {
        $sql = "update seller set `fcm_id` ='$fcm_id' where id = '$seller_id' ";
        if ($db->sql($sql)) {
            $response['error'] = false;
            $response['mesage'] = "Seller fcm_id updated succesfully";
        } else {
            $response['error'] = true;
            $response['message'] = "Can not update fcm_id of Seller";
        }
        print_r(json_encode($response));
    }
}
/* 
---------------------------------------------------------------------------------------------------------
*/

/*
7.get_seller_transactions
    accesskey:90336
    get_seller_transactions:1
    seller_id:1
    offset:0            // {optional}
    limit:10            // {optional}
    sort:id             // {optional}
    order:DESC / ASC    // {optional}
*/
if (isset($_POST['get_seller_transactions']) && !empty($_POST['get_seller_transactions'] == 1)) {
    if (empty($_POST['seller_id'])) {
        $response['error'] = true;
        $response['message'] = "Seller ID should be filled!";
        print_r(json_encode($response));
        return false;
    }
    $offset = (isset($_POST['offset']) && !empty($fn->xss_clean($_POST['offset'])) && is_numeric($_POST['offset'])) ? $db->escapeString($fn->xss_clean($_POST['offset'])) : 0;
    $limit = (isset($_POST['limit']) && !empty($fn->xss_clean($_POST['limit'])) && is_numeric($_POST['limit'])) ? $db->escapeString($fn->xss_clean($_POST['limit'])) : 10;
    $sort = (isset($_POST['sort']) && !empty($fn->xss_clean($_POST['sort']))) ? $db->escapeString($fn->xss_clean($_POST['sort'])) : 'id';
    $order = (isset($_POST['order']) && !empty($fn->xss_clean($_POST['order']))) ? $db->escapeString($fn->xss_clean($_POST['order'])) : 'DESC';

    $seller_id = $db->escapeString($fn->xss_clean($_POST['seller_id']));

    if (!empty($seller_id)) {
        $sql = "SELECT count(id) as total from seller_wallet_transactions where seller_id=" . $seller_id;
        $db->sql($sql);
        $res = $db->getResult();
        $total = $res[0]['total'];

        $sql = "SELECT * FROM `seller_wallet_transactions` where seller_id= $seller_id ORDER BY $sort $order LIMIT $offset , $limit";
        $db->sql($sql);
        $res = $db->getResult();

        $response['error'] = false;
        $response['message'] = "Transcations retrived successfully";
        $response['total'] = $total;
        for ($i = 0; $i < count($res); $i++) {
            $res[$i]['order_id'] = !empty($res[$i]['order_id']) ? $res[$i]['order_id'] : "";
            $res[$i]['order_item_id'] = !empty($res[$i]['order_item_id']) ? $res[$i]['order_item_id'] : "";
        }
        $response['data'] = array_values($res);
    } else {
        $response['error'] = true;
        $response['message'] = "No data found!";
    }
    print_r(json_encode($response));
}
/* 
---------------------------------------------------------------------------------------------------------
*/

/* 
8. get_orders
    accesskey:90336
    get_orders:1
    seller_id:1
    order_id:12608          // {optional}
    start_date:2020-06-05   // {optional} {YYYY-mm-dd}
    end_date:2020-06-05     // {optional} {YYYY-mm-dd}
    limit:10                // {optional}
    offset:0                // {optional}
    filter_order:received | processed | shipped | delivered | cancelled | returned | awaiting_payment    // {optional}
*/
if (isset($_POST['get_orders']) && !empty($_POST['get_orders'])) {
    if (empty($_POST['seller_id'])) {
        $response['error'] = true;
        $response['message'] = "Seller ID should be filled!";
        print_r(json_encode($response));
        return false;
    }
    $where = '';
    $seller_id = $db->escapeString($fn->xss_clean($_POST['seller_id']));
    $order_id = (isset($_POST['order_id']) && !empty($_POST['order_id']) && is_numeric($_POST['order_id'])) ? $db->escapeString($fn->xss_clean($_POST['order_id'])) : "";
    $limit = (isset($_POST['limit']) && !empty($_POST['limit']) && is_numeric($_POST['limit'])) ? $db->escapeString($fn->xss_clean($_POST['limit'])) : 10;
    $offset = (isset($_POST['offset']) && !empty($_POST['offset']) && is_numeric($_POST['offset'])) ? $db->escapeString($fn->xss_clean($_POST['offset'])) : 0;

    if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
        $where .= " AND oi.order_id= $order_id";
    }
    if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
        $start_date = $db->escapeString($fn->xss_clean($_POST['start_date']));
        $end_date = $db->escapeString($fn->xss_clean($_POST['end_date']));
        $where .= " AND DATE(oi.date_added)>=DATE('" . $start_date . "') AND DATE(oi.date_added)<=DATE('" . $end_date . "')";
    }
    if (isset($_POST['filter_order']) && $_POST['filter_order'] != '') {
        $filter_order = $db->escapeString($fn->xss_clean($_POST['filter_order']));
        $where .= " AND oi.`active_status`='" . $filter_order . "'";
    }

    $sql = "select count(oi.id) as total from order_items oi where seller_id=" . $seller_id . $where;
    $db->sql($sql);
    $res = $db->getResult();
    $total = $res[0]['total'];
    $sql = "select DISTINCT o.id,oi.seller_id, o.*,(select name from users u where u.id=o.user_id) as user_name from orders o JOIN order_items oi ON o.id=oi.order_id where oi.seller_id=" . $seller_id . $where . " ORDER BY date_added DESC LIMIT $offset,$limit";
    $db->sql($sql);
    $res = $db->getResult();
    $i = 0;
    $j = 0;
    foreach ($res as $row) {
        $final_sub_total = 0;
        // print_r($row);
        if ($row['discount'] > 0) {
            $discounted_amount = $row['total'] * $row['discount'] / 100;
            $final_total = $row['total'] - $discounted_amount;
            $discount_in_rupees = $row['total'] - $final_total;
        } else {
            $discount_in_rupees = 0;
        }

        $res[$i]['discounted_price'] = strval($discount_in_rupees);
        $final_total = ceil($res[$i]['final_total']);
        $res_seller = $fn->get_data($columns = ['name', 'mobile', 'latitude', 'longitude', 'state', 'street', 'pincode_id', 'city_id'], "id=" . $res[$i]['seller_id'], 'seller');
        $res_pincode = $fn->get_data($columns = ['pincode'], "id=" . $res_seller[0]['pincode_id'], 'pincodes');
        $res_city = $fn->get_data($columns = ['name'], "id=" . $res_seller[0]['city_id'], 'cities');
        $city = isset($res_city[0]['name']) && !empty($res_city[0]['name']) ? $res_city[0]['name'] . ' - ' : '';
        $state = (!empty($res_seller[0]['state'])) ? $res_seller[0]['state'] . ", " : "";
        $street = (!empty($res_seller[0]['street'])) ? $res_seller[0]['street'] . ", " : "";
        $pincode = (!empty($res_seller[0]['pincode_id'])) ? $city . $res_pincode[0]['pincode'] : "";
        $seller_address = $state  . $street . $pincode;
        $res[$i]['final_total'] = strval($final_total + $res[$i]['delivery_charge']);

        $res[$i]['date_added'] = date('d-m-Y h:i:sa', strtotime($res[$i]['date_added']));
        $sql = "select oi.*,v.id as variant_id, p.name,p.image,p.manufacturer,p.made_in,p.return_status,p.cancelable_status,p.till_status,v.measurement,(select short_code from unit u where u.id=v.measurement_unit_id) as unit from order_items oi join product_variant v on oi.product_variant_id=v.id join products p on p.id=v.product_id where oi.order_id=" . $row['id'] . " AND oi.seller_id=$seller_id";
        $db->sql($sql);
        $res[$i]['items'] = $db->getResult();
        unset($res[$i]['status']);
        unset($res[$i]['active_status']);
        for ($j = 0; $j < count($res[$i]['items']); $j++) {
            unset($res[$i]['items'][$j]['status']);
            $final_sub_total += $res[$i]['items'][$j]['sub_total'];

            if (!empty($res[$i]['items'][$j]['seller_id'])) {
                $seller_info = $fn->get_data($columns = ['name', 'store_name'], "id=" . $res[$i]['items'][$j]['seller_id'], 'seller');
                $res[$i]['items'][$j]['seller_name'] = $seller_info[0]['name'];
                $res[$i]['items'][$j]['seller_store_name'] = $seller_info[0]['store_name'];
            } else {
                $res[$i]['items'][$j]['seller_id'] = "";
                $res[$i]['items'][$j]['seller_name'] = "";
                $res[$i]['items'][$j]['seller_store_name'] = "";
            }
            $res[$i]['items'][$j]['seller_id'] = $res[$i]['seller_id'];

            $res[$i]['items'][$j]['seller_address'] = $seller_address;
            $res[$i]['items'][$j]['seller_name'] = !empty($res_seller[0]['name']) ? $res_seller[0]['name'] : '';
            $res[$i]['items'][$j]['seller_mobile'] = $res_seller[0]['mobile'];
            $res[$i]['items'][$j]['seller_address'] = $seller_address;
            $res[$i]['items'][$j]['seller_latitude'] = !empty($res_seller[0]['latitude']) ? $res_seller[0]['latitude'] : '0';
            $res[$i]['items'][$j]['seller_longitude'] = !empty($res_seller[0]['longitude']) ? $res_seller[0]['longitude'] : '0';
            if (!empty($res[$i]['items'][$j]['delivery_boy_id'])) {
                $delvery_boy_info = $fn->get_data($columns = ['name'], "id=" . $res[$i]['items'][$j]['delivery_boy_id'], 'delivery_boys');
                $res[$i]['items'][$j]['delivery_boy_name'] = $delvery_boy_info[0]['name'];
            } else {
                $res[$i]['items'][$j]['delivery_boy_name'] = "";
            }
            $item_details = $fn->get_product_by_variant_id2($res[$i]['items'][$j]['product_variant_id']);
            $res[$i]['items'][$j]['return_days'] = ($item_details['return_days'] != "") ? $item_details['return_days'] : '0';

            $res[$i]['items'][$j]['image'] = DOMAIN_URL . $res[$i]['items'][$j]['image'];
        }
        $res[$i]['final_total'] = strval($final_sub_total + $res[$i]['delivery_charge']);
        $res[$i]['total'] = strval($final_sub_total);
        $i++;
    }
    $orders = $order = array();

    if (!empty($res)) {
        $orders['error'] = false;
        $orders['total'] = $total;
        $orders['data'] = array_values($res);
        print_r(json_encode($orders));
    } else {
        $res['error'] = true;
        $res['message'] = "No orders found!";
        print_r(json_encode($res));
        return false;
    }
}

/* 
---------------------------------------------------------------------------------------------------------
*/

/*
9.update_order_status
    accesskey:90336
    update_order_status:1
    order_id:169
    seller_id:1
    order_item_id:12577
    delivery_boy_id:12577
    status:received | processed | shipped | delivered | cancelled | returned
*/
if (isset($_POST['update_order_status']) && !empty($_POST['update_order_status'] == 1)) {

    if (empty($_POST['order_id']) || empty($_POST['seller_id']) || empty($_POST['order_item_id'])) {
        $response['error'] = true;
        $response['message'] = "Please pass all mandatory fields!";
        print_r(json_encode($response));
        return false;
    }
    $id = $db->escapeString(trim($fn->xss_clean($_POST['order_id'])));
    $postStatus = isset($_POST['status']) && !empty($_POST['status']) ? $db->escapeString(trim($fn->xss_clean(($_POST['status'])))) : '';
    $seller_id = $db->escapeString(trim($fn->xss_clean(($_POST['seller_id']))));
    $order_item_id = $db->escapeString(trim($fn->xss_clean(($_POST['order_item_id']))));
    $delivery_boy_id = isset($_POST['delivery_boy_id']) && !empty($_POST['delivery_boy_id']) ? $db->escapeString(trim($fn->xss_clean(($_POST['delivery_boy_id'])))) : 0;

    $response = $fn->update_order_status($id, $order_item_id, $postStatus, $delivery_boy_id);
    print_r($response);
}

/* 
---------------------------------------------------------------------------------------------------------
*/

/* 10.add_products
    accesskey:90336
    add_products:1
    seller_id:1
    name:chocolate-boxes            
    category_id:31 
    description:chocolates
    till_status: received 
    delivery_places:0 OR 1 OR 2 [ 0=included, 1=excluded, 2=all ]
    pincodes:1,4,5                 //{must blank when delivery_places=2}
    indicator:0 
    subcategory_id:115          // {optional}
    return_days:7 {optional}
    tax_id:4                    // {optional}
    manufacturer:india          // {optional}
    made_in:india               // {optional}
    return_status:0 / 1         // {optional}
    cancelable_status:0 / 1     // {optional}
    till_status:received / processed / shipped           // {optional}
    indicator:0 - none / 1 - veg / 2 - non-veg          // {optional}
    image:FILE          
    other_images[]:FILE
    loose_stock:997                   // {optional}
    loose_stock_unit_id:1             // {optional}

    type:packet
    measurement:500,400
    measurement_unit_id:4,1
    price:175,145
    discounted_price:60,30    // {optional} 
    serve_for:Available,sold out
    stock:992,225
    stock_unit_id:4,1            

    type:loose
    measurement:1,1
    measurement_unit_id:1,5
    price:100,400
    discounted_price:20,15       // {optional}
    serve_for:Available/Sold Out
*/
if (isset($_POST['add_products']) && !empty($_POST['add_products'])) {

    if (ALLOW_MODIFICATION == 0 && !defined(ALLOW_MODIFICATION)) {
        $response['error'] = true;
        $response['message'] = "This operation is not allowed in demo panel!.!";
        print_r(json_encode($response));
        return false;
    }
    // if (!empty($_POST['name']) || !empty($_POST['seller_id']) || !empty($_POST['category_id']) || !empty($_POST['image']) || !empty($_POST['description']) || !empty($_POST['type']) || empty($_POST['pincode_type'])) {

    //     if ($_POST['type']) {
    //         if (empty($_POST['measurement']) || empty($_POST['measurement_unit_id']) || empty($_POST['price'])) {
    //             $response['error'] = true;
    //             $response['message'] = "Please pass product variants fields!";
    //             print_r(json_encode($response));
    //             return false;
    //         }
    //     }
    // }
    $res_msg = "";
    $res_msg .= (empty($_POST['name']) || $_POST['name'] == "") ? "name," : "";
    $res_msg .= (empty($_POST['seller_id']) || $_POST['seller_id'] == "") ? "seller_id," : "";
    $res_msg .= (empty($_POST['category_id']) || $_POST['category_id'] == "") ? "category_id," : "";
    $res_msg .= (empty($_FILES['image']) || $_FILES['image'] == "") ? "image," : "";
    $res_msg .= (empty($_POST['description']) || $_POST['description'] == "") ? "description," : "";
    $res_msg .= (empty($_POST['type']) || $_POST['type'] == "") ? "type," : "";
    if (!empty($_POST['type']) || $_POST['type'] != "") {
        if ($_POST['type'] == "packet") {
            $res_msg .= (empty($_POST['measurement']) || $_POST['measurement'] == "") ? "measurement," : "";
            $res_msg .= (empty($_POST['measurement_unit_id']) || $_POST['measurement_unit_id'] == "") ? "measurement_unit_id," : "";
            $res_msg .= (empty($_POST['price']) || $_POST['price'] == "") ? "price," : "";
            $res_msg .= (empty($_POST['serve_for']) || $_POST['serve_for'] == "") ? "serve_for," : "";
            $res_msg .= (empty($_POST['stock']) || $_POST['stock'] == "") ? "stock," : "";
            $res_msg .= (empty($_POST['stock_unit_id']) || $_POST['stock_unit_id'] == "") ? "stock_unit_id," : "";
        } else if ($_POST['type'] == "loose") {
            $res_msg .= (empty($_POST['measurement']) || $_POST['measurement'] == "") ? "measurement," : "";
            $res_msg .= (empty($_POST['measurement_unit_id']) || $_POST['measurement_unit_id'] == "") ? "measurement_unit_id," : "";
            $res_msg .= (empty($_POST['price']) || $_POST['price'] == "") ? "price," : "";
            $res_msg .= (empty($_POST['serve_for']) || $_POST['serve_for'] == "") ? "serve_for," : "";
            $res_msg .= (empty($_POST['loose_stock']) || $_POST['loose_stock'] == "") ? "loose_stock," : "";
            $res_msg .= (empty($_POST['loose_stock_unit_id']) || $_POST['loose_stock_unit_id'] == "") ? "loose_stock_unit_id," : "";
        }
    }

    $res_msg .= ($_POST['delivery_places'] == "") ? "delivery_places," : "";
    $res_msg .= ((empty($_POST['pincodes']) || $_POST['pincodes'] == "") && $_POST['delivery_places'] != "2") ? "pincodes," : "";

    if ($res_msg != "") {
        $response['error'] = true;
        $response['message'] = "This fields " . trim($res_msg, ",") . " should be Passed!";
        print_r(json_encode($response));
        return false;
        exit();
    }

    $name = $db->escapeString($fn->xss_clean($_POST['name']));
    $seller_id = $db->escapeString($fn->xss_clean($_POST['seller_id']));
    $tax_id = (isset($_POST['tax_id']) && $_POST['tax_id'] != '') ? $db->escapeString($fn->xss_clean($_POST['tax_id'])) : '0';
    $return_days = (isset($_POST['return_days']) && $_POST['return_days'] != '') ? $db->escapeString($fn->xss_clean($_POST['return_days'])) : 0;
    $slug = $function->slugify($db->escapeString($fn->xss_clean($_POST['name'])));
    $category_id = $db->escapeString($fn->xss_clean($_POST['category_id']));
    $subcategory_id = (isset($_POST['subcategory_id']) && $_POST['subcategory_id'] != '') ? $db->escapeString($fn->xss_clean($_POST['subcategory_id'])) : 0;
    $description = $db->escapeString($fn->xss_clean($_POST['description']));
    $manufacturer = (isset($_POST['manufacturer']) && $_POST['manufacturer'] != '') ? $db->escapeString($fn->xss_clean($_POST['manufacturer'])) : '';
    $made_in = (isset($_POST['made_in']) && $_POST['made_in'] != '') ? $db->escapeString($fn->xss_clean($_POST['made_in'])) : '';
    $indicator = (isset($_POST['indicator']) && $_POST['indicator'] != '') ? $db->escapeString($fn->xss_clean($_POST['indicator'])) : '0';
    $return_status = (isset($_POST['return_status']) && $_POST['return_status'] != '') ? $db->escapeString($fn->xss_clean($_POST['return_status'])) : '0';
    $cancelable_status = (isset($_POST['cancelable_status']) && $_POST['cancelable_status'] != '') ? $db->escapeString($fn->xss_clean($_POST['cancelable_status'])) : '0';
    $till_status = (isset($_POST['till_status']) && $_POST['till_status'] != '') ? $db->escapeString($fn->xss_clean($_POST['till_status'])) : '';
    $loose_stock = (isset($_POST['loose_stock']) && $_POST['loose_stock'] != '') ? $db->escapeString($fn->xss_clean($_POST['loose_stock'])) : '';
    $loose_stock_unit_id = (isset($_POST['loose_stock_unit_id']) && $_POST['loose_stock_unit_id'] != '') ? $db->escapeString($fn->xss_clean($_POST['loose_stock_unit_id'])) : '';
    $type = $db->escapeString($fn->xss_clean($_POST['type']));

    $seller_data = $fn->get_data($columns = ['require_products_approval'], 'id=' . $seller_id, 'seller');
    $pr_approval = $seller_data[0]['require_products_approval'];
    $is_approved = ($pr_approval == 1) ? 1 : 2;

    $image = (isset($_POST['image'])) ? $db->escapeString($fn->xss_clean($_FILES['image']['name'])) : '';
    $image_error = (isset($_POST['image_error'])) ? $db->escapeString($fn->xss_clean($_FILES['image']['error'])) : '';
    $image_type = (isset($_POST['image_type'])) ? $db->escapeString($fn->xss_clean($_FILES['image']['type'])) : '';

    $allowedExts = array("gif", "jpeg", "jpg", "png");

    error_reporting(E_ERROR | E_PARSE);
    $extension = end(explode(".", $_FILES["image"]["name"]));
    $error['other_images'] = $error['image'] = '';


    $d_type = "";
    $pincode_type = (isset($_POST['delivery_places']) && $_POST['delivery_places'] != '') ? $db->escapeString($fn->xss_clean($_POST['delivery_places'])) : '';
    if ($pincode_type == "2") {
        $d_type = "all";
        $pincodes = NULL;
    } else {
        if ($pincode_type == "0") {
            $d_type = "included";
        } else if ($pincode_type == "1") {
            $d_type = "excluded";
        }
        $pincodes = $db->escapeString($fn->xss_clean($_POST['pincodes']));
    }
    $discounted_price1 = (!empty($_POST['discounted_price'])) ? $db->escapeString($fn->xss_clean($_POST['discounted_price'])) : '0';
    $price1 = $db->escapeString($fn->xss_clean($_POST['price']));
    $discounted_price = explode(",", $discounted_price1);
    $price = explode(",", $price1);

    for ($i = 0; $i < count($discounted_price); $i++) {
        $discounted_price1 = (!empty($discounted_price)) ? $discounted_price[$i] : '0';
        if ($discounted_price[$i] > $price[$i]) {
            $response['error'] = true;
            $response['message'] = "Discounted price can not be greater than price";
            print_r(json_encode($response));
            return false;
        }
    }
    if ($image_error > 0) {
        $response['error'] = true;
        $response['message'] = "Image Not uploaded!";
        print_r(json_encode($response));
        return false;
    } else {
        $result = $fn->validate_image($_FILES["image"]);
        if ($result) {
            $response['error'] = true;
            $response['message'] = "image type must jpg, jpeg, gif, or png!";
            print_r(json_encode($response));
            return false;
        }
    }
    if ($_FILES["other_images"]["error"] == 0) {
        for ($i = 0; $i < count($_FILES["other_images"]["name"]); $i++) {
            if ($_FILES["other_images"]["error"][$i] > 0) {
                $response['error'] = true;
                $response['message'] = "Other Images not uploaded!";
                print_r(json_encode($response));
                return false;
            } else {
                $result = $fn->validate_other_images($_FILES["other_images"]["tmp_name"][$i], $_FILES["other_images"]["type"][$i]);
                if ($result) {
                    $response['error'] = true;
                    $response['message'] = "other image type must jpg, jpeg, gif, or png!";
                    print_r(json_encode($response));
                    return false;
                }
            }
        }
    }

    $string = '0123456789';
    $file = preg_replace("/\s+/", "_", $_FILES['image']['name']);

    $image = $function->get_random_string($string, 4) . "-" . date("Y-m-d") . "." . $extension;
    $upload = move_uploaded_file($_FILES['image']['tmp_name'], '../../upload/images/' . $image);
    $other_images = '';

    if (isset($_FILES['other_images']) && ($_FILES['other_images']['size'][0] > 0)) {
        $file_data = array();
        $target_path = '../../upload/other_images/';
        $target_path1 = 'upload/other_images/';
        for ($j = 0; $j < count($_FILES["other_images"]["name"]); $j++) {

            $filename = $_FILES["other_images"]["name"][$j];
            $temp = explode('.', $filename);
            $filename = microtime(true) . '-' . rand(100, 999) . '.' . end($temp);
            $file_data[] = $target_path1 . '' . $filename;
            if (!move_uploaded_file($_FILES["other_images"]["tmp_name"][$j], $target_path . '' . $filename)) {
                $response['error'] = true;
                $response['message'] = "Other Images not uploaded!";
                print_r(json_encode($response));
                return false;
            }
        }
        $other_images = json_encode($file_data);
    }
    $upload_image = 'upload/images/' . $image;

    $sql = "INSERT INTO products (name,tax_id,seller_id,slug,category_id,subcategory_id,image,other_images,description,indicator,manufacturer,made_in,return_status,cancelable_status, till_status,type,pincodes,is_approved,return_days) VALUES('$name','$tax_id','$seller_id','$slug','$category_id','$subcategory_id','$upload_image','$other_images','$description','$indicator','$manufacturer','$made_in','$return_status','$cancelable_status','$till_status','$d_type','$pincodes','$is_approved','$return_days')";

    if ($db->sql($sql)) {
        $sql = "SELECT id from products where seller_id = $seller_id ORDER BY id DESC limit 1";
        $db->sql($sql);
        $res_inner = $db->getResult();

        $product_id = $db->escapeString($res_inner[0]['id']);
        $type = $db->escapeString($fn->xss_clean($_POST['type']));

        $measurement1 = $db->escapeString($fn->xss_clean($_POST['measurement']));
        $measurement_unit_id1 = $db->escapeString($fn->xss_clean($_POST['measurement_unit_id']));
        $price1 = $db->escapeString($fn->xss_clean($_POST['price']));
        $discounted_price1 = (!empty($_POST['discounted_price'])) ? $db->escapeString($fn->xss_clean($_POST['discounted_price'])) : '0';
        $stock1 = $db->escapeString($fn->xss_clean($_POST['stock']));
        $serve_for = $db->escapeString($fn->xss_clean($_POST['serve_for']));
        $stock_unit_id1 = $db->escapeString($fn->xss_clean($_POST['stock_unit_id']));

        $measurement = explode(",", $measurement1);
        $measurement_unit_id = explode(",", $measurement_unit_id1);
        $price = explode(",", $price1);
        $discounted_price = explode(",", $discounted_price1);
        $stock = explode(",", $stock1);
        $serve_for = explode(",", $serve_for);
        $stock_unit_id = explode(",", $stock_unit_id1);


        // $serve_for = ($stock[$i] == 0 || $stock[$i] <= 0) ? 'Sold Out' : 'Available';

        if ($type == 'packet') {
            if (!(count($measurement) == count($measurement_unit_id) && count($measurement_unit_id) == count($price) && count($price) == count($stock) && count($stock) == count($serve_for) && count($serve_for) == count($stock_unit_id))) {
                $response['error'] = true;
                $response['message'] = "Pass correct count for variants";
                print_r(json_encode($response));
                return false;
                exit();
            }
            $v_ids = array();
            for ($i = 0; $i < count($measurement); $i++) {
                $serve_for_lbl = ($stock[$i] == 0) ? 'Sold Out' : $serve_for[$i];
                $data = array(
                    'type' => $type,
                    'product_id' => $product_id,
                    'measurement' => $measurement[$i],
                    'measurement_unit_id' => $measurement_unit_id[$i],
                    'price' => $price[$i],
                    'discounted_price' => (!empty($discounted_price[$i])) ? $discounted_price[$i] : "0",
                    'serve_for' => $serve_for_lbl,
                    'stock' => $stock[$i],
                    'stock_unit_id' => $stock_unit_id[$i],
                );
                $db->insert('product_variant', $data);
                $res4 = $db->getResult();
                $v_ids[] = $res4[0];
            }
            if (!empty($res4)) {
                $response['error'] = false;
                $response['message'] = "Product of packet variant Added";
            } else {
                $response['error'] = true;
                $response['message'] = "Product of packet variant Not Added";
            }
        } elseif ($type == 'loose') {
            $serve_for_loose = ($loose_stock == 0) ? 'Sold Out' : $db->escapeString($fn->xss_clean($_POST['serve_for']));
            for ($i = 0; $i < count($measurement); $i++) {
                $data = array(
                    'type' => $type,
                    'product_id' => $product_id,
                    'measurement' => $measurement[$i],
                    'measurement_unit_id' => $measurement_unit_id[$i],
                    'price' => $price[$i],
                    'discounted_price' => $discounted_price[$i],
                    'serve_for' => $serve_for_loose,
                    'stock' => $loose_stock,
                    'stock_unit_id' => $loose_stock_unit_id,
                );
                $db->insert('product_variant', $data);
                $res4 = $db->getResult();
                $v_ids[] = $res4[0];
            }
            if (!empty($res4)) {
                $response['error'] = false;
                $response['message'] = "Product of loose variant Added";
            } else {
                $response['error'] = true;
                $response['message'] = "Product of loose variant Not Added";
            }
        }
    } else {
        $response['error'] = true;
        $response['message'] = "Product Not Added";
    }
    $tax_data = array();
    $product_data = $fn->get_data(0, "slug='" . $slug . "'", 'products');
    $sql = "SELECT (SELECT MIN(pv.price) FROM product_variant pv WHERE pv.product_id=p.id) as price FROM products p   where p.id = " . $product_data[0]['id'];
    $db->sql($sql);
    $pr_price = $db->getResult();
    $seller_data = $fn->get_data($columns = ['name', 'status'], "id=" . $product_data[0]['seller_id'], 'seller');
    if (!empty($product_data[0]['tax_id'])) {
        $tax_data = $fn->get_data($columns = ['title', 'percentage'], "id=" . $product_data[0]['tax_id'], 'taxes');
    }


    $rows = array();
    for ($i = 0; $i < count($measurement); $i++) {
        $serve_for_lbl = ($stock[$i] == 0) ? 'Sold Out' : $serve_for[$i];
        $ms_unit_name = $fn->get_data($columns = ['short_code'], "id=" . $measurement_unit_id[$i], 'unit');
        $stk_unit_id = ($loose_stock_unit_id != "") ? $loose_stock_unit_id : $stock_unit_id[$i];
        $stock_unit_name = $fn->get_data($columns = ['short_code'], "id=" . $stk_unit_id, 'unit');
        $tempRow = array(
            'id' => strval($v_ids[$i]),
            'type' => $type,
            'product_id' => $product_id,
            'measurement' => $measurement[$i],
            'measurement_unit_id' => $measurement_unit_id[$i],
            'measurement_unit_name' => $ms_unit_name[0]['short_code'],
            'price' => $price[$i],
            'discounted_price' => (!empty($discounted_price[$i])) ? $discounted_price[$i] : "0",
            'serve_for' => $serve_for_lbl,
            ($loose_stock != "") ? 'loose_stock' : 'stock' => ($loose_stock != "") ? $loose_stock : $stock[$i],
            ($loose_stock != "") ? 'loose_stock_unit_id' : 'stock_unit_id' => ($loose_stock != "") ? $loose_stock_unit_id :  $stock_unit_id[$i],
            'stock_unit_name' => $stock_unit_name[0]['short_code'],

        );
        $rows[] = $tempRow;
    }
    if (!empty($product_data[0]['other_images'])) {
        $other_i = json_decode($product_data[0]['other_images'], true);
        for ($j = 0; $j < count($other_i); $j++) {
            $other_i[$j] = DOMAIN_URL . $other_i[$j];
        }
    }
    //params 

    $res_data = array(
        "id" => $product_data[0]['id'],
        "name" => $name,
        "seller_id" => $seller_id,
        "subcategory_id" => $subcategory_id,
        "tax_id" => $tax_id,
        "category_id" => $category_id,
        "description" => $description,
        "manufacturer" => $manufacturer,
        "made_in" => $made_in,
        "indicator" => $indicator,
        "return_status" => $return_status,
        "return_days" => $return_days,
        "cancelable_status" => $cancelable_status,
        "till_status" => $till_status,
        "delivery_places" => $pincode_type,
        "pincodes" => $pincodes,
        "type" => $type,
        "row_order" => $product_data[0]['row_order'],
        "slug" => $product_data[0]['slug'],
        "status" => $product_data[0]['status'],
        "date_added" => $product_data[0]['date_added'],
        "is_approved" => $product_data[0]['is_approved'],
        "seller_name" => $seller_data[0]['name'],
        "seller_status" => $seller_data[0]['status'],
        "price" => $pr_price[0]['price'],
        "tax_title" => (!empty($tax_data)) ? $tax_data[0]['title'] : "",
        "tax_percentage" => (!empty($tax_data)) ? $tax_data[0]['percentage'] : "0",
        "image" => DOMAIN_URL . $product_data[0]['image'],
        "other_images" => (!empty($other_i)) ? $other_i : "",
        "variants" => $rows,
    );
    $response['data'] = $res_data;
    print_r(json_encode($response));
    return false;
    exit();
}

/* 
---------------------------------------------------------------------------------------------------------
*/

/*
11.update_products
    accesskey:90336
    update_products:1
    seller_id:1
    id:833
    name:chocolate-popcorn           
    description:chocolates
    category_id:31 
    subcategory_id:115          // {optional}
    delivery_places:0 OR 1 OR 2 [ 0=included, 1=excluded, 2=all ]
    pincodes:1,4,5                 //{must blank when delivery_places=2}
    return_days:7 {optional}
    tax_id:4                    // {optional}
    manufacturer:india          // {optional}
    made_in:india               // {optional}
    return_status:0 / 1         // {optional}
    cancelable_status:0 / 1     // {optional}
    till_status:received / processed / shipped           // {optional}
    indicator:0 - none / 1 - veg / 2 - non-veg          // {optional}
    product_variant_id:510,209
    image:FILE           //{optional}
    other_images[]:FILE    //{optional}
    loose_stock:997                   // {optional}
    loose_stock_unit_id:1             // {optional}

    type:packet
    measurement:500,400
    measurement_unit_id:4,1
    price:175,145
    discounted_price:60,30    // {optional} 
    serve_for:Available,sold out
    stock:992,225
    stock_unit_id:4,1            

    type:loose
    measurement:1,1
    measurement_unit_id:1,5
    price:100,400
    discounted_price:20,15       // {optional}
    serve_for:Available/Sold Out
    stock:997
    stock_unit_id:1
*/
if (isset($_POST['update_products']) && !empty($_POST['update_products'])) {
    if (ALLOW_MODIFICATION == 0 && !defined(ALLOW_MODIFICATION)) {
        $response['error'] = true;
        $response['message'] = "This operation is not allowed in demo panel!.!";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['category_id'])  || empty($_POST['description']) || empty($_POST['type'])) {
        $response['error'] = true;
        $response['message'] = "Please pass all fields!";
        print_r(json_encode($response));
        return false;
    }
    // if ($_POST['type']) {
    //     if (empty($_POST['measurement']) || empty($_POST['measurement_unit_id']) || empty($_POST['price']) || empty($_POST['discounted_price']) || empty($_POST['serve_for'])) {
    //         $response['error'] = true;
    //         $response['message'] = "Please pass product variants fields!";
    //         print_r(json_encode($response));
    //         return false;
    //     }
    // }

    $d_type = "";
    $pincode_type = (isset($_POST['delivery_places']) && $_POST['delivery_places'] != '') ? $db->escapeString($fn->xss_clean($_POST['delivery_places'])) : '';
    if ($pincode_type == "2") {
        $d_type = "all";
        $pincodes = NULL;
    } else {
        if ($pincode_type == "0") {
            $d_type = "included";
        } else if ($pincode_type == "1") {
            $d_type = "excluded";
        }
        $pincodes = $db->escapeString($fn->xss_clean($_POST['pincodes']));
    }

    $seller_id = $db->escapeString($fn->xss_clean($_POST['seller_id']));
    $name = $db->escapeString($fn->xss_clean($_POST['name']));
    if (strpos($name, '-') !== false) {
        $temp = (explode("-", $name)[1]);
    } else {
        $temp = $name;
    }

    $slug = $function->slugify($temp);
    $id = $db->escapeString($fn->xss_clean($_POST['id']));
    $sql = "SELECT slug FROM products where id!=" . $id;
    $db->sql($sql);
    $res = $db->getResult();
    $i = 1;
    foreach ($res as $row) {
        if ($slug == $row['slug']) {
            $slug = $slug . '-' . $i;
            $i++;
        }
    }
    $category_data = array();
    $product_status = "";
    $sql_query = "SELECT categories FROM seller where id = $seller_id";
    $db->sql($sql_query);
    $res1 = $db->getResult();
    $category_ids = $res1[0]['categories'];

    $sql = "SELECT id,name from category where id IN($category_ids) order by id asc";
    $db->sql($sql);
    $category_data = $db->getResult();
    $sql = "SELECT * from subcategory where category_id IN($category_ids)";
    $db->sql($sql);
    $subcategory = $db->getResult();
    $sql = "SELECT image, other_images FROM products WHERE id =" . $id;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row) {
        $previous_menu_image = $row['image'];
        $other_images = $row['other_images'];
    }

    $name = $db->escapeString($fn->xss_clean($_POST['name']));
    $subcategory_id = (isset($_POST['subcategory_id']) && $_POST['subcategory_id'] != '') ? $db->escapeString($fn->xss_clean($_POST['subcategory_id'])) : 0;
    $category_id = $db->escapeString($fn->xss_clean($_POST['category_id']));
    $serve_for = $db->escapeString($fn->xss_clean($_POST['serve_for']));
    $description = $db->escapeString($fn->xss_clean($_POST['description']));
    $manufacturer = (isset($_POST['manufacturer']) && $_POST['manufacturer'] != '') ? $db->escapeString($fn->xss_clean($_POST['manufacturer'])) : '';
    $made_in = (isset($_POST['made_in']) && $_POST['made_in'] != '') ? $db->escapeString($fn->xss_clean($_POST['made_in'])) : '';
    $indicator = (isset($_POST['indicator']) && $_POST['indicator'] != '') ? $db->escapeString($fn->xss_clean($_POST['indicator'])) : '0';
    $return_status = (isset($_POST['return_status']) && $_POST['return_status'] != '') ? $db->escapeString($fn->xss_clean($_POST['return_status'])) : '0';
    $return_days = (isset($_POST['return_days']) && $_POST['return_days'] != '') ? $db->escapeString($fn->xss_clean($_POST['return_days'])) : 0;
    $cancelable_status = (isset($_POST['cancelable_status']) && $_POST['cancelable_status'] != '') ? $db->escapeString($fn->xss_clean($_POST['cancelable_status'])) : '0';
    $till_status = (isset($_POST['till_status']) && $_POST['till_status'] != '') ? $db->escapeString($fn->xss_clean($_POST['till_status'])) : '';
    $tax_id = (isset($_POST['tax_id']) && $_POST['tax_id'] != '') ? $db->escapeString($fn->xss_clean($_POST['tax_id'])) : 0;
    $loose_stock = (isset($_POST['loose_stock']) && $_POST['loose_stock'] != '') ? $db->escapeString($fn->xss_clean($_POST['loose_stock'])) : '';
    $loose_stock_unit_id = (isset($_POST['loose_stock_unit_id']) && $_POST['loose_stock_unit_id'] != '') ? $db->escapeString($fn->xss_clean($_POST['loose_stock_unit_id'])) : '';
    $Delivery_places = "";


    $d_type = $pincode_type = "";
    $pincode_type = (isset($_POST['delivery_places']) && $_POST['delivery_places'] != '') ? $db->escapeString($fn->xss_clean($_POST['delivery_places'])) : '';
    if ($pincode_type == "2") {
        $d_type = "all";
        $pincodes = NULL;
    } else {
        if ($pincode_type == "0") {
            $d_type = "included";
        } else if ($pincode_type == "1") {
            $d_type = "excluded";
        }
        $pincodes = $db->escapeString($fn->xss_clean($_POST['pincodes']));
    }
    if (isset($_FILES['other_images']) && ($_FILES['other_images']['size'][0] > 0)) {
        $file_data = array();
        $target_path = '../../upload/other_images/';
        $target_path1 = 'upload/other_images/';

        for ($i = 0; $i < count($_FILES["other_images"]["name"]); $i++) {
            if ($_FILES["other_images"]["error"][$i] > 0) {
                $response['error'] = true;
                $response['message'] = "Other Images not uploaded!";
                print_r(json_encode($response));
                return false;
            } else {
                $result = $fn->validate_other_images($_FILES["other_images"]["tmp_name"][$i], $_FILES["other_images"]["type"][$i]);
                if ($result) {
                    $response['error'] = true;
                    $response['message'] = "Other image type must jpg, jpeg, gif, or png!";
                    print_r(json_encode($response));
                    return false;
                }
            }
            $filename = $_FILES["other_images"]["name"][$i];
            $temp = explode('.', $filename);
            $filename = microtime(true) . '-' . rand(100, 999) . '.' . end($temp);
            $file_data[] = 'upload/other_images/' . $filename;

            if (!move_uploaded_file($_FILES["other_images"]["tmp_name"][$i], $target_path . $filename)) {
                $response['error'] = true;
                $response['message'] = "Other Images not uploaded!";
                print_r(json_encode($response));
                return false;
            }
        }
        if (!empty($other_images)) {
            $arr_old_images = json_decode($other_images);
            $all_images = array_merge($arr_old_images, $file_data);
            $all_images = $db->escapeString(json_encode(array_values($all_images)));
        } else {
            $all_images = $db->escapeString(json_encode($file_data));
        }
        if (empty($error)) {
            $sql = "update `products` set `other_images`='" . $all_images . "' where `id`= $id  and seller_id=$seller_id";
            $db->sql($sql);
        }
    }
    if (strpos($name, "'") !== false) {
        $name = str_replace("'", "''", "$name");
        if (strpos($description, "'") !== false) {
            $description = str_replace("'", "''", "$description");
        }
    }

    if (isset($_FILES['image']) && ($_FILES['image']['size'][0] > 0)) {
        $image = $db->escapeString($fn->xss_clean($_FILES['image']['name']));
        $image_error = $db->escapeString($fn->xss_clean($_FILES['image']['error']));
        $image_type = $db->escapeString($fn->xss_clean($_FILES['image']['type']));
        $error = array();
        $allowedExts = array("gif", "jpeg", "jpg", "png");

        error_reporting(E_ERROR | E_PARSE);
        $extension = end(explode(".", $_FILES["image"]["name"]));

        if (!empty($image)) {
            $result = $fn->validate_image($_FILES["image"]);
            if ($result) {
                $response['error'] = true;
                $response['message'] = "image type must jpg, jpeg, gif, or png!";
                print_r(json_encode($response));
                return false;
            }
        }
        $string = '0123456789';
        $file = preg_replace("/\s+/", "_", $_FILES['image']['name']);
        $function = new functions;
        $image = $function->get_random_string($string, 4) . "-" . date("Y-m-d") . "." . $extension;
        $delete = unlink("$previous_menu_image");

        $upload = move_uploaded_file($_FILES['image']['tmp_name'], '../../upload/images/' . $image);
        $upload_image = 'upload/images/' . $image;
        $sql_query = "UPDATE products SET name = '$name' , category_id = '$category_id' , tax_id = '$tax_id' ,slug = '$slug' ,subcategory_id = '$subcategory_id', image = '$upload_image', description = '$description', indicator = '$indicator', manufacturer = '$manufacturer', made_in = '$made_in', return_status = '$return_status' , return_days = '$return_days', cancelable_status = '$cancelable_status', till_status = '$till_status', seller_id = '$seller_id' WHERE id = '$id' and seller_id=$seller_id ";
    } else if ($pincode_type != "") {
        $sql_query = "UPDATE products SET name = '$name' ,type= '$d_type',pincodes = '$pincodes' ,tax_id = '$tax_id' ,slug = '$slug' ,category_id = '$category_id' , subcategory_id = '$subcategory_id' ,description = '$description', indicator = '$indicator', manufacturer = '$manufacturer', made_in = '$made_in', return_status = '$return_status',return_days = '$return_days', cancelable_status = '$cancelable_status', till_status = '$till_status' WHERE id = '$id'  and seller_id=$seller_id";
    } else {
        $sql_query = "UPDATE products SET name = '$name' ,tax_id = '$tax_id' ,slug = '$slug' ,category_id = '$category_id' , subcategory_id = '$subcategory_id' ,description = '$description', indicator = '$indicator', manufacturer = '$manufacturer', made_in = '$made_in', return_status = '$return_status', cancelable_status = '$cancelable_status',return_days = '$return_days', till_status = '$till_status' WHERE id = '$id' and seller_id=$seller_id";
    }
    $db->sql($sql_query);
    $res = $db->getResult();
    $type = $db->escapeString($fn->xss_clean($_POST['type']));
    $product_variant_id1 = $db->escapeString($fn->xss_clean($_POST['product_variant_id']));
    $product_variant_id = explode(",", $product_variant_id1);

    $measurement1 = $db->escapeString($fn->xss_clean($_POST['measurement']));
    $measurement_unit_id1 = $db->escapeString($fn->xss_clean($_POST['measurement_unit_id']));
    $price1 = $db->escapeString($fn->xss_clean($_POST['price']));
    $discounted_price1 = !empty($_POST['discounted_price']) ? $db->escapeString($fn->xss_clean($_POST['discounted_price'])) : 0;
    // $serve_for1 = $db->escapeString($fn->xss_clean($_POST['serve_for']));
    $stock1 = $db->escapeString($fn->xss_clean($_POST['stock']));
    $serve_for2 =  $db->escapeString($fn->xss_clean($_POST['serve_for']));
    $stock_unit_id1 = $db->escapeString($fn->xss_clean($_POST['stock_unit_id']));

    $measurement = explode(",", $measurement1);
    $measurement_unit_id = explode(",", $measurement_unit_id1);
    $price = explode(",", $price1);
    $discounted_price = explode(",", $discounted_price1);
    $serve_for = explode(",", $serve_for2);

    $stock = explode(",", $stock1);
    $stock_unit_id = explode(",", $stock_unit_id1);

    $product_data = $fn->get_data(0, 'id=' . $id, 'products');
    $tax_data = array();
    $sql = "SELECT (SELECT MIN(pv.price) FROM product_variant pv WHERE pv.product_id=p.id) as price FROM products p   where p.id = " . $id;
    $db->sql($sql);
    $pr_price = $db->getResult();
    $seller_data = $fn->get_data($columns = ['name', 'status'], "id=" . $product_data[0]['seller_id'], 'seller');
    if (!empty($product_data[0]['tax_id'])) {
        $tax_data = $fn->get_data($columns = ['title', 'percentage'], "id=" . $product_data[0]['tax_id'], 'taxes');
    }
    $rows = array();
    for ($i = 0; $i < count($measurement); $i++) {
        $serve_for_lbl = ($stock[$i] == 0) ? 'Sold Out' : $serve_for[$i];
        $ms_unit_name = $fn->get_data($columns = ['short_code'], "id=" . $measurement_unit_id[$i], 'unit');
        $stk_unit_id = ($loose_stock_unit_id != "") ? $loose_stock_unit_id : $stock_unit_id[$i];
        $stock_unit_name = $fn->get_data($columns = ['short_code'], "id=" . $stk_unit_id, 'unit');

        $tempRow = array(
            'id' => $product_variant_id[$i],
            'type' => $type,
            'product_id' => $id,
            'measurement' => $measurement[$i],
            'measurement_unit_id' => $measurement_unit_id[$i],
            'measurement_unit_name' => $ms_unit_name[0]['short_code'],
            'price' => $price[$i],
            'discounted_price' => (!empty($discounted_price[$i])) ? $discounted_price[$i] : "0",
            'serve_for' => $serve_for_lbl,
            ($loose_stock != "") ? 'loose_stock' : 'stock' => ($loose_stock != "") ? $loose_stock : $stock[$i],
            ($loose_stock != "") ? 'loose_stock_unit_id' : 'stock_unit_id' => ($loose_stock != "") ? $loose_stock_unit_id :  $stock_unit_id[$i],
            'stock_unit_name' => $stock_unit_name[0]['short_code'],
        );
        $rows[] = $tempRow;
    }
    if (!empty($product_data[0]['other_images'])) {
        $other_i = json_decode($product_data[0]['other_images'], true);
        for ($j = 0; $j < count($other_i); $j++) {
            $other_i[$j] = DOMAIN_URL . $other_i[$j];
        }
    }
    //params
    $res_data = array(
        "id" => $id,
        "name" => $name,
        "seller_id" => $seller_id,
        "subcategory_id" => $subcategory_id,
        "tax_id" => $tax_id,
        "category_id" => $category_id,
        "description" => $description,
        "manufacturer" => $manufacturer,
        "made_in" => $made_in,
        "indicator" => $indicator,
        "return_status" => $return_status,
        "return_days" => $return_days,
        "cancelable_status" => $cancelable_status,
        "till_status" => $till_status,
        "delivery_places" => $pincode_type,
        "pincodes" => $pincodes,
        "type" => $type,
        "row_order" => $product_data[0]['row_order'],
        "slug" => $product_data[0]['slug'],
        "status" => $product_data[0]['status'],
        "date_added" => $product_data[0]['date_added'],
        "is_approved" => $product_data[0]['is_approved'],
        "seller_name" => $seller_data[0]['name'],
        "seller_status" => $seller_data[0]['status'],
        "price" => $pr_price[0]['price'],
        "tax_title" => (!empty($tax_data)) ? $tax_data[0]['title'] : "",
        "tax_percentage" => (!empty($tax_data)) ? $tax_data[0]['percentage'] : "0",
        "image" => DOMAIN_URL . $product_data[0]['image'],
        "other_images" => (!empty($other_i)) ? $other_i : "",
        "variants" => $rows,
    );

    $pr_variant_test = $fn->get_data($columns = ['id'], "product_id=" . $id, "product_variant");
    for ($i = 0; $i < count($product_variant_id); $i++) {
        if (in_array($product_variant_id[$i], $pr_variant_test)) {
            $response['error'] = true;
            $response['message'] = "Invalid product variant id.";
            print_r(json_encode($response));
            return false;
        }

        if ($_POST['type'] == "packet") {

            if (!(count($measurement) == count($measurement_unit_id) && count($measurement_unit_id) == count($price) && count($price) == count($stock) && count($stock) == count($serve_for) && count($serve_for) == count($stock_unit_id))) {
                $response['error'] = true;
                $response['message'] = "Pass correct count for variants";
                print_r(json_encode($response));
                return false;
                exit();
            }
            $serve_for_lbl = ($stock[$i] == 0) ? 'Sold Out' : $serve_for[$i];

            $data = array(
                'type' => $type,
                'id' => $product_variant_id[$i],
                'measurement' => $measurement[$i],
                'measurement_unit_id' => $measurement_unit_id[$i],
                'price' => $price[$i],
                'discounted_price' => $discounted_price[$i],
                'serve_for' => $serve_for_lbl,
                'stock' => $stock[$i],
                'stock_unit_id' => $stock_unit_id[$i],
            );
            $db->update('product_variant', $data, 'id=' . $data['id']);
            $res = $db->getResult();
        } elseif ($_POST['type'] == "loose") {
            $serve_for_loose = ($stloose_stockock1 == 0) ? 'Sold Out' : $db->escapeString($fn->xss_clean($_POST['serve_for']));
            $data = array(
                'type' => $type,
                'id' => $product_variant_id[$i],
                'measurement' => $measurement[$i],
                'measurement_unit_id' => $measurement_unit_id[$i],
                'price' => $price[$i],
                'discounted_price' => $discounted_price[$i],
                'serve_for' => $serve_for_loose,
                'stock' => $loose_stock,
                'stock_unit_id' => $loose_stock_unit_id,
            );
            $db->update('product_variant', $data, 'id=' . $data['id']);
            $res = $db->getResult();
        }
    }
    $response['error'] = false;
    $response['message'] = "Product updated successfully";
    $response['data'] = $res_data;

    print_r(json_encode($response));
    return false;
}

/* 
---------------------------------------------------------------------------------------------------------
*/

/*
12.delete_products
    accesskey:90336
    delete_products:1
    product_variants_id:668
    product_id:879
*/
if (isset($_POST['delete_products']) && !empty($_POST['delete_products']) && ($_POST['delete_products'] == 1)) {
    if (ALLOW_MODIFICATION == 0 && !defined(ALLOW_MODIFICATION)) {
        $response['error'] = true;
        $response['message'] = "This operation is not allowed in demo panel!.!";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['product_variants_id'])) {
        $response['error'] = true;
        $response['message'] = "Please pass product variants id fields!";
        print_r(json_encode($response));
        return false;
    }
    $product_variants_id = (isset($_POST['product_variants_id'])) ? $db->escapeString($fn->xss_clean($_POST['product_variants_id'])) : "";

    $product_id = $fn->get_product_id_by_variant_id($product_variants_id);

    $sql_query = "DELETE FROM cart WHERE product_id = $product_id  AND product_variant_id = $product_variants_id";
    $db->sql($sql_query);
    $sql_query = "DELETE FROM product_variant WHERE product_id=" . $product_id;
    $db->sql($sql_query);

    $sql = "SELECT count(id) as total from product_variant WHERE product_id=" . $product_id;
    $db->sql($sql);
    $total = $db->getResult();

    if ($total[0]['total'] == 0) {
        $sql_query = "SELECT image FROM products WHERE id =" . $product_id;
        $db->sql($sql_query);
        $res = $db->getResult();
        unlink('../../' . $res[0]['image']);

        $sql_query = "SELECT other_images FROM products WHERE id =" . $product_id;
        $db->sql($sql_query);
        $res = $db->getResult();
        if (!empty($res[0]['other_images'])) {
            $other_images = json_decode($res[0]['other_images']);
            foreach ($other_images as $other_image) {
                unlink('../../' . $other_image);
            }
        }

        $sql_query = "DELETE FROM products WHERE id =" . $product_id;
        $db->sql($sql_query);

        $sql_query = "DELETE FROM favorites WHERE product_id = " . $product_id;
        $db->sql($sql_query);
    }
    $response['error'] = false;
    $response['message'] = "product delete successfully!";
    print_r(json_encode($response));
    return false;
}

if (isset($_POST['get_seller_by_id']) && !empty($_POST['get_seller_by_id'])) {

    /* 
    13. get_seller_by_id
        accesskey:90336
        seller_id:78
        get_seller_by_id:1
    */
    if (empty($_POST['seller_id'])) {
        $response['error'] = true;
        $response['message'] = "Seller id should be Passed!";
        print_r(json_encode($response));
        return false;
        exit();
    }
    $id = $db->escapeString($fn->xss_clean($_POST['seller_id']));

    if (isset($_POST['slug']) && !empty($_POST['slug'])) {
        $slug = $db->escapeString($fn->xss_clean($_POST['slug']));
        $where = " AND `slug` = '$slug' ";
    }
    $sql = "SELECT * FROM seller	WHERE id = '" . $id . "'" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    $db->disconnect();
    $rows = $tempRow = array();
    if ($num == 1) {
        $res[0]['fcm_id'] = !empty($res[0]['fcm_id'])  ? $res[0]['fcm_id'] : "";
        $res[0]['longitude'] = !empty($res[0]['longitude'])  ? $res[0]['longitude'] : "0";
        $res[0]['latitude'] = !empty($res[0]['latitude'])  ? $res[0]['latitude'] : "0";
        $res[0]['national_identity_card'] = !empty($res[0]['national_identity_card'])  ?  DOMAIN_URL . 'upload/seller/' . $res[0]['national_identity_card'] : "";
        $res[0]['address_proof'] = !empty($res[0]['address_proof']) ?  DOMAIN_URL . 'upload/seller/' . $res[0]['address_proof'] : "";
        $res[0]['logo'] = (!empty($res[0]['logo'])) ? DOMAIN_URL . 'upload/seller/' . $res[0]['logo'] : "";
        $state = (!empty($row['state'])) ? $row['state'] . ", " : "";
        $street = (!empty($row['street'])) ? $row['street'] . ", " : "";
        $pincode = (!empty($row['pincode_id'])) ? $res_pincode[0]['city'] . " - " . $res_pincode[0]['pincode'] : "";
        $seller_address = $state  . $street . $pincode;
        $res[0]['seller_address'] = (!empty($seller_address))  ? $seller_address : "";
        $response['error'] = false;
        $response['message'] = "Seller Data Fetched Successfully";
        $response['currency'] =  $fn->get_settings('currency');
        $response['data'] = $res;
        $response['data'][0]['balance'] = ceil($response['data'][0]['balance']);
    } else {
        $response['error'] = true;
        $response['message'] = "No data found!";
    }
    print_r(json_encode($response));
}

if (isset($_POST['get_taxes']) && $_POST['get_taxes'] == 1) {
    /*  
    14. get_taxes
        accesskey:90336
        get_taxes:1
    */

    $sql = "SELECT * FROM taxes ";
    $db->sql($sql);
    $res = $db->getResult();

    if (!empty($res)) {
        $response['error'] = false;
        $response['message'] = "Taxes retrieved successfully";
        $response['data'] = $res;
    } else {
        $response['error'] = true;
        $response['message'] = "No data found!";
    }
    print_r(json_encode($response));
    return false;
}


if (isset($_POST['get_units']) && $_POST['get_units'] == 1) {
    /*  
    15. get_units
        accesskey:90336
        get_units:1
    */

    $sql = "SELECT * FROM unit ";
    $db->sql($sql);
    $res = $db->getResult();

    for ($i = 0; $i < count($res); $i++) {
        $res[$i]['parent_id'] = (!empty($res[$i]['parent_id'])) ? $res[$i]['parent_id'] : "0";
        $res[$i]['conversion'] = (!empty($res[$i]['conversion'])) ? $res[$i]['conversion'] : "0";
    }

    if (!empty($res)) {
        $response['error'] = false;
        $response['message'] = "Units retrieved successfully";
        $response['data'] = $res;
    } else {
        $response['error'] = true;
        $response['message'] = "No data found!";
    }
    print_r(json_encode($response));
    return false;
}

if (isset($_POST['get_pincodes']) && $_POST['get_pincodes'] == 1) {
    /*  
    16. get_pincodes
        accesskey:90336
        get_pincodes:1
    */

    $sql = "SELECT * FROM pincodes ";
    $db->sql($sql);
    $res = $db->getResult();

    if (!empty($res)) {
        $response['error'] = false;
        $response['message'] = "Pincodes retrieved successfully";
        $response['data'] = $res;
    } else {
        $response['error'] = true;
        $response['message'] = "No data found!";
    }
    print_r(json_encode($response));
    return false;
}

if (isset($_POST['delete_other_images']) && $_POST['delete_other_images'] == 1) {

    /*  
    17. delete_other_images
        accesskey:90336
        delete_other_images:1
        seller_id:1
        product_id:1
        image:1    // {index of other image array}
    */

    if (ALLOW_MODIFICATION == 0 && !defined(ALLOW_MODIFICATION)) {
        $response['error'] = true;
        $response['message'] = "This operation is not allowed in demo panel!.!";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['seller_id']) || empty($_POST['product_id']) || empty($_POST['image'])) {
        $response['error'] = true;
        $response['message'] = "All fields should be Passed!";
        print_r(json_encode($response));
        return false;
        exit();
    }
    $seller_id = $db->escapeString($fn->xss_clean($_POST['seller_id']));
    $pid = $db->escapeString($fn->xss_clean($_POST['product_id']));
    $i = $db->escapeString($fn->xss_clean($_POST['image']));

    $result = $fn->delete_other_images($pid, $i, $seller_id);
    if ($result == 1) {
        $response['error'] = false;
        $response['message'] = "Image deleted successfully";
    } else if ($result == 2) {
        $response['error'] = true;
        $response['message'] = "Seller have not this product";
    } else {
        $response['error'] = true;
        $response['message'] = "Image is not deleted. try agian later";
    }
    print_r(json_encode($response));
    return false;
    exit();
}


if (isset($_POST['delete_variant']) && $_POST['delete_variant'] == 1) {

    /*  
    18. delete_variant
        accesskey:90336
        delete_variant:1
        variant_id:1
    */
    if (ALLOW_MODIFICATION == 0 && !defined(ALLOW_MODIFICATION)) {
        $response['error'] = true;
        $response['message'] = "This operation is not allowed in demo panel!.!";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['variant_id'])) {
        $response['error'] = true;
        $response['message'] = "All fields should be Passed!";
        print_r(json_encode($response));
        return false;
        exit();
    }
    $v_id = $db->escapeString($fn->xss_clean($_POST['variant_id']));

    $result = $fn->delete_variant($v_id);
    if ($result) {
        $response['error'] = false;
        $response['message'] = "Product variant deleted successfully!";
    } else {
        $response['error'] = true;
        $response['message'] = "Product variant not exist or some error occured!";
    }
    print_r(json_encode($response));
    return false;
    exit();
}

if (isset($_POST['get_customers']) && !empty($_POST['get_customers'])) {
    /* 
   19.get_customers
	   accesskey:90336
	   get_customers:1
	   pincode_id:119  {optional}
	   limit:10  {optional}
	   offset:0    {optional}
	   sort:id      {optional}
	   order:ASC/DESC {optional}
	   search:value {optional}
   */
    $where = '';
    $offset = (isset($_POST['offset']) && !empty(trim($_POST['offset'])) && is_numeric($_POST['offset'])) ? $db->escapeString(trim($fn->xss_clean($_POST['offset']))) : 0;
    $limit = (isset($_POST['limit']) && !empty(trim($_POST['limit'])) && is_numeric($_POST['limit'])) ? $db->escapeString(trim($fn->xss_clean($_POST['limit']))) : 10;

    $sort = (isset($_POST['sort']) && !empty(trim($_POST['sort']))) ? $db->escapeString(trim($fn->xss_clean($_POST['sort']))) : 'u.id';
    $order = (isset($_POST['order']) && !empty(trim($_POST['order']))) ? $db->escapeString(trim($fn->xss_clean($_POST['order']))) : 'DESC';

    if (isset($_POST['pincode_id']) && $_POST['pincode_id'] != '') {
        $pincode_id = $db->escapeString($fn->xss_clean($_POST['pincode_id']));
        $where .= ' where ua.pincode_id=' . $pincode_id;
    }
    if (isset($_POST['search']) && !empty($_POST['search'])) {
        $search = $db->escapeString($fn->xss_clean($_POST['search']));
        if (isset($_POST['pincode_id']) && $_POST['pincode_id'] != '') {
            $where .= " and u.`id` like '%" . $search . "%' OR u.`name` like '%" . $search . "%' OR u.`email` like '%" . $search . "%' OR u.`mobile` like '%" . $search . "%' ";
        } else {
            $where .= " Where u.`id` like '%" . $search . "%' OR u.`name` like '%" . $search . "%' OR u.`email` like '%" . $search . "%' OR u.`mobile` like '%" . $search . "%'";
        }
    }
    $sql = "SELECT COUNT(DISTINCT(u.id)) as total FROM `users` u LEFT JOIN user_addresses ua on u.id=ua.user_id LEFT JOIN pincodes p on p.id=ua.pincode_id LEFT JOIN area a on a.id=ua.area_id " . $where;
    $db->sql($sql);
    $res = $db->getResult();
    if (!empty($res)) {
        foreach ($res as $row)
            $total = $row['total'];

        $sql = "SELECT DISTINCT u.*,a.name as area_name,p.pincode as pincode,c.name as city,ua.pincode_id FROM `users` u LEFT JOIN user_addresses ua on u.id=ua.user_id LEFT JOIN pincodes p on p.id=ua.pincode_id LEFT JOIN area a on a.id=ua.area_id LEFT JOIN cities c on c.id=a.city_id " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . ", " . $limit;
        $db->sql($sql);
        $res = $db->getResult();
        $rows = array();
        $tempRow = array();

        foreach ($res as $row) {
            $tempRow['id'] = $row['id'];
            $tempRow['name'] = $row['name'];
            $path = DOMAIN_URL . 'upload/profile/';
            if (!empty($row['profile'])) {
                $tempRow['profile'] = $path . $row['profile'];
            } else {
                $tempRow['profile'] = $path . "default_user_profile.png";
            }
            $tempRow['email'] = $row['email'];
            $tempRow['mobile'] = $row['mobile'];
            $tempRow['balance'] = $row['balance'];
            $tempRow['referral_code'] = $row['referral_code'];
            $tempRow['friends_code'] = !empty($row['friends_code']) ? $row['friends_code'] : '-';
            $tempRow['city'] = !empty($row['city']) ? $row['city'] : '';
            $tempRow['pincode_id'] = !empty($row['pincode_id']) ? $row['pincode_id'] : '';
            $tempRow['pincode'] = !empty($row['pincode']) ? $row['pincode'] : '';
            $tempRow['area'] = !empty($row['area_name']) ? $row['area_name'] : '';
            $tempRow['status'] = $row['status'];
            $tempRow['created_at'] = $row['created_at'];
            $rows[] = $tempRow;
        }
        $response['error'] = false;
        $response['message'] = "Customers fatched successfully.";
        $response['total'] = $total;
        $response['data'] = $rows;
    } else {
        $response['error'] = true;
        $response['message'] = "Something went wrong, please try again leter.";
    }
    print_r(json_encode($response));
}

if (isset($_POST['send_request']) && $_POST['send_request'] == 1) {
    /*
    20.send_request
        accesskey:90336
        send_request:1
        type:seller
        type_id:3
        amount:1000
        message:Message {optional}
    */
    $res_msg = "";
    $res_msg .= (empty($_POST['type']) || $_POST['type'] == "") ? "type," : "";
    $res_msg .= (empty($_POST['type_id']) || $_POST['type_id'] == "") ? "type_id," : "";
    if ($res_msg != "") {
        $response['error'] = true;
        $response['message'] = "this fields " . trim($res_msg, ",") . " should be Passed!";
        print_r(json_encode($response));
        return false;
        exit();
    }
    // if (empty($_POST['type']) || empty($_POST['type_id']) || empty($_POST['amount']) ) {
    //     $response['error'] = true;
    //     $response['message'] = "All fields should be Passed!";
    //     print_r(json_encode($response));
    //     return false;
    //     exit();
    // }
    $type = $db->escapeString($fn->xss_clean($_POST['type']));
    $type_id = $db->escapeString($fn->xss_clean($_POST['type_id']));
    $amount  = $db->escapeString($fn->xss_clean($_POST['amount']));
    $order_id = (isset($_POST['order_id']) && !empty($_POST['order_id'])) ? $db->escapeString($fn->xss_clean($_POST['order_id'])) : "";
    $order_item_id = (isset($_POST['order_item_id']) && !empty($_POST['order_item_id'])) ? $db->escapeString($fn->xss_clean($_POST['order_item_id'])) : "";
    $message = (isset($_POST['message']) && !empty($_POST['message'])) ? $db->escapeString($fn->xss_clean($_POST['message'])) : "";
    // $type1 = $type == 'user' ? 'user' : 'delivery boy';
    if (!empty($type) && !empty($type_id) && !empty($amount)) {
        // check if such user or delivery boy exists or not
        if ($fn->is_user_or_dboy_exists($type, $type_id)) {
            // checking if balance is greater than amount requested or not 
            $balance = $fn->get_user_or_delivery_boy_balance($type, $type_id);
            if ($balance >= $amount) {
                // Debit amount requeted
                $new_balance =  $balance - $amount;
                if ($fn->debit_balance($type, $type_id, $new_balance)) {
                    // store wallet transaction
                    if ($type == 'seller') {
                        $fn->add_wallet_transaction($order_id, $order_item_id, $type_id, $type, $amount, $message, 'seller_wallet_transactions');
                    }
                    // store withdrawal request
                    if ($fn->store_withdrawal_request($type, $type_id, $amount, $message)) {
                        $sql = "select balance from seller where id=$type_id";
                        $db->sql($sql);
                        $res = $db->getResult();
                        $response['error'] = false;
                        $response['message'] = 'Withdrawal request accepted successfully!please wait for confirmation.';
                        $response['updated_balance'] = $res[0]['balance'];
                    } else {
                        $response['error'] = true;
                        $response['message'] = 'Something went wrong please try again later!';
                    }
                } else {
                    $response['error'] = true;
                    $response['message'] = 'Something went wrong please try again later!';
                }
            } else {
                $response['error'] = true;
                $response['message'] = 'Insufficient balance';
            }
        } else {
            $response['error'] = true;
            $response['message'] = 'No such ' . $type . ' exists';
        }
    } else {
        $response['error'] = true;
        $response['message'] = 'Please pass all the fields!';
    }
    print_r(json_encode($response));
    return false;
}

if (isset($_POST['get_requests']) && $_POST['get_requests'] == 1) {
    /*
    21.get_requests
        accesskey:90336
        get_requests:1
        type:seller
        type_id:3
        offset:0    // {optional}
        limit:5     // {optional}
    */

    $type  = $db->escapeString($fn->xss_clean($_POST['type']));
    $type_id = $db->escapeString($fn->xss_clean($_POST['type_id']));
    $limit = (isset($_POST['limit']) && !empty($_POST['limit']) && is_numeric($_POST['limit'])) ? $db->escapeString($fn->xss_clean($_POST['limit'])) : 10;
    $offset = (isset($_POST['offset']) && !empty($_POST['offset']) && is_numeric($_POST['offset'])) ? $db->escapeString($fn->xss_clean($_POST['offset'])) : 0;
    if (!empty($type) && !empty($type_id)) {
        $result = $fn->is_records_exists($type, $type_id, $offset, $limit);
        if (!empty($result)) {
            /* if records found return data */
            $sql = "SELECT count(id) as total from withdrawal_requests where `type` = '" . $type . "' AND `type_id` = " . $type_id;
            $db->sql($sql);
            $total = $db->getResult();
            $response['error'] = false;
            $response['total'] = $total[0]['total'];
            $response['data'] = array_values($result);
        } else {
            $response['error'] = true;
            $response['message'] = "Data does't exists!";
        }
    } else {
        $response['error'] = true;
        $response['message'] = 'Please pass all the fields!';
    }

    print_r(json_encode($response));
    return false;
}

if (isset($_POST['update_seller_profile']) && $_POST['update_seller_profile'] == 1) {
    if (ALLOW_MODIFICATION == 0 && !defined(ALLOW_MODIFICATION)) {
        $response['error'] = true;
        $response['message'] = "This operation is not allowed in demo panel!.!";
        print_r(json_encode($response));
        return false;
    }
    /* 
    {optional -> if not added }
    22.update_seller_profile   
        accesskey:90336
        update_seller_profile:1
        seller_id:1
        name:ekart seller  
        store_name:ekart seller store  
        email:infinitietechnologies03@gmail.com
        tax_name:GST
        tax_number:GST6754321
        pan_number:GNU12345
        status: 0 -> Deactivated, 1-> Activated/Approved  // {optional}
        store_url:https://www.store.com            // {optional}
        description:values                        // {optional}
        street:street1                         // {optional}
        pincode_id:1                              // {optional}
        state:gujarat                             // {optional}
        account_number:123456789265421                   // {optional}
        ifsc_code:DFG34557WD                      // {optional}
        account_name:ekart seller                       // {optional}
        bank_name:SBI                             // {optional}
        old_password:                             // {optional}
        update_password:                          // {optional}
		confirm_password:                         // {optional}
		store_logo: image_file  { jpg, png, gif, jpeg } // {optional -> do not set if no change}
		national_id_card: image_file  { jpg, png, gif, jpeg } // {optional -> do not set if no change}
		address_proof: image_file  { jpg, png, gif, jpeg }  // {optional -> do not set if no change}
		latitude:value                       // {optional}
		longitude:value                         // {optional}
       
    */
    $res_msg = "";
    $res_msg .= (empty($_POST['seller_id']) || $_POST['seller_id'] == "") ? "seller_id," : "";
    $res_msg .= (empty($_POST['name']) || $_POST['name'] == "") ? "name," : "";
    $res_msg .= (empty($_POST['store_name']) || $_POST['store_name'] == "") ? "store_name," : "";
    $res_msg .= (empty($_POST['email']) || $_POST['email'] == "") ? "email," : "";
    $res_msg .= (empty($_POST['tax_name']) || $_POST['tax_name'] == "") ? "tax_name," : "";
    $res_msg .= (empty($_POST['tax_number']) || $_POST['name'] == "") ? "tax_number," : "";
    $res_msg .= (empty($_POST['pan_number']) || $_POST['name'] == "") ? "pan_number," : "";
    if ($res_msg != "") {
        $response['error'] = true;
        $response['message'] = "This fields " . trim($res_msg, ",") . " should be Passed!";
        print_r(json_encode($response));
        return false;
        exit();
    }
    $id = $db->escapeString($fn->xss_clean($_POST['seller_id']));
    $name = $db->escapeString($fn->xss_clean($_POST['name']));
    $store_name = $db->escapeString($fn->xss_clean($_POST['store_name']));
    $email = $db->escapeString($fn->xss_clean($_POST['email']));
    $tax_name = $db->escapeString($fn->xss_clean($_POST['tax_name']));
    $tax_number = $db->escapeString($fn->xss_clean($_POST['tax_number']));
    $pan_number = $db->escapeString($fn->xss_clean($_POST['pan_number']));

    $status = (isset($_POST['status']) && $_POST['status'] != "") ? $db->escapeString($fn->xss_clean($_POST['status'])) : "2";
    $store_url = (isset($_POST['store_url']) && $_POST['store_url'] != "") ? $db->escapeString($fn->xss_clean($_POST['store_url'])) : "";
    $store_description = (isset($_POST['description']) && $_POST['description'] != "") ? $db->escapeString($fn->xss_clean($_POST['description'])) : "";
    $street = (isset($_POST['street']) && $_POST['street'] != "") ? $db->escapeString($fn->xss_clean($_POST['street'])) : "";
    $pincode_id = (isset($_POST['pincode_id']) && $_POST['pincode_id'] != "") ? $db->escapeString($fn->xss_clean($_POST['pincode_id'])) : "0";
    $state = (isset($_POST['state']) && $_POST['state'] != "") ? $db->escapeString($fn->xss_clean($_POST['state'])) : "";
    $account_number = (isset($_POST['account_number']) && $_POST['account_number'] != "") ? $db->escapeString($fn->xss_clean($_POST['account_number'])) : "";
    $bank_ifsc_code = (isset($_POST['ifsc_code']) && $_POST['ifsc_code'] != "") ? $db->escapeString($fn->xss_clean($_POST['ifsc_code'])) : "";
    $account_name = (isset($_POST['account_name']) && $_POST['account_name'] != "") ? $db->escapeString($fn->xss_clean($_POST['account_name'])) : "";
    $bank_name = (isset($_POST['bank_name']) && $_POST['bank_name'] != "") ? $db->escapeString($fn->xss_clean($_POST['bank_name'])) : "";
    $latitude = (isset($_POST['latitude']) && $_POST['latitude'] != "") ? $db->escapeString($fn->xss_clean($_POST['latitude'])) : "0";
    $longitude = (isset($_POST['longitude']) && $_POST['longitude'] != "") ? $db->escapeString($fn->xss_clean($_POST['longitude'])) : "0";


    $old_password = (isset($_POST['old_password']) && !empty(trim($_POST['old_password']))) ? $db->escapeString(trim($fn->xss_clean($_POST['old_password']))) : "";
    $update_password = (isset($_POST['update_password']) && !empty(trim($_POST['update_password']))) ? $db->escapeString(trim($fn->xss_clean($_POST['update_password']))) : "";
    $confirm_password = (isset($_POST['confirm_password']) && !empty(trim($_POST['confirm_password']))) ? $db->escapeString(trim($fn->xss_clean($_POST['confirm_password']))) : "";
    $change_password = false;


    /* check if id is not empty and there is valid data in it */
    if (!isset($_POST['seller_id']) || empty(trim($_POST['seller_id'])) || !is_numeric($_POST['seller_id'])) {
        $response['error'] = true;
        $response['message'] = "Invalid Id of Seller";
        print_r(json_encode($response));
        return false;
        exit();
    }

    $sql = "SELECT * from seller where id='$id'";
    $db->sql($sql);
    $res_id = $db->getResult();
    $num = $db->numRows($res_id);
    if ($num != 1) {
        $response['error'] = true;
        $response['message'] = "Seller is not Registered.";
        print_r(json_encode($response));
        return false;
        exit();
    }
    if (!empty($res_id) && ($res_id[0]['status'] == 2 || $res_id[0]['status'] == 7)) {
        $response['error'] = true;
        $response['message'] = "Seller can not update becasue you have not-approoved or removed.";
        print_r(json_encode($response));
        return false;
        exit();
    }

    /* if any of the password field is set and old password is not set */
    if ((!empty($confirm_password) || !empty($update_password)) && empty($old_password)) {
        $response['error'] = true;
        $response['message'] = "Please enter old password.";
        print_r(json_encode($response));
        return false;
        exit();
    }

    /* either of the password field is not empty and is they don't match */
    if ((!empty($confirm_password) || !empty($update_password)) && ($update_password != $confirm_password)) {
        $response['error'] = true;
        $response['message'] = "Password and Confirm Password mismatched.";
        print_r(json_encode($response));
        return false;
        exit();
    }

    /* when all conditions are met check for old password in database */
    if (!empty($confirm_password) && !empty($update_password) && !empty($old_password)) {
        $old_password = md5($old_password);
        $sql = "Select password from `seller` where id = '$id' and password = '$old_password' ";
        $db->sql($sql);
        $res = $db->getResult();

        if (empty($res)) {
            $response['error'] = true;
            $response['message'] = "Old password mismatched.";
            print_r(json_encode($response));
            return false;
            exit();
        }
        $change_password = true;
        $confirm_password = md5($confirm_password);
    }


    if (!empty($change_password)) {
        $sql = "UPDATE `seller` SET `name`='$name',`store_name`='$store_name',`email`='$email',`password`='$confirm_password',`store_url`='$store_url',`store_description`='$store_description',`street`='$street',`pincode_id`='$pincode_id',`state`='$state',`account_number`='$account_number',`bank_ifsc_code`='$bank_ifsc_code',`account_name`='$account_name',`bank_name`='$bank_name',`latitude`='$latitude',`longitude`='$longitude',`status`=$status,`pan_number`='$pan_number',`tax_name`='$tax_name',`tax_number`='$tax_number' WHERE id=" . $id;
    } else {
        $sql = "UPDATE `seller` SET `name`='$name',`store_name`='$store_name',`email`='$email',`store_url`='$store_url',`store_description`='$store_description',`street`='$street',`pincode_id`='$pincode_id',`state`='$state',`account_number`='$account_number',`bank_ifsc_code`='$bank_ifsc_code',`account_name`='$account_name',`bank_name`='$bank_name',`latitude`='$latitude',`longitude`='$longitude',`status`=$status,`pan_number`='$pan_number',`tax_name`='$tax_name',`tax_number`='$tax_number' WHERE id=" . $id;
    }

    if ($db->sql($sql)) {

        if (isset($_FILES['store_logo']) && $_FILES['store_logo']['size'] != 0 && $_FILES['store_logo']['error'] == 0 && !empty($_FILES['store_logo'])) {
            //image isn't empty and update the image
            $old_logo = $res_id[0]['logo'];
            $extension = pathinfo($_FILES["store_logo"]["name"])['extension'];

            $result = $fn->validate_image($_FILES["store_logo"]);
            if ($result) {
                $response['error'] = true;
                $response['message'] = "Store logo image type must jpg, jpeg, gif, or png!.";
                return false;
                exit();
            }
            $target_path = '../../upload/seller/';
            $filename = microtime(true) . '.' . strtolower($extension);
            $full_path = $target_path . "" . $filename;
            if (!move_uploaded_file($_FILES["store_logo"]["tmp_name"], $full_path)) {
                $response['error'] = true;
                $response['message'] = "Can not upload image.";
                return false;
                exit();
            }
            if (!empty($old_logo)) {
                unlink($target_path . $old_logo);
            }
            $sql = "UPDATE seller SET `logo`='" . $filename . "' WHERE `id`=" . $id;
            $db->sql($sql);
        }
        if (isset($_FILES['national_id_card']) && $_FILES['national_id_card']['size'] != 0 && $_FILES['national_id_card']['error'] == 0 && !empty($_FILES['national_id_card'])) {
            //image isn't empty and update the image
            $old_national_identity_card = $res_id[0]['national_identity_card'];
            $extension = pathinfo($_FILES["national_id_card"]["name"])['extension'];

            $result = $fn->validate_image($_FILES["national_id_card"]);
            if ($result) {
                $response['error'] = true;
                $response['message'] = "National id card image type must jpg, jpeg, gif, or png!.";
                return false;
                exit();
            }
            $target_path = '../../upload/seller/';
            $national_id_card = microtime(true) . '.' . strtolower($extension);
            $full_path = $target_path . "" . $national_id_card;
            if (!move_uploaded_file($_FILES["national_id_card"]["tmp_name"], $full_path)) {
                $response['error'] = true;
                $response['message'] = "Can not upload image.";
                return false;
                exit();
            }
            if (!empty($old_national_identity_card)) {
                unlink($target_path . $old_national_identity_card);
            }
            $sql = "UPDATE seller SET `national_identity_card`='" . $national_id_card . "' WHERE `id`=" . $id;
            $db->sql($sql);
        }
        if (isset($_FILES['address_proof']) && $_FILES['address_proof']['size'] != 0 && $_FILES['address_proof']['error'] == 0 && !empty($_FILES['address_proof'])) {
            //image isn't empty and update the image
            $old_address_proof = $res_id[0]['address_proof'];;
            $extension = pathinfo($_FILES["address_proof"]["name"])['extension'];

            $result = $fn->validate_image($_FILES["address_proof"]);
            if ($result) {
                $response['error'] = true;
                $response['message'] = "Address proof card image type must jpg, jpeg, gif, or png!.";;
                return false;
                exit();
            }
            $target_path = '../../upload/seller/';
            $address_proof = microtime(true) . '.' . strtolower($extension);
            $full_path = $target_path . "" . $address_proof;
            if (!move_uploaded_file($_FILES["address_proof"]["tmp_name"], $full_path)) {
                $response['error'] = true;
                $response['message'] = "Can not upload image.";
                return false;
                exit();
            }
            if (!empty($old_address_proof)) {
                unlink($target_path . $old_address_proof);
            }
            $sql = "UPDATE seller SET `address_proof`='" . $address_proof . "' WHERE `id`=" . $id;
            $db->sql($sql);
        }
        $response['error'] = false;
        $response['message'] = "Information Updated Successfully.";
        $response['message'] .= ($change_password) ? " and password also updated successfully." : "";
    } else {
        $response['error'] = true;
        $response['message'] = "Some Error Occurred! Please Try Again.";
    }
    print_r(json_encode($response));
}

/*
3.get_delivery_boys
    accesskey:90336
    get_delivery_boys:1
*/
if (isset($_POST['get_delivery_boys']) && !empty($_POST['get_delivery_boys'] == 1)) {

    $sql = "SELECT count(id) as total FROM `delivery_boys`";
    $db->sql($sql);
    $total = $db->getResult();

    $sql = "SELECT * FROM delivery_boys ORDER BY id DESC";
    $db->sql($sql);
    $res1 = $db->getResult();

    if (!empty($res1)) {
        for ($i = 0; $i < count($res1); $i++) {
            $res1[$i]['driving_license'] = !empty($res1[$i]['driving_license'])  ?  DOMAIN_URL . 'upload/delivery-boy/' . $res1[$i]['driving_license'] : "";
            $res1[$i]['national_identity_card'] = !empty($res1[$i]['national_identity_card'])  ?  DOMAIN_URL . 'upload/delivery-boy/' . $res1[$i]['national_identity_card'] : "";
        }
        $response['error'] = false;
        $response['message'] = "Delivery boys retrieved successfully";
        $response['total'] = $total[0]['total'];
        $response['data'] = $res1;
    } else {
        $response['error'] = true;
        $response['message'] = "No data found!";
    }
    print_r(json_encode($response));
    return false;
}


