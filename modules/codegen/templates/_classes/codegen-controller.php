<?= "<?php" ?>


use core\controller\BaseController;

class <?= $controllerName ?>Controller extends BaseController {


	<?php foreach($actions as $a) : ?>
	public function action_<?= $a ?>() {
	
	
<?php if (in_array($a, ['index', 'edit'])) : ?>
			$this->render();
<?php endif; ?>
		}


	<?php endforeach; ?>

}

