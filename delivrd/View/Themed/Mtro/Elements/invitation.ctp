<?php $invitation = $this->requestAction(array('plugin'=>'networks', 'controller' => 'networks', 'action' => 'getinvites')); ?>
<?php #pr($invitation); ?>
<?php if($invitation) { ?>
<div id="invitationAlert" style="display: none;">
    <div class="alert alert-warning clearfix">
        <i class="fa fa-sitemap fa-2x"></i>
        <span class="lead">&nbsp;You have invitation to network <strong><?php echo h($invitation['Network']['name']); ?></strong></span>
        <div class="text-right">
            <a id="declineInv" href="<?php echo $this->Html->url(array('plugin'=>'networks', 'controller'=>'networks', 'action'=>'decline', $invitation['NetworksInvite']['id'])); ?>" class="btn btn-danger">Decline</a>
            <a href="<?php echo $this->Html->url(array('plugin'=>'networks', 'controller'=>'networks', 'action'=>'accept', $invitation['NetworksInvite']['id'])); ?>" class="btn btn-success">Accept</a>
        </div>
    </div>
</div>
<script>
    var $html = $('#invitationAlert').html();
    $(document).ready(function(){
        $('div.page-content-wrapper').find('div.page-content').prepend($html);
        $('#declineInv').click(function(){
            var link = $(this);
            
            $.ajax({
                type: 'POST',
                url: link.attr('href'),
                data: '',
                dataType:'json',
                beforeSend: function() {
                    
                },
                success:function (r, status) {
                    link.parents('div.alert-warning').remove();
                }
            });
            return false;
        })
    });

</script>
<?php } ?>