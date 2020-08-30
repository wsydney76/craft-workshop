<?php

namespace project\modules\ads\controllers;

use Craft;
use craft\db\Paginator;
use craft\helpers\AdminTable;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use project\modules\ads\AdsModule;
use project\modules\ads\models\AdModel;
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
use function ucfirst;

class AdsController extends Controller
{
    public $allowAnonymous = ['new', 'create', 'index'];

    /**
     * @param string $type
     * @return Response
     */
    public function actionIndex($type = ''): Response
    {

        $settings = AdsModule::getInstance()->settings;
        $request = Craft::$app->request;
        $user = Craft::$app->user->identity;

        $query = AdModel::find()->status('active');

        if ($type) {
            if ($type == 'myads') {
                $this->requireLogin();
                $query = $query->user($user);
            } else {
                $query = $query->type($type);
            }
        }

        $paginator = new Paginator($query, [
            'pageSize' => $settings->perPage,
            'currentPage' => $request->getParam('page', 1)
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
    public function actionNew(): Response
    {

        $user = Craft::$app->user->identity;
        $email = $user ? $user->email : '';

        $ad = Craft::$app->urlManager->getRouteParams()['ad'] ?? new AdModel(['email' => $email]);

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

        $request = Craft::$app->request;
        $session = Craft::$app->session;
        $urlManager = Craft::$app->urlManager;

        $ad = new AdModel();
        $ad->scenario = AdModel::SCENARIO_CREATE;
        $ad->attributes = $request->post('ad');
        $ad->status = 'open';

        if (!$ad->save()) {
            $session->setError(Craft::t('site', 'We could not save your ad.'));
            $urlManager->setRouteParams([
                'ad' => $ad
            ]);
            return null;
        }

        $session->setNotice(Craft::t('site', 'We accepted your ad, thank you'));
        return $this->redirectToPostedUrl(['id' => $ad->id]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionEdit($id): Response
    {
        $this->requirePermission('editAds');

        $ad = Craft::$app->urlManager->getRouteParams()['ad'] ?? AdModel::findOne($id);

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

        $request = Craft::$app->request;
        $session = Craft::$app->session;
        $urlManager = Craft::$app->urlManager;

        $ad = AdModel::findOne($request->getRequiredBodyParam('id'));

        if (!$ad) {
            throw new NotFoundHttpException();
        }

        $ad->scenario = AdModel::SCENARIO_UPDATE;
        $ad->attributes = $request->post('ad');

        if (!$ad->getDirtyAttributes()) {
            $session->setError(Craft::t('ads', 'Nothing has changed'));
            return null;
        }

        if (!$ad->save()) {
            $session->setError(Craft::t('ads', 'Could not save ad.'));
            $urlManager->setRouteParams([
                'ad' => $ad
            ]);
            return null;
        }

        $session->setNotice(Craft::t('ads', 'Ad saved'));
        return $this->redirectToPostedUrl();
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionSetStatus(): Response
    {
        $this->requireAcceptsJson();
        $this->requirePermission('editAds');

        $request = Craft::$app->request;

        $ids = $request->getRequiredBodyParam('ids');
        $status = $request->getRequiredBodyParam('status');

        $ads = AdModel::find()
            ->where(['in', 'id', $ids])
            ->all();

        foreach ($ads as $ad) {
            $ad->status = $status;
            $ad->save();
        }

        return $this->asJson(['success' => true, 'ids' => $ids]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws Exception
     * @throws ForbiddenHttpException
     */
    public function actionWithdraw($id = 0): Response
    {

        $session = Craft::$app->session;
        $user = Craft::$app->user->identity;

        if (!$user) {
            throw new ForbiddenHttpException();
        }

        $ad = AdModel::findOne($id);

        if (!$ad) {
            throw new ForbiddenHttpException();
        }

        if ($user->email != $ad->email) {
            throw new ForbiddenHttpException();
        }

        $ad->status = 'closed';

        if (!$ad->save()) {
            $session->setError('Could not withdraw Ad');
            return $this->redirect($app->request->referrer);
        }

        $session->setNotice(Craft::t('site', 'Ad withdrawn'));
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
    public function actionDelete(): Response
    {
        $this->requireAcceptsJson();
        $this->requirePermission('editAds');

        $id = Craft::$app->request->getRequiredBodyParam('id');

        $ad = AdModel::findOne($id);
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
    public function actionTableData(): Response
    {
        $settings = AdsModule::getInstance()->settings;
        $this->requireAcceptsJson();
        $this->requirePermission('editAds');

        $request = Craft::$app->request;
        $view = Craft::$app->view;
        $formatter = Craft::$app->formatter;

        $page = $request->getParam('page') ?: 1;
        $limit = $request->getParam('per_page') ?: $settings->perPage;
        $orderBy = $request->getParam('sort') ?: 'dateCreated desc';
        $orderBy = str_replace('|', ' ', $orderBy);

        $query = AdModel::find();

        foreach (['search', 'type', 'status', 'email'] as $param) {
            $query = $query->$param($request->getParam($param));
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
                'status' => $ad->status == 'open' && $ad->dateCreated > date('Y-m-d', strtotime($settings->activePeriod)),

                'email' => $ad->email,
                'statusText' => ucfirst($ad->status),
                'date' => $formatter->asRelativeTime($ad->dateCreatedLocal),

                'detail' => [
                    'handle' => Stringy::create($ad->text)->shortenAfterWord(40),
                    'content' => $view->renderTemplate('ads/detail', [
                        'ad' => $ad
                    ])
                ]
            ];
        }

        return $this->asJson(compact('pagination', 'data'));
    }
}
