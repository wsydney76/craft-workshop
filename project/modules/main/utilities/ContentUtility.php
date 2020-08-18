<?php

namespace project\modules\main\utilities;

use Craft;
use craft\base\Utility;

class ContentUtility extends Utility
{
    public static function id(): string
    {
        return 'contentUtility';
    }

    public static function displayName(): string
    {
        return Craft::t('main', 'Project Content');
    }

    public static function iconPath()
    {
        return Craft::getAlias('@app/icons/book.svg');
    }

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    public static function contentHtml(): string
    {
        $days = Craft::$app->urlManager->getRouteParams()['days'] ?? 30;
        return Craft::$app->view->renderTemplate('main/contentutility.twig', ['days' => $days]);
    }
}
