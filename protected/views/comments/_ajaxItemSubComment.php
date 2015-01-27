<li>
	<?= $sublevel->id ?>


	<div class="row">
		<?php if ($sublevel->sublevel): ?>
			<button role="show-more" data-id="<?= $sublevel->id ?>">Показать все</button>
		<?php endif ?>
	</div>
</li>
<script>
	$( document ).ready(function() {
	    $('[role="show-more"]').off().click(function (e) {
			$butt = $(this);
			$.ajax({
				type: "POST",
				url: "http://comments/index.php?r=comments/getMore",
				data: { id: $(this).attr('data-id'), location: "Boston" },
				success: function function_name (data) {
					console.log(data);
					$butt.parent().append('<p>'+data+'</p>');
				}
			});

			e.preventDefault();
	    });
	});

</script>