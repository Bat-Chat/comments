<li>
	<?= $sublevel->content ?>


	<div class="row">
		<?php if ($sublevel->sublevel): ?>
			<button role="show-more" data-id="<?= $sublevel->id ?>">Посмотреть ответы</button>
		<?php endif ?>
		<button role="get-form" data-parent-id="<?= $sublevel->id ?>" data-root-id="<?= $sublevel->root_id ?>">Ответить</button>
	</div>
	<ul></ul>
</li>
<script>
	$( document ).ready(function() {
	    $('[role="show-more"]').off().click(function (e) {
			$butt = $(this);
			var isShortcut = $butt.attr('data-view') == 'shortcut' ? true : false;
			$.ajax({
				type: "POST",
				url: "http://comments/index.php?r=comments/getMore",
				data: { id: $butt.attr('data-id'), isShortcut: isShortcut },
				success: function function_name (data) {
					console.log(data);
					$butt.parent().next().prepend(data);
				}
			});

			e.preventDefault();
	    });

	    $('[role="get-form"]').off().click(function (e) {
	    	$butt = $(this);
	    	$form = $('[data-cont="ajax-comment"]').clone().show();

	    	$butt.parent().append($form);

	    	init();

	    	e.preventDefault();
	    });

	    init();

	    function init() {
		    $('[role="add-comment"]').off().click(function (e) {
		    	$butt = $(this);
		    	$formCover = $('[data-cont="form-cover"]');
		    	var rootId = $formCover.find('[data-root-id]').attr('data-root-id');
		    	var parentId = $formCover.find('[data-parent-id]').attr('data-parent-id');
		    	var content = $butt.parents('form').find('[name="content"]').val();

		    	$.ajax({
					type: "POST",
					url: "http://comments/index.php?r=comments/addComment",
					data: { rootId: rootId, parentId: parentId, content: content },
					success: function function_name (data) {
						$newComment = $formCover.next('ul').find('li').first().clone();
						$newComment.find('[data-root-id]').attr('data-root-id', data.root_id);
						$newComment.find('[data-parent-id]').attr('data-parent-id', data.parent_id);
						$newComment.html(content);
						$formCover.next('ul').append($newComment);
						$formCover.next('ul').find('li').first().remove();
						$formCover.find('[data-cont="ajax-comment"]').hide();
						console.log(data);
					}
				});

		    	e.preventDefault();
		    });
	    }

	});

</script>