<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Import Inventory Records</h4>
    </div>
    <div class="modal-body">
        <h3>File: <?php echo h($uname); ?></h3>
        <?php if($is_error) { ?>
        <div class="alert alert-danger"><?php echo $msg; ?></div>
        <?php } else { ?>
        <h4 class="text-center text-info"><strong>Import Complete</strong></h4>
        <div>
            <strong>Total Found: <?php echo $numrec; ?></strong><br>
            <strong>Success: <?php echo $success; ?></strong><br>
            <strong>Errors: <?php echo $danger; ?></strong><br>
            <?php if($errors) { ?>
            <?php foreach ($errors as $value) { ?>
                <div class="text-danger"><small><i><?php echo $value; ?></i></small></div>
            <?php } ?>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
    <div class="modal-footer">
        <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>