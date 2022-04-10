<?php
session_start();
error_reporting(true);
$latest_version = "v1.0.2.4";
$session_var = "count".$latest_version;
$session_val = "applied".$latest_version;
/*
*	Update script for eCart - Multivendor PHP Admin Panel from v1.0.2 to v1.0.2.1
*	All Right reserved to WRTeam.in
*	
*/
if (!isset($_SESSION[$session_var]) && $_SESSION[$session_var] != $session_val) {
	include('../includes/crud.php');
	$db = new Database();
	$db->connect();
   
    
    /* updateing Letest version in database */
    $db->sql("SELECT * FROM `updates` where `version`='$latest_version' ");
    $res = $db->getResult();
	if(empty($res)){
        $db->sql("INSERT INTO `updates` (`version`) VALUES ('$latest_version')");
    }
    $db->sql("INSERT INTO `updates` (`version`) VALUES ('$latest_version')");
    // $zip = new ZipArchive;
	// if ($zip->open('update-files/folders.zip') === TRUE) {
	// 	$zip->extractTo('../');
	// 	$zip->close();
	// }   
    $db->sql("ALTER TABLE `seller` ADD `slug` VARCHAR(256) NULL DEFAULT NULL AFTER `store_name`;");
    $db->sql("ALTER TABLE `category` ADD `slug` VARCHAR(256) NULL DEFAULT NULL AFTER `name`;");
    $db->sql("ALTER TABLE `seller` ADD `forgot_password_code` VARCHAR(256) NULL DEFAULT NULL AFTER `longitude`;");
    $db->sql("ALTER TABLE `seller` ADD `view_order_otp` tinyint(2) DEFAULT 0 AFTER `forgot_password_code`;");
    $db->sql("ALTER TABLE `seller` ADD `assign_delivery_boy` tinyint(2) DEFAULT 0 AFTER `view_order_otp`;");
    /* dist files start */
	copy('update-files/dist/js/covert.js', '../dist/js/covert.js');
    copy('update-files/dist/css/AdminLTE.min.css', '../dist/css/AdminLTE.min.css');
    /* dist files end */

    /* includes files start */
	copy('update-files/includes/custom-functions.php', '../includes/custom-functions.php');
	copy('update-files/includes/firebase.php', '../includes/firebase.php');
    copy('update-files/includes/functions.php', '../includes/functions.php');
    copy('update-files/includes/push.php', '../includes/push.php');
    copy('update-files/includes/variables.php', '../includes/variables.php');
    /* includes files end */

    /* library starts here */
    copy('update-files/library/class.phpmailer.php', '../library/class.phpmailer.php');
	copy('update-files/library/class.smtp.php', '../library/class.smtp.php');
	copy('update-files/library/jwt.php', '../library/jwt.php');
	copy('update-files/library/products.csv', '../library/products.csv');
	copy('update-files/library/products.txt', '../library/products.txt');
	copy('update-files/library/seller-products.csv', '../library/seller-products.csv');
	copy('update-files/library/seller-products.txt', '../library/seller-products.txt');
	/* library ends here */

   

	/* root files start here */
	copy('update-files/about-us.php', '../about-us.php');
	copy('update-files/add-area.php', '../add-area.php');
	copy('update-files/add-category.php', '../add-category.php');
    copy('update-files/add-city.php', '../add-city.php');
	copy('update-files/add-delivery-boy.php', '../add-delivery-boy.php');
	copy('update-files/add-media.php', '../add-media.php');
	copy('update-files/add-pincode.php', '../add-pincode.php');
	copy('update-files/add-product.php', '../add-product.php');
	copy('update-files/add-seller.php', '../add-seller.php');
	copy('update-files/add-subcategory.php', '../add-subcategory.php');
	copy('update-files/add-tax.php', '../add-tax.php');
	copy('update-files/add-unit.php', '../add-unit.php');
	copy('update-files/admin-profile.php', '../admin-profile.php');
	copy('update-files/areas.php', '../areas.php');
	copy('update-files/bulk-update.php', '../bulk-update.php');
	copy('update-files/bulk-upload.php', '../bulk-upload.php');
	copy('update-files/categories-order.php', '../categories-order.php');
	copy('update-files/categories.php', '../categories.php');
    copy('update-files/city.php', '../city.php');
	copy('update-files/contact-us.php', '../contact-us.php');
	copy('update-files/contact.php', '../contact.php');
	copy('update-files/customers.php', '../customers.php');
	copy('update-files/delete-area.php', '../delete-area.php');
	copy('update-files/delete-category.php', '../delete-category.php');
    copy('update-files/delete-city.php', '../delete-city.php');
	copy('update-files/delete-order.php', '../delete-order.php');
	copy('update-files/delete-pincode.php', '../delete-pincode.php');
	copy('update-files/delete-product.php', '../delete-product.php');
	copy('update-files/delete-query.php', '../delete-query.php');
	copy('update-files/delete-subcategory.php', '../delete-subcategory.php');
	copy('update-files/delete-tax.php', '../delete-tax.php');
	copy('update-files/delivery-boy-play-store-privacy-policy.php', '../delivery-boy-play-store-privacy-policy.php');
	copy('update-files/delivery-boy-play-store-terms-conditions.php', '../delivery-boy-play-store-terms-conditions.php');
	copy('update-files/delivery-boy-privacy-policy.php', '../delivery-boy-privacy-policy.php');
	copy('update-files/delivery-boys.php', '../delivery-boys.php');
	copy('update-files/edit-area.php', '../edit-area.php');
	copy('update-files/edit-category.php', '../edit-category.php');
    copy('update-files/edit-city.php', '../edit-city.php');
	copy('update-files/edit-pincode.php', '../edit-pincode.php');
	copy('update-files/edit-image.php', '../edit-image.php');
	copy('update-files/edit-product.php', '../edit-product.php');
	copy('update-files/edit-query.php', '../edit-query.php');
	copy('update-files/edit-seller.php', '../edit-seller.php');
	copy('update-files/edit-subcategory.php', '../edit-subcategory.php');
	copy('update-files/edit-tax.php', '../edit-tax.php');
	copy('update-files/edit-unit.php', '../edit-unit.php');
    copy('update-files/faq.php', '../faq.php');
    copy('update-files/footer.php', '../footer.php'); 
	copy('update-files/forgot-password.php', '../forgot-password.php');
	copy('update-files/fund-transfers.php', '../fund-transfers.php');
	copy('update-files/header.php', '../header.php');
	copy('update-files/home.php', '../home.php');
	copy('update-files/index.php', '../index.php');
	copy('update-files/info.php', '../info.php');
	copy('update-files/invoice.php', '../invoice.php');
    copy('update-files/logout.php', '../logout.php');
	copy('update-files/low-stock-products.php', '../low-stock-products.php');
	copy('update-files/main-slider.php', '../main-slider.php');
	copy('update-files/manage-customer-wallet.php', '../manage-customer-wallet.php');
	copy('update-files/manager-app-play-store-privacy-policy.php', '../manager-app-play-store-privacy-policy.php');
	copy('update-files/manager-app-play-store-terms-conditions.php', '../manager-app-play-store-terms-conditions.php');
	copy('update-files/manager-app-privacy-policy.php', '../manager-app-privacy-policy.php');
    copy('update-files/media.php', '../media.php');
	copy('update-files/new-offers.php', '../new-offers.php');
	copy('update-files/notification-settings.php', '../notification-settings.php');
	copy('update-files/notification.php', '../notification.php');
	copy('update-files/order-detail.php', '../order-detail.php');
	copy('update-files/orders.php', '../orders.php');
	copy('update-files/payment-methods-settings.php', '../payment-methods-settings.php');
	copy('update-files/payment-requests.php', '../payment-requests.php');
	copy('update-files/pincodes.php', '../pincodes.php');
	copy('update-files/play-store-privacy-policy.php', '../play-store-privacy-policy.php');
	copy('update-files/privacy-policy.php', '../privacy-policy.php');
	copy('update-files/product-detail.php', '../product-detail.php');
	copy('update-files/product-sales-report.php', '../product-sales-report.php');
	copy('update-files/products-order.php', '../products-order.php');
	copy('update-files/products-taxes.php', '../products-taxes.php');
    copy('update-files/products.php', '../products.php');
	copy('update-files/promo-code.php', '../promo-code.php');
	copy('update-files/purchase-code.php', '../purchase-code.php');
	copy('update-files/reset-password.php', '../reset-password.php');
	copy('update-files/return-requests.php', '../return-requests.php');
	copy('update-files/sales-report.php', '../sales-report.php');
	copy('update-files/sections.php', '../sections.php');
	copy('update-files/seller-play-store-privacy-policy.php', '../seller-play-store-privacy-policy.php');  // here
	copy('update-files/seller-play-store-terms-conditions.php', '../seller-play-store-terms-conditions.php');
	copy('update-files/seller-privacy-policy.php', '../seller-privacy-policy.php');
	copy('update-files/seller-wallet-transactions.php', '../seller-wallet-transactions.php');
	copy('update-files/sellers.php', '../sellers.php');
	copy('update-files/send-multiple-push.php', '../send-multiple-push.php');
	copy('update-files/settings.php', '../settings.php');
	copy('update-files/sold-out-products.php', '../sold-out-products.php');
	copy('update-files/subcategories.php', '../subcategories.php');
	copy('update-files/system-users.php', '../system-users.php');
	copy('update-files/terms-conditions.php', '../terms-conditions.php');
	copy('update-files/time-slots.php', '../time-slots.php');
	copy('update-files/transaction.php', '../transaction.php'); 
	copy('update-files/units.php', '../units.php'); 
	copy('update-files/update-seller-commission.php', '../update-seller-commission.php'); 
	copy('update-files/view-category-product.php', '../view-category-product.php');
	copy('update-files/view-product-variants.php', '../view-product-variants.php');
	copy('update-files/view-product.php', '../view-product.php');
	copy('update-files/view-seller-orders.php', '../view-seller-orders.php');
	copy('update-files/view-seller-products.php', '../view-seller-products.php');
	copy('update-files/view-subcategory.php', '../view-subcategory.php');
	copy('update-files/view-subcategory-product.php', '../view-subcategory-product.php');
	copy('update-files/wallet-transactions.php', '../wallet-transactions.php');
	copy('update-files/withdrawal-requests.php', '../withdrawal-requests.php');

	copy('update-files/api-firebase/get-bootstrap-table-data.php', '../api-firebase/get-bootstrap-table-data.php');
	copy('update-files/api-firebase/login.php', '../api-firebase/login.php');
	copy('update-files/api-firebase/order-process.php', '../api-firebase/order-process.php');
	copy('update-files/api-firebase/cart.php', '../api-firebase/cart.php');
	copy('update-files/bootstrap/css/custom.css', '../bootstrap/css/custom.css');
	copy('update-files/delivery-boy/api/api-v1.php', '../delivery-boy/api/api-v1.php');
	copy('update-files/delivery-boy/public/orders-data.php', '../delivery-boy/public/orders-data.php');	
	copy('update-files/includes/custom-functions.php', '../includes/custom-functions.php');
	copy('update-files/public/add-product-form.php', '../public/add-product-form.php');
	copy('update-files/public/add-seller-form.php', '../public/add-seller-form.php');
	copy('update-files/public/edit-seller-form.php', '../public/edit-seller-form.php');
	copy('update-files/public/confirm-delete-product.php', '../public/confirm-delete-product.php');
	copy('update-files/public/db-operation.php', '../public/db-operation.php');
	copy('update-files/public/orders-data.php', '../public/orders-data.php');
	copy('update-files/public/orders-table.php', '../public/orders-table.php');
	copy('update-files/public/reset-password.php', '../public/reset-password.php');
	copy('update-files/public/send-forgot-password-mail.php', '../public/send-forgot-password-mail.php');
	copy('update-files/public/setting-form.php', '../public/setting-form.php');
	copy('update-files/seller/api/api-v1-docs.txt', '../seller/api/api-v1-docs.txt');
	copy('update-files/seller/api/api-v1.php', '../seller/api/api-v1.php');
	copy('update-files/seller/edit-profile.php', '../seller/edit-profile.php');
	copy('update-files/seller/forgot-password.php', '../seller/forgot-password.php');
	copy('update-files/seller/sign-up.php', '../seller/sign-up.php');
	copy('update-files/seller/get-bootstrap-table-data.php', '../seller/get-bootstrap-table-data.php');
	copy('update-files/seller/home.php', '../seller/home.php');
	copy('update-files/seller/header.php', '../seller/header.php');
	copy('update-files/seller/public/login-form.php', '../seller/public/login-form.php');
	copy('update-files/seller/public/orders-data.php', '../seller/public/orders-data.php');
	copy('update-files/seller/public/reset-password.php', '../seller/public/reset-password.php');
	copy('update-files/seller/public/add-product-form.php', '../seller/public/add-product-form.php');
	copy('update-files/seller/public/db-operation.php', '../seller/public/db-operation.php');
	copy('update-files/seller/reset-password.php', '../seller/reset-password.php');
	copy('update-files/update-seller-commission.php', '../update-seller-commission.php');
	copy('update-files/public/edit-product-form.php', '../public/edit-product-form.php');






	/* root files end here */

	echo "Congratulations! You have successfully upgraded your system!<br/><h4>If you liked our Auto Update system</h4>";

	$_SESSION[$session_var] = $session_val;
	echo "Operation done successfully! Do not perform this second time! ";
} else {

	exit("<label class='label label-danger' >Operation already applied! Cannot perform this second time! Please now delete the <b>/update</b> folder from your server directory</label>");
}
