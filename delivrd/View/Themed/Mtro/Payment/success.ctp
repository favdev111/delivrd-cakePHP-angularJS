<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                
                <?php if($status == 'Completed') { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bs-callout bs-callout-success">
                                <span class="font-green-jungle help">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                    Payment Completed!
                                </span>
                                <p style="font-size:14px;">Thank you for your payment. Your account was updated, you have <strong>Unlimited</strong> plan now.</p>
                            </div>
                        </div>
                    </div>
                <?php } else if($status != 'Completed') { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bs-callout bs-callout-info">
                                <span class="font-blue-steel help">
                                    <i class="fa fa-info" aria-hidden="true"></i>
                                    Payment in process!
                                </span>
                                <p style="font-size:14px;">
                                    Thank you for your payment. Payment in confirmation process, can't take some min.
                                    Your account was updated, you have <strong>Unlimited</strong> plan now.
                                    If you have any question please contact with admin.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->