<?php
/**
 * @link https://github.com/gromver/yii2-platform-news.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-news/blob/master/LICENSE
 * @package yii2-platform-news
 * @version 1.0.0
 */

namespace gromver\platform\news\controllers\backend;


use gromver\platform\core\controllers\BackendController;
use gromver\platform\news\models\Category;
use gromver\platform\news\models\Post;
use gromver\platform\news\models\PostSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * Class PostController implements the CRUD actions for Post model.
 * @package yii2-platform-news
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PostController extends BackendController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'delete'],
                    'bulk-delete' => ['post', 'delete'],
                    'publish' => ['post'],
                    'unpublish' => ['post'],
                    'ordering' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'delete', 'publish', 'unpublish', 'index', 'view', 'select'],
                        'roles' => ['readPost'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['createPost'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['ordering'],
                        'roles' => ['updatePost'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['bulk-delete'],
                        'roles' => ['deletePost'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param string $route
     * @return mixed
     */
    public function actionSelect($route = 'news/frontend/post/view')
    {
        $searchModel = new PostSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        Yii::$app->applyModalLayout();

        return $this->render('select', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'route' => $route
            ]);
    }

    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param string|null $category_id
     * @param string|null $backUrl
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($category_id = null, $backUrl = null)
    {
        $model = new Post();
        $model->loadDefaultValues();
        $model->status = Post::STATUS_PUBLISHED;

        if (isset($category_id) && $category = Category::findOne($category_id)) {
            /** @var Category $category */
            $model->category_id = $category->id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect($backUrl ? $backUrl : ['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param null $backUrl
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id, $backUrl = null)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->can('updatePost', ['post' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect($backUrl ? $backUrl : ['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->can('deletePost', ['post' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionBulkDelete()
    {
        $data = Yii::$app->request->getBodyParam('data', []);

        $models = Post::find()->where(['id'=>$data])->all();

        foreach($models as $model) {
            $model->delete();
        }

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * @param integer $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionPublish($id)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->can('updatePost', ['post' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $model->status = Post::STATUS_PUBLISHED;
        $model->save();

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * @param integer $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUnpublish($id)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->can('updatePost', ['post' => $model])) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        $model->status = Post::STATUS_UNPUBLISHED;
        $model->save();

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * @return \yii\web\Response
     */
    public function actionOrdering()
    {
        $data = Yii::$app->request->getBodyParam('data', []);

        foreach($data as $id => $order){
            if($target = Post::findOne($id)) {
                $target->updateAttributes(['ordering'=>$order]);
            }
        }

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested page does not exist.'));
        }
    }
}
