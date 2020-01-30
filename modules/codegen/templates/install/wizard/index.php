<!DOCTYPE html>
<html>

<head>
	<script src="js/jquery-3.3.1.min.js"></script>
	<link href="css/less/base.less" rel="stylesheet/less" type="text/css" />
	<script src="lib/less/dist/less.js"></script>
	<link href="lib/font-awesome-4/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	
	<title>Toolbox installatie</title>
	
	<style type="text/css">
	
	   [name=api_key] { width: 300px; }
	   [name=data_dir] { width: 500px; }
	</style>
</head>

<body>

	<div class="main-content">
		<div class="page-header">
			<h1>Toolbox wizard</h1>
		</div>
		
		<?= $form->render() ?>
		
	</div>

</body>

</html>
