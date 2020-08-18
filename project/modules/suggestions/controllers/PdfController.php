<?php

namespace project\modules\suggestions\controllers;

use Craft;
use craft\web\Controller;
use project\modules\suggestions\models\SuggestionModel;
use project\modules\suggestions\SuggestionModule;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\web\HttpException;
use yii\web\RangeNotSatisfiableHttpException;
use yii\web\Response;

class PdfController extends Controller
{
    /**
     * @return \craft\web\Response|\yii\console\Response|Response
     * @throws HttpException
     * @throws InvalidConfigException
     * @throws LoaderError
     * @throws RangeNotSatisfiableHttpException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function actionCreate()
    {
        $query = SuggestionModel::find()->orderBy('dateCreated desc');

        $ids = Craft::$app->request->getBodyParam('ids');
        if ($ids) {
            $query = $query->andWhere(['in', 'id', $ids]);
        }

        $suggestions = $query->all();

        if (!$suggestions) {
            Craft::$app->session->setError('No suggestions found');
            return $this->redirect(Craft::$app->request->referrer);
        }

        $html = Craft::$app->view->renderTemplate('suggestions/suggestions_pdf', ['suggestions' => $suggestions]);

        $pdf = SuggestionModule::$services->pdfService->createPdfFromHtml($html);

        return Craft::$app->response->sendContentAsFile($pdf, 'suggestions.pdf', [
            'mimeType' => 'application/pdf'
        ]);
    }
}
