<?php


namespace project\modules\main\services;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use craft\elements\GlobalSet;
use craft\elements\User;
use craft\errors\ElementNotFoundException;
use craft\errors\InvalidPluginException;
use craft\models\Section;
use Throwable;
use yii\base\Exception;
use function array_key_exists;

class MigrationService extends Component
{


    /**
     * @param string $name
     * @return string
     */
    public function testInstance($name = '')
    {
        // Test Method
        return "Hello {$name}, congrats, you could get a service instance.";
    }

    /**
     * @param $handle
     * @param $data
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function populateSingle($handle, $data)
    {
        foreach ($data as $siteHandle => $siteData) {
            $site = craft::$app->sites->getSiteByHandle($siteHandle);
            $entry = Entry::find()->site($site)->section($handle)->one();
            $entry->title = $siteData['title'];
            $entry->setFieldValues($siteData['fields']);
            if (!Craft::$app->elements->saveElement($entry)) {
                print "Could not save single {$handle} {$siteHandle}\n";
            }
        }
    }

    /**
     * @param $sectionHandle
     * @param $data
     * @return bool
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function createEntry($sectionHandle, $data)
    {
        $siteEn = craft::$app->sites->getSiteByHandle('en');
        $siteDe = craft::$app->sites->getSiteByHandle('de');

        $entry = Entry::find()->anyStatus()->site($siteEn)->section($sectionHandle)->slug($data['en']['slug'])->one();
        if ($entry) {
            print "Entry {$sectionHandle}  exists\n";
            return $entry->id;
        }

        /** @var Section $section */
        $section = craft::$app->sections->getSectionByHandle($sectionHandle);

        $entry = new Entry();
        $entry->title = $data['en']['title'] ?? '';
        $entry->slug = $data['en']['slug'] ?? '';
        $entry->sectionId = $section->id;
        $entry->typeId = $section->getEntryTypes()[0]->id;
        $entry->authorId = User::find()->one()->id;
        $entry->setFieldValues($data['en']['fields']);
        $entry->enabled = true;
        $entry->enabledForSite = true;

        if (!Craft::$app->elements->saveElement($entry)) {
            print "Could not save  En\n";
            return false;
        }

        if (array_key_exists('de', $data)) {
            $entryId = $entry->id;

            $entry = Entry::find()->id($entryId)->site($siteDe)->one();
            $entry->title = $data['de']['title'] ?? '';
            $entry->slug = $data['de']['slug'] ?? '';
            $entry->setFieldValues($data['de']['fields']);
            $entry->enabledForSite = true;
            if (!Craft::$app->elements->saveElement($entry)) {
                print "Could not save {$data['de']['title']} De\n";
                return false;
            }
        }
        return $entry->id;
    }

    /**
     * @param $firstName
     * @param $lastName
     * @return bool
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function populateFirstUser($firstName, $lastName)
    {
        $user = User::find()->orderBy('id')->one();
        $user->firstName = $firstName;
        $user->lastName = $lastName;
        if (!Craft::$app->elements->saveElement($user)) {
            print "Could not save User\n";
            return false;
        }
        return true;
    }

    /**
     * @param $volumeHandle
     * @param array $paths
     * @return bool
     */
    public function populateAssets($volumeHandle, Array $paths)
    {
        $assetIndexer = Craft::$app->getAssetIndexer();
        $session = $assetIndexer->getIndexingSessionId();
        $volume = Craft::$app->volumes->getVolumeByHandle($volumeHandle);

        if (!$volume) {
            return false;
        }

        foreach ($paths as $path) {
            try {
                $assetIndexer->indexFile($volume, $path, $session);
            } catch (Throwable $e) {
                print 'error: ' . $e->getMessage() . PHP_EOL . PHP_EOL;
            }
        }
        return true;
    }


    /**
     * @param $handle
     * @param $featuredImage
     * @param $data
     * @return bool
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function populateGlobal($handle, $data): bool
    {

        foreach ($data as $siteHandle => $fields) {
            $site = craft::$app->sites->getSiteByHandle($siteHandle);
            $global = GlobalSet::find()->site($site)->handle($handle)->one();
            $global->setFieldValues($fields);
            if (!Craft::$app->elements->saveElement($global)) {
                print "Could not save global {$handle} {$siteHandle}\n";
            }
        }

        return true;
    }


    /**
     * @param $sectionHandle
     * @param $attribute
     * @param $slugFrom
     * @param $slugTo
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function setRelation($sectionHandle, $attribute, $slugFrom, $slugTo)
    {
        $site = craft::$app->sites->getSiteByHandle('de');
        /** @var Entry $entryFrom */
        $entryFrom = Entry::find()->section($sectionHandle)->anyStatus()->site($site)->slug($slugFrom)->one();
        $entryTo = Entry::find()->section($sectionHandle)->anyStatus()->site($site)->slug($slugTo)->one();
        if ($entryFrom && $entryTo) {
            $entryFrom->setFieldValues([$attribute => [$entryTo->id]]);
            if (!Craft::$app->elements->saveElement($entryFrom)) {
                print "Could not set relation {$attribute} {$slugFrom} {$slugTo}\n";
            }
        }
    }

    /**
     * @param $handle
     * @throws InvalidPluginException
     */
    public function disablePlugin ($handle) {
        $service = Craft::$app->plugins;
        if ($service->isPluginEnabled($handle)) {
            $service->disablePlugin($handle);
        }
    }

}
