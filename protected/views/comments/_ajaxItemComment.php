<?php $model = new Comments; ?>
<li>
	<div class="row content">
		<?= $comment->content ?>
	</div>

        
	<div class="row form-cover" data-cont="form-cover">
		<?php if ((count($comment->sublevel) > $this->visibleCommentsCount)): ?>
			<button role="show-more" data-view="shortcut" data-id="<?= $comment->id ?>">Показать все</button>
		<?php endif ?>
		<button role="get-form" data-parent-id="<?= $comment->id ?>" data-root-id="<?= $comment->root_id ?>">Ответить</button>
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
