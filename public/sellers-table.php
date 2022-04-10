<section class="content-header">
    <h1>
        Sellers /
        <small><a href="home.php"><i class="fa fa-home"></i> Home</a></small>
    </h1>
    <ol class="breadcrumb">
        <a class="btn btn-block btn-default" href="add-seller.php"><i class="fa fa-plus-square"></i> Add New Seller</a>
    </ol>
</section>
<?php
if ($permissions['sellers']['read'] == 1) {
?>
    <!-- Main content -->
    <section class="content">
        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <div class="col-xs-12">
                <div class="box">
                    <!-- <div class="col-xs-6"> -->
                    <div class="box-header">
                        <div class="row col-md-4">
                            <h4 class="box-title">Filter Seller by Status </h4>
                            <select id="filter_seller" name="filter_seller" required class="form-control ">
                                <option value="">All</option>
                                <option value="1">Approved</option>
                                <option value="2">Not-Approved</option>
                                <option value="0">Deactivate</option>
                                <option value="7">Removed</option>
                            </select>
                        </div>
                           
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">

                        <table id='seller_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=seller" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="id" data-sort-order="desc" data-show-export="true" data-export-types='["txt","excel"]' data-export-options='{"fileName": "sellers-list-<?= date('d-m-Y') ?>","ignoreColumn": ["operate"] }'>
                            <thead>
                                <tr>
                                    <th data-field="id" data-sortable="true">ID</th>
                                    <th data-field="name" data-sortable="true">Name</th>
                                    <th data-field="store_name" data-sortable="true">Store Name</th>
                                    <th data-field="email" data-sortable="true">Email</th>
                                    <th data-field="mobile">Mobile</th>
                                    <th data-field="balance" data-sortable="true">Balance</th>
                                    <th data-field="store_url" data-sortable="true" data-visible="false">Store URL</th>
                                    <th data-field="logo" data-sortable="true">Logo</th>
                                    <th data-field="address_proof" data-sortable="true" data-visible="false">Address Proof</th>
                                    <th data-field="national_identity_card" data-sortable="true" data-visible="false">National Identity Card</th>
                                    <th data-field="store_description" data-sortable="true" data-visible="false">Description</th>
                                    <th data-field="street" data-sortable="true" data-visible="false">Street</th>
                                    <th data-field="pincode_id" data-sortable="true" data-visible="false">Pincode Id</th>
                                    <th data-field="city_id" data-sortable="true" data-visible="false">City Id</th>
                                    <th data-field="state" data-sortable="true" data-visible="false">State</th>
                                    <th data-field="categories" data-sortable="true">Categories</th>
                                    <th data-field="account_number" data-sortable="true" data-visible="false">Account Number</th>
                                    <th data-field="bank_ifsc_code" data-sortable="true" data-visible="false">Bank IFSC Code</th>
                                    <th data-field="account_name" data-sortable="true" data-visible="false">Account Name</th>
                                    <th data-field="bank_name" data-sortable="true" data-visible="false">Bank Name</th>
                                    <th data-field="commission" data-sortable="true">Commission(%)</th>
                                    <th data-field="status" data-sortable="true">Status</th>
                                    <th data-field="require_products_approval" data-sortable="true">Pr. Approval?</th>
                                    <th data-field="operate" data-events="actionEvents">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="separator"> </div>
        </div>
        <!-- /.row (main row) -->
    </section>
<?php } else { ?>
    <div class="alert alert-danger topmargin-sm" style="margin-top: 20px;">You have no permission to view Sellers.</div>
<?php } ?>

<script>
    $('#filter_seller').on('change', function() {
        $('#seller_table').bootstrapTable('refresh');

    });

    function queryParams(p) {
        return {
            "filter_seller": $('#filter_seller').val(),
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search
        };
    }
</script>
<!-- <script>
    window.actionEvents = {
        'click .restore-seller': function(e, value, rows, index) {
            var s_id = $(this).data("id");
            if (confirm("Are you sure? you want to restore this Seller")) {
                $.ajax({
                    url: 'public/db-operation.php',
                    type: "POST",
                    data: 'seller_id=' + s_id + '&remove_seller=1&type=restore',
                    success: function(result) {
                        if (result == 1)
                            // alert("done");
                            $('#seller_table').bootstrapTable('refresh');
                        else
                            alert('Error! Product could not be deactivated.');
                    }
                });
            }
        }

    };
</script> -->