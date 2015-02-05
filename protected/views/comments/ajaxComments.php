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

<ul class="pagination clearfix" data-comments-per-page="<?= $commentsPerRage ?>" data-visible-comments-count="<?= $visibleCommentsCount ?>">
<?php for($i=1; $i <= $pagesCount; $i++): ?>
	<li><a class="<?= $page == $i ? 'active' : '' ?>" href="<?= Yii::app()->createUrl('comments/ajaxComments', ['page' => $i]) ?>"><?= $i ?></a></li>	
<?php endfor; ?>
</ul>

<script>
$( document ).ready(function() {
	$defaultForm = $('[data-template="ajax-comment"]').clone().show();
	$defaultForm.removeAttr('data-template');
	$defaultForm.find('[role="cancel-form"]').remove();
	$('#default-form').append($defaultForm);

	var visibleCommentsCount = $('[data-visible-comments-count]').attr('data-visible-comments-count');

	initGetForm();
	initShowMore();
	initAddComment();

	/*
	 * Подтянуть все или остальные комменты принадлежащие любому родителю
	 */
	function initShowMore() {
		$('[role="show-more"]').off().click(function (e) {
			$button = $(this);

			// вернуть все или все кроме уже показанных на странице комментов
			var isShortcut = $button.attr('data-view') == 'shortcut' ? true : false;

			$.ajax({
				type: "POST",
				url: "http://comments/index.php?r=comments/getMore",
				data: { id: $button.attr('data-id'), isShortcut: isShortcut },
				success: function (data) {
					$button.parent().next().prepend(data);
					initShowMore();
					initGetForm();

					$button.attr('role', 'toggle-section');
					initToggleButton();
				}
			});

			e.preventDefault();
		});
	}

	/*
	 * Возвращает склонированную с шаблона форму
	 */
	function getCloneForm() {
		return $('[data-template="ajax-comment"]').clone().show().removeAttr('data-template');
	}

	/*
	 * Вывести форму добавления коммента
	 */
	function initGetForm() {
		$('[role="get-form"]').off().click(function (e) {
			$button = $(this);
			$form = $button.parent().find('[data-cont="ajax-comment"]');

			// если нажимать подряд на одну и ту же кнопку
			if($form.is(':visible')) {
				return;
			}

			// скрыть все формы для ajax кроме формы для рутовых комментов (внизу страницы)
			$('[data-cont="form-cover"]').not('#default-form').find('[data-cont="ajax-comment"]').hide();

			// если форма уже была создана для текущего коммента просто паказать ее и очистить текст
			if($form.length > 0) {
				$form.show().find('textarea').val('');
				return;
			}

			// если формы не было созать ее по шаблону
			$form = getCloneForm();
			
			$button.parent().append($form);

			// инициализировать события для новой формы
			initAddComment();
			initCancelForm();

			e.preventDefault();
		});	    	
	}

	/*
	 * Инициализировать конпку закрытия формы
	 */
	function initCancelForm() {
		$('[role="cancel-form"]').off().click(function (e) {
			$(this).parents('[data-cont="ajax-comment"]').hide();

			e.preventDefault();
		});	
	}

	/*
	 * Инициализировать кнопку сворачивания/разворачивания для уже подгруженных дочерних комментов
	 */
	function initToggleButton() {
		$('[role="toggle-section"]').off().click(function (e) {
			$(this).toggleClass('closed');

			if($(this).parent().next('ul').children('li').length > visibleCommentsCount) {
				// для больше чем "visibleCommentsCount" комментов скрывать все кроме "visibleCommentsCount" последних комментов
				$(this).parent().next('ul').children('li:lt('+(-visibleCommentsCount)+')').toggle();
			} else {
				// иначе скрывать все
				$(this).parent().next('ul').children('li').toggle();
			}

			e.preventDefault();
		});
	}

	/*
	 * Получить значение параметра из url
	 */
	function getUrlParam(name) {
		var result = new RegExp('[\?&amp;]' + name + '=([^&amp;#]*)').exec(window.location.href);
		return result && result[1] || '';
	}

	/*
	 * Инициализировать отправку запроса на добавление коммента
	 */
	function initAddComment() {
		$('[role="add-comment"]').off().click(function(e) {
			// найти обертку для формы
			$formCover = $(this).parents('[data-cont="form-cover"]');

			// собрать атрибуты для создания комментария
			var rootId = $formCover.find('[data-root-id]').attr('data-root-id');
			var content = $(this).parents('form').find('[name="content"]').val();
			var parentId = $formCover.find('[data-parent-id]').attr('data-parent-id');

			$.ajax({
				type: "POST",
				url: "http://comments/index.php?r=comments/addComment",
				data: { rootId: rootId, parentId: parentId, content: content },
				dataType: 'json',
				success: function (data) {
					if(data.success) {
						appendComment($formCover, data);						
					} else {
						displayError($formCover, data);
					}
				}
			});

			e.preventDefault();
		});
	}

	/*
	 * Вернет сообщение, что добавленный коммент будет находится на другой странице после перезагрузки текущей
	 */
	function getOrderInfoMessage() {
		$infoMessage = $('[data-template="order-info"]').clone();
		$infoMessage.removeAttr('data-template');
		$infoMessage.find('[data-link="new-comment"]').attr('href', 'http://comments/index.php?r=comments/ajaxComments&page=').text('#');

		return $infoMessage;
	}

	/*
	 * Вывести ошибки валидации при добавления коммента
	 */
	function displayError($formCover, data) {
		$formCover.find('.errors').html('');
		$.each(data.errors, function(index, attrErrors) {
			$formCover.find('.errors').append('<div>'+attrErrors.join(',')+'</div>');
		});
	}


	/*
	 * Вывести только, что добавленный коммент
	 */
	function appendComment($formCover, data) {
		$newComment = $('[data-template="item-comment"]').clone();
		$newComment.removeAttr('data-template');
		$newComment.find('[data-root-id]').attr('data-root-id', data.attrs.root_id);
		$newComment.find('[data-parent-id]').attr('data-parent-id', data.attrs.id);
		$newComment.find('.content').html(data.attrs.content);
		$formCover.find('.errors').html('');


		var countComments = $formCover.next('ul').children('li').length;
		// если у родителя уже было больше или равно чем "visibleCommentsCount" комментов
		if(countComments >= visibleCommentsCount) {

			// скрыть первый (более старый) коммент если дочерние не были развернуты 
			// или их к-во было равно "visibleCommentsCount"
			if(!$formCover.children('[role="toggle-section"]').hasClass('closed')){
				$formCover.next('ul').children('li:visible').first().hide();
			}

			$showMore = $formCover.children('[role="show-more"]');
			$toggle = $formCover.children('[role="toggle-section"]');
			if($showMore.length > 0) {
				$showMore.attr('data-view', 'shortcut');
			}

			// если к-во комментов было равно "visibleCommentsCount" добавить кнопку сворачивания/разворачивания
			if(countComments == visibleCommentsCount && $showMore.length == 0 && $toggle.length == 0) {
				$toggleButt = $('[data-template="toggle-section"]').clone();
				$toggleButt.removeAttr('data-template');
				$formCover.append($toggleButt);

				initToggleButton();
			}
		}

		// вывести новый коммент и удалить форму если она не для рутовых комментов (внизу страницы)
		if($formCover.attr('id') != 'default-form') {
			$formCover.next('ul').append($newComment);
			$formCover.find('[data-cont="ajax-comment"]').remove();
		} else {
			// вывести рутовый коммент
			$('#content').find('ul').first().prepend($newComment);

			// очистить текст формы
			$formCover.find('textarea').val('');

			// если текущая страница не первая 
			if(getUrlParam('page') > 1) {
				// добавить сообщение с указанием на какой странице окажется коммент
				$newComment.append(getOrderInfoMessage());
				// пересчитать ссылки всех выше описанных сообщений
				recountLinks();
			}
		}

		initGetForm();
	}

	/*
	 * Делает пересчет ссылок на страницы где будут отображаться добавленные рутовые комментарии
	 * после перезагрузки текущей страницы
	 * т.е. самые новые будут на странице "1", а следующие в зависимости к-ва комментов на странице
	 */
	function recountLinks() {
		// все ссылки на страницы кроме шаблона (":visible")
		$links = $('[data-link]:visible');

		var page,
				href,
				countLinks = $links.length,
				commentsPerRage = iterator = $('[data-comments-per-page]').attr('data-comments-per-page');

		for(var i = 0; i < countLinks; i++) {
			page = Math.floor(iterator/commentsPerRage);

			// заменить текст и номер страницы в ссылке на нужный
			href = $links.eq(i).attr('href');
			href = href.replace(/([\d]*$)/, page);
			$links.eq(i).text(page).attr('href', href);

			iterator++;
		}
	}
});
</script>

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