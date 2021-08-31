<script>
$(function() {
	var msg = "<?php echo $message; ?>";
	if(msg != '') {
		toastr["info"](msg);
	}
});
</script>