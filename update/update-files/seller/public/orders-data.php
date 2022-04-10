<?php
include_once('../includes/variables.php');
include_once('../includes/crud.php');
include_once('../includes/custom-functions.php');
$function = new custom_functions();
$currency = $function->get_settings('currency');

$ID = (isset($_GET['id'])) ? $db->escapeString($function->xss_clean($_GET['id'])) : "";
if (!isset($_SESSION['seller_id']) && !isset($_SESSION['seller_name'])) {
    header("location:index.php");
} else {
    $seller_id = $_SESSION['seller_id'];
}

// create array variable to handle error
// $update_order_permission = $permissions['orders']['update'];
$allowed = ALLOW_MODIFICATION;
$seller_name = "";

$error = array();

$sql = "SELECT oi.*,o.final_total as payable_total,oi.id as order_item_id,p.*,v.product_id, v.measurement,o.*,o.total as order_total,o.wallet_balance,o.otp,oi.active_status as oi_active_status,u.email,u.name as uname,u.country_code,p.name as pname,(SELECT short_code FROM unit un where un.id=v.measurement_unit_id)as mesurement_unit_name 
        FROM `order_items` oi
        JOIN users u ON u.id=oi.user_id
        JOIN product_variant v ON oi.product_variant_id=v.id
        JOIN products p ON p.id=v.product_id
        JOIN orders o ON o.id=oi.order_id
    WHERE o.id = $ID AND oi.seller_id = $seller_id";
$db->sql($sql);
$res = $db->getResult();
$items = [];
foreach ($res as $row) {
    $data = array($row['product_id'], $row['product_variant_id'], $row['pname'], $row['measurement'], $row['mesurement_unit_name'], $row['quantity'], $row['discounted_price'], $row['price'], $row['oi_active_status'], $row['cancelable_status'], $row['order_item_id'], $row['sub_total'], $row['tax_amount'], $row['tax_percentage'], $row['seller_id'], $row['delivery_boy_id']);
    array_push($items, $data);
}
?>
<style>
    @media (min-width: 992px) {
        .col-md-3 {
            width: 20% !important;
        }
    }
</style>
<section class="content-header">
    <h1>Order Detail</h1>
    <?php echo isset($error['update_data']) ? $error['update_data'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Order Detail</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-bordered">
                        <tr>
                            <input type="hidden" name="hidden" id="order_id" value="<?php echo $res[0]['id']; ?>">
                            <th style="width: 10px">ID</th>
                            <td><?php echo $res[0]['id']; ?></td>
                        </tr>
                        <tr>
                            <th style="width: 10px">Name</th>
                            <td><?= $res[0]['uname']; ?></td>
                        </tr>
                        <tr>
                            <th style="width: 10px">Email</th>
                            <td><?= ($fn->get_seller_permission($seller_id,'customer_privacy')) ? $res[0]['email'] : str_repeat("*", strlen($res[0]['email']) - 13) . substr($res[0]['email'], -13); ?></td>
                        </tr>
                        <tr>
                            <th style="width: 10px">Contact</th>
                            <td><?= ($fn->get_seller_permission($seller_id,'customer_privacy')) ? $res[0]['mobile'] : str_repeat("*", strlen($res[0]['mobile']) - 3) . substr($res[0]['mobile'], -3); ?></td>
                        </tr>
                        <tr>
                            <th style="width: 10px">O. Note</th>
                            <td><?php echo $res[0]['order_note']; ?></td>
                        </tr>
                        <tr>
                            <th style="width: 10px">Area</th>
                            <?php
                            if (!empty($res[0]['area_id'])) {
                                $area_id = $res[0]['area_id'];
                                $sql = "SELECT * FROM `area` WHERE id =$area_id";
                                $db->sql($sql);
                                $res_areas = $db->getResult();
                            } else {
                                $res_areas = array();
                            }
                            ?>
                            <td><?= (!empty($res_areas)) ? $res_areas[0]['name'] : "" ?></td>
                        </tr>
                        <tr>
                            <th style="width: 10px">Pincode</th>
                            <?php
                            $pincode_id = $res[0]['pincode_id'];
                            $sql = "SELECT * FROM `pincodes` WHERE id =$pincode_id";
                            $db->sql($sql);
                            $res_pincodes = $db->getResult();
                            ?>
                            <td><?= (!empty($res_pincodes)) ? $res_pincodes[0]['pincode'] : "" ?></td>
                        </tr>
                        <?php
                        if($fn->get_seller_permission($seller_id,'view_order_otp')){
                        ?>
                        <tr>
                            <th style="width: 10px">OTP</th>
                            <td><?= (isset($res[0]['otp']) && !empty($res[0]['otp'])) ? $res[0]['otp'] : "-" ?></td>
                        </tr>
                        <?php }?>

                        <tr>
                            <th style="width: 10px">Items</th>
                            <td>
                                <div class="container-fluid">
                                    <?php $total = 0;
                                    foreach ($items as $item) { ?>
                                        <div class="card col-md-3">
                                            <div class="card-body">
                                                <?php if ($item[8] == 'received') {
                                                    $active_status = '<label class="label label-primary">' . $item[8] . '</label>';
                                                }
                                                if ($item[8] == 'processed') {
                                                    $active_status = '<label class="label label-info">' . $item[8] . '</label>';
                                                }
                                                if ($item[8] == 'shipped') {
                                                    $active_status = '<label class="label label-warning">' . $item[8] . '</label>';
                                                }
                                                if ($item[8] == 'delivered') {
                                                    $active_status = '<label class="label label-success">' . $item[8] . '</label>';
                                                }
                                                if ($item[8] == 'returned' || $item[8] == 'cancelled') {
                                                    $active_status = '<label class="label label-danger">' . $item[8] . '</label>';
                                                }
                                                if ($item[8] == 'awaiting_payment') {
                                                    $active_status = '<label class="label label-secondary">Awaiting Payment</label>';
                                                }
                                                $array[] = $item[8];

                                                $total += $subtotal = ($item[6] != 0 && $item[6] < $item[7]) ? ($item[6] * $item[5]) : ($item[7] * $item[5]);
                                                echo   "<b>Product Id : </b>" . $item[0] . "  " . $active_status . "<br>";
                                                echo "<b> Product Variant Id : </b>" . $item[1] . "</br>";
                                                echo " <b>Name : </b>" . $item[2] . "</br>";
                                                echo " <b>Unit : </b>" . $item[3] . " " . $item[4] . "</br>";
                                                echo " <b>Quantity : </b>" . $item[5] . "</br>";
                                                echo " <b>Price(" . $currency . ") : </b>" . $item[7] . "</br>";
                                                echo " <b>Discounted Price(" . $currency . ") : </b>" . $item[6] . "</br>";
                                                echo " <b>Tax Amount(" . $currency . ") : </b>" . $item[12] . "</br>";
                                                echo " <b>Tax Percentage(%) : </b>" . $item[13] . "</br>";
                                                echo " <b>Subtotal(" . $currency . ") : </b>" . $item[11] . "  ";
                                                echo "<a href='view-product-variants.php?id=" . $item[0] . "' class='btn btn-success btn-xs' title='View Product'><i class='fa fa-eye'></i> Product</a> <br> <br>";
                                                ?>
                                                <select name="status" id="status" class="form-control status">
                                                    <option value="awaiting_payment" <?= ($item[8] == "awaiting_payment") ? 'selected' : ''; ?> data-value='<?= $item[0] ?>' data-value1='<?= $item[10] ?>'>Awaiting Payment</option>
                                                    <option value="received" <?= ($item[8] == "received") ? 'selected' : ''; ?> data-value='<?= $item[0] ?>' data-value1='<?= $item[10] ?>'>Received</option>
                                                    <option value="processed" <?= ($item[8] == "processed") ? 'selected' : ''; ?> data-value='<?= $item[0] ?>' data-value1='<?= $item[10] ?>'>Processed</option>
                                                    <option value="shipped" <?= ($item[8] == "shipped") ? 'selected' : ''; ?> data-value='<?= $item[0] ?>' data-value1='<?= $item[10] ?>'>Shipped</option>
                                                    <option value="delivered" <?= ($item[8] == "delivered") ? 'selected' : ''; ?> data-value='<?= $item[0] ?>' data-value1='<?= $item[10] ?>'>Delivered</option>
                                                    <option value="cancelled" <?= ($item[8] == "cancelled") ? 'selected' : ''; ?> data-value='<?= $item[0] ?>' data-value1='<?= $item[10] ?>'>Cancel</option>
                                                    <option value="returned" <?= ($item[8] == "returned") ? 'selected' : ''; ?> data-value='<?= $item[0] ?>' data-value1='<?= $item[10] ?>'>Returned</option>

                                                </select>
                                                </br>
                                                <?php
                                                if($fn->get_seller_permission($seller_id,'assign_delivery_boy')){
                                                // $sql = "SELECT id,name FROM delivery_boys WHERE status=1";
                                                $sql = "SELECT id,name,pincode_id FROM delivery_boys WHERE status=1 and FIND_IN_SET($pincode_id, pincode_id) ";
                                                $db->sql($sql);
                                                $result = $db->getResult();
                                                ?>
                                                <select name='deliver_by' class='form-control deliver_by' required>
                                                    <option value=''>Select Delivery Boy</option>
                                                    <?php foreach ($result as $row1) {
                                                        if ($item[15] == $row1['id']) { ?>
                                                            <option value='<?= $row1['id'] ?>' selected data-value1='<?= $item[10]  ?>'><?= $row1['name'] ?></option>
                                                        <?php } else { ?>
                                                            <option value='<?= $row1['id'] ?>' data-value1='<?= $item[10]  ?>'><?= $row1['name'] ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>
                                                <?php }?>
                                                <hr>
                                                <div class="clearfix">
                                                    <a href="#" title='update' id="submit_btn" class="btn btn-primary col-sm-12 col-md-12 update_order_item_status " data-value1='<?= $item[10] ?>' data-value2='<?= $item[8] ?>'>Update</a>
                                                    <hr>

                                                </div>

                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <!-- <tr>
                            <th style="width: 10px">Sub Total (<?= $settings['currency'] ?>)</th>
                            <td><?php echo $res[0]['sub_total']; ?></td>
                        </tr> -->
                        <tr>
                            <th style="width: 10px">Total (<?= $settings['currency'] ?>)</th>
                            <td><?php echo array_sum(array_column($res, "sub_total")); ?></td>
                        </tr>
                        <?php if ($res[0]['discount'] > 0) {
                            $discounted_amount = $res[0]['total'] * $res[0]['discount'] / 100; /*  */
                            $final_total = $res[0]['total'] - $discounted_amount;
                            $discount_in_rupees = $res[0]['total'] - $final_total;
                            $discount_in_rupees = $discount_in_rupees;
                        } else {
                            $discount_in_rupees = 0;
                        } ?>
                        <tr>
                            <th style="width: 10px">Disc. <?= $settings['currency'] ?>(%)</th>
                            <td><?php echo  $discount_in_rupees . '(' . round($res[0]['discount'], 2) . '%)'; ?></td>
                        </tr>
                        <input type="hidden" name="total_amount" id="total_amount" value="<?php echo $res[0]['payable_total']; ?>">
                        <tr>
                            <th style="width: 10px">Payment Method</th>
                            <td><?php echo $res[0]['payment_method']; ?></td>
                        </tr>
                        <tr>
                            <th style="width: 10px">Address</th>
                            <td><?php echo $res[0]['address']; ?></td>
                        </tr>
                        <tr>
                            <th style="width: 10px">Order Date</th>
                            <td><?php echo date('d-m-Y', strtotime($row['date_added'])); ?></td>
                        </tr>
                        <tr>
                            <th style="width: 10px">Delivery Boy Name</th>
                            <?php if (!empty($res[0]['delivery_boy_id'])) {
                                $delivery_boy_id = $res[0]['delivery_boy_id'];
                                $sql = "SELECT * FROM `delivery_boys` WHERE id =$delivery_boy_id";
                                $db->sql($sql);
                                $deli_boy_res = $db->getResult();
                            } else {
                                $deli_boy_res = array();
                            }
                            ?>
                            <td><?= (!empty($deli_boy_res)) ? $deli_boy_res[0]['name'] : "" ?></td>
                        </tr>
                        <tr>
                            <th style="width: 10px">Delivery Time</th>
                            <td><?php echo $res[0]['delivery_time']; ?></td>
                        </tr>
                        <?php

                        // $status = json_decode($res[0]['order_status']);
                        // $i = count($status);
                        $currentStatus1 = "received";
                        ?>
                    </table>
                    <!-- /.box-body -->
                    <div class="alert alert-danger" id="result_fail" style="display:none"></div>
                    <div class="alert alert-success" id="result_success" style="display:none"></div>
                    <div class="box-footer clearfix">
                        <?php
                        $check_array = array("awaiting_payment", "cancelled", "returned");
                        $result1 = array_diff($array, $check_array);
                        if (!empty($result1)) { ?>
                            <button class="btn btn-primary pull-right" onclick="myfunction()"><i class="fa fa-download"></i>Generate Invoice</button>
                        <?php } else { ?>
                            <button class="btn btn-primary disabled pull-right"><i class="fa fa-download"></i> Generate Invoice</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    var allowed = '<?= $allowed; ?>';
    var delivery_by = "";
    $(".deliver_by").change(function(e) {
        delivery_by = $(this).val();
    });
    var status = "";
    $(".status").change(function(e) {
        status = $(this).val();
    });

    $(document).on('click', '.update_order_item_status', function(e) {
        e.preventDefault();
        if (allowed == 0) {
            alert('Sorry! This operation is not allowed in demo panel!.');
            window.location.reload();
            return false;
        }
        
        var status1 = status;
        var id = $('#order_id').val();
        var item_id = $(this).data('value1');
        var delivery_by1 = delivery_by;
        // alert("STATUS : " + status1  + " ITEM ID: " + item_id);

        var dataString = 'update_order_status=1&order_id=' + id + '&status=' + status1 + '&order_item_id=' + item_id + '&delivery_boy_id=' + delivery_by + '&ajaxCall=1';
        if (confirm("Are you sure? you want to change the order item status")) {
            $.ajax({
                url: "../api-firebase/order-process.php",
                type: "POST",
                data: dataString,
                beforeSend: function() {
                    $('#submit_btn').html('Please wait..');
                    $('#submit_btn').attr('disabled', true);
                },
                dataType: "json",
                success: function(data) {
                    if (data.error == true) {
                        alert(data.message);
                    } else {
                        alert(data.message);
                        location.reload(true);
                    }
                    $('#submit_btn').attr('disabled', false);
                    $('#submit_btn').html('Update');
                }
            });
        }
    });

    $(document).on('click', '.update_order_total_payable', function(e) {
        e.preventDefault();
        if (allowed == 0) {
            alert('Sorry! This operation is not allowed in demo panel!.');
            window.location.reload();
            return false;
        }

        var discount = $('#input_discount').val();
        var total_payble = $('#final_total').val();
        var id = $('#order_id').val();
        var dataString = 'update_order_total_payable=true&id=' + id + '&discount=' + discount + '&total_payble=' + total_payble + '&ajaxCall=1';
        $.ajax({
            url: "../../api-firebase/order-process.php",
            type: "POST",
            data: dataString,
            beforeSend: function() {
                $(this).html('...');
            },
            dataType: "json",
            success: function(data) {
                var result = $.map(data, function(value, index) {
                    return [value];
                });
                alert(result[1]);
                if (!result[0]) {}
                location.reload();
            }

        });
    });

    function myfunction() {
        window.location.href = 'invoice.php?id=<?php echo $res[0]['id']; ?>';
    }
    $('#input_discount').on('input', function() {
        var total = $("#total_amount").val();
        var discount = $('#input_discount').val();
        discounted_amount = total * discount / 100;
        final_total = total - discounted_amount;
        if (discount >= 0) {
            $("#final_total").val(Math.round((final_total + Number.EPSILON) * 100) / 100);
        }
    });
</script>

<?php $db->disconnect(); ?>