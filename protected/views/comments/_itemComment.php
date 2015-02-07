<?php $model = new Comments; ?>
<li>
	<div class="row content">
		<?= $comment->content ?>
	</div>
	<div class="row show-form">
		<button role="show-form">Ответить</button>
		<div class="date"><?= TimeHelper::format($comment->created_at) ?></div>
	</div>
	<div class="form hide" data-cont="form">
		<?php
			$form=$this->beginWidget('CActiveForm', [
				'id' => 'comment'.$comment->id,
				'enableAjaxValidation'=>true,
				'clientOptions'=> [
					'validateOnSubmit'=>true,
				],
			]);
		?>
		<div class="row">
			<?= $form->textArea($model, 'content') ?>
			<?= $form->error($model, 'content'); ?>
			<?= $form->hiddenField($model, 'parent_id', ['value' => $comment->id]) ?>
			<?= $form->hiddenField($model, 'root_id', ['value' => $comment->root_id]) ?>
		</div>
		<div class="row submit">
			<button>Оставить коммент</button>
		</div>
		<?php $this->endWidget(); ?>
	</div>
	<?= $this->getTree($comments, $comment->id) ?>
</li>
