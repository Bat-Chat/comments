<?php $model = new Comments; ?>
<li>
	<div class="row">
		<?= $comment->content ?>
	</div>

	<div class="row">
		<button>Показать все</button>
		<button>Ответить</button>
	</div>

	<ul>
		<?php
		foreach(array_reverse($comment->sublevel(['limit'=>2])) as $sublevel) {
			echo $this->renderPartial('_ajaxItemSubComment', [
		        'sublevel' => $sublevel,
		    ]);
		}
		?>		
	</ul>
</li>
