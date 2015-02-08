
<?php

class CommentsController extends Controller
{

	// для выборки нужного к-ва комментариев нужен глобальный offset
	public $offset = 0;

	// количество отображаемых дочерних комментариев для действия "actionAjaxComments"
	public $visibleCommentsCount = 2;

	// количество отображаемых на странице комментариев
	public $commentsPerRage = 10;

	// значение обозначающее, что комментарий рутовый
	public $rootParentId = 0;


	/*
	 * Часть кода для вывода комментариев с помощбю ajax
	 */


	/*
	 * Страница с выводом комментариев через ajax
	 */
	public function actionAjaxComments() {
		// номер страницы
		$page = Yii::app()->request->getParam('page');
		$page = $page ? $page : 1;

		$commentsPerRage = $this->commentsPerRage;
		$offset = ($page-1) * $commentsPerRage;

		$pagesCount = ceil($this->getRootCommentsCount() / $commentsPerRage);

		$rootComments = $this->getRootComment($commentsPerRage, $offset);

		$this->render('ajaxComments', [
			'comments' => $rootComments,
			'page' => $page,
			'pagesCount' => $pagesCount,
			'commentsPerRage' => $commentsPerRage,
			'visibleCommentsCount' => $this->visibleCommentsCount,
		]);
	}

	/*
	 * Получить все рутовые комментарии
	 */
	public function getRootComment($limit, $offset) {
		return Comments::model()->findAll([
			'condition' => 'parent_id = :rootParentId',
			'offset' => $offset,
			'limit' => $limit,
			'order' => 'id DESC',
			'params' => ['rootParentId' => $this->rootParentId]
		]);
	}

	/*
	 * Создать комментарий (для ajax запроса)
	 */
	public function actionAddComment() {
		$attrs = Yii::app()->request->getParam('commentAttrs');

		if(!empty($attrs)) {
			$model = new Comments;

			$model->attributes = $attrs;
			if($model->save()) {
				if($model->root_id == $this->rootParentId) {
					// если был создан рутовый коммент то назначить ему собственную группу
					$model->root_id = $model->id;
					$model->save();
				}

				$model->refresh();
				$model->created_at = TimeHelper::format($model->created_at);

				echo json_encode(['success' => true, 'attrs' => $model->attributes]);
			} else {
				echo json_encode(['success' => false, 'errors' => $model->errors]);
			}
		}
	}
	
	/*
	 * Возвращает дочерние комменты
	 */
	public function actionGetSubComments() {
		$id = Yii::app()->request->getParam('id');

		// вернуть все кроме уже показанных на странице комментов
		$offset = Yii::app()->request->getParam('offset');

		$comments = Comments::model()->findAll([
			'condition' => 'parent_id = :parentId',
			'order' => 'id DESC',
			'offset' => $offset,
			'params' => ['parentId' => $id]
		]);

		// собрать отображение всех выводимых комментариев
		$html = '';
		foreach($comments as $comment) {
			$html .= $this->renderPartial('_ajaxItemSubComment', [
				'sublevel' => $comment,
			], true);
		}

		echo $html;
	}


	/*
	 * Часть кода для обычного вывода комментариев
	 */


	/*
	 * Страница с простым выводом комментариев
	 */
	public function actionComments() {
		$attrs = Yii::app()->request->getParam('Comments');
		if($attrs) {
			$model = new Comments;

			// ajax валидация
			if(isset($_POST['ajax'])) {
				if($_POST['ajax'] == 'comment'.$attrs['parent_id'] or $_POST['ajax'] == 'newComment') {
					echo CActiveForm::validate($model);
				}
				Yii::app()->end();
			}

			$model->attributes = $attrs;
			$model->save();
			if($model->root_id == $this->rootParentId) {
				// если был создан рутовый коммент то назначить ему собственную группу
				$model->root_id = $model->id;
				$model->save();
			}
		}

		$page = Yii::app()->request->getParam('page');
		$page = $page ? $page : 1;

		$commentsPerRage = $this->commentsPerRage;
		$this->offset = ($page-1) * $commentsPerRage;

		$pagesCount = ceil($this->getAllCommentsCount() / $commentsPerRage);

		// получить комментарии
		$comments = $this->getComments();
		$comments = json_decode(json_encode($comments), FALSE);

		$this->render('index', [
			'pagesCount' => $pagesCount,
			'comments' => $comments,
		]);
	}

	/*
	 * Возвращает комментарии группами ("root_id"), т.е. не разрывая записи от общего рутового коммента
	 * Например "commentsPerRage" равен 100
	 * 	на первой странице:
	 * 		если в первом рутовом комментарии 60, во втором 30, а третем 40 всех дочерних комментариев - вернет первые 2
	 * 		если в первом рутовом комментарии 60, во втором 50 всех дочерних комментариев - вернет только первый
	 * 		если в первом рутовом комментарии 120 всех дочерних комментариев - вернет первый
	 */
	public function getComments($rootGroupsOffset = 0) {
		$commentsPerRage = $this->commentsPerRage;
		// получить к-во всех комментов
		$allCommentsCount = $this->getAllCommentsCount();
		// если offset больше чем к-во комментов вывести все
		if($this->offset >= $allCommentsCount) {
			return $this->getAllComments();
		}

		// получить к-во рутовых комментов
		$rootCommentsCount = $this->getRootCommentsCount($rootGroupsOffset);
		// получить столько групп комментов, что бы их общее к-во было наиболее приближено к "commentsPerRage"
		for ($i = 1; $i <= $rootCommentsCount; $i++) {
			// текущий набор рутовых id
			$rootIds = $this->getRootIds($i, $rootGroupsOffset);

			// получить к-во всех комментов для текущего набора (rootIds)
			$commentsCount = $this->getCommentsCountByIds($rootIds);

			// если первая группа комментов больше чем "commentsPerRage" вывести ее
			if($i == 1 && $commentsCount > $commentsPerRage) {
				return $this->getCommentsByIds($rootIds);
			}

			// если текущая страница не первая
			if($this->offset > 0 && $rootGroupsOffset == 0) {
				if($this->offset > $commentsCount) {
					continue;
				} elseif($commentsPerRage == $commentsCount) {
					$this->offset = 0;
					// вернуть комментарии пропустив рутовые комментарии не пподходящие по пагинации
					return $this->getComments(count($rootIds));
				} else {
					$this->offset = 0;
					// вернуть комментарии пропустив рутовые комментарии не пподходящие по пагинации
					// здесь "-1" потому, что к-во комментов в "rootIds" группах больше чем "commentsPerRage"
					return $this->getComments(count($rootIds)-1);
				}
			}

			if($commentsCount <= $commentsPerRage) {
				// если к-во комментов меньше "commentsPerRage" но рутовых комментов больше нет вывести все
				if($i == $rootCommentsCount) {
					return $this->getCommentsByIds($rootIds);
				}

				// если к-во комментов меньше "commentsPerRage" перейти к след итерации
				continue;
			} else {
				// если к-во набранных комментов больше чем "commentsPerRage" вывести их без последней группы
				array_pop($rootIds);
				return $this->getCommentsByIds($rootIds);
			}            
		}
	}

	/*
	 * Количество всех комментариев
	 */
	public function getAllCommentsCount() {
		return Yii::app()->db->createCommand("
			SELECT count(*) count FROM comments ORDER BY id
		")->queryRow()['count'];
	}

	/*
	 * Возвращает все комментарии
	 */
	public function getAllComments() {
		return $comments = Yii::app()->db->createCommand("
			SELECT * FROM comments
			ORDER BY CASE WHEN parent_id = ".$this->rootParentId." THEN id END DESC, CASE WHEN parent_id > ".$this->rootParentId." THEN id END ASC
		")->queryAll();
	}

	/*
	 * Количество рутовых комментариев
	 */
	public function getRootCommentsCount($offset = 0) {
		return Yii::app()->db->createCommand("
			SELECT count(*) count FROM (SELECT * FROM comments WHERE parent_id = 0 ORDER BY id DESC LIMIT 1111 OFFSET {$offset}) count
		")->queryRow()['count'];
	}

	/*
	 * Возвращает id всех рутовых комменитариев
	 */
	public function getRootIds($limit = -1, $offset = 0) {
		$rootIds = Yii::app()->db->createCommand()
			->select('id')
			->from('comments')
			->where('parent_id = :rootParentId', ['rootParentId' => $this->rootParentId])
			->order('id DESC')
			->limit($limit)
			->offset($offset)
			->queryAll();

		$ids = [];
		foreach ($rootIds as $key => $value) {
			$ids[] = $value['id'];
		}

		return $ids;
	}

	/*
	 * Возвращает к-во комментариев находящихся в "ids" группах
	 */
	public function getCommentsCountByIds($ids) {
		$ids = implode(',', $ids);

		if(!$ids) {
			return null;
		}

		return Yii::app()->db->createCommand("
			SELECT count(*) count FROM comments WHERE root_id IN({$ids}) ORDER BY id ASC
		")->queryRow()['count'];
	}

	/*
	 * Возвращает комментарии находящиеся в "ids" группах
	 */
	public function getCommentsByIds($ids) {
		$ids = implode(',', $ids);

		if(!$ids) {
			return null;
		}

		return $comments = Yii::app()->db->createCommand("
			SELECT * FROM comments WHERE root_id IN({$ids})
			ORDER BY CASE WHEN parent_id = ".$this->rootParentId." THEN id END DESC, CASE WHEN parent_id > ".$this->rootParentId." THEN id END ASC
		")->queryAll();
	}

	/*
	 * Строит дерево вложенных комментариев 
	 */
	public function getTree($comments, $parentId) {
		$html = '';
		foreach($comments as $comment) {
			if($comment->parent_id != $parentId) {
				continue;
			}

			// добавить комментарий в группу
			$html .= $this->renderPartial('_itemComment', [
				'comment' => $comment,
				'comments' => $comments
			], true);
		}

		if(!$html) {
			return '';
		}

		// обертка группы комментариев
		return $this->renderPartial('_coverComment', [
			'html' => $html,
		], true);
	}
}