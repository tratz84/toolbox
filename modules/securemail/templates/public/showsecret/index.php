<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">

	<title>Secure Message</title>
	
	<link href="<?= BASE_URL ?>/lib/fontawesome-free-5.15.3-web/css/v4-shims.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= BASE_URL ?>/lib/fontawesome-free-5.15.3-web/css/all.min.css" rel="stylesheet" type="text/css" />
	<script src="<?= BASE_URL ?>/js/jquery-3.3.1.min.js"></script>
	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	
	<meta name="robots" content="noindex,nofollow" />
	
	<style type="text/css">
	body {
	   font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
	}
	
    .short-description {
        margin: 0 0 10px;
    }
    
    .expiry-message {
        margin: 25px 0 5px;
    }
	
	.btn {
	   background-color: #ececec; padding: 5px 8px 8px;
	   text-decoration: none;
	   color: #00f;
	}
	
    .itx-toast {
    	position: fixed;
    	z-index: 999999;
    	bottom: 25px;
    	left: calc( 50% - 100px );
    }
    .itx-toast .toast-message {
    	position: relative;
    	text-align: center;
    	background-color: #fff;
    	border: 1px solid #ccc;
    	box-shadow: 0px 0px 5px black;
    	padding: 10px;
    	width: 200px;
    	margin-top: 10px;
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
		let r = await fetch( <?= json_encode($messageurl) ?> );
		let json = await r.json();

		if (json.success) {
			// empty container
			let c = document.getElementById('otc-container');
			c.innerHTML = '';
			c.style = '';

			let htmlRemove = '';
			if (json.ttl_type == 'expires') {
				htmlRemove = '<div style="float: right;"><a href="javascript:void(0);" class="btn btn-remove" onclick="removeMessage_Click();">Bericht verwijderen</a></div>';
			}
			

			
			let spnTitle = document.createElement('div');
			spnTitle.style = 'font-weight: bold; margin-bottom: 5px;';
			spnTitle.innerText = 'Bericht';

			let txta = document.createElement('textarea');
			txta.id = 'plain-message';
			txta.value = json.message;
			txta.setAttribute('readonly', 'readonly');
			txta.style = 'width: 100%; height: 120px;';

			if (json.short_description) {
				let c_sd = $('<div class="short-description" />');
				c_sd.text( json.short_description );
				$(c).append( c_sd );
			}
			
			if (htmlRemove != '') {
				$(c).append( htmlRemove );
			}
			c.appendChild( spnTitle );

			$(c).append('<div style="clear: both; height: 5px;"></div>');
			c.appendChild( txta );


			let htmlCopy = $('<div style="margin: 5px 0 10px;"><a href="javascript:void(0);" onclick="copyText();" class="btn"><span class="fa fa-copy"></span></a></div>');
			$(c).append( htmlCopy );

			if (json.ttl_type == 'expires') {
				let c_expires = $('<div class="expiry-message" />');
				c_expires.text('Dit bericht verloopt op: ' + json.ttl_expires_on_format);
				$(c).append( c_expires );
			}
			
			
		}
		else {
			document.getElementById('otc-container').innerHTML = 'Error: ' + json.message;
		}
	}

	function copyText() {
		copyToClipboard( $('#plain-message').val() );
	}
	

	var itxToastTimeout = null;
	function showToastMessage( msg, opts ) {
		opts = opts ? opts : {};
		
		// remove old
		if ($('.itx-toast').length == 0) {
			$(document.body).append('<div class="itx-toast"></div>');
		}
		
		let tm = $('<div class="toast-message-container"><div class="toast-message"></div></div>');
		
		if (opts.error) {
			tm.addClass('error');
		}
		
		tm.find('.toast-message').text( msg );
		$('.itx-toast').prepend( tm );
		
		itxToastTimeout = setTimeout(function() {
			tm.animate({
				height: 0
			}, 500, 'swing', function() {
				$(tm).remove();
				if ($('.itx-toast > .toast-message').length == 0) {
					$('.itx-toast').remove();
				}
			});
		}, 2000);
	}

	function copyToClipboard( t ) {
		var inp = document.createElement('input');
		inp.type = 'text';
		inp.value = t;

		document.body.appendChild( inp );

		inp.select();
		inp.setSelectionRange(0, 99999); /* For mobile devices */

		var r = document.execCommand("copy");

		inp.remove();
		
		if (r) {
			showToastMessage( 'Bericht gekopieerd naar klembord' );
		}
		else {
			showToastMessage( 'Niet gelukt de tekst te kopiëren' );
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
					
					<?php if ($sm->getViewMethod() == 'click2watch') : ?>
						<div style="text-align: center;" id="otc-container">
        					Klik om het bericht te bekijken
        					
        					<div style="margin: 20px 0 8px;">
        						<a href="javascript:void(0);" class="btn btn-view" onclick="requestMessage_Click();">Bekijk bericht</a>
        					</div>
        				</div>
					
					<?php else : ?>
    					<div class="short-description">
    						<?= esc_html($sm->getShortDescription()) ?>
    					</div>
    				
    					<div style="">
    						<div style="float: right;">
    							<a href="javascript:void(0);" class="btn btn-remove" onclick="removeMessage_Click();">Bericht verwijderen</a>
    						</div>
        					<div style="font-weight: bold; margin-bottom: 5px;">
        						Bericht
        					</div>
        				</div>
        				
        				<div style="clear: both; height: 5px;"></div>
    					
    					<textarea readonly="readonly" id="plain-message" style="width: 100%; height: 120px;"><?= esc_html($sm->getPlainMessage()) ?></textarea>
    					
    					<div style="margin: 5px 0 10px;">
    						<a href="javascript:void(0);" onclick="copyText();" class="btn"><span class="fa fa-copy"></span></a>
    					</div>
    					
    					<div class="expiry-message">
    						Dit bericht verloopt op: <?= format_datetime($sm->getTtlExpiresOn(), 'd-m-Y H:i') ?>
    					</div>
    					
    					
    				<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
			
			
		</div>
	
	</div>

	

</body>


</html>

