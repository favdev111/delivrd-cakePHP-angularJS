<?php $this->AjaxValidation->active(); ?>
<div class="page-content-wrapper">
        <div class="page-content">
            <?php echo $this->element('expirytext'); ?>

<!-- BEGIN PAGE CONTENT-->
            <?php echo $this->Session->flash(); ?>
            <div class="row">
                <div class="col-md-12">
                    <blockquote>
                        <p style="font-size:16px">
                            Update your inventory by a csv file
                        </p>
                        <p style="font-size:16px">
                            <?php echo $this->Html->link('<i class="fa fa-cloud-download"></i> Download Sample File','/inventories/exportcsv',array('class' => 'btn blue-hoki fileinput-button','escape'=> false)); ?>
                        </p>
                    </blockquote>
                    <br>
                    <form id="fileupload" action="/inventories/uploadcsv" method="POST" enctype="multipart/form-data">
                        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                        <div class="row fileupload-buttonbar">
                            <div class="col-lg-7">
                                <!-- The fileinput-button span is used to style the file input field as button -->
                                <span class="btn green fileinput-button">
                                    <i class="fa fa-plus"></i>
                                    <span>
                                    Add file... </span>
                                    <input type="file" accept=".csv" name="uploadedfile">
                                </span>
                                <button type="submit" class="btn blue start">
                                    <i class="fa fa-upload"></i>
                                    <span>Start upload</span>
                                </button>

                                <button type="reset" class="btn warning cancel">
                                    <i class="fa fa-ban-circle"></i>
                                    <span>Cancel upload</span>
                                </button>
                                <!-- The global file processing state -->
                                    <span class="fileupload-process">
                                    </span>
                            </div>
                            <!-- The global progress information -->
                            <div class="col-lg-5 fileupload-progress fade">
                                <!-- The global progress bar -->
                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar progress-bar-success" style="width:0%;">
                                    </div>
                                </div>
                                <!-- The extended global progress information -->
                                <div class="progress-extended">
                                     &nbsp;
                                </div>
                            </div>
                        </div>
                        <!-- The table listing the files available for upload/download -->
                        <table role="presentation" class="table table-striped clearfix">
                            <tbody class="files">
                            </tbody>
                        </table>
                    </form>
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h3 class="panel-title">Important!</h3>
                        </div>
                            <div class="panel-body">
                                <ul>
                                    <li>
                                         The maximum file size for uploads is <strong>100k</strong>
                                    </li>
                                    <li>
                                         Only csv files can be uploaded.
                                    </li>
                                </ul>
                            </div>
                    </div>
                </div>
            </div>
<!-- END PAGE CONTENT-->
        </div>
</div>
</div> 
</div>
</div> 
<!-- END CONTENT -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger label label-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
            </div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn blue start" disabled>
                    <i class="fa fa-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn red cancel">
                    <i class="fa fa-ban"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-download fade">
            <td>
                <span class="preview">
                    {% if (file.thumbnailUrl) { %}
                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                    {% } %}
                </span>
            </td>
            <td>
                <p class="name">
                    {% if (file.url) { %}
                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                    {% } else { %}
                        <span>{%=file.uname%}</span>
                    {% } %}
                </p>
                {% if (file.error) { %}
                    <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                {% } %}
            </td>
            <td>
                <span class="size">{%=o.formatFileSize(file.size)%}</span>
            </td>
            <td>
                <a href="<?php echo $this->Html->url('importcsv', true); ?>/{%=file.name%}/{%=file.uname%}" data-toggle="modal" data-target="#ajaxModal" rel="modal-lg" class="btn blue"><i class="fa fa-barcode"></i>Create Inventory Records</a>
            </td>
           
        </tr>
    {% } %}
</script>

<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/vendor/tmpl.min.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/vendor/load-image.min.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/vendor/canvas-to-blob.min.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.iframe-transport.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-audio.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-video.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/global/plugins/jquery-file-upload/js/jquery.fileupload-ui.js', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/assets/admin/pages/scripts/form-fileupload.js', array('block' => 'pageBlock')); ?>

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
jQuery(document).ready(function() { 
    FormFileUpload.init();
});
<?php $this->Html->scriptEnd(); ?>