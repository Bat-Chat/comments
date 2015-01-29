<ul>
	<?php
	foreach($comments as $comment) {
		echo $this->renderPartial('_ajaxItemComment', [
	        'comment' => $comment,
	    ]);
	} 
	?>
</ul>

<?php
$model = new Comments;
echo $this->renderPartial('_form', [
    'model' => $model,
]);

echo $this->renderPartial('_ajaxForm', [
    'model' => $model,
]);

?>

<ul>
<?php for($i=1; $i <= $pagesCount; $i++): ?>
	<li><a href="<?= Yii::app()->createUrl('comments/ajaxComments', ['page' => $i]) ?>"><?= $i ?></a></li>	
<?php endfor; ?>
</ul>