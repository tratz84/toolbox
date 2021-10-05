





<div id="webmail-outbox-container"></div>



<script>


<?= $wo_it->render() ?>

//load IndexTable on tab activation
var outboxtab_first_open = true;
$(window).on('tabcontainer-item-click', function(e, f) {
	if (outboxtab_first_open == false) return;
	
	var tab_name = $( f ).data('tab-name');

	if (tab_name != 'mail-outbox')
		return;
	
	woutbox_it.load();
	
	outboxtab_first_open = false;
});

</script>
