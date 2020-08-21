<?php

namespace project\modules\ads\controllers;

use Craft;
use craft\helpers\AdminTable;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use project\modules\ads\records\AdsRecord;
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
     * @return Response
     */
    public function actionNew()
    {
        $ad = Craft::$app->urlManager->getRouteParams()['ad'] ?? new AdsRecord();
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

        $ad = new AdsRecord();
        $ad->scenario = AdsRecord::SCENARIO_CREATE;
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
        return $this->redirectToPostedUrl(['type' => $ad->type]);
    }

    /**
     * @param string $type
     * @return Response
     */
    public function actionIndex($type = '')
    {
        $query = AdsRecord::find()
            ->orderBy('dateCreated desc');

        if ($type) {
            $query = $query->where(['type' => $type]);
        }
        $ads = $query->all();

        return $this->renderTemplate('_ads/list', ['ads' => $ads, 'type' => $type]);
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

        $ad = AdsRecord::findOne($id);
        if (!$ad) {
            throw new NotFoundHttpException();
        }

        if (!$ad->delete()) {
            return $this->asErrorJson('Could not delete Ad');
        }

        return $this->asJson(['success' => true]);
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

        $ad = Craft::$app->urlManager->getRouteParams()['ad'] ?? AdsRecord::findOne($id);

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

        $ad = AdsRecord::findOne($app->request->getRequiredBodyParam('id'));

        if (!$ad) {
            throw new NotFoundHttpException();
        }

        $ad->scenario = AdsRecord::SCENARIO_UPDATE;
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
        $sort = Craft::$app->request->getParam('sort') ?: 'id desc';
        $sort = str_replace('|', ' ', $sort);

        $query = AdsRecord::find();

        $search = Craft::$app->request->getParam('search');
        if ($search) {
            $query = $query->where([
                'or',
                ['like', 'title', $search],
                ['like', 'text', $search],
                ['like', 'email', $search],
            ]);
        }

        $count = $query->count();
        $pagination = AdminTable::paginationLinks($page, $count, $limit);

        $offset = ($page - 1) * $limit;
        $ads = $query->orderBy($sort)->offset($offset)->limit($limit)->all();

        $data = [];
        foreach ($ads as $ad) {
            $data[] = [
                'id' => $ad->id,
                'type' => ucfirst($ad->type),
                'title' => $ad->title,
                'url' => UrlHelper::cpUrl("ads/edit/{$ad->id}"),
                'email' => $ad->email,
                'status' => $ad->status == 'closed',
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
