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
		<?= $form->hiddenField($model, 'parent_id', ['value' => 0]) ?>
		<?= $form->hiddenField($model, 'root_id', ['value' => null]) ?>
	</div>
	<div class="row submit">
		<?= CHtml::submitButton('Оставить коммент'); ?>
	</div>
	<?php $this->endWidget(); ?>
</div>