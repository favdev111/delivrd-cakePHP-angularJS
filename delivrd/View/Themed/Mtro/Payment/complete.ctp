<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                
                <?php if($status == 'completed') { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bs-callout bs-callout-success">
                                <span class="font-green-jungle help">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                    Payment Completed!
                                </span>
                                <p style="font-size:14px;">Thank you for your payment. Your account was updated, you have <strong>Unlimited</strong> plan now.</p>
                            </div>

                            
                            <div class="bs-callout bs-callout-info">
                                <span class="font-blue-steel help">
                                    <i class="fa fa-info" aria-hidden="true"></i>
                                    What's next?
                                </span>
                                <p style="font-size:14px;">
                                    Read our <a href="https://delivrd.freshdesk.com/support/solutions/articles/17000091133--getting-started-with-delivrd" class="font-blue-steel" target="_blank">Getting Started with Delivrd</a> guide.
                                </p>
                            </div>


                            <div class="bs-callout bs-callout-info">
                                <span class="font-blue-steel help">
                                    <i class="fa fa-info" aria-hidden="true"></i>
                                    Do you have your products data in an Excel sheet?
                                </span>
                                <p style="font-size:14px;">
                                    Send it over through <a href="https://delivrd.freshdesk.com/support/tickets/new" target="_blank">Support</a> (or click <span class="bg-green-jungle bg-font-green-jungle" style="padding:0 3px 2px;">Support</span> button on right hand side of this screen).
                                    We will help you import your products to Delivrd.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php } else if($status == 'error') { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bs-callout bs-callout-danger">
                                <span class="font-red-pink help">
                                    <i class="fa fa-ban" aria-hidden="true"></i>
                                    Payment error!
                                </span>
                                <p style="font-size:14px;">
                                    We receive error, please contact admin.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php } else if($status == 'trial') { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bs-callout bs-callout-success">
                                <span class="font-green-jungle help">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                    Subscription trail period started!
                                </span>
                                <p style="font-size:14px;">Thank you for your subscription. Your account was updated, you have <strong>Unlimited</strong> plan now.</p>
                            </div>

                            
                            <div class="bs-callout bs-callout-info">
                                <span class="font-blue-steel help">
                                    <i class="fa fa-info" aria-hidden="true"></i>
                                    What's next?
                                </span>
                                <p style="font-size:14px;">
                                    Read our <a href="https://delivrd.freshdesk.com/support/solutions/articles/17000091133--getting-started-with-delivrd" class="font-blue-steel" target="_blank">Getting Started with Delivrd</a> guide.
                                </p>
                            </div>


                            <div class="bs-callout bs-callout-info">
                                <span class="font-blue-steel help">
                                    <i class="fa fa-info" aria-hidden="true"></i>
                                    Do you have your products data in an Excel sheet?
                                </span>
                                <p style="font-size:14px;">
                                    Send it over through <a href="https://delivrd.freshdesk.com/support/tickets/new" target="_blank">Support</a> (or click <span class="bg-green-jungle bg-font-green-jungle" style="padding:0 3px 2px;">Support</span> button on right hand side of this screen).
                                    We will help you import your products to Delivrd.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bs-callout bs-callout-info">
                                <span class="font-blue-steel help">
                                    <i class="fa fa-info" aria-hidden="true"></i>
                                    Payment status: <?php echo $status; ?>
                                </span>
                                <p style="font-size:14px;">
                                    Your account will updated at once payment was received.
                                </p>
                            </div>

                            
                            <div class="bs-callout bs-callout-info">
                                <span class="font-blue-steel help">
                                    <i class="fa fa-info" aria-hidden="true"></i>
                                    What's next?
                                </span>
                                <p style="font-size:14px;">
                                    Read our <a href="https://delivrd.freshdesk.com/support/solutions/articles/17000091133--getting-started-with-delivrd" class="font-blue-steel" target="_blank">Getting Started with Delivrd</a> guide.
                                </p>
                            </div>


                            <div class="bs-callout bs-callout-info">
                                <span class="font-blue-steel help">
                                    <i class="fa fa-info" aria-hidden="true"></i>
                                    Do you have your products data in an Excel sheet?
                                </span>
                                <p style="font-size:14px;">
                                    Send it over through <a href="https://delivrd.freshdesk.com/support/tickets/new" target="_blank">Support</a> (or click <span class="bg-green-jungle bg-font-green-jungle" style="padding:0 3px 2px;">Support</span> button on right hand side of this screen).
                                    We will help you import your products to Delivrd.
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