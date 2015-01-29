<?php
	if($comments) {
		echo $this->getTree($comments, 0);
	}

	$model = new Comments;
	echo $this->renderPartial('_form', [
        'model' => $model,
    ]);
?>

<ul>
<?php for($i=1; $i <= $pagesCount; $i++): ?>
	<li><a href="<?= Yii::app()->createUrl('comments/comments', ['page' => $i]) ?>"><?= $i ?></a></li>	
<?php endfor; ?>
</ul>