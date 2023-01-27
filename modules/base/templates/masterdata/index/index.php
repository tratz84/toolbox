
<div class="page-header">
	<h1><?= t('Master data') ?></h1>
</div>


<div id="menu-container">

	<div class="functions-menu-page" ez-for="menu" ez-key="title" ez-item="m">
		<div class="col-12 col-sm-4 col-lg-3 setting-menu-tag-container">
			<h2>{{ title }}</h2>
		
			<ul class="" ez-for="m" ez-item="mi">
				<li><a href="javascript:window.location = appUrl( '{{ mi.url }}' );">{{ mi.title }}</a></li>
			</ul>
		</div>
	</div>
</div>


<script>

$(document).ready(function() {
	let json_menu = <?= json_encode( $menu ) ?>;
	let t = new EzTemplate('menu-container');
	t.setVar('menu', json_menu);
	t.render();
});

</script>

