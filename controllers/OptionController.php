<?php

namespace rabint\option\controllers;

use Yii;
use rabint\option\models\Option;
use yii\filters\VerbFilter;

/**
 * OptionController implements the CRUD actions for Option model.
 */
class OptionController extends \rabint\controllers\AdminController {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        $ret = parent::behaviors();
        return $ret + [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Option models.
     * @return mixed
     */
    public function actionIndex($tab = 'default', $grp = "global") {
        $options = Option::setup($tab);

        if (Yii::$app->request->isPost) {

            foreach ($_POST as $option => $data) {
                if (!is_array($data)) {
                    continue;
                }
                foreach ($data as $k => &$dt) {
                    $optionConf = \rabint\helpers\collection::arraySearchDeep($options[$option], $k, 'name');
                    $optionConf = \rabint\helpers\collection::arrayGetValueByPath($options[$option], substr($optionConf, 0, strrpos($optionConf, '.')));
                    foreach ($dt as $j => $d) {
                        if (strlen($d) == 1 and $d == 1
                            and (
                                $optionConf['type'] == 'attachment_id'
                                OR
                                $optionConf['type'] == 'attachment_url'
                            )
                        ) {
//                            $dt[$j] = null;
                            unset($dt[$j]);
                        }
                    }
                    $dt = array_values($dt);
                    foreach ($dt as $j => $d) {
                        if (empty($d)) {
                            unset($dt[$j]);
                        }
                    }
                    if (empty($dt)) {
                        unset($data[$k]);
                    }
                }
                $data = json_encode($data);
                $newOpt = Option::findOne(['grp' => $grp, 'key' => $option]);
                if (empty($newOpt)) {
                    $newOpt = new Option();
                }
                $newOpt->key = $option;
                $newOpt->grp = $grp;
                $newOpt->data = $data;
                $newOpt->save($newOpt);
            }
            Yii::$app->session->setFlash('success', \Yii::t('rabint', 'تغییرات ذخیره گردید'));
            return $this->refresh();
        }
        return $this->render('index', [
            'options' => $options,
            'tab' => $tab,
            'DATA' => Option::getOptions($grp),
        ]);
    }

}
