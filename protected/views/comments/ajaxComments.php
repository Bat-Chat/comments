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
}
</style>
<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.3.1/less.min.js"></script>
<script>
$( document ).ready(function() {
	$defaultForm = $('[data-template="ajax-comment"]').clone().show();
	$defaultForm.removeAttr('data-template');
	$defaultForm.find('[role="cancel-form"]').remove();
	$('#default-form').append($defaultForm);


	function initShowMore() {
		$('[role="show-more"]').off().click(function (e) {
			$butt = $(this);
			var isShortcut = $butt.attr('data-view') == 'shortcut' ? true : false;
			$.ajax({
				type: "POST",
				url: "http://comments/index.php?r=comments/getMore",
				data: { id: $butt.attr('data-id'), isShortcut: isShortcut },
				success: function function_name (data) {
					$butt.parent().next().prepend(data);

					$butt.attr('role', 'toggle-section');
					initToggle();
				}
			});

			e.preventDefault();
		});
	}

	function initGetForm() {
		$('[role="get-form"]').off().click(function (e) {
			$butt = $(this);
			$form = $butt.parent().find('[data-cont="ajax-comment"]');

			if($form.is(':visible')) {
				return;
			}

			// скрыть все формы для ajax
			$('[data-cont="ajax-comment"]').hide();

			// если форма уже была создана для текущего коммента просто паказать ее и очистить текст
			if($form.length > 0) {
				$form.show().find('textarea').val('');
				return;
			}

			// если формы не было созать ее по шаблону
			$form = $('[data-template="ajax-comment"]').clone().show();
			$form.removeAttr('data-template');

			$butt.parent().append($form);

			// инициализировать события для новой формы
			initAddComment();
			initCancelForm();

			e.preventDefault();
		});	    	
	}

	function initCancelForm() {
		$('[role="cancel-form"]').off().click(function (e) {
			$butt = $(this);
			$butt.parents('[data-cont="ajax-comment"]').hide();

			e.preventDefault();
		});	
	}

	function initToggle() {
		$('[role="toggle-section"]').off().click(function (e) {
			$butt = $(this);
			$butt.toggleClass('closed');

			if($butt.parent().next('ul').children('li').length > 2) {
				$butt.parent().next('ul').children('li:lt(-2)').toggle();
			} else {
				$butt.parent().next('ul').find('li').toggle();
			}

			e.preventDefault();
		});
	}

	initShowMore();
	initGetForm();
	initAddComment();

	

	urlParam = function(name){
	    var result = new RegExp('[\?&amp;]' + name + '=([^&amp;#]*)').exec(window.location.href);
	    return result && result[1] || '';
	}

	function initAddComment() {
		$('[role="add-comment"]').off().click(function (e) {
			$butt = $(this);
			$formCover = $butt.parents('[data-cont="form-cover"]');
			var rootId = $formCover.find('[data-root-id]').attr('data-root-id');
			var parentId = $formCover.find('[data-parent-id]').attr('data-parent-id');
			var content = $butt.parents('form').find('[name="content"]').val();

			$.ajax({
				type: "POST",
				url: "http://comments/index.php?r=comments/addComment",
				data: { rootId: rootId, parentId: parentId, content: content },
				dataType: 'json',
				success: function (data) {
					$newComment = $('[data-template="item-comment"]').clone();
					$newComment.removeAttr('data-template');
					$newComment.find('[data-root-id]').attr('data-root-id', data.root_id);
					$newComment.find('[data-parent-id]').attr('data-parent-id', data.id);
					$newComment.find('.content').html(content);

					var countComments = $formCover.next('ul').children('li').length;
					// если у родителя уже было больше или равно чем "visibleCommentsCount" комментов
					if(countComments >= 2) {

						// скрыть первый (более старый) коммент если дочерние не были развернуты 
						// или их к-во было равно "visibleCommentsCount"
						if(!$formCover.children('[role="toggle-section"]').hasClass('closed')){
							$formCover.next('ul').children('li:visible').first().hide();
						}

						$showMore = $formCover.children('[role="show-more"]');
						if($showMore.length > 0) {
							$showMore.attr('data-view', 'shortcut');
						}

						// если к-во комментов было равно "visibleCommentsCount" добавить кнопку сворачивания/разворачивания
						if(countComments == 2 && $showMore.length == 0) {
							$toggleButt = $('[data-template="toggle-section"]').clone();
							$toggleButt.removeAttr('data-template');
							$formCover.append($toggleButt);

							initToggle();
						}
					}

					if($formCover.attr('id') != 'default-form') {
						$formCover.next('ul').append($newComment);
						$formCover.find('[data-cont="ajax-comment"]').remove();
					} else {

						if(urlParam('page') > 1) {
							$infoMessage = $('#order-info').clone();
							$newComment.append($infoMessage);
						}

						$('#content').find('ul').first().prepend($newComment);
					}

					initGetForm();
				}
			});

			e.preventDefault();
		});
	}

});
</script>
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
echo $this->renderPartial('_ajaxForm', [
    'model' => $model,
]);
?>

<div id="default-form" data-cont="form-cover">
	<div data-parent-id="0" data-root-id="0"></div>
</div>

<ul>
<?php for($i=1; $i <= $pagesCount; $i++): ?>
	<li><a href="<?= Yii::app()->createUrl('comments/ajaxComments', ['page' => $i]) ?>"><?= $i ?></a></li>	
<?php endfor; ?>
</ul>