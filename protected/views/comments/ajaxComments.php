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
echo $this->renderPartial('_templates', [
	'model' => $model,
]);
?>

<div id="default-form" data-cont="form-cover">
	<div data-parent-id="0" data-root-id="0"></div>
</div>

<ul class="pagination clearfix" data-comments-per-page="<?= $commentsPerRage ?>" data-visible-comments-count="<?= $visibleCommentsCount ?>">
<?php for($i=1; $i <= $pagesCount; $i++): ?>
	<li><a class="<?= $page == $i ? 'active' : '' ?>" href="<?= Yii::app()->createUrl('comments/ajaxComments', ['page' => $i]) ?>"><?= $i ?></a></li>	
<?php endfor; ?>
</ul>


<!-- задаю стили здесь в файле т.к. при подключении файла они не работали (только на "body" применялись стили) -->
<style type="text/less">

button {
	background-color: #000;
	background-image: linear-gradient(rgb(77, 77, 77), rgb(47, 47, 47));
	border: 1px #000 solid;
	border-radius: 5px;
	padding: 5px 10px;
	font-weight: bold;
	color: #b6b6b6;
}

button:hover {
	background-image: linear-gradient(rgb(87, 87, 87), rgb(57, 57, 57));
}

button:active {
	background-image: linear-gradient(rgb(47, 47, 47), rgb(17, 17, 17));
}

button.show-more:after {
	position: relative;
	top: -10px;
	right: -5px;
	width: 0;
	height: 0;
	content: '';
	border-left: 8px solid transparent;
	border-right: 8px solid transparent;
	border-bottom: 8px solid #5C736D;
}

button.show-more.closed:after {
	top: 12px;
	border-bottom: none;
	border-top: 8px solid #5C736D;
}
/*
button.show-more.active:after {
	top: 12px;
	border-bottom: none;
	border-top: 8px solid #5C736D;
}*/

.form {
	background-color: #3d3d3d;
	padding: 1px 10px;
	margin-top: 10px;

	textarea {
		border-color: #3d3d3d;
		width: 99%;
		height: 80px;
		background-color: #252527;
		color: #657879;
		font: 16px sans-serif;
		resize: vertical;
	}
}

#content{
	background-color: #252527;

	ul {
		border-left: 1px #3d3d3d solid;
		margin: 10px 0;

		li {
			list-style: none;
			color: #657879;

			.form-cover {
				border-top: 1px #3d3d3d solid;
				padding-top: 10px;
				margin-top: 10px;
			}

			.content {
				font: 18px sans-serif;
			}
		}
	}

	ul.pagination {
		border: none;

		li {
			float: left;
			margin: 5px 10px;

			a {
				color: #b6b6b6;
				font: 700 18px sans-serif;
			}

			a.active {
				color: #666;
				text-decoration: none;
				pointer-events: none;
				cursor: default;
			}
		}
	}
}
</style>
<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.3.1/less.min.js"></script>