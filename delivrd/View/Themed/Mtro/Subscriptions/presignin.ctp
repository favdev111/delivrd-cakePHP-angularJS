<body class="login">
<style>
    .li-features li {line-height: 28px; font-size:15px;}
    .li-features li>i {font-size:20px;vertical-align: -2px;}
    .copyright {color:#FFF !important;}
</style>
<div class="content" style="width:620px">
    <div class="text-center margin-bottom-30"><a href="<?php echo $this->Html->url('/');?>"><img src=<?php echo Configure::read('LoginLogoURL') ?> ></a></div>

    <div>
        
        <div class="text-center" style="margin-bottom: 30px">
            <button type="button" class="btn btn-lg blue-madison" data-toggle="modal" data-target="#videoDemoModal">
                <i class="fa fa-play-circle" aria-hidden="true"></i> Vied Demo<br>
                <span style="font-size: 9px;color: #FFF;">View delivrd Demo</span>
            </button>
        </div>

        <ul class="list-unstyled li-features">
            <li><i class="fa fa-check-square font-green-jungle"></i> Free 30 day trial</li>
            <li><i class="fa fa-check-square font-green-jungle"></i> Cancel subscription at any time</li>
            <li><i class="fa fa-check-square font-green-jungle"></i> Our support team will walk you through initial setup of Delivrd</li>
            <li><i class="fa fa-check-square font-green-jungle"></i> Unlimited products, locations, orders and serial numbers</li>
            <li><i class="fa fa-check-square font-green-jungle"></i> Never run out of stock with Low Inventory Alerts</li>
            <li><i class="fa fa-check-square font-green-jungle"></i> Eliminate costly shipping mistakes with 360° barcode scanning</li>
            <li><i class="fa fa-check-square font-green-jungle"></i> The easiest, most powerful inventory management solution you’ll ever use</li>
        </ul>

        <div class="text-center">
            <a href="<?php echo $this->Html->url(['plugin' => false, 'controller' => 'subscriptions', 'action' => 'signin']); ?>" taraget='' class='btn' style='margin: 8px 0px 10px 2px;border-radius: 6px;color: #fff;background-color: #f19071;border-color: #f19071;'>
                Subscribe using PayPal or Credit Card
            </a>
        </div>
    </div>

</div>
<div class="copyright">
    <?php echo date('Y'); ?> © <?php echo Configure::read('OperatorName') ?>
</div>

<div class="modal fade" id="videoDemoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: none;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/imSgxWwiIjY"></iframe>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center !important">
                    <a href="<?php echo $this->Html->url(['plugin' => false, 'controller' => 'subscriptions', 'action' => 'signin']); ?>" taraget='' class='btn btn-lg' style='margin: 8px 0px 10px 2px;border-radius: 6px;color: #fff;background-color: #f19071;border-color: #f19071;'>
                        Subscribe using PayPal or Credit Card
                    </a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    link = 'https://youtu.be/imSgxWwiIjY';
</script>