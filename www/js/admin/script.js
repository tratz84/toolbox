


function admin_autologin( contextName, username ) {
	formpost('/?m=admin&c=customer&a=do_autologin', {
		contextName: contextName,
		username: username
	}, { target: '_blank' } );

	close_popup();
}



