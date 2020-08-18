<?php

namespace project\modules\suggestions\widgets;

use Craft;
use craft\base\Widget;
use project\modules\suggestions\records\SuggestionRecord;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;

/**
 *
 * @property string|false $bodyHtml
 * @property null|string $settingsHtml
 * @property string $title
 */
class SuggestionWidget extends Widget
{

    public $limit = 3;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('suggestions','Suggestions');
    }


    public function getTitle(): string
    {
        return Craft::t('suggestions','Suggestions');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['limit'], 'number', 'integerOnly' => true];
        return $rules;
    }

    /**
     * @return string|null
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function getSettingsHtml()
    {
        return Craft::$app->view->renderTemplate('suggestions/widget_settings',
            [
                'widget' => $this
            ]);
    }

    /**
     * @return false|string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function getBodyHtml()
    {

        $openSuggestions = SuggestionRecord::find()
            ->status('open')
            ->orderBy('id desc')
            ->limit($this->limit ?: 5)
            ->all();

        $mySuggestions = SuggestionRecord::find()
            ->user(Craft::$app->user->identity)
            ->orderBy('id desc')
            ->limit($this->limit ?: 5)
            ->all();

        return Craft::$app->view->renderTemplate('suggestions/widget',
            [
                'openSuggestions' => $openSuggestions,
                'mySuggestions' => $mySuggestions
            ]);

    }

}
