<div class="form hide" data-cont="ajax-comment">
	<?php
		$form=$this->beginWidget('CActiveForm', [
			'id' => 'ajax-comment',
		]);
	?>
	<div class="row">
		<textarea name="content" cols="20" rows="2"></textarea>
	</div>
	<div class="row submit">
		<button role="add-comment">
			Оставить коммент
		</button>
	</div>
	<?php $this->endWidget(); ?>
</div>