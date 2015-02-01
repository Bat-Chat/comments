<?php

class m150123_165629_addCommentsTable extends CDbMigration
{

	public function safeUp()
	{
		// $this->dropTable('comments');

		$this->createTable('comments', [
            'id' => 'pk',
            'content' => 'string',
            'parent_id' => 'integer DEFAULT 0',
            'root_id' => 'integer DEFAULT 0',
        ]);


		// $k = -1;
		// for ($i=1; $i <= 100; $i++) {
		// 	$k = $k+1;
		// 	if ($k == 10) {
		// 		$k = 0;
		// 	}
		// 	echo $i. '__' . $k ."\n";
		// 	// echo $k . "\n";
	 //        $this->insert('comments', array(
		// 		'id' => $i,
		// 		'parent_id' => $k,
		// 		'content' => $i . '_____' . $k,
		// 	));
		// }

		// 	$r = 1;
		// for ($i=1; $i <= 1000; $i++) { 
		// 	$k = rand(0, $i);
		// 	if ($k == 0) {
		// 		$r = $i;
		// 	}
		// 	$this->insert('comments', array(
		// 		'id' => $i,
		// 		'parent_id' => $k,
		// 		'content' => $i . '_____',
	 //            'root_id' => $r,
		// 	));
		// }

		// for ($i=1; $i <= 10; $i++) { 
		// 	$this->insert('comments', array(
		// 		'id' => $i,
		// 		'parent_id' => $i-1,
		// 		'root_id' => 1,
		// 		'content' => 'id '.$i. '---level '.($i-1),
		// 	));
		// }


		$roots = [1,101,201,301,401,501,601,701,801,901];
		$rootId = null;
		for ($i=1; $i <= 1000; $i++) {
			$parentId = $i-1;
			if (in_array($i, $roots)) {
				$rootId = $i;
				$parentId = 0;
			}
			$this->insert('comments', array(
				'id' => $i,
				'parent_id' => $parentId,
				'root_id' => $rootId,
				// 'content' => 'content '.$i,
				'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero dolores, consequuntur nisi eum alias ipsa delectus numquam quidem, perspiciatis beatae at, assumenda accusantium? Optio nam architecto doloribus eligendi, eum soluta minus fugit rem perspiciatis maxime odio sapiente consectetur cumque ea, placeat praesentium? Aliquam similique, dolores quidem distinctio deleniti vel iure possimus non ipsam earum accusamus aspernatur neque magni facere, blanditiis voluptate adipisci illum, saepe soluta laudantium quos amet! Magni distinctio alias eveniet saepe doloribus quasi, suscipit nam eos aperiam cumque voluptate quam, cupiditate incidunt obcaecati harum reiciendis repellat minima! Illum sed consequatur voluptates. Perspiciatis ea vero inventore voluptate ab odio reprehenderit, odit soluta ut iusto quam nam voluptates, ad error temporibus officia repudiandae vel esse fugiat autem eligendi dolorum in velit. Excepturi totam, distinctio a quod, praesentium rem sit consequatur culpa, magni mollitia alias. Tempora praesentium, ad ullam quisquam facere nobis et nemo earum quas tempore dolores voluptatibus, dolor quidem.',
			));
		}

		// return false;
	}

	public function safeDown()
	{
		$this->dropTable('comments');
	}
}