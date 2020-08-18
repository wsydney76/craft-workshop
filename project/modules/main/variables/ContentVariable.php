<?php

namespace project\modules\main\variables;

use Craft;
use craft\base\Component;
use craft\elements\db\EntryQuery;
use craft\elements\db\MatrixBlockQuery;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\helpers\Template;
use project\modules\main\MainModule;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;
use yii\base\Exception;

class ContentVariable extends Component
{
    public function getFeaturedSectionEntries($nav = '')
    {
        return MainModule::$services->content->getFeaturedSectionEntries($nav);
    }

    public function getScreeningQuery($id)
    {
        return MatrixBlock::find()->id($id);
    }

    /**
     * @param $id
     * @param array $options
     * @return Markup
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function loadTemplate($id, $options = []): Markup
    {
        $options['isLivePreview'] = Craft::$app->request->getQueryParam('x-craft-live-preview') ? 'yes' : 'no';
        return Template::raw(Craft::$app->view->renderTemplate('main/loadtemplate',
            ['id' => $id, 'options' => $options]));
    }

    /**
     * @return Markup
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function csrfinput()
    {
        return Template::raw(Craft::$app->view->renderTemplate('main/csrftoken'));
    }

    public function getCast(): EntryQuery
    {
        $query = MatrixBlock::find()
            ->field('cast');

        return $this->_getPersonsByBlock($query);
    }

    public function getPersonsByDepartment($department): EntryQuery
    {
        $query = MatrixBlock::find()
            ->field('crew')
            ->relatedTo($department);

        return $this->_getPersonsByBlock($query);
    }

    protected function _getPersonsByBlock(MatrixBlockQuery $query) : EntryQuery
    {
        $blockIds =$query
            ->ownerId(Entry::find()->section('film')->ids())
            ->ids();
        if (!$blockIds) {
            $blockIds = [0];
        }
        $query = Entry::find()
            ->section('person')
            ->relatedTo($blockIds)
            ->orderBy('title');
        return $query;
    }
}
