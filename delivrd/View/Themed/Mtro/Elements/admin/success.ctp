<script>
$(function() {
	var msg = "<?php echo $message; ?>";
	if(msg != '') {
		toastr["success"](msg);
	}
});
</script>