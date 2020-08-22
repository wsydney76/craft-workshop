<?php

namespace project\modules\ads\controllers;

use Craft;
use craft\db\Paginator;
use craft\helpers\AdminTable;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use project\modules\ads\AdsModule;
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
    public $allowAnonymous = ['new', 'create', 'index'];

    /**
     * @param string $type
     * @return Response
     */
    public function actionIndex($type = '') :Response
    {
        $query = AdRecord::find()->status('active');

        if ($type) {
            if ($type == 'myads') {
                $this->requireLogin();
                $query = $query->user(Craft::$app->user->identity);
            } else {
                $query = $query->type($type);
            }
        }

        $paginator = new Paginator($query, [
            'pageSize' => AdsModule::PERPAGE,
            'currentPage' => Craft::$app->request->getParam('page', 1)
        ]);

        return $this->renderTemplate('_ads/list', [
            'ads' => $paginator->getPageResults(),
            'type' => $type,
            'paginator' => $paginator
        ]);
    }

    /**
     * @return Response
     */
    public function actionNew() :Response
    {
        $ad = Craft::$app->urlManager->getRouteParams()['ad'] ?? new AdRecord();

        $user = Craft::$app->user->identity;
        if ($user) {
            $ad->email = $user->email;
        }

        return $this->renderTemplate('_ads/new', [
            'ad' => $ad
        ]);
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
    public function actionEdit($id) :Response
    {
        $this->requirePermission('editAds');

        $ad = Craft::$app->urlManager->getRouteParams()['ad'] ?? AdRecord::findOne($id);

        if (!$ad) {
            throw new NotFoundHttpException();
        }

        return $this->renderTemplate('ads/edit', [
            'ad' => $ad
        ]);
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
     * @param int $id
     * @return Response
     * @throws Exception
     * @throws ForbiddenHttpException
     */
    public function actionWithdraw($id = 0) :Response
    {

        $app = Craft::$app;

        $user = $app->user->identity;
        if (!$user) {
            throw new ForbiddenHttpException();
        }

        $ad = AdRecord::findOne($id);

        if (!$ad) {
            throw new ForbiddenHttpException();
        }

        if ($user->email != $ad->email) {
            throw new ForbiddenHttpException();
        }

        $ad->status = 'closed';

        if (!$ad->save()) {
            $app->session->setError('Could not withdraw Ad');
            return $this->redirect($app->request->referrer);
        }

        $app->session->setNotice(Craft::t('site', 'Ad withdrawn'));
        return $this->redirect(UrlHelper::siteUrl('ads/myads'));
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionDelete() :Response
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
    public function actionData() :Response
    {
        $this->requireAcceptsJson();
        $this->requirePermission('editAds');

        $page = Craft::$app->request->getParam('page') ?: 1;
        $limit = Craft::$app->request->getParam('per_page') ?: AdsModule::PERPAGE;
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
                'status' => $ad->status == 'open' && $ad->dateCreated > date('Y-m-d', strtotime(AdsModule::ACTIVEPERIOD)),

                'email' => $ad->email,
                'date' => $ad->dateCreatedLocal()->format('Y-m-d G:i'),

                'detail' => [
                    'handle' => Stringy::create($ad->text)->shortenAfterWord(40),
                    'content' => Craft::$app->view->renderTemplate('ads/detail', [
                        'ad' => $ad
                    ])
                ]
            ];
        }

        return $this->asJson(compact('pagination', 'data'));
    }
}
