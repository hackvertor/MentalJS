<!doctype HTML>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Content injection demo</title>
		<script src="../javascript/purify.js"></script>
		<script src="../javascript/Mental.js"></script>
		<script src="content_injection.js"></script>
	</head>
	<body>
		<plaintext id="MentalRender" />
		<h1>Content Injection demo</h1>
		<?php echo $_GET['x']?>
	</body>
</html>
