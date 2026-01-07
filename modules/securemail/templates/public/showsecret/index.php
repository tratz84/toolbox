<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">

	<title>Secure Message</title>
	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	
	<meta name="robots" content="noindex,nofollow" />
	
	<style type="text/css">
	body {
	   font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
	}
	
	.btn {
	   background-color: #ececec; padding: 5px 8px 8px;
	   text-decoration: none;
	}
	
	</style>
	
	<script>
	function removeMessage_Click() {
		let c = confirm('Weet je zeker dat je dit bericht wilt verwijderen?');
		if (!c) return;

		let delurl = <?= json_encode($delurl) ?>;
		window.location = delurl;
	}

	async function requestMessage_Click() {
		let r = await fetch( <?= json_encode($otcurl) ?> );
		let json = await r.json();

		console.log(json);
		if (json.success) {
			// empty container
			let c = document.getElementById('otc-container');
			c.innerHTML = '';
			c.style = '';

			let spnTitle = document.createElement('div');
			spnTitle.style = 'font-weight: bold; margin-bottom: 5px;';
			spnTitle.innerText = 'Bericht';

			let txta = document.createElement('textarea');
			txta.value = json.message;
			txta.setAttribute('readonly', 'readonly');
			txta.style = 'width: 100%; height: 120px;';

			c.appendChild( spnTitle );
			c.appendChild( txta );
			
		}
		else {
			document.getElementById('otc-container').innerHTML = 'Error: ' + json.message;
		}
				
		
	}
	
	</script>
	
</head>


<body>


	<div style="margin-top: 10vh;">
		<?php if (ctx()->hasLogoFile()) : ?>
			<div style="text-align: center;">
				<img src="<?= appUrl('/?m=base&c=auth&a=logo') ?>" style="max-width: 700px;" />
			</div>
		<?php endif; ?>
	
		<div style="border: 1px solid #ececec; max-width: 600px; margin: 25px auto; padding: 10px;">
			<?php if ($error) : ?>
				Er is een fout opgetreden: <?= $error ?>
				
			<?php else : ?>
				<?php if ($sm->getTtlType() == 'once') : ?>
					
					<div style="text-align: center;" id="otc-container">
    					U kunt dit bericht 1x bekijken, daarna wordt deze verwijderd.
    					
    					<div style="margin: 20px 0 8px;">
    						<a href="javascript:void(0);" class="btn btn-view" onclick="requestMessage_Click();">Bekijk bericht</a>
    					</div>
    				</div>
				<?php else : ?>
				
					<div style="margin: 0 0 10px;">
						<?= esc_html($sm->getShortDescription()) ?>
					</div>
				
					<div style="font-weight: bold; margin-bottom: 5px;">
						Bericht
					</div>
					
					<textarea readonly="readonly" style="width: 100%; height: 120px;"><?= esc_html($sm->getPlainMessage()) ?></textarea>
					
					<div style="margin-bottom: 5px;">
						Dit bericht verloopt op: <?= format_datetime($sm->getTtlExpiresOn(), 'd-m-Y H:i') ?>
					</div>
					
					<div style="margin: 10px 0 8px; border-top: 1px solid #ececec; padding-top: 15px; margin-top: 10px;">
						<a href="javascript:void(0);" class="btn btn-remove" onclick="removeMessage_Click();">Bericht verwijderen</a>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			
			
		</div>
	
	</div>

	

</body>


</html>

