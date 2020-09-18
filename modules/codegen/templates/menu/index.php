
<div class="page-header">
	<h1>Codegen</h1>
</div>


<div class="functions-menu-page">

	<div class="col-xs-12 col-sm-4 col-lg-3 setting-menu-tag-container">
		<h2>Base</h2>
		
		<ul>
			<li>
				<a href="<?= appUrl('/?m=codegen&c=settings') ?>">Settings</a>
			</li>
		</ul>
	</div>

	<div class="col-xs-12 col-sm-4 col-lg-3 setting-menu-tag-container">
		<h2>DB</h2>
		
		<ul>
			<li>
				<a href="<?= appUrl('/?m=codegen&c=daoGenerator') ?>">DAO Generator</a>
			</li>
			<li>
				<a href="<?= appUrl('/?m=codegen&c=generator/model') ?>">Model Generator</a>
			</li>
			
		</ul>
	</div>
	
	<div class="col-xs-12 col-sm-4 col-lg-3 setting-menu-tag-container">
		<h2>Forms</h2>
		
		<ul>
			<li>
				<a href="<?= appUrl('/?m=codegen&c=formgenerator&a=list') ?>">Forms generator</a>
			</li>
		</ul>
		<h2>Lists</h2>
		<ul>
			<li>
				<a href="<?= appUrl('/?m=codegen&c=listeditgenerator&a=list') ?>">ListEditWidget generator</a>
			</li>
			<li>
				<a href="<?= appUrl('/?m=codegen&c=listformgenerator&a=list') ?>">ListFormWidget generator</a>
			</li>
		</ul>
	</div>

	<div class="col-xs-12 col-sm-4 col-lg-3 setting-menu-tag-container">
		<h2>Pages</h2>
		
		<ul>
			<li>
				<a href="<?= appUrl('/?m=codegen&c=generator/indexTable') ?>">IndexTable generator</a>
			</li>
		</ul>
	</div>
	
	
	<div class="col-xs-12 col-sm-4 col-lg-3 setting-menu-tag-container">
		<h2>Code</h2>
		<ul>
			<li>
				<a href="<?= appUrl('/?m=codegen&c=generateModule') ?>">Generate module</a>
			</li>
			<li>
				<a href="<?= appUrl('/?m=codegen&c=base/controllerGenerator') ?>">Generate controller</a>
			</li>
			<?php /*
			<li>
				<a href="<?= appUrl('/?m=codegen&c=datamodel/module') ?>">Datamodel</a>
			</li>
			*/ ?>
			<li>
				<a href="<?= appUrl('/?m=codegen&c=menugeneratorController') ?>">Menu Generator</a>
			</li>
			<li>
				<a href="<?= appUrl('/?m=codegen&c=config/usercapability') ?>">User capabilities</a>
			</li>
		</ul>
	</div>

</div>
