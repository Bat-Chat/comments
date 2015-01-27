<?php $model = new Comments; ?>
<li>
	<div class="row">
		<?= $row->content ?>
	</div>
	<div class="form">
		<?php
			$form=$this->beginWidget('CActiveForm', [
				'id' => 'comm'.$row->id,
				'enableAjaxValidation'=>true,
				'clientOptions'=> [
					'validateOnSubmit'=>true,
				],
			]);
		?>
		<div class="row">
			<?= $form->textArea($model, 'content') ?>
			<?= $form->error($model, 'content'); ?>
			<?= $form->hiddenField($model, 'parent_id', ['value' => $row->id]) ?>
			<?= $form->hiddenField($model, 'root_id', ['value' => $row->root_id]) ?>
		</div>
		<div class="row submit">
			<?= CHtml::submitButton('Оставить коммент'); ?>
		</div>
		<?php $this->endWidget(); ?>
	</div>
	<?= $this->getTree($comments, $row->id) ?>
</li>
