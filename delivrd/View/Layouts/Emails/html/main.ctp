<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
        <title><?php echo $this->fetch('title'); ?></title>
</head>
<body>
	<table style="width:100%;border: 1px solid #ebebeb;" cellspacing="0" cellpadding="0">
		<tr>
			<td style="background:#2D5F8B;padding:20px 10px"><img src="https://delivrdapp.com/theme/Mtro/assets/admin/layout/img/logo_b.png"> </td>
		</tr>
		<tr>
			<td style="padding:20px 10px">
				<?php echo $this->fetch('content'); ?>
			</td>
		</tr>
		<tr>
			<td style="padding:20px 10px;border-top: 1px solid #ebebeb;text-align: right;"><small>2019 &copy; Delivrd</small></td>
		</tr>
	</table>
</body>
</html>