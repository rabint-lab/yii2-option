<?php

namespace rabint\option\models;

use Yii;

/**
 * This is the model class for table "opt_options".
 *
 * @property integer $id
 * @property string $grp
 * @property string $key
 * @property string $data
 * @property integer $updated_at
 * @property integer $created_at
 */
class Option extends \common\models\base\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'system_option';
    }

    public static function tabs()
    {
        $options = include \Yii::getAlias('@app/config/options.php');
        foreach ($options as &$page) {
            unset($page['tabs']);
        }
        return $options;
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//                [['grp'], 'required'],
            [['data'], 'string'],
            [['updated_at', 'created_at'], 'integer'],
            [['grp'], 'string', 'max' => 100],
            [['key'], 'string', 'max' => 255],
        ];
    }

    public function beforeSave($insert)
    {
//        $this->grp = 'global';
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rabint', 'شناسه'),
            'grp' => Yii::t('rabint', 'گروه'),
            'key' => Yii::t('rabint', 'کلید'),
            'data' => Yii::t('rabint', 'مقدار'),
            'updated_at' => Yii::t('rabint', 'آخرین ویرایش'),
            'created_at' => Yii::t('rabint', 'تاریخ ایجاد'),
        ];
    }

    static function defaultOption($key, $field, $firstRow)
    {
        $opts = [];
        foreach (self::tabs() as $tab => $value) {
            $opts += self::setup($tab);
        }
        if (!isset($opts[$key])) {
            return false;
        }
        $ret = [0 => []];
        if (empty($field)) {
            foreach ($opts[$key]['options'] as $anOpt) {
                $ret[0][$anOpt['name']] = '';
            }
        } else {
//            foreach ()
//            var_dump($opts[$key]['options'][]);
//            if (!isset($opts[$key]['options'][$field])) {
//                return false;
//            }else{
            $ret[0] = '';
//            }
        }
        if ($firstRow) {
            $ret = reset($ret);
        }
        return $ret;
    }

    static function get($key, $field = '', $firstRow = false, $default = false)
    {
        static $cache = [];
        $cacheKey = $key . $field . ($firstRow ? "1" : "0") . ($default ? "1" : "0");

        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }
        /**
         * set language
         */
        if (count(Yii::$app->params['availableLocales']) <= 1) {
            /**
             * ONE LANGUAGE
             */
            $lang = "global";
        } else {
            /**
             * MULTI LANGUAGE
             */
            $lang = Yii::$app->language;
            if ($lang == Yii::$app->params['availableLocales'][0]) {
                $lang = "global";
            }
        }

        /**
         * get data form db
         */
        $data = static::findOne(['key' => $key, 'grp' => $lang]);

        /**
         * create default data
         */
        if (!$default) {
            $default = self::defaultOption($key, $field, $firstRow, $default);
        }
        /**
         * find errors
         */
        if (
            $data == null
            OR (!isset($data->data))
            OR (empty($data->data))
            OR (!\rabint\helpers\collection::isJson($data->data))
        ) {
            return $cache[$cacheKey] = $default;
        }

        /**
         * decode data
         */
        $data = json_decode($data->data, true);
        $DATA = \rabint\helpers\collection::stripslashesDeep($data);

        /**
         * set output by params
         */
        if (empty($field)) {
            $return = $cache[$cacheKey] =\rabint\helpers\collection::rotateArray($DATA);
        } else {
            if (!isset($DATA[$field])) {
                return $cache[$cacheKey] =$default;
            }
            $return = $DATA[$field];
        }

        if ($firstRow) {
            $return = reset($return);
        }
        return $cache[$cacheKey] = self::fillByDefault($return, $default);
    }

    static function fillByDefault($data, $default)
    {
        if (is_array($default) and is_array($data)) {
            $firstDRow = reset($default);
            $firstRow = reset($data);
            if (is_array($firstDRow) and is_array($firstRow)) {
                /**
                 *  2d array
                 */
                foreach ($data as &$arow) {
                    $arow = array_merge($firstDRow, $arow);
                }
            } else {
                /**
                 *  1d array
                 */
                $data = array_merge($default, $data);
            }
        }
        if (empty($data)) {
            $data = $default;
        }
        return $data;
    }

    static function getOptions($grp = "global")
    {
        $data = static::find()->where(['grp' => $grp])->asArray()->all();
        $DATA = array();
        foreach ($data as $value) {
            if (\rabint\helpers\collection::isJson($value['data'])) {
                $value['data'] = json_decode($value['data'], true);
            }
            $value['data'] = \rabint\helpers\collection::stripslashesDeep($value['data']);
            $DATA["{$value['key']
                    }"] = $value['data'];
        }

        return $DATA;
    }

    static function setup($tab = "global")
    {
        $options = include \Yii::getAlias('@app/config/options.php');
        return $options[$tab]['tabs'];
    }

}
