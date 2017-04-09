<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>z</title>
	<link rel="stylesheet" href="<?php echo PUB ?>css/z.min.css">
	<script src="<?php echo PUB ?>libs/jquery.min.js"></script>
	<script src="<?php echo PUB ?>js/z.min.js"></script>
</head>
<body>
	<div class="z-content">
		<table class="z-table">
		<tr>
			<th colspan="2">测试</th>
		</tr>
		<?php foreach ($lists as $key => $value) { ?>
			<tr>
				<td class="z-tag"><?php echo $value['id'] ?></td>
				<td><?php echo $value['nickname'] ?></td>
			</tr>
		<?php } ?>
		</table>
	</div>
	<button class="z-btn z-icon z-gotop">&#xe6be;</button>
	<!-- <script>
		$(function(){
			$.alert('aa')
		})
	</script> -->
</body>
</html>