<li>
	<div class="row content">
		<?= $comment->content ?>
	</div>
		
	<div class="row form-cover" data-cont="form-cover">
		<?php if ((count($comment->sublevel) > $this->visibleCommentsCount)): ?>
			<button class="show-more closed" role="show-more" data-id="<?= $comment->id ?>">Все ответы</button>
		<?php endif ?>
		<button role="get-form" data-parent-id="<?= $comment->id ?>" data-root-id="<?= $comment->root_id ?>">Ответить</button>
		<div class="date"><?= TimeHelper::format($comment->created_at) ?></div>
	</div>

	<ul>
		<?php
		foreach(array_reverse($comment->sublevel(['limit'=>$this->visibleCommentsCount])) as $sublevel) {
			echo $this->renderPartial('_ajaxItemSubComment', [
				'sublevel' => $sublevel,
			]);
		}
		?>		
	</ul>
</li>
