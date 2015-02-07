<li>
	<div class="row content">
		<?= $sublevel->content ?>
	</div>

	<div class="row form-cover" data-cont="form-cover">
		<?php if ($sublevel->sublevel): ?>
			<button class="show-more closed" role="show-more" data-id="<?= $sublevel->id ?>">Все ответы</button>
		<?php endif ?>
		<button role="get-form" data-parent-id="<?= $sublevel->id ?>" data-root-id="<?= $sublevel->root_id ?>">Ответить</button>
		<div class="date"><?= TimeHelper::format($sublevel->created_at) ?></div>
	</div>
	<ul></ul>
</li>