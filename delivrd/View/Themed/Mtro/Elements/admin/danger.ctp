<script>
$(function() {
toastr.options = {
    tapToDismiss: false,
    closeButton: true,
    closeHtml: '<button><i class="fa fa-close"></i></button>',
    timeOut: false,
    iconClass: 'toast-error',
};
	var msg = "<?php echo $message; ?>";
	if(msg != '') {
		toastr["error"](msg);
	}
});
</script>