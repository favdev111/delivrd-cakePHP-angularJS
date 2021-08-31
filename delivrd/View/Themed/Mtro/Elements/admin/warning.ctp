<script>
$(function() {
	var msg = "<?php echo $message; ?>";
	if(msg != '') {
		toastr["warning"](msg);
	}
});
</script>