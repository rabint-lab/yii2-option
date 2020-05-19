<?php

use yii\helpers\Html;
use \yii\widgets\ActiveForm;

$this->context->layout = '@themeLayouts/main';
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$tabDesc = rabint\option\models\Option::tabs()[$tab];
$this->title = Yii::t('rabint', 'Options');
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $tabDesc['title'];
?>
<div class="grid-box option-index">
    <div class="clearfix"></div>
    <p class="description">
        <br />
        <?= $tabDesc['description']; ?>
        <br />
    </p>
    <div class="row">
        <div class="col-sm-12">
            <div class="block">
                <?php $form = ActiveForm::begin(); ?>
                <?php
                $tabBlock = $contentBlock = '';
                $tabs = 1;
                $btns = '';
                foreach ($options as $group => $option) {
                    $this->beginBlock('tabBlock');
                    echo $tabBlock;
                    ?>
                    <li role="presentation" class="nav-item <?php echo ($tabs == 1) ? 'active' : ''; ?>">
                        <a href="#<?php echo $group; ?>" class="nav-link" aria-controls="<?php echo $group; ?>" role="tab" data-toggle="tab"><?php echo $option['title'] ?></a>
                    </li>
                    <?php
                    $this->endBlock();
                    $tabBlock = $this->blocks['tabBlock'];
                    /* =================================================================== */
                    $this->beginBlock('contentBlock');
                    echo $contentBlock;
                    ?>
                    <div role="tabpanel" class=" tab-pane <?php echo ($tabs == 1) ? 'active' : ''; ?>" id="<?php echo $group; ?>">
                        <?= isset($option['description']) ? '<p class="alert alert-default">' . $option['description'] . '</p>' : ''; ?>
                        <div class="groupCnts">
                            <?php
                            if (!isset($DATA[$group])) {
                                $groupValues = FALSE;
                            } else {
                                $groupValues = (empty($DATA[$group])) ? [] : \rabint\helpers\collection::rotateArray($DATA[$group]);
                            }
                            if (isset($option['clone']) and ($option['clone'] === 1 or $option['clone'] === TRUE)) {
                                $CLONE = TRUE;
                                $REAPEAT = 1;
                            } elseif (isset($option['clone']) and ((int) $option['clone']) > 1) {
                                $REAPEAT = (int) $option['clone'];
                                $CLONE = FALSE;
                            } else {
                                $CLONE = FALSE;
                                $REAPEAT = 1;
                            }
                            if (!($groupValues)) {
                                $groupValues = [];
                            }
                            $forCnt = max(count($groupValues), $REAPEAT);
                            for ($ct = 0; $ct < $forCnt; $ct++) {
                                if ($groupValues === false) {
                                    $values = FALSE;
                                } else {
                                    $values = (isset($groupValues[$ct])) ? $groupValues[$ct] : [];
                                }
                                if ($CLONE) {
                                    echo '<div class="optGroup clonelyGroup ">' . "\n";
                                } else {
                                    echo '<div class="optGroup">' . "\n";
                                }

                                foreach ($option['options'] as $data) {
                                    $data = array_merge(
                                        [
                                            'name' => '',
                                            'default' => '',
                                            'label' => 'label_not_set!',
                                            'hint' => '',
                                            'tagOption' => [],
                                        ],
                                        $data
                                    );

                                    /* =================================================================== */
                                    $name = $data['name'];
                                    $default_val = $data['default'];
                                    if ($values === false) {
                                        $tag_value = $default_val;
                                    } else {
                                        $tag_value = (isset($values[$name])) ? $values[$name] : '';
                                    }
                                    $tagOption = $data['tagOption'];
                                    $name = $group . '[' . $name . '][]';
                                    $label = '<div class="form-group"><label for="" >' . $data['label'] . '</label>';
                                    $labelEnd = '<p class="help-block">' . $data['hint'] . '</p></div>';

                                    $label2 = '<div class="form-group"><label for="" >';
                                    $label2End = $data['label'] . '</label>' . '<p class="help-block">' . $data['hint'] . '</p></div>';

                                    /* =================================================================== */

                                    switch ($data['type']) {
                                        case 'attachment_id':
                                            $tagOption = array_merge(['class' => 'form-control'], $tagOption);
                                            $echo = $label . \rabint\helpers\widget::uploaderStatic($name, (int) $tag_value, ['maxFileSize' => 100 * 1024 * 1024, 'acceptFileTypes' => new \yii\web\JsExpression('/(\.|\/)(gif|mp4|jpe?g|png)$/i')]) . $labelEnd;
                                            $echo2 = $label . \rabint\helpers\widget::uploaderStatic($name, (int) $default_val, ['maxFileSize' => 100 * 1024 * 1024, 'acceptFileTypes' => new \yii\web\JsExpression('/(\.|\/)(gif|mp4|jpe?g|png)$/i')]) . $labelEnd;
                                            break;
                                        case 'attachment_url':
                                            $tagOption = array_merge(['class' => 'form-control'], $tagOption);
                                            $echo = $label . \rabint\helpers\widget::uploaderStatic($name, $tag_value, ['returnType' => 'path', 'maxFileSize' => 100 * 1024 * 1024, 'acceptFileTypes' => new \yii\web\JsExpression('/(\.|\/)(gif|mp4|jpe?g|png)$/i')]) . $labelEnd;
                                            $echo2 = $label . \rabint\helpers\widget::uploaderStatic($name, $default_val, ['returnType' => 'path', 'maxFileSize' => 100 * 1024 * 1024, 'acceptFileTypes' => new \yii\web\JsExpression('/(\.|\/)(gif|mp4|jpe?g|png)$/i')]) . $labelEnd;
                                            break;
                                        case 'wysiwyg':
                                            $tagOption = array_merge(['class' => 'form-control'], $tagOption);
                                            $echo = $label . \rabint\helpers\widget::wysiwygStatic($name, $tag_value) . $labelEnd;
                                            $echo2 = $label . \rabint\helpers\widget::wysiwygStatic($name, $default_val) . $labelEnd;
                                            break;
                                        case 'textarea':
                                            $tagOption = array_merge(['class' => 'form-control'], $tagOption);
                                            $echo = $label . Html::textarea($name, $tag_value, $tagOption) . $labelEnd;
                                            $echo2 = $label . Html::textarea($name, $default_val, $tagOption) . $labelEnd;
                                            break;
                                        case 'select':
                                            $tagOption = array_merge(['class' => 'form-control', 'prompt' => ''], $tagOption);
                                            $echo = $label . Html::dropDownList($name, $tag_value, $data['items'], $tagOption) . $labelEnd;
                                            $echo2 = $label . Html::dropDownList($name, $default_val, $data['items'], $tagOption) . $labelEnd;
                                            break;
                                        case 'checkboxlist':
                                            $tagOption = array_merge(['class' => 'checkboxlist', 'prompt' => ''], $tagOption);
                                            $echo = $label . Html::checkboxList($name, $tag_value, $data['items'], $tagOption) . $labelEnd;
                                            $echo2 = $label . Html::checkboxList($name, $default_val, $data['items'], $tagOption) . $labelEnd;
                                            break;
                                        case 'seprator':
                                            $tag = (isset($data['tag'])) ? $data['tag'] : 'h5';
                                            $echo = Html::tag($tag, $data['label'], ['class' => 'sepratorTag']);
                                            $echo2 = Html::tag($tag, $data['label'], ['class' => 'sepratorTag']);
                                            break;
                                        case 'checkbox':
                                            $tagOption = array_merge(['class' => ''], $tagOption);
                                            $echo = $label2 . Html::checkbox($name, $tag_value, $tagOption) . $label2End;
                                            $echo2 = $label2 . Html::checkbox($name, $default_val, $tagOption) . $label2End;
                                            break;
                                        default:
                                            $tagOption = array_merge(['class' => 'form-control'], $tagOption);
                                            $echo = $label . Html::input($data['type'], $name, $tag_value, $tagOption) . $labelEnd;
                                            $echo2 = $label . Html::input($data['type'], $name, $default_val, $tagOption) . $labelEnd;
                                            break;
                                    }
                                    $echo = '<div class="rec">' . $echo . '</div>';

                                    if ($ct == 0 and $CLONE) {
                                        $btns .= '<div class="rec">' . $echo2 . '</div>';
                                        $echo2 = "";
                                    }

                                    echo $echo;
                                    $echo = '';
                                }
                                if ($ct == 0) {
                                    $btns .= '<span class="move_btn">' .
                                        '<a class="rabint_moveup_btn" title="' . \Yii::t('app', 'انتقال به راست') . '"><i class="fas fa-chevron-circle-right fa-2x"></i></a>' .
                                        '<a class="rabint_movedown_btn" title="' . \Yii::t('app', 'انتقال به چپ') . '"><i class="fas fa-chevron-circle-left fa-2x"></i></a>' .
                                        '</span>';
                                }
                                echo '<span class="move_btn">' .
                                    '<a class="rabint_moveup_btn" title="' . \Yii::t('app', 'انتقال به راست') . '"><i class="fas fa-chevron-circle-right fa-2x"></i></a>' .
                                    '<a class="rabint_movedown_btn" title="' . \Yii::t('app', 'انتقال به چپ') . '"><i class="fas fa-chevron-circle-left fa-2x"></i></a>' .
                                    '</span>';

                                if ($ct == 0 and $CLONE) {
                                    $btns .= '<span class="mines_btn">' .
                                        '<a class="rabint_unclone_btn" title="' . \Yii::t('app', 'حذف') . '"><i class=" fas fa-times-circle fa-2x"></i></a>' .
                                        '</span>';
                                    $btns .= '<div class="clear"></div>' . "\n";
                                }
                                if ($CLONE) {
                                    echo '<span class="mines_btn">' .
                                        '<a class="rabint_unclone_btn" title="' . \Yii::t('app', 'حذف') . '"><i class="fas fa-times-circle fa-2x"></i></a>' .
                                        '</span>';
                                    echo '<div class="clear"></div>' . "\n";
                                }
                                echo '</div>';
                            }
                            ?>
                        </div>

                        <?php
                        if ($CLONE) {
                            echo '<a class="rabint_clone_btn" title="' . \Yii::t('app', 'افزودن یک مورد') . '"><i class="fas fa-plus-circle fa-2x"></i></a>';
                        }
                        echo '<div class="example" style="display:none!important">' . $btns . '</div>' . "\n";
                        echo '<div class="clearfix"></div>' . "\n";
                        ?>

                        <div class="clearfix"></div>
                    </div>

                    <?php
                    $this->endBlock();
                    $contentBlock = $this->blocks['contentBlock'];
                    $tabs++;
                    $btns = '';
                }
                ?>
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul id="myTab" class="nav nav-tabs nav-tabs-alt js-tabs-enabled" role="tablist">
                        <?= $this->blocks['tabBlock'] ?>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <?= $this->blocks['contentBlock'] ?>
                    </div>
                </div>
                <div class="clearfix spacer"></div>
                <div class="center">
                    <?= Html::submitButton(Yii::t('rabint', 'Update'), ['class' => 'btn btn-primary center btn-flat']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    <?php ob_start(); ?>
    $(function() {
        $('#myTab a').click(function(e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });
    $(document).ready(function() {
        $('.rabint_unclone_btn').each(function() {
            $count = $(this).parent().parent().parent().children().length;
        });

        $('.option-index').on('click', '.rabint_unclone_btn', function() {
            $count = $(this).parent().parent().parent().children().length;
            if ($count > 4)
                $(this).parent().parent().remove();

        });
        $('.option-index').on('click', '.rabint_clone_btn', function() {
            $exam = $(this).parent().find('.example').html();
            $(this).before('<div class="optGroup clonelyGroup">' + $exam + '</div>');
            $count = $(this).parent().parent().parent().children().length;
            if ($count > 4)
                $('.rabint_unclone_btn').show();
        });
        $('.option-index').on('click', '.rabint_moveup_btn', function() {
            $item = $(this).parents('.optGroup');
            if ($item.index() == 0) {
                return;
            }
            $prev = $($item).prev();
            $item.insertBefore($prev);

        });
        $('.option-index').on('click', '.rabint_movedown_btn', function() {
            $item = $(this).parents('.optGroup');
            $next = $($item).next();
            if ($next.hasClass('optGroup')) {
                $item.insertAfter($next);
            }

        });

        $('input.save_options[type="submit"]').click(function() {
            $('.nav-tab-content .example').remove();
        });
    });
    <?php
    $script = ob_get_clean();
    $this->registerJs($script, $this::POS_END);
    ?>
</script>