<?php
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Access-Control-Allow-Origin: *');


include('../includes/crud.php');
include('../includes/custom-functions.php');
include('verify-token.php');
$fn = new custom_functions();
$db = new Database();
$db->connect();

$config = $fn->get_configurations();
$time_slot_config = $fn->time_slot_config();
if (isset($config['system_timezone']) && isset($config['system_timezone_gmt'])) {
    date_default_timezone_set($config['system_timezone']);
    $db->sql("SET `time_zone` = '" . $config['system_timezone_gmt'] . "'");
} else {
    date_default_timezone_set('Asia/Kolkata');
    $db->sql("SET `time_zone` = '+05:30'");
}
include('../includes/variables.php');


/* 
-------------------------------------------
APIs for Multi Vendor
-------------------------------------------
1. get_all_products
2. get_products_offline
3. get_variants_offline
4. get_similar_products
5. products_search
6. get_all_products_name
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
}

if (isset($_POST['get_all_products']) && $_POST['get_all_products'] == 1) {
    /* 
    1.get_all_products
        accesskey:90336
        get_all_products:1
        pincode_id:1 // {optional}
        pincode:5           // {optional}
        product_id:219      // {optional}
        user_id:1782        // {optional}
        seller_id:1         // {optional}
        category_id:29      // {optional}
        subcategory_id:132  // {optional}
        limit:5             // {optional}
        offset:1            // {optional}
        search:dhosa        // {optional}
        slug:pizza-1        // {optional}
        seller_slug:ekart-seller-store //{optional}
        sort:new / old / high / low  // {optional}
    */

    $limit = (isset($_POST['limit']) && !empty($_POST['limit']) && is_numeric($_POST['limit'])) ? $db->escapeString($fn->xss_clean($_POST['limit'])) : 10;
    $offset = (isset($_POST['offset']) && !empty($_POST['offset']) && is_numeric($_POST['offset'])) ? $db->escapeString($fn->xss_clean($_POST['offset'])) : 0;

    $sort = (isset($_POST['sort']) && !empty($_POST['sort'])) ? $db->escapeString($fn->xss_clean($_POST['sort'])) : 'id';

    $product_id = (isset($_POST['product_id']) && !empty($_POST['product_id'])) ? $db->escapeString($fn->xss_clean($_POST['product_id'])) : "";
    $user_id = (isset($_POST['user_id']) && !empty($_POST['user_id'])) ? $db->escapeString($fn->xss_clean($_POST['user_id'])) : "";
    $pincode_id = (isset($_POST['pincode']) && !empty($_POST['pincode'])) ? $db->escapeString($fn->xss_clean($_POST['pincode'])) : "";
    $pincode = (isset($_POST['pincode_id']) && !empty($_POST['pincode_id'])) ? $db->escapeString($fn->xss_clean($_POST['pincode_id'])) : "";

    $category_id = (isset($_POST['category_id']) && !empty($_POST['category_id'])) ? $db->escapeString($fn->xss_clean($_POST['category_id'])) : "";
    $subcategory_id = (isset($_POST['subcategory_id']) && $_POST['subcategory_id'] != "") ? $db->escapeString($fn->xss_clean($_POST['subcategory_id'])) : "0";
    $seller_id = (isset($_POST['seller_id']) && !empty($_POST['seller_id'])) ? $db->escapeString($fn->xss_clean($_POST['seller_id'])) : "";

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

    $is_pincode = $fn->get_data($column = ['pincode'], "pincode=" . $pincode_id, "pincodes");
    if (empty($is_pincode)) {
        $response['error'] = true;
        $response['message'] = "Invalid Pincode passed.";
        print_r(json_encode($response));
        return false;
        exit();
    }
$where = "";
    if (isset($_POST['search']) && $_POST['search'] != '') {
        $search = $db->escapeString($fn->xss_clean($_POST['search']));
        $where .= " AND p.`id` like '%" . $search . "%' OR p.`name` like '%" . $search . "%' OR s.`name` like '%" . $search . "%' OR p.`subcategory_id` like '%" . $search . "%' OR p.`category_id` like '%" . $search . "%' OR p.`slug` like '%" . $search . "%' OR p.`description` like '%" . $search . "%' ";
    }

    if (isset($_POST['product_id']) && !empty($_POST['product_id']) && is_numeric($_POST['product_id'])) {
        $where .= " AND p.`id` = " . $product_id;
    }
    
    if (isset($_POST['seller_slug']) && !empty($_POST['seller_slug'])) {
        $seller_slug = $db->escapeString($fn->xss_clean($_POST['seller_slug']));
        $where .= " AND s.`slug` =  '$seller_slug' ";
    }
    if (isset($_POST['slug']) && !empty($_POST['slug'])) {
        $slug = $db->escapeString($fn->xss_clean($_POST['slug']));
        $where .= " AND p.`slug` =  '$slug' ";
    }

    if (isset($_POST['seller_id']) && !empty($_POST['seller_id']) && is_numeric($_POST['seller_id'])) {
        $where .= " AND p.`seller_id` = " . $seller_id;
    }

    if (isset($_POST['category_id']) && !empty($_POST['category_id']) && is_numeric($_POST['category_id'])) {
        $where .= " AND p.`category_id`=" . $category_id;
    }
    if (isset($_POST['subcategory_id']) && $_POST['subcategory_id'] != "" && is_numeric($_POST['subcategory_id'])) {
        $where .=  " AND p.`subcategory_id`=" . $subcategory_id;
    }
    if (isset($_POST['pincode_id']) && $_POST['pincode_id'] != "" && is_numeric($_POST['pincode_id'])) {
        if ( !empty($where) ) {
            $where .=  " AND ((p.type='included' and FIND_IN_SET('$pincode', p.pincodes)) or p.type = 'all') OR ((p.type='excluded' and NOT FIND_IN_SET('$pincode', p.pincodes)) $where)";
        }else{
            $where .=  " AND ((p.type='included' and FIND_IN_SET('$pincode', p.pincodes)) or p.type = 'all') OR ((p.type='excluded' and NOT FIND_IN_SET('$pincode', p.pincodes)) )";
        }
    }

    $sql = "SELECT count(p.id) as total FROM `products` p LEFT JOIN `seller` s ON s.id=p.seller_id WHERE p.is_approved = 1 AND p.status = 1 AND s.status = 1 $where ";
    $db->sql($sql);
    $total = $db->getResult();

    $sql = "SELECT p.*,p.type as d_type, s.name as seller_name,s.slug as seller_slug,s.status as seller_status,(SELECT " . $price . " FROM product_variant pv WHERE pv.product_id=p.id) as price FROM `products` p JOIN `seller` s ON s.id=p.seller_id WHERE p.is_approved = 1 AND p.status = 1 AND s.status = 1 $where ORDER BY $sort LIMIT $offset,$limit ";
    $db->sql($sql);
    $res = $db->getResult();

    if (!empty($pincode_id) || $pincode_id != "") {
        if (!empty($product_id)) {
            $sql = "SELECT * from products where id=$product_id";
            $db->sql($sql);
            $res_delivery = $db->getResult();
            foreach ($res_delivery as $row) {
                $pincodes = ($row['type'] == "all") ? "" : $row['pincodes'];
                $sql = "SELECT pincode FROM `pincodes` where id IN($pincodes)";
                $db->sql($sql);
                $res_pincodes = $db->getResult();
                $pincodes = implode(",", array_column($res_pincodes, "pincode"));
                if ($row['type'] == "all") {
                    $response['error'] = false;
                } else if ($row['type'] == "included") {
                    if (strpos($pincodes, $pincode_id) !== false) {
                        $response['error'] = false;
                    } else {
                        $response['error'] = true;
                    }
                } else if ($row['type'] == "excluded") {
                    if (strpos($pincodes, $pincode_id) !== false) {
                        $response['error'] = true;
                    } else {
                        $response['error'] = false;
                    }
                } else {
                    $response['error'] = false;
                }
            }
            $response['message'] = "Pincodes checked successfully";
            print_r(json_encode($response));
            return false;
        }else{
            $response['error'] = true;
            $response['message'] = "Please pass Product id for pincode checking.";
            print_r(json_encode($response));
            return false;
        }
    }

    $product = array();
    $i = 0;
    $sql = "SELECT id FROM cart limit 1";
    $db->sql($sql);
    $res_cart = $db->getResult();


    foreach ($res as $row) {
        $sql = "SELECT *,(SELECT short_code FROM unit u WHERE u.id=pv.measurement_unit_id) as measurement_unit_name,(SELECT short_code FROM unit u WHERE u.id=pv.stock_unit_id) as stock_unit_name FROM product_variant pv WHERE pv.product_id=" . $row['id'] . " ORDER BY `pv`.`serve_for` ASC";
        $db->sql($sql);
        $variants = $db->getResult();
        if (empty($variants)) {
            continue;
        }
        if(!empty($pincode) ){
            $res_pincode = $fn->get_data($column=['pincode'],"id=".$pincode , 'pincodes');
            $row['deliverable_area'] = $res_pincode[0]['pincode'];
        }else{
            $row['deliverable_area'] = "";
        }

        if (!empty($pincode_id) || $pincode_id != "") {
            $pincodes = ($row['d_type'] == "all") ? "" : $row['d_type'];
            if ($pincodes != "") {
                $sql = "SELECT pincode FROM `pincodes` where id IN($pincodes)";
                $db->sql($sql);
                $res_pincodes = $db->getResult();
                $pincodes = implode(",", array_column($res_pincodes, "pincode"));
            }

            if ($row['d_type'] == "all") {
                $row['is_item_deliverable'] = false;
            } else if ($row['d_type'] == "included") {
                if (strpos($pincodes, $pincode_id) !== false) {
                    $row['is_item_deliverable']  = false;
                } else {
                    $row['is_item_deliverable']  = true;
                }
            } else if ($row['d_type'] == "excluded") {
                if (strpos($pincodes, $pincode_id) !== false) {
                    $row['is_item_deliverable']  = true;
                } else {
                    $row['is_item_deliverable']  = false;
                }
            }
        } else {
            $row['is_item_deliverable'] = false;
        }

        unset($row['type']);
        $row['seller_name'] = !empty($row['seller_name']) ? $row['seller_name'] : "";
        $row['pincodes'] = (isset($row['pincodes']) == null)  ? "" : $row['pincodes'];
        $row['is_approved'] = (isset($row['is_approved']) == null)  ? "" : $row['is_approved'];
        $row['seller_id'] = (isset($row['seller_id']) == null)  ? "" : $row['seller_id'];

        $row['other_images'] = json_decode($row['other_images'], 1);
        $row['other_images'] = (empty($row['other_images'])) ? array() : $row['other_images'];
        for ($j = 0; $j < count($row['other_images']); $j++) {
            $row['other_images'][$j] = DOMAIN_URL . $row['other_images'][$j];
        }

        $row['image'] = DOMAIN_URL . $row['image'];
        if ($row['tax_id'] == 0) {
            $row['tax_title'] = "";
            $row['tax_percentage'] = "0";
        } else {
            $t_id = $row['tax_id'];
            $sql_tax = "SELECT * from taxes where id= $t_id";
            $db->sql($sql_tax);
            $res_tax1 = $db->getResult();
            foreach ($res_tax1 as $tax1) {
                $row['tax_title'] = (!empty($tax1['title'])) ? $tax1['title'] : "";
                $row['tax_percentage'] =  (!empty($tax1['percentage'])) ? $tax1['percentage'] : "0";
            }
        }
        if (!empty($user_id)) {
            $sql = "SELECT id from favorites where product_id = " . $row['id'] . " AND user_id = " . $user_id;
            $db->sql($sql);
            $favorite = $db->getResult();
            if (!empty($favorite)) {
                $row['is_favorite'] = true;
            } else {
                $row['is_favorite'] = false;
            }
        } else {
            $row['is_favorite'] = false;
        }

        $product[$i] = $row;

        for ($k = 0; $k < count($variants); $k++) {
            if ($variants[$k]['stock'] <= 0) {
                $variants[$k]['serve_for'] = 'Sold Out';
            } else {
                $variants[$k]['serve_for'] = $variants[$k]['serve_for'];
            }


            if (!empty($user_id)) {
                $sql = "SELECT qty as cart_count FROM cart where product_variant_id= " . $variants[$k]['id'] . " AND user_id=" . $user_id;
                $db->sql($sql);
                $res = $db->getResult();
                if (!empty($res)) {
                    foreach ($res as $row1) {
                        $variants[$k]['cart_count'] = $row1['cart_count'];
                    }
                } else {
                    $variants[$k]['cart_count'] = "0";
                }
            } else {
                $variants[$k]['cart_count'] = "0";
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
        $response['message'] = "No products available.";
    }
    print_r(json_encode($response));
    return false;
}

if (isset($_POST['get_products_offline']) && $_POST['get_products_offline'] == 1 && isset($_POST['product_ids']) && !empty($_POST['product_ids'])) {
    /* 
    2.get_products_offline
        accesskey:90336
        get_products_offline:1
        product_ids:214,215 
        slug:mixed-fruit-1        // {optional}
    */

    $product_ids = $db->escapeString($fn->xss_clean($_POST['product_ids']));
    $where = "";
    if (isset($_POST['slug']) && !empty($_POST['slug'])) {
        $slug = $db->escapeString($fn->xss_clean($_POST['slug']));
        $where = " AND p.`slug` =  '$slug' ";
    }
    $sql = "SELECT  count(p.id) as total FROM `products` p JOIN `seller`s ON s.id=p.seller_id WHERE p.is_approved = 1 AND p.status = 1 AND s.status = 1 AND p.id IN ($product_ids) " .$where;
    $db->sql($sql);
    $total = $db->getResult();

    $sql = "SELECT p.*,s.name as seller_name,s.status as seller_status FROM `products` p JOIN `seller`s ON s.id=p.seller_id WHERE p.is_approved = 1 AND p.status = 1 AND s.status = 1 AND p.id IN ($product_ids)" .$where;
    $db->sql($sql);
    $res = $db->getResult();
    $product = array();
    $i = 0;

    foreach ($res as $row) {
        $sql = "SELECT *,(SELECT short_code FROM unit u WHERE u.id=pv.measurement_unit_id) as measurement_unit_name,(SELECT short_code FROM unit u WHERE u.id=pv.stock_unit_id) as stock_unit_name FROM product_variant pv WHERE pv.product_id=" . $row['id'] . " ORDER BY serve_for ASC";
        $db->sql($sql);
        $variants = $db->getResult();
        if (empty($variants)) {
            continue;
        }
        $row['type'] = (isset($row['type']) == null)  ? "" : $row['type'];
        $row['pincodes'] = (isset($row['pincodes']) == null)  ? "" : $row['pincodes'];
        $row['is_approved'] = (isset($row['is_approved']) == null)  ? "" : $row['is_approved'];
        $row['seller_id'] = (isset($row['seller_id']) == null)  ? "" : $row['seller_id'];
        $row['seller_name'] = (isset($row['seller_name']) == null)  ? "" : $row['seller_name'];

        $row['other_images'] = json_decode($row['other_images'], 1);
        $row['other_images'] = (empty($row['other_images'])) ? array() : $row['other_images'];

        for ($j = 0; $j < count($row['other_images']); $j++) {
            $row['other_images'][$j] = DOMAIN_URL . $row['other_images'][$j];
        }

        if ($row['tax_id'] == 0 || $row['tax_id'] == "") {
            $row['tax_title'] = "";
            $row['tax_percentage'] = "0";
        } else {
            $t_id = $row['tax_id'];
            $sql_tax = "SELECT * from taxes where id= $t_id";
            $db->sql($sql_tax);
            $res_tax = $db->getResult();
            foreach ($res_tax as $tax) {
                $row['tax_title'] = $tax['title'];
                $row['tax_percentage'] = $tax['percentage'];
            }
        }

        for ($k = 0; $k < count($variants); $k++) {
            if ($variants[$k]['stock'] <= 0) {
                $variants[$k]['serve_for'] = 'Sold Out';
            } else {
                $variants[$k]['serve_for'] = 'Available';
            }
            $variants[$k]['cart_count'] = "0";
        }
        $row['is_favorite'] = false;

        $row['image'] = DOMAIN_URL . $row['image'];
        $product[$i] = $row;
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
        $response['message'] = "No products available";
    }
    print_r(json_encode($response));
    return false;
}

if (isset($_POST['get_variants_offline']) && $_POST['get_variants_offline'] == 1 && isset($_POST['variant_ids']) && !empty($_POST['variant_ids'])) {
    /* 
    3.get_variants_offline
        accesskey:90336
        get_variants_offline:1
        variant_ids:55,56
        pincode_id:1    //{optional}
    */

    $variant_ids = $db->escapeString($fn->xss_clean($_POST['variant_ids']));
    $pincode_id  = (isset($_POST['pincode_id']) && !empty($_POST['pincode_id'])) ? $db->escapeString($fn->xss_clean_array($_POST['pincode_id'])) : "";

    $where = "";
    if (isset($_POST['slug']) && !empty($_POST['slug'])) {
        $slug = $db->escapeString($fn->xss_clean($_POST['slug']));
        $where = " AND p.`slug` = '$slug' ";
    }

    $sql = "SELECT  count(pv.id) as total FROM product_variant pv JOIN products p ON p.id=pv.product_id JOIN seller s ON s.id=p.seller_id where pv.id IN ($variant_ids) and p.is_approved = 1 AND p.status = 1 AND s.status = 1 " . $where;
    $db->sql($sql);
    $total = $db->getResult();

    $sql = "SELECT pv.*,p.tax_id FROM product_variant pv JOIN products p ON p.id=pv.product_id where pv.id IN ($variant_ids)" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    $i = 0;
    $j = 0;

    foreach ($res as $row) {
        $sql = "select pv.*,p.*,s.name as seller_name,p.type as d_type,s.status as seller_status,pv.measurement,(select short_code from unit u where u.id=pv.measurement_unit_id) as unit from product_variant pv left join products p on p.id=pv.product_id JOIN seller s ON s.id=p.seller_id where pv.id=" . $row['id'];
        $db->sql($sql);
        $res[$i]['item'] = $db->getResult();

        for ($k = 0; $k < count($res[$i]['item']); $k++) {
            if (!empty($pincode_id) || $pincode_id != "") {
                $pincodes = ($res[$i]['item'][$k]['d_type'] == "all") ? "" :$res[$i]['item'][$k]['pincodes'];
                if ($res[$i]['item'][$k]['d_type'] == "all") {
                    $res[$i]['item'][$k]['is_item_deliverable'] = false;
                } else if ($res[$i]['item'][$k]['d_type'] == "included") {
                    if (strpos($pincodes, $pincode_id) !== false) {
                        $res[$i]['item'][$k]['is_item_deliverable']  = false;
                    } else {
                        $res[$i]['item'][$k]['is_item_deliverable']  = true;
                    }
                } else if ($res[$i]['item'][$k]['d_type'] == "excluded") {

                    if (strpos($pincodes, $pincode_id) !== false) {
                        $res[$i]['item'][$k]['is_item_deliverable']  = true;
                    } else { 
                        $res[$i]['item'][$k]['is_item_deliverable']  = false;
                    }
                }           
            } else {
                $res[$i]['item'][$k]['is_item_deliverable'] = false;
            }
            $res[$i]['item'][$k]['cart_count'] = "0";
            $res[$i]['item'][$k]['other_images'] = json_decode($res[$i]['item'][$k]['other_images']);
            $res[$i]['item'][$k]['other_images'] = empty($res[$i]['item'][$k]['other_images']) ? array() : $res[$i]['item'][$k]['other_images'];
            for ($l = 0; $l < count($res[$i]['item'][$k]['other_images']); $l++) {
                $other_images = DOMAIN_URL . $res[$i]['item'][$k]['other_images'][$l];
                $res[$i]['item'][$k]['other_images'][$l] = $other_images;
            }

            if ($row['tax_id'] == 0) {
                $res[$i]['item'][$k]['tax_title'] = "";
                $res[$i]['item'][$k]['tax_percentage'] = "0";
            } else {
                $t_id = $row['tax_id'];
                $sql_tax = "SELECT * from taxes where id= $t_id";
                $db->sql($sql_tax);
                $res_tax = $db->getResult();
                foreach ($res_tax as $tax) {
                    $res[$i]['item'][$k]['tax_title'] = $tax['title'];
                    $res[$i]['item'][$k]['tax_percentage'] = $tax['percentage'];
                }
            }
        }

        for ($j = 0; $j < count($res[$i]['item']); $j++) {
            $res[$i]['item'][$j]['image'] = !empty($res[$i]['item'][$j]['image']) ? DOMAIN_URL . $res[$i]['item'][$j]['image'] : "";
        }
        $i++;
    }
    if (!empty($res)) {
        $response['error'] = false;
        $response['message'] = "Product Varients retrived successfully!";
        $response['total'] = $total[0]['total'];
        $response['data'] = array_values($res);
    } else {
        $response['error'] = true;
        $response['message'] = "No item(s) found!";
    }
    print_r(json_encode($response));
    return false;
}

if (isset($_POST['get_similar_products']) && $_POST['get_similar_products'] == 1) {
    /*  
    4. get_similar_products
        accesskey:90336
        get_similar_products:1
        product_id:211
        category_id:28
        limit:6         // {optional}
        user_id:369     // {optional}
    */

    if (empty($_POST['product_id']) || empty($_POST['category_id'])) {
        $response['error'] = true;
        $response['message'] = "Missing arguments!";
        print_r(json_encode($response));
        return false;
    }

    $product_id = $db->escapeString($fn->xss_clean($_POST['product_id']));
    $category_id = $db->escapeString($fn->xss_clean($_POST['category_id']));
    $user_id = (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) ? $db->escapeString($fn->xss_clean($_POST['user_id'])) : "";
    $row1 = array();

    $limit = (isset($_POST['limit']) && !empty($_POST['limit']) && is_numeric($_POST['limit'])) ? $db->escapeString($fn->xss_clean($_POST['limit'])) : 6;
    $offset = 0;
    $order =  "RAND()";

    $sql = "SELECT count(p.id) as total FROM `products` p JOIN `seller`s ON s.id=p.seller_id where p.id != $product_id AND p.category_id = $category_id AND p.is_approved = 1 AND p.status = 1 and s.status = 1  ORDER BY $order LIMIT $offset,$limit";
    $db->sql($sql);
    $total1 = $db->getResult();

    $sql = "SELECT p.*,s.name as seller_name,s.status as seller_status,(SELECT MIN(pv.price) FROM product_variant pv WHERE pv.product_id=p.id) as price FROM products p  JOIN seller s on s.id=p.seller_id where p.id != $product_id and p.status=1  and p.is_approved = 1 and  s.status = 1 and category_id = $category_id ORDER BY $order LIMIT $offset,$limit";
    $db->sql($sql);
    $res = $db->getResult();

    if (!empty($res)) {
        foreach ($res as $row) {
            $tempRow['id'] = $row['id'];
            $tempRow['seller_id'] = $row['seller_id'];
            $tempRow['seller_name'] = $row['seller_name'];
            $tempRow['tax_id'] = $row['tax_id'];
            $tempRow['row_order'] = $row['row_order'];
            $tempRow['name'] = $row['name'];
            $tempRow['slug'] = $row['slug'];
            $tempRow['category_id'] = $row['category_id'];
            $tempRow['subcategory_id'] = $row['subcategory_id'];
            $tempRow['indicator'] = $row['indicator'];
            $tempRow['manufacturer'] = $row['manufacturer'];
            $tempRow['made_in'] = $row['made_in'];
            $tempRow['return_status'] = $row['return_status'];
            $tempRow['cancelable_status'] = $row['cancelable_status'];
            $tempRow['till_status'] = $row['till_status'];
            $tempRow['seller_status'] = $row['seller_status'];
            $tempRow['date_added'] = $row['date_added'];
            $tempRow['price'] = $row['price'];
            $tempRow['date_added'] = $row['date_added'];
            $tempRow['type'] = $row['type'];
            $tempRow['pincodes'] = $row['pincodes'];
            $tempRow['is_approved'] = $row['is_approved'];
            $tempRow['return_days'] = $row['return_days'];
            $tempRow['image'] = (!empty($row['image'])) ? DOMAIN_URL . '' . $row['image'] : '';

            if (!empty($row['other_images']) && $row['other_images'] != "") {
                $row['other_images'] = json_decode($row['other_images'], 1);
                for ($j = 0; $j < count($row['other_images']); $j++) {
                    $tempRow['other_images'][$j] = DOMAIN_URL . $row['other_images'][$j];
                }
            } else {
                $tempRow['other_images'] = array();
            }

            if ($row['tax_id'] == 0) {
                $tempRow['tax_title'] = "";
                $tempRow['tax_percentage'] = "0";
            } else {
                $t_id = $row['tax_id'];
                $sql_tax = "SELECT * from taxes where id= $t_id";
                $db->sql($sql_tax);
                $res_tax = $db->getResult();
                foreach ($res_tax as $tax) {
                    $tempRow['tax_title'] = $tax['title'];
                    $tempRow['tax_percentage'] = $tax['percentage'];
                }
            }

            if (!empty($user_id)) {
                $sql = "SELECT id from favorites where product_id = " . $row['id'] . " AND user_id = " . $user_id;
                $db->sql($sql);
                $result = $db->getResult();
                if (!empty($result)) {
                    $tempRow['is_favorite'] = true;
                } else {
                    $tempRow['is_favorite'] = false;
                }
            } else {
                $tempRow['is_favorite'] = false;
            }

            $tempRow['description'] = $row['description'];
            $tempRow['status'] = $row['status'];

            $sql1 = "SELECT *,(SELECT short_code FROM unit u WHERE u.id=pv.measurement_unit_id) as measurement_unit_name,(SELECT short_code FROM unit u WHERE u.id=pv.stock_unit_id) as stock_unit_name FROM product_variant pv WHERE pv.product_id=" . $row['id'] . " ORDER BY serve_for ASC";
            $db->sql($sql1);
            $variants = $db->getResult();
            if (empty($variants)) {
                continue;
            }
            for ($k = 0; $k < count($variants); $k++) {
                if (!empty($user_id)) {
                    $sql = "SELECT qty as cart_count FROM cart where product_variant_id= " . $variants[$k]['id'] . " AND user_id=" . $user_id;
                    $db->sql($sql);
                    $res = $db->getResult();
                    if (!empty($res)) {
                        foreach ($res as $row1) {
                            $variants[$k]['cart_count'] = $row1['cart_count'];
                        }
                    } else {
                        $variants[$k]['cart_count'] = "0";
                    }
                } else {
                    $variants[$k]['cart_count'] = "0";
                }
            }
            $tempRow['variants'] = $variants;
            $rows[] = $tempRow;
        }
        $response['error'] = false;
        $response['message'] = 'Product retrived successfully!';
        $response['total'] = $total1[0]['total'];
        $response['data'] = $rows;
    } else {
        $response['error'] = true;
        $response['message'] = 'Data not Found!';
    }
    print_r(json_encode($response));
    return false;
}

if (isset($_POST['type']) && $_POST['type'] == 'products_search') {
    /*  
    5. products_search
        accesskey:90336
	    type:products_search
	    search:Himalaya Baby Powder 
    */

    $limit = (isset($_POST['limit']) && !empty($_POST['limit']) && is_numeric($_POST['limit'])) ? $db->escapeString($fn->xss_clean($_POST['limit'])) : 10;
    $offset = (isset($_POST['offset']) && !empty($_POST['offset']) && is_numeric($_POST['offset'])) ? $db->escapeString($fn->xss_clean($_POST['offset'])) : 0;

    $sort = (isset($_POST['sort']) && !empty($_POST['sort'])) ? $db->escapeString($fn->xss_clean($_POST['sort'])) : "id";
    $order = (isset($_POST['order']) && !empty($_POST['order'])) ? $db->escapeString($fn->xss_clean($_POST['order'])) : "DESC";

    $where = '';
    if (isset($_POST['search']) && $_POST['search'] != '') {
        $search = $db->escapeString($fn->xss_clean($_POST['search']));
        $where = " AND (p.`id` like '%" . $search . "%' OR p.`name` like '%" . $search . "%' OR p.`image` like '%" . $search . "%' OR p.`subcategory_id` like '%" . $search . "%' OR p.`slug` like '%" . $search . "%' OR p.`description` like '%" . $search . "%')";
    }

    $user_id = (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) ? $db->escapeString($fn->xss_clean($_POST['user_id'])) : "";
    $sql = "SELECT COUNT(p.id) as total FROM `products`p JOIN `seller` s ON s.id=p.seller_id WHERE p.is_approved = 1 AND p.status = 1 AND s.status = 1 " .$where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row) {
        $total = $row['total'];
    }
    $sql = "SELECT p.*,s.name as seller_name,s.status as seller_status FROM `products`p JOIN seller s ON s.id=p.seller_id WHERE p.is_approved = 1 AND p.status = 1 AND s.status = 1 " . $where;
    $db->sql($sql);
    $res = $db->getResult();
    $product = array();
    $i = 0;

    foreach ($res as $row) {
        $sql = "SELECT *,(SELECT short_code FROM unit u WHERE u.id=pv.measurement_unit_id) as measurement_unit_name,(SELECT short_code FROM unit u WHERE u.id=pv.stock_unit_id) as stock_unit_name FROM product_variant pv WHERE pv.product_id=" . $row['id'] . " ORDER BY serve_for ASC";
        $db->sql($sql);
        $variants = $db->getResult();
        if (empty($variants)) {
            continue;
        }
        if (!empty($user_id)) {
            $sql = "SELECT id from favorites where product_id = " . $row['id'] . " AND user_id = " . $user_id;
            $db->sql($sql);
            $result = $db->getResult();
            if (!empty($result)) {
                $row['is_favorite'] = true;
            } else {
                $row['is_favorite'] = false;
            }
        } else {
            $row['is_favorite'] = false;
        }

        $row['type'] = (isset($row['type']) == null)  ? "" : $row['type'];
        $row['pincodes'] = (isset($row['pincodes']) == null)  ? "" : $row['pincodes'];
        $row['is_approved'] = (isset($row['is_approved']) == null)  ? "" : $row['is_approved'];
        $row['seller_id'] = (isset($row['seller_id']) == null)  ? "" : $row['seller_id'];

        $row['other_images'] = json_decode($row['other_images'], 1);
        $row['other_images'] = (empty($row['other_images'])) ? array() : $row['other_images'];
        for ($j = 0; $j < count($row['other_images']); $j++) {
            $row['other_images'][$j] = DOMAIN_URL . $row['other_images'][$j];
        }
        if ($row['tax_id'] == 0) {
            $row['tax_title'] = "";
            $row['tax_percentage'] = "0";
        } else {
            $t_id = $row['tax_id'];
            $sql_tax = "SELECT * from taxes where id= $t_id";
            $db->sql($sql_tax);
            $res_tax = $db->getResult();
            foreach ($res_tax as $tax) {
                $row['tax_title'] = $tax['title'];
                $row['tax_percentage'] = $tax['percentage'];
            }
        }
        $row['image'] = DOMAIN_URL . $row['image'];
        $product[$i] = $row;
        for ($k = 0; $k < count($variants); $k++) {
            if (!empty($user_id)) {
                $sql = "SELECT qty as cart_count FROM cart where product_variant_id= " . $variants[$k]['id'] . " AND user_id=" . $user_id;
                $db->sql($sql);
                $res = $db->getResult();
                if (!empty($res)) {
                    foreach ($res as $row1) {
                        $variants[$k]['cart_count'] = $row1['cart_count'];
                    }
                } else {
                    $variants[$k]['cart_count'] = "0";
                }
            } else {
                $variants[$k]['cart_count'] = "0";
            }
        }
        $product[$i]['variants'] = $variants;
        $i++;
    }
    if (empty($product)) {
        $response['error'] = true;
        $response['message'] = 'No Products available';
    } else {
        $response['error'] = false;
        $response['message'] = 'Products Retrived successfuly!';
        $response['total'] = $total;
        $response['data'] = array_values($product);
    }
    print_r(json_encode($response));
    return false;
}

if (isset($_POST['get_all_products_name']) && $_POST['get_all_products_name'] == 1) {
    /*  
    5. get_all_products_name
        accesskey:90336
        get_all_products_name:1
    */

    $sql = "SELECT name FROM `products`";
    $db->sql($sql);
    $res = $db->getResult();
    $rows = $tempRow = $blog_array = $blog_array1 = array();
    foreach ($res as $row) {
        $tempRow['name'] = $row['name'];
        $rows[] = $tempRow;
    }
    $names = array_column($rows, 'name');

    $pr_names = implode(",", $names);
    $response['error'] = false;
    $response['data'] = $pr_names;

    print_r(json_encode($response));
    return false;
}
