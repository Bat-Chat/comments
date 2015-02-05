<div id="templates" class="hide">
	<div class="form" data-template="ajax-comment" data-cont="ajax-comment">
		<?php
			$form=$this->beginWidget('CActiveForm', [
				'id' => 'ajax-comment',
			]);
		?>
		<div class="row">
			<textarea name="content" cols="20" rows="2"></textarea>
		</div>
		<div class="errors"></div>
		<div class="row submit">
			<button role="add-comment">
				Оставить коммент
			</button>
			<button role="cancel-form">
				Отменить
			</button>
		</div>
		<?php $this->endWidget(); ?>
	</div>

	<li data-template="item-comment">
		<div class="row content"></div>

		<div class="row form-cover" data-cont="form-cover">
			<button role="get-form" data-parent-id="" data-root-id="">Ответить</button>
		</div>
		<ul></ul>
	</li>

	<div id="order-info" data-template="order-info">
		<p>После перезагрузки страницы этот комментарий будет отображаться на <a data-link="new-comment" href=""></a> странице</p>
	</div>

	<button data-template="toggle-section" role="toggle-section" data-view="shortcut" data-id="">Показать все</button>
</div>