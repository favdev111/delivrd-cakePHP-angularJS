<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Subscription <?= $subscription['Subscription']['ext_id']; ?></h4>
    </div>
    <div class="modal-body">
        <div class="row>">
            <div class="col-md-12">
                <?php $result = json_decode($subscription['Subscription']['memo']); ?>
                <?php foreach ($result as $key => $value) { ?>
                    <li><strong><?php echo $key; ?>:</strong> <?php echo $value; ?></li>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>