<?php
include_once('includes/functions.php');
include_once('includes/custom-functions.php');
$fn = new custom_functions;

// $ID = (isset($_GET['id'])) ? $db->escapeString($fn->xss_clean($_GET['id'])) : "";
if (isset($_GET['id'])) {
    $ID = $db->escapeString($fn->xss_clean($_GET['id']));
} else {
    // $ID = "";
    return false;
    exit(0);
}

if (isset($_POST['btnEdit'])) {
    if (ALLOW_MODIFICATION == 0 && !defined(ALLOW_MODIFICATION)) {
        echo '<label class="alert alert-danger">This operation is not allowed in demo panel!.</label>';
        return false;
    }
    if ($permissions['locations']['update'] == 1) {
        $pincode = $db->escapeString($fn->xss_clean($_POST['pincode']));
        $status = $db->escapeString($fn->xss_clean($_POST['status']));

        // create array variable to handle error
        $error = array();

        if (empty($pincode)) {
            $error['pincode'] = " <span class='label label-danger'>Required!</span>";
        }

        if (!empty($pincode)  && $status != "") {
            $sql_query = "UPDATE pincodes SET pincode = '$pincode' ,`status`=$status WHERE id = $ID";
            $db->sql($sql_query);
            $update_result = $db->getResult();
            if (!empty($update_result)) {
                $update_result = 0;
            } else {
                $update_result = 1;
            }
            if ($update_result == 1) {
                $error['update_data'] = "<section class='content-header'><span class='label label-success'>Pincode updated Successfully</span></section>";
            } else {
                $error['update_data'] = " <span class='label label-danger'>Failed update</span>";
            }
        }
    } else {
        $error['update_data'] = "<section class='content-header'><span class='label label-danger'>You have no permission to update pincode</span></section>";
    }
}

// create array variable to store previous data

$sql_query = "SELECT * FROM pincodes WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

?>
<section class="content-header">
    <h1>Edit Pincode <small><a href='pincodes.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;Back</a></small></h1>
    <small><?php echo isset($error['update_data']) ? $error['update_data'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <?php if ($permissions['locations']['update'] == 0) { ?>
                <div class="alert alert-danger">You have no permiss+ion to update pincodes</div>
            <?php } ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit Pincode</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <form method="post" id="edit_form" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Pincode</label><?php echo isset($error['pincode']) ? $error['pincode'] : ''; ?>
                            <input type="text" name="pincode" required class="form-control" value="<?= $res[0]['pincode']; ?>" />
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <div id="status" class="btn-group">
                                <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                    <input type="radio" name="status" value="0" <?= ($res[0]['status'] == 0) ? 'checked' : ''; ?>> Deactive
                                </label>
                                <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                    <input type="radio" name="status" value="1" <?= ($res[0]['status'] == 1) ? 'checked' : ''; ?>> Active
                                </label>
                            </div>
                        </div>
                    </div>


            </div><!-- /.box-body -->

            <div>
                <input type="submit" class="btn-primary btn" value="Update" name="btnEdit" />
            </div>
            </form>
        </div><!-- /.box -->
    </div>
    </div>
</section>

<div class="separator"> </div>
<?php
$db->disconnect(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
    $('#edit_form').validate({
        rules: {
            pincode: "required",
           
        }
    });
</script>