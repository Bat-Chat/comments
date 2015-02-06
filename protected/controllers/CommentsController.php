
<?php

class CommentsController extends Controller
{

	// для выборки нужного к-ва комментариев нужен глобальный offset
	public $offset = 0;

	// количество отображаемых дочерних комментариев для действия "actionAjaxComments"
	public $visibleCommentsCount = 2;

	// количество отображаемых на странице комментариев
	public $commentsPerRage = 3;


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

		$pagesCount = ceil($this->getRootCount()['count'] / $commentsPerRage);

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
			'condition' => 'parent_id = 0',
			'offset' => $offset,
			'limit' => $limit,
			'order' => 'id DESC'
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
				if($model->root_id == 0) {
					// если был создан рутовый коммент то назначить ему собственную группу
					$model->root_id = $model->id;
					$model->save();
				}

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

		// указывает или вернуть все или все кроме уже показанных на странице комментов ($this->visibleCommentsCount)
		$isShortcut = Yii::app()->request->getParam('isShortcut');
		$isShortcut = filter_var($isShortcut, FILTER_VALIDATE_BOOLEAN);

		$comments = Comments::model()->findAll('parent_id = :id', ['id' => $id]);
		if ($isShortcut) {
			// удалить уже показанные комментарии
			array_splice($comments, -$this->visibleCommentsCount, $this->visibleCommentsCount);
		}

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

			if(isset($_POST['ajax'])) {
				if($_POST['ajax'] == 'comm'.$attrs['parent_id'] or $_POST['ajax'] == 'newComment') {
					echo CActiveForm::validate($model);
				}
				Yii::app()->end();
			}

			$model->attributes = $attrs;
			$model->save();
			if($attrs['root_id'] == 0) {
				$model->root_id = $model->id;
				$model->save();
			}
		}

		$page = Yii::app()->request->getParam('page');
		$page = $page ? $page : 1;

		$commentsPerRage = 10;
		$this->offset = ($page-1) * $commentsPerRage;

		$pagesCount = ceil($this->getCommentsCount()['count'] / $commentsPerRage);

		$comments = $this->getComments($commentsPerRage);
		$comments = json_decode(json_encode($comments), FALSE);

		$this->render('index', [
			'pagesCount' => $pagesCount,
			'comments' => $comments,
		]);
	}

	public function getComments($commentsPerRage, $rootOffset = 0) {
		$commCount = $this->getCommentsCountByIds($this->getRootIds(1111));
		if($this->offset >= $commCount['count']) {
			return $this->getCommentsByIds($this->getRootIds(1111));
		}

		$rootCount = $this->getRootCount($rootOffset);
		for ($i = 1; $i <= $rootCount['count']; $i++) {
			$rootIds = $this->getRootIds($i, $rootOffset);

			$commentsCount = $this->getCommentsCountByIds($rootIds);

			// если первая группа комментов больше чем лимит вывести ее
			if($i == 1 && $commentsCount['count'] > $commentsPerRage) {
				return $this->getCommentsByIds($rootIds);
			}

			if($this->offset > 0 && $rootOffset == 0) {
				if($this->offset > $commentsCount['count']) {
					continue;
				} elseif($commentsPerRage == $commentsCount['count']) {
					$this->offset = 0;
					$rootOffsett = count($rootIds);
					return $this->getComments($commentsPerRage, $rootOffsett);
				} else {
					$this->offset = 0;
					$rootOffsett = count($rootIds)-1;
					return $this->getComments($commentsPerRage, $rootOffsett);
				}
			} elseif($this->offset > 0 && $rootOffset > 0) {
				print_r($commentsCount['count']);die;
			}

			if($commentsCount['count'] <= $commentsPerRage) {
				// если к-во комментов меньше лимита но рутовых комментов больше нет вывести все
				if($i == $rootCount['count']) {
					return $this->getCommentsByIds($rootIds);
				}
				// если к-во комментов меньше лимита перейти к след итерации
				continue;
			} else {
				array_pop($rootIds);
				return $this->getCommentsByIds($rootIds);
			}            
		}
	}

	public function getCommentsCount() {
		return Yii::app()->db->createCommand("
			SELECT count(*) count FROM comments ORDER BY id
		")->queryRow();
	}

	public function getRootCount($rootOffset = 0) {
		return Yii::app()->db->createCommand("
			SELECT count(*) count FROM (SELECT * FROM comments WHERE parent_id = 0 ORDER BY id DESC LIMIT 1111 OFFSET {$rootOffset}) rc
		")->queryRow();
	}

	public function getRootIds($index, $rootOffset = 0) {
		$rootIds = Yii::app()->db->createCommand("
			SELECT id FROM comments WHERE parent_id = 0 ORDER BY id DESC LIMIT {$index} OFFSET {$rootOffset}
		")->queryAll();

		$ids = [];
		foreach ($rootIds as $key => $value) {
			$ids[] = $value['id'];
		}

		return $ids;
	}

	public function getCommentsCountByIds($ids) {
		$ids = implode(',', $ids);

		if(!$ids) {
			return null;
		}

		return Yii::app()->db->createCommand("
			SELECT count(*) count FROM (SELECT * from comments WHERE root_id IN({$ids}) ORDER BY id ASC) c
		")->queryRow();
	}

	public function getCommentsByIds($ids) {
		$ids = implode(',', $ids);

		if(!$ids) {
			return null;
		}

		return $comments = Yii::app()->db->createCommand("
			SELECT * FROM comments WHERE root_id IN({$ids})
			ORDER BY CASE WHEN parent_id = 0 THEN id END DESC, CASE WHEN parent_id > 0 THEN id END ASC
		")->queryAll();
	}

	public function getTree($comments, $parentId) {
		$html = '';
		foreach($comments as $row) {
			if($row->parent_id == $parentId) {
				$html .= $this->renderPartial('_itemComment', [
					'row' => $row,
					'comments' => $comments
				], true);
			}
		}

		if(!$html) {
			return '';
		}

		return $this->renderPartial('_coverComment', [
			'html' => $html,
		], true);
	}
}