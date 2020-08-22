<?php

namespace project\modules\ads\controllers;

use Craft;
use craft\helpers\AdminTable;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use project\modules\ads\records\AdRecord;
use Stringy\Stringy;
use Throwable;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;
use yii\db\StaleObjectException;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AdsController extends Controller
{
    public $allowAnonymous = ['new', 'create', 'find'];

    /**
     * @param string $type
     * @return Response
     */
    public function actionIndex($type = '')
    {
        $query = AdRecord::find()->status('open');

        if ($type) {
            $query = $query->type($type);
        }
        $ads = $query->all();

        return $this->renderTemplate('_ads/list', ['ads' => $ads, 'type' => $type]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionShow($id)
    {
        $ad = AdRecord::find()->status('open')->id($id)->one();
        if (!$ad) {
            throw new NotFoundHttpException();
        }

        return $this->renderTemplate('_ads/show', ['ad' => $ad]);
    }

    /**
     * @return Response
     */
    public function actionNew()
    {
        $ad = Craft::$app->urlManager->getRouteParams()['ad'] ?? new AdRecord();
        return $this->renderTemplate('_ads/new', ['ad' => $ad]);
    }

    /**
     * @return Response|null
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        $this->requirePostRequest();

        $app = Craft::$app;

        $ad = new AdRecord();
        $ad->scenario = AdRecord::SCENARIO_CREATE;
        $ad->attributes = $app->request->post('ad');
        $ad->status = 'open';

        if (!$ad->save()) {
            $app->session->setError(Craft::t('site', 'We could not save your ad.'));
            $app->urlManager->setRouteParams([
                'ad' => $ad
            ]);
            return null;
        }

        $app->session->setNotice(Craft::t('site', 'We accepted your ad, thank you'));
        return $this->redirectToPostedUrl(['id' => $ad->id]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionEdit($id)
    {
        $this->requirePermission('editAds');

        $ad = Craft::$app->urlManager->getRouteParams()['ad'] ?? AdRecord::findOne($id);

        if (!$ad) {
            throw new NotFoundHttpException();
        }

        return $this->renderTemplate('ads/edit', ['ad' => $ad]);
    }

    /**
     * @return Response|null
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionUpdate()
    {
        $this->requirePostRequest();
        $this->requirePermission('editAds');

        $app = Craft::$app;

        $ad = AdRecord::findOne($app->request->getRequiredBodyParam('id'));

        if (!$ad) {
            throw new NotFoundHttpException();
        }

        $ad->scenario = AdRecord::SCENARIO_UPDATE;
        $ad->attributes = $app->request->post('ad');

        if (!$ad->save()) {
            $app->session->setError(Craft::t('site', 'Could not save ad.'));
            $app->urlManager->setRouteParams([
                'ad' => $ad
            ]);
            return null;
        }

        $app->session->setNotice(Craft::t('site', 'Ad saved'));
        return $this->redirectToPostedUrl();
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionDelete()
    {
        $this->requireAcceptsJson();
        $this->requirePermission('editAds');

        $id = Craft::$app->request->getRequiredBodyParam('id');

        $ad = AdRecord::findOne($id);
        if (!$ad) {
            throw new NotFoundHttpException();
        }

        if (!$ad->delete()) {
            return $this->asErrorJson('Could not delete Ad');
        }

        return $this->asJson(['success' => true]);
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionData()
    {
        $this->requireAcceptsJson();
        $this->requirePermission('editAds');

        $page = Craft::$app->request->getParam('page') ?: 1;
        $limit = Craft::$app->request->getParam('per_page') ?: 20;
        $orderBy = Craft::$app->request->getParam('sort') ?: 'dateCreated desc';
        $orderBy = str_replace('|', ' ', $orderBy);

        $query = AdRecord::find();

        $search = Craft::$app->request->getParam('search');
        if ($search) {
            $query = $query->search($search);
        }

        $count = $query->count();
        $pagination = AdminTable::paginationLinks($page, $count, $limit);

        $offset = ($page - 1) * $limit;
        $ads = $query->orderBy($orderBy)->offset($offset)->limit($limit)->all();

        $data = [];
        foreach ($ads as $ad) {
            $data[] = [
                'id' => $ad->id,
                'type' => ucfirst($ad->type),
                'title' => $ad->title,
                'url' => UrlHelper::cpUrl("ads/{$ad->id}"),
                'email' => $ad->email,
                'status' => $ad->status == 'open',
                'date' => $ad->dateCreatedLocal()->format('Y-m-d G:i'),
                'detail' => [
                    'handle' => Stringy::create($ad->text)->shortenAfterWord(40),
                    'content' => Craft::$app->view->renderTemplate('ads/detail', ['ad' => $ad])
                ]
            ];
        }

        return $this->asJson(compact('pagination', 'data'));
    }
}
