<?php
	if($comments) {
		echo $this->getTree($comments, $this->rootParentId);
	}

	$model = new Comments;
	echo $this->renderPartial('_form', [
        'model' => $model,
    ]);
?>

<ul class="pagination clearfix">
<?php for($i=1; $i <= $pagesCount; $i++): ?>
	<li><a href="<?= Yii::app()->createUrl('comments/comments', ['page' => $i]) ?>"><?= $i ?></a></li>	
<?php endfor; ?>
</ul>

<script>
	$( document ).ready(function () {
		$('[role="show-form"]').click(function() {
			$(this).parent().next('[data-cont="form"]').toggle();
		})
	});
</script>

<!-- задаю стили здесь в файле т.к. при подключении файла они не работали (только на "body" применялись стили) -->
<style type="text/less">

button {
	padding: 5px 10px;
	background-color: #000;
	background-image: linear-gradient(rgb(77, 77, 77), rgb(47, 47, 47));
	border: 1px #000 solid;
	border-radius: 5px;
	font-weight: bold;
	cursor: pointer;
	color: #b6b6b6;
}

button:hover {
	background-image: linear-gradient(rgb(87, 87, 87), rgb(57, 57, 57));
}

button:active {
	background-image: linear-gradient(rgb(47, 47, 47), rgb(17, 17, 17));
}

.form {
	background-color: #3d3d3d;
	padding: 1px 10px;
	margin-top: 10px;

	textarea {
		width: 99%;
		height: 80px;
		border-color: #3d3d3d;
		background-color: #252527;
		color: #657879;
		font: 16px sans-serif;
		resize: vertical;
	}

	.error textarea {
		background-color: #252527 !important;
		border-color: #3d3d3d !important;
	}

	.errorMessage {
		color: #657879 !important;
	}
}

.show-form {
	border-top: 1px #3d3d3d solid;
	margin-top: 10px;
	padding-top: 10px;

	.date {
		top: -5px;
		float: right;
		position: relative;
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