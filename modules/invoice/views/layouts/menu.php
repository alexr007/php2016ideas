	<div id="mainmenu">
	<?php 
		$module="/invoice";
		$user = new RuntimeUser();
		
		$this->widget('zii.widgets.CMenu',[
			'items'=>[
                ['label'=>'Invoice Parse',	'url'=>[$module.'/invoiced/parse'],	'visible'=>$user->isOperator()],
			],
		]);
	?>
	</div>
