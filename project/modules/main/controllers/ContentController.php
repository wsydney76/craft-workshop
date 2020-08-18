<?php

namespace project\modules\main\controllers;

use Craft;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\web\Controller;
use project\modules\main\exporters\ScreeningsExporter;
use project\modules\main\MainModule;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;
use yii\base\ExitException;
use yii\base\InvalidArgumentException;
use craft\web\Response;
use function session_abort;

class ContentController extends Controller
{
    protected $allowAnonymous = true;

    /**
     * @return string
     */
    public function actionTest(): string
    {
        // http://<domain>/actions/main/content/test
        return 'Here is a web controller';
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function actionLoadtemplate(): string
    {
        $params = Craft::$app->request->getQueryParams();

        return $this->view->renderTemplate($params['template'], ['params'=> $params] );
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function actionCsrftoken(): string
    {
        return $this->view->renderTemplate('main/csrfinput',['token' => Craft::$app->getRequest()->getCsrfToken()]);
    }

    /**
     * @return \craft\web\Response|string|Response
     */
    public function actionRandomFullsizeImage()
    {

        $image = Asset::find()
            ->kind('image')
            ->width('> 1900')
            ->height('> 800')
            ->orderBy('rand()')
            ->one();

//        $globals = GlobalSet::find()->handle('siteInfo')->one();
//        if (!$globals) {
//            return '';
//        }
//
//        $image = $globals->defaultFeaturedImage->one();

        if (!$image) {
            return '';
        }

        return Craft::$app->response->sendFile(
            $image->volume->getRootPath() . DIRECTORY_SEPARATOR . $image->path,
            $image->filename,
            ['inline' => true, 'mimeType' => $image->mimeType]);
    }

    /**
     * @return \craft\web\Response
     * @throws ExitException
     */
    public function actionExportScreenings(): Response
    {
        // http://<domain>/actions/main/content/export-screenings

        $query = Entry::find()->section('screening')->orderBy('showtime');
        $exporter = new ScreeningsExporter();
        $response = Craft::$app->response;
        $format = Craft::$app->request->getParam('format', 'json');
        if (!in_array($format, ['json', 'xml'], true)) {
            throw new InvalidArgumentException('Invalid format ' . $format . ', allowed:json (default),xml');
        }

        $response->data = $exporter->export($query);
        $response->format = $format;

        switch ($response->format) {
            case 'json':
                $response->formatters['json']['prettyPrint'] = true;
                break;
            case 'xml':
                $response->formatters['xml']['rootTag'] = 'screenings';
                break;
        }

        return $response;
    }

    /**
     * @return \yii\web\Response|null
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionAddDaysToShowtime()
    {
        $app = Craft::$app;
        $session = $app->session;

        $days = (int)Craft::$app->request->getRequiredParam('days');

        if (!$days || $days > 180 || $days < -180) {
            $session->setError(Craft::t('main', 'Invalid number of days specified'));
            $app->urlManager->setRouteParams(
                ['days' => $days]
            );
            return null;
        }

        if (!MainModule::getInstance()->content->addDaysToShowtime($days)) {
            $session->setError(Craft::t('main', 'An unexpected error occured'));
            return $this->redirectToPostedUrl();
        }

        $session->setNotice($days .  ' ' . Craft::t('main', 'days added'));

        return $this->redirectToPostedUrl();
    }

}
