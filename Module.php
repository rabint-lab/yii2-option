<?php

namespace rabint\option;

use Yii;
use rabint\option\models\Option;

/**
 * option module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'rabint\option\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }


    public static function adminMenu()
    {
        $optionItems = [];
        if (count(Yii::$app->params['availableLocales']) <= 1) {
            /**
             * ONE LANGUAGE
             */
            $lkey = 'global';
            $lData = ' (' . \Yii::t('rabint', 'پیشفرض') . ')';
            $langItems = [];
            foreach (Option::tabs() as $opt => $data) {
                $langItems[] = [
                    'label' => $data['title'],
                    'url' => ['/option/option/index', 'tab' => $opt, 'grp' => $lkey],
                    'icon' => '<i class="far fa-circle"></i>'
                ];
            }
            $optionItems = $langItems;
        } else {
            /**
             * MULTI LANGUAGE
             */
            $il=0;
            foreach (Yii::$app->params['availableLocales'] as $lkey => $lData) {
                if ($il == 0) {
                    $lkey = 'global';
                    $lData['title'] = $lData['title'] . ' (' . \Yii::t('rabint', 'پیشفرض') . ')';
                }
                $langItems = [];
                foreach (Option::tabs() as $opt => $data) {
                    $langItems[] = [
                        'label' => $data['title'],
                        'url' => ['/option/option/index', 'tab' => $opt, 'grp' => $lkey],
                        'icon' => '<i class="far fa-circle"></i>'
                    ];
                }
                $optionItems[] = [
                    'label' => $lData['title'],
                    'url' => '#',
                    'icon' => '<i class="fas fa-cogs"></i>',
                    'items' => $langItems,
                ];
                $il++;
            }
        }


        return [
            'label' => Yii::t('rabint', 'تنظیمات'),
            'icon' => '<i class="fas fa-cogs"></i>',
            'options' => ['class' => 'treeview'],
            'url' => '#',
            'visible' => Yii::$app->user->can('manager'),
            'items' => $optionItems,
        ];
    }
}
