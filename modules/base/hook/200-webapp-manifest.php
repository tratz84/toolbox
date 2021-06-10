<?php




hook_eventbus_subscribe('base', 'decorator-render-head', function($evt) {

    $urlManifest = appUrl('/?m=base&c=webapp/manifest');
    $urlServiceWorker = json_encode(appUrl('/?mpf=/module/base/webapp/serviceworker.js'));
    
    print <<<HTML

        <link crossorigin="use-credentials" rel="manifest" href="{$urlManifest}" />
        <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register( {$urlServiceWorker} ).then(function(registration) {
			      // Registration was successful
			      console.log('ServiceWorker registration successful with scope: ', registration.scope);
			    }, function(err) {
			      // registration failed :(
			      console.log('ServiceWorker registration failed: ', err);
			    });
			  });
			}
		</script>

HTML;
    
});



