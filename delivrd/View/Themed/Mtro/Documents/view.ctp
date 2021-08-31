<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Documents &raquo; {{title}}</h4>
        </div>
        <div class="modal-body">
            <?php echo $this->Form->create('Document', array('type' => 'file', 'class' => 'form-horizontal', 'ng-submit' => 'addDocument($event)')); ?>
                <?php echo $this->Form->hidden('model_type', array(
                    'label' => false,
                    'class' => 'form-control input-md',
                    'value' => $model_type,
                    'ng-model' => 'formData.model_type'
                )); ?>
                <?php echo $this->Form->hidden('model_id', array(
                    'label' => false,
                    'class' => 'form-control input-md',
                    'value' => $model_id,
                    'ng-model' => 'formData.model_id'
                )); ?>

                <div class="form-group">
                    <div class="hide">
                        <?php echo $this->Form->input('file', array(
                            'label' => false,
                            'div' => false,
                            'type' => 'file',
                            'file-model' => 'myFile',
                        )); ?>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary documentUploadBtn" type="button"><i class="fa fa-file"></i> Upload</button>
                    </div>
                    <div class="col-md-9">
                        <span id="documentDetails" class="muted" style="line-height:33px;font-style:italic;font-size:16px;color:#666"></span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <?php echo $this->Form->textarea('remark', array(
                            'label' => false,
                            'class' => 'form-control',
                            'div' =>false,
                            'placeholder' => 'Type your remarks',
                            'ng-model' => 'formData.remark',
                        ));?>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-md-12 text-right">
                        <button class="btn btn-success">Add Document</button>
                    </div>
                </div>
            
            <?php echo $this->Form->end(); ?>

            <table class="table table-bordered">
                <thead  ng-if="documents.length > 0">
                    <th>File</th>
                    <th>Remarks</th>
                    <th>Created</th>
                    <th width="10px"></th>
                </thead>
                <tbody  ng-if="documents.length > 0">
                    <tr ng-repeat="line in documents | orderBy:sortType:sortReverse" id="line_{{line.Document.id}}">
                        <td><a href="{{line.Document.attachment_path}}" target="_blank">{{line.Document.attachment_name}}</a></td>
                        <td>{{line.Document.remark}}</td>
                        <td>{{line.Document.created}}</td>
                        <td><a href class="btn btn-xs btn-danger" ng-click="removeDocument(line.Document.id)"><i class="fa fa-trash"></i></a></td>
                    </tr>
                </tbody>
                <tr ng-if="documents.length == 0">
                    <td colspan="4"><h4 class="text-info text-center">No documents</h4></td>
                </tr>
            </table>
            
        </div>
        <div class="modal-footer">
            <button type="button" id="closeBtn" ng-click="close($event)" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.documentUploadBtn').click(function(){
            $('#DocumentFile').trigger('click');
            return false;
        });

        $("#DocumentFile").change(function (e) {
            var fileName = e.target.files[0];
            $('#documentDetails').html('File: '+fileName.name+' Size: '+fileName.size+' Type: '+fileName.type)
        });
    });
</script>
