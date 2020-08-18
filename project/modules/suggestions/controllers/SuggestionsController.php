<?php

namespace project\modules\suggestions\controllers;

use Craft;
use craft\elements\User;
use craft\helpers\AdminTable;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use project\modules\suggestions\models\SuggestionModel;
use project\modules\suggestions\services\SuggestionsService;
use project\modules\suggestions\SuggestionModule;
use Throwable;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class SuggestionsController
 *
 * @package project\modules\suggestions
 */
class SuggestionsController extends Controller
{

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionIndex(): Response
    {
        $this->requireCpRequest();
        $this->requirePermission('viewSuggestions');

        $statusCounts = SuggestionModule::$services->suggestionsService->getStatusCounts();

        return $this->renderTemplate('suggestions/suggestions_index.twig',
            [
                'statusCounts' => $statusCounts,
                'suggestionStatus' => SuggestionModule::$suggestionStatus
            ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionShow($id): Response
    {
        $this->requireCpRequest();
        $this->requirePermission('viewSuggestions');

        // This would be set if there is a preceding failed updateStatus action
        $suggestion = Craft::$app->urlManager->getRouteParams()['suggestion'] ?? null;

        if (!$suggestion) {
            $suggestion = SuggestionModel::find()->id($id)->trashed(null)->one();

            if (!$suggestion) {
                throw new NotFoundHttpException();
            }
        }

        return $this->renderTemplate('suggestions/suggestions_detail.twig', [
            'suggestion' => $suggestion
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

        /** @var SuggestionsService $service */
        $service = SuggestionModule::$services->suggestionsService;

        $suggestion = new SuggestionModel();
        $suggestion->scenario = SuggestionModel::SCENARIO_CREATE;
        $suggestion->attributes = $app->request->post('suggestion');
        $suggestion->status = 'open';

        if (!$service->saveSuggestion($suggestion)) {
            $app->session->setError(Craft::t('suggestions', 'We could not save your suggestion.'));
            $app->urlManager->setRouteParams([
                'suggestion' => $suggestion
            ]);
            return null;
        }

        $app->session->setNotice(Craft::t('suggestions', 'Thank you for your suggestion.'));
        return $this->redirectToPostedUrl(['uid' => $suggestion->uid]);
    }

    /**
     * @return Response|null
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdateStatus()
    {
        $this->requireCpRequest();
        $this->requirePostRequest();
        $this->requirePermission('viewSuggestions');

        $request = Craft::$app->request;
        $session = Craft::$app->session;
        $service = SuggestionModule::getInstance()->suggestionsService;

        $id = $request->getRequiredBodyParam('id');
        $suggestion = SuggestionModel::findOne($id);

        if (!$suggestion) {
            throw new NotFoundHttpException();
        }

        $entries = $request->getBodyParam('entries');
        $users = $request->getBodyParam('user');

        $suggestion->setSavePoint();

        $suggestion->status = $request->getRequiredBodyParam('status');
        $suggestion->relatedEntryId = $entries ? $entries[0] : '';
        $suggestion->userId = $users ? $users[0] : '';
        $suggestion->notes = $request->getRequiredBodyParam('notes');

        if (!$suggestion->hasChangedAttributes(['status','relatedEntryId','userId','notes'])) {
            $session->setError(Craft::t('suggestions', 'Nothing has changed'));
            return null;
        }

        if (!$service->saveSuggestion($suggestion)) {
            $session->setError(Craft::t('suggestions', 'Could not save status update'));
            Craft::$app->urlManager->setRouteParams([
                'suggestion' => $suggestion
            ]);

            return null;
        }

        $session->setNotice(Craft::t('suggestions', 'Status update saved'));

        return $this->redirectToPostedUrl();
    }

    /**
     * @param null $id
     * @return Response|null
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionDelete($id = null)
    {
        $this->requireCpRequest();
        $this->requirePermission('viewSuggestions');

        $request = Craft::$app->request;
        $session = Craft::$app->session;
        $service = SuggestionModule::getInstance()->suggestionsService;

        if ($request->getAcceptsJson()) {
            $id = $request->getRequiredBodyParam('id');
        }

        $suggestion = SuggestionModel::findOne($id);

        $title = 'n/a';
        if ($suggestion) {
            $title = $suggestion->title;
            $service->deleteSuggestion($suggestion);
            $suggestion = SuggestionModel::findOne($id);
        }

        if ($suggestion) {
            $message = Craft::t('suggestions', 'Could not delete suggestion');
            if ($request->getAcceptsJson()) {
                return $this->asErrorJson($message);
            }

            $session->setError($message);
            return null;
        }

        if ($request->getAcceptsJson()) {
            return $this->asJson([
                'success' => true
            ]);
        }
        $session->setNotice("\"{$title}\"" . Craft::t('suggestions', 'deleted'));
        return $this->redirect(UrlHelper::cpUrl('suggestions'));
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws Exception
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \Exception
     */
    public function actionData(): Response
    {
        $this->requireAcceptsJson();
        $this->requirePermission('viewSuggestions');

        $page = Craft::$app->request->getParam('page') ?: 1;
        $limit = Craft::$app->request->getParam('per_page') ?: 40;
        $sort = Craft::$app->request->getParam('sort') ?: 'id desc';
        $sort = str_replace('|', ' ', $sort);

        $query = SuggestionModel::find();

        $status = Craft::$app->request->getParam('status');
        if ($status) {
            $query = $status == 'trashed' ? $query->trashed() : $query->status($status);
        }

        $userId = Craft::$app->request->getParam('userId');
        if ($userId) {
            $query = $query->userId($userId)->status('atwork');
        }

        $search = Craft::$app->request->getParam('search');
        if ($search) {
            $query = $query->search($search);
        }

        $count = $query->count();
        $pagination = AdminTable::paginationLinks($page, $count, $limit);

        $offset = ($page - 1) * $limit;
        $suggestions = $query->orderBy($sort)->offset($offset)->limit($limit)->all();

        $suggestionStatus = SuggestionModule::$suggestionStatus;

        $data = [];

        foreach ($suggestions as $suggestion) {
            /** @var SuggestionModel $suggestion */
            $user = $suggestion->userId ? User::find()->id($suggestion->userId)->one() : null;

            $data[] = [
                'id' => $suggestion->id,
                'title' => $suggestion->title,
                'url' => $suggestion->cpEditUrl,
                'status' => $suggestion->status == 'accepted',
                'name' => $suggestion->name,
                'email' => $suggestion->email,
                'date' => $suggestion->dateCreated->format('Y-m-d G:i'),
                'suggestionStatus' => $suggestionStatus[$suggestion->status],
                'user' => $user ? $user->name : '',
                'detail' => [
                    'handle' => ' <span data-icon="info"></span>',
                    'content' => Craft::$app->view->renderTemplate(
                        'suggestions/partials/suggestions_more.twig', ['suggestion' => $suggestion])
                ],
                'menu' => [
                    'label' => Craft::t('suggestions', 'Page'),
                    'url' => UrlHelper::siteUrl('suggestion/' . $suggestion->uid)
                ]
            ];
        }

        return $this->asJson(compact('pagination', 'data'));
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     */
    public function actionSelfAssign(): Response
    {
        $this->requireAcceptsJson();
        $this->requirePermission('viewSuggestions');

        $ids = Craft::$app->request->getBodyParam('ids');
        if (!$ids) {
            return $this->asJson(['success' => false]);
        }

        $suggestions = SuggestionModel::find()->where(['in', 'id', $ids])->all();
        $userId = Craft::$app->user->identity->id;

        /** @var SuggestionModel $suggestion */
        foreach ($suggestions as $suggestion) {

            if ($suggestion->userId != $userId) {
                $suggestion->userId = $userId;
                $suggestion->status = 'atwork';
                SuggestionModule::$services->suggestionsService->saveSuggestion($suggestion);
            }
        }

        return $this->asJson(['success' => true, 'ids' => $ids]);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     */
    public function actionSetStatus(): Response
    {

        $this->requireAcceptsJson();
        $this->requirePermission('viewSuggestions');

        $ids = Craft::$app->request->getBodyParam('ids');
        if (!$ids) {
            return $this->asJson(['success' => false]);
        }

        $status = Craft::$app->request->getBodyParam('status');
        if (!$status) {
            return $this->asJson(['success' => false]);
        }

        $suggestions = SuggestionModel::find()->where(['in', 'id', $ids])->all();
        foreach ($suggestions as $suggestion) {
            if ($suggestion->status != $status) {
                $suggestion->status = $status;
                SuggestionModule::$services->suggestionsService->saveSuggestion($suggestion);
            }
        }

        return $this->asJson(['success' => true, 'ids' => $ids]);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     */
    public function actionRestore(): Response
    {
        $this->requireAcceptsJson();
        $this->requirePermission('viewSuggestions');

        $ids = Craft::$app->request->getBodyParam('ids');
        if (!$ids) {
            return $this->asJson(['success' => false]);
        }

        $suggestions = SuggestionModel::find()->trashed()->where(['in', 'id', $ids])->all();
        foreach ($suggestions as $suggestion) {
            SuggestionModule::$services->suggestionsService->restoreSuggestion($suggestion);
        }

        return $this->asJson(['success' => true, 'ids' => $ids]);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionMassDelete(): Response
    {
        $this->requireAcceptsJson();
        $this->requirePermission('viewSuggestions');

        $ids = Craft::$app->request->getBodyParam('ids');
        if (!$ids) {
            return $this->asJson(['success' => false]);
        }

        $suggestions = SuggestionModel::find()->where(['in', 'id', $ids])->all();
        foreach ($suggestions as $suggestion) {
            SuggestionModule::$services->suggestionsService->deleteSuggestion($suggestion);
        }

        return $this->asJson(['success' => true, 'ids' => $ids]);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionHardDelete(): Response
    {
        $this->requireAcceptsJson();
        $this->requirePermission('viewSuggestions');

        $ids = Craft::$app->request->getBodyParam('ids');
        if (!$ids) {
            return $this->asJson(['success' => false]);
        }

        $suggestions = SuggestionModel::find()->trashed()->where(['in', 'id', $ids])->all();
        foreach ($suggestions as $suggestion) {
            SuggestionModule::$services->suggestionsService->hardDeleteSuggestion($suggestion);
        }

        return $this->asJson(['success' => true, 'ids' => $ids]);
    }


    protected $allowAnonymous = ['create'];

}
