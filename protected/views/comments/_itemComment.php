<?php $model = new Comments; ?>
<li>
	<div class="row content">
		<?= $row->content ?>
	</div>
	<div class="row show-form" role="show-form">
		<button>Ответить</button>
	</div>
	<div class="form hide" data-cont="form">
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
			<button>Оставить коммент</button>
		</div>
		<?php $this->endWidget(); ?>
	</div>
	<?= $this->getTree($comments, $row->id) ?>
</li>
