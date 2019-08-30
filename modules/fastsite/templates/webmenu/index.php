

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=webmenu&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
	<h1>Menu</h1>
</div>


<div class="webmenu-container" style="width: 300px; float: left;">
	<?= $controller->renderMenus($menus) ?>
</div>

<div style="padding-left: 20px; margin-top: 35px;">
	<a href="javascript:void(0);" class="menu-item-up fa fa-chevron-up"></a>
	<br/>
	<a href="javascript:void(0);" class="menu-item-down fa fa-chevron-down"></a>
</div>


<script>

$(document).ready(function() {
	$('.menu-item a').click(function() {
		menuitem_Click(this);
		
		return false;
	});

	$('.menu-item a').dblclick(function() {
		window.location = $(this).attr('href');
	});
});

$('.menu-item-up').click(function() { webmenuitemSortUpdate('up'); });
$('.menu-item-down').click(function() { webmenuitemSortUpdate('down'); });


function webmenuitemSortUpdate( direction ) {
	var mi = $('.webmenu-container .menu-item.selected');
	if (mi.length==0) return;

	var ids = [];
	mi.closest('.menu-container').find('> .menu-item').each(function(index, node) {
		ids.push( $(node).data('menu-id') );
	});
	
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=fastsite&c=webmenu&a=sort_item'),
		data: {
			ids: ids.join(','),
			selectedId: mi.data('menu-id'),
			direction: direction
		}
	});


	if (direction == 'up') {
		var p = mi.prev();
		if (p)
			p.insertAfter(mi);
	} else if (direction == 'down') {
		var p = mi.next();
		if (p)
			p.insertBefore(mi);
	}
	
}


function menuitem_Click(anchor) {
	$('.menu-item').removeClass('selected');
	$(anchor).closest('.menu-item').addClass('selected');


	return false;
}


</script>


