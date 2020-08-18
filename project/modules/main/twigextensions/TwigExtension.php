<?php

namespace project\modules\main\twigextensions;

use project\modules\main\helpers\AgnosticFetchHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{
    public function getName(): string
    {
        return 'Custom Twig Extension';
    }

    public function getFilters()
    {
        return [

            new TwigFilter('one', function($stuff) {
                return AgnosticFetchHelper::one($stuff);
            }),
            new TwigFilter('all', function($stuff) {
                return AgnosticFetchHelper::all($stuff);
            })
        ];
    }

}
