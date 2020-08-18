<?php
/*
 * The only purpose of this class is being index be Phpstorm, so that custom extensions to the Craft variable
 * will be available in twig
 *
 * This class will never be instanciated.
 */

namespace project\_faux;

use Craft;
use craft\commerce\elements\db\OrderQuery;
use craft\commerce\elements\db\ProductQuery;
use craft\commerce\elements\db\SubscriptionQuery;
use craft\commerce\Plugin;
use craft\web\twig\variables\CraftVariable;
use project\modules\main\modules\members\variables\MembersVariable;
use project\modules\main\modules\members\variables\WatchListVariable;
use project\modules\main\variables\ContentVariable;
use project\modules\main\variables\GdprVariable;
use project\modules\suggestions\records\db\SuggestionRecordQuery;
use project\modules\suggestions\variables\SuggestionsVariable;
use spicyweb\embeddedassets\Variable;

class CraftTwigExtension extends CraftVariable
{
    public function suggestions(): SuggestionRecordQuery
    {

    }

    public function suggestionModule(): SuggestionsVariable
    {

    }

    public function content(): ContentVariable
    {

    }

    public function watchlist(): WatchListVariable
    {

    }

    public function members(): MembersVariable
    {

    }

    public function commerce(): Plugin
    {

    }

    public function orders(): OrderQuery
    {

    }

    public function products(): ProductQuery
    {

    }

    public function subscriptions(): SubscriptionQuery
    {

    }

    public function embeddedAssets(): Variable
    {

    }

    public function gdpr(): GdprVariable
    {

    }
}
