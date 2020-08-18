<?php

namespace project\modules\main\modules\members\controllers;

use Craft;
use craft\elements\Entry;
use craft\errors\ElementNotFoundException;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use Throwable;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use function in_array;

class MembersController extends Controller
{

    /**
     * @param $id
     * @return \craft\web\Response|\yii\console\Response|Response
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function actionAddToWatchlist($id)
    {
        $this->requireLogin();

        $entry = Entry::find()->site('*')->id($id)->one();

        if (!$entry) {
            throw  new NotFoundHttpException();
        }

        $user = Craft::$app->user->identity;

        // Get old values
        $ids = $user->watchList->site('*')->unique()->anyStatus()->ids();

        // do nothing if new id already in list
        if (in_array($id, $ids, false)) {
            return $this->_getReturnObject(false, 'Item already exists in watchlist.');
        }

        // Add new value and sort
        $ids[] = $id;
        $ids = Entry::find()->id($ids)->site('*')->unique()->orderBy('showtime')->ids();

        // Set new sorted values and save
        $user->setFieldValue('watchList', $ids);

        if (!Craft::$app->elements->saveElement($user)) {
            return $this->_getReturnObject(false, 'Could not save record.');
        }

        return $this->_getReturnObject(true, 'Item added to watchlist.');
    }

    /**
     * @param bool $success
     * @param string $message
     * @return \craft\web\Response|\yii\console\Response|Response
     */
    private function _getReturnObject(bool $success, string $message)
    {
        $message = Craft::t('site', $message);
        if (Craft::$app->request->getAcceptsJson()) {
            return $this->asJson(compact('success', 'message'));
        }
        if ($success) {
            Craft::$app->session->setNotice($message);
        } else {
            Craft::$app->session->setError($message);
        }
        return Craft::$app->response->redirect(UrlHelper::url('members/myprogram'));
    }

    /**
     * @param $id
     * @return \craft\web\Response|\yii\console\Response|Response
     * @throws ElementNotFoundException
     * @throws Exception
     * @throws Throwable
     */
    public function actionDeleteFromWatchlist($id)
    {
        $this->requireLogin();

        $user = Craft::$app->user->identity;

        $ids = $user->watchList->site('*')->unique()->anyStatus()->ids();

        $ids = array_diff($ids, [$id]);

        $user->setFieldValue('watchList', $ids);

        if (!Craft::$app->elements->saveElement($user)) {
            return $this->_getReturnObject(false, 'Could not save record.');
        }

        return $this->_getReturnObject(true, 'Item deleted from watchlist.');
    }

}
