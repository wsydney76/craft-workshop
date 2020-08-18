<?php

namespace project\modules\main\models;

use craft\base\Model;
use craft\validators\ColorValidator;
use whoisjuan\craftcolormixer\twigextensions\CraftColorMixerTwigExtension;

class BackgroundColorModel extends Model
{
    public $overlayColor = '#000000';
    public $overlayIsDark = 'auto';
    public $overlayTransparency = 100;
    public $useGradient = false;
    public $gradientDirection = 'to right';
    public $gradientColor = '#000000';

    public $darkClass = 'background-isdark';


    const SCENARIO_NOGRADIENT = 'nogradient';

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_NOGRADIENT] = ['overlayColor', 'overlayTransparency'];
        return $scenarios;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['overlayColor', 'gradientColor'], 'trim'],
            [['overlayColor', 'gradientColor', 'overlayTransparency'], 'required'],
            ['overlayColor', ColorValidator::class],
            ['gradientColor', ColorValidator::class],
            ['overlayTransparency', 'number', 'min' => 0, 'max' => 100]
        ];
    }

    public function isDark()
    {
        if ($this->overlayIsDark != 'auto') {
            return ($this->overlayIsDark == 'dark');
        }
        $colorHelper = new CraftColorMixerTwigExtension();
        return $colorHelper->isDark($this->overlayColor);
    }

    public function getAttr($addClass = [])
    {
        $attr = [];
        if($addClass) {
            $attr['class'] = $addClass;
        }
        if ($this->isDark() && $this->darkClass) {
            $attr['class'][] = $this->darkClass;
        }
        $attr['style'] = $this->getStyles();

        return $attr;
    }

    public function getStyles()
    {
        $colors = $this->colorsToRgba($this->overlayTransparency);

        return [
            'background-image' => "linear-gradient({$this->gradientDirection},{$colors[1]},{$colors[2]})",
            'background-repeat' => 'no-repeat',
            'background-position' => 'center center',
            'background-size' => 'cover'
        ];
    }

    public function setDarkClass($class) {
        $this->darkClass = $class;
    }

    protected function colorsToRgba($transparency) {
        $colors[1] = $this->getRgba($this->overlayColor, $transparency);
        if ($this->useGradient) {
            $colors[2] = $this->getRgba($this->gradientColor, $transparency);
        } else {
            $colors[2] = $colors[1];
        }
        return $colors;
    }

    protected function getRgba($hex, $transparency)
    {
        $transparency = $transparency / 100;
        $colorHelper = new CraftColorMixerTwigExtension();
        $color = $colorHelper->hexToRgb($hex, true);
        return "rgba({$color['R']},{$color['G']},{$color['B']},{$transparency})";
    }

}
