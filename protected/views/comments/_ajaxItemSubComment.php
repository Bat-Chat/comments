<li>
	<div class="row content">
		<?= $sublevel->content ?>
	</div>

	<div class="row form-cover" data-cont="form-cover">
		<?php if ($sublevel->sublevel): ?>
			<button role="show-more" data-id="<?= $sublevel->id ?>">Посмотреть ответы</button>
		<?php endif ?>
		<button role="get-form" data-parent-id="<?= $sublevel->id ?>" data-root-id="<?= $sublevel->root_id ?>">Ответить</button>
	</div>
	<ul></ul>
</li>