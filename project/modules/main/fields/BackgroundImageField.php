<?php

namespace project\modules\main\fields;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Json;
use project\modules\main\MainModule;
use project\modules\main\models\BackgroundImageModel;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;

class BackgroundImageField extends BackgroundColorField
{

    public $data = null;
    public $hasBlockImage = false;
    public $assetSources = [];
    public $sectionSources = [];

    /**
     * @return string
     */
    public static function displayName(): string
    {
        return 'Background Image';
    }

    /**
     * @param mixed $value
     * @param ElementInterface|null $element
     * @return mixed
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (empty($value)) {
            $value = [];
        } else if (is_string($value)) {
            $value = Json::decode($value);
        }

        $this->data = new BackgroundImageModel($value);
        return $this->data;
    }


    /**
     * @param mixed $value
     * @param ElementInterface|null $element
     * @return string
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $assetSources = [];
        foreach ($this->assetSources as $volumeHandle) {
            $volume = Craft::$app->volumes->getVolumeByHandle($volumeHandle);
            if ($volume) {
                $folder = Craft::$app->assets->getRootFolderByVolumeId($volume->id);
                $assetSources[] = 'folder:' . $folder->uid;
            }
        }

        $entrySources = [];
        foreach ($this->sectionSources as $sectionHandle) {
            $section = Craft::$app->sections->getSectionByHandle($sectionHandle);
            if ($section) {
                $entrySources[] = 'section:' . $section->uid;
            }
        }

        return Craft::$app->getView()->renderTemplate('main/fields/backgroundimagefield_input', [
            'field' => $this,
            'value' => $value,
            'assetSources' => $assetSources,
            'entrySources' => $entrySources,
            'mode' => 'image'
        ]);
    }
    

    /**
     * @return string|null
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getSettingsHtml()
    {

        $volumes = Craft::$app->volumes->getAllVolumes();
        $assetOptions = [];
        foreach ($volumes as $volume) {
            $assetOptions[] = ['value' => $volume->handle, 'label' => $volume->name];
        }

        $sections = Craft::$app->sections->getAllSections();
        $sectionOptions = [];
        foreach ($sections as $section) {
            $sectionOptions[] = ['value' => $section->handle, 'label' => $section->name];
        }

        return Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'lightswitchField',
                [
                    [
                        'label' => Craft::t('site', 'Has Block Image'),
                        'id' => 'hasBlockImage',
                        'name' => 'hasBlockImage',
                        'on' => $this->hasBlockImage,
                        'instructions' => Craft::t('site', 'If a matrix block type has an asset field with the handle "image", it can be used as background')
                    ]
                ]) .
            Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'checkboxSelectField',
                [
                    [
                        'label' => Craft::t('site', 'Asset Sources'),
                        'id' => 'assetSources',
                        'name' => 'assetSources',
                        'options' => $assetOptions,
                        'values' => $this->assetSources
                    ]
                ]) .
            Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'checkboxSelectField',
                [
                    [
                        'label' => Craft::t('site', 'Section Sources'),
                        'id' => 'sectionSources',
                        'name' => 'sectionSources',
                        'options' => $sectionOptions,
                        'values' => $this->sectionSources
                    ]
                ]);
    }

    /**
     * @param ElementInterface $element
     * @param bool $isNew
     * @throws \Throwable
     */
    public function afterElementSave(ElementInterface $element, bool $isNew)
    {

        /** @var Element $element */

        if ($element->isFieldDirty($this->handle) && !$element->propagating) {

            $value = $element->getFieldValue($this->handle);

            $targetIds = [];

            if ($value['entryId']) {
                $targetIds[] = $value['entryId'][0];
            }
            if ($value['assetId']) {
                $targetIds[] = $value['assetId'][0];
            }

            MainModule::$services->content->saveRelations($this, $element, $targetIds);
        }
    }

}
