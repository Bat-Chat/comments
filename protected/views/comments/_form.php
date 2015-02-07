<div class="form">
	<?php
	$form=$this->beginWidget('CActiveForm', [
		'id' => 'newComment',
		'enableAjaxValidation'=>true,
		'clientOptions'=> [
			'validateOnSubmit'=>true,
		],
	]);
	?>
	<div class="row">
		<?= $form->textArea($model, 'content') ?>
		<?= $form->error($model, 'content'); ?>
		<?= $form->hiddenField($model, 'parent_id', ['value' => $this->rootParentId]) ?>
		<?= $form->hiddenField($model, 'root_id', ['value' => $this->rootParentId]) ?>
	</div>
	<div class="row submit">
		<button>Оставить коммент</button>
	</div>
	<?php $this->endWidget(); ?>
</div>