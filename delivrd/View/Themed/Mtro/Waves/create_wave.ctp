<div class="modal-dialog modal-md" >
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Create Wave</h4>
        </div>

        <div class="modal-body" ng-if="!showForm">
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-12">
                    <h3 class="text-center">Do you want to release draft sales orders?</h3>
                    <div class="text-center">
                        <button class="btn btn-primary" ng-click="startForm()" data-toggle="tooltip" data-placement="bottom" title="All draft orders will be released and added to wave">Yes</button> 
                        <button class="btn btn-info" ng-click="close($event)" data-toggle="tooltip" data-placement="bottom" title="I will review and release orders manually">No</button>
                    </div>
                </div>
            </div>
        </div>

        <?php echo $this->Form->create('Wave', array(
            'url' => array('controller' => 'waves', 'action' => 'createWave'),
            'class' => 'form-horizontal',
            'ng-submit'=>'createWave($event)',
            'ng-if' => 'showForm',
            'id' => 'createWaveFormM')); ?>
        <div class="modal-body">
            <div class="alert alert-danger hide" id="modalFormMsg"></div>
            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label">Courier: </label>
                </div>
                <div class="col-md-8">
                    <?php echo $this->Form->input('courier_id',array( 'label' => false, 'class' => 'form-control input-sm','empty' => 'Select...' )); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label">Resources: </label>
                </div>
                <div class="col-md-8">
                    <?php  echo $this->Form->input('resource_id',array( 'label' => false, 'class' => 'form-control input-sm','empty' => 'Select...' )); ?>
                    <?php  echo $this->Form->hidden('order_id',array('label' => false, 'id' => 'selected_orders', 'value'=>'{{order_ids}}')); ?>
                </div>
            </div>
            
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-md blue">Save</button>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>

<script>
    //var window.showForm = 'test';

    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip()
        //$('#WaveCourierId').select2({minimumResultsForSearch: -1});
        //$('#WaveResourceId').select2({minimumResultsForSearch: -1});
    });
</script>
<style>
    .select2-drop-mask {z-index: 10051}
    .select2-drop {z-index: 10052}
</style>