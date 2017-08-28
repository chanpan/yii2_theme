<?php

namespace backend\modules\ezforms2\classes;

use yii\helpers\Html;
use yii\helpers\Url;
use Yii;

/**
 * Description of EzfHelper
 *
 * @author appxq
 */
class EzfHelper {

    /**
     * Creates data
     *
     * @param int $ezf_id ezform id
     * @param array $initdata เซ็ตค่าให้กับตัวแปรในฟอร์ม
     * @param int $target ezform target
     * @param string $reloadDiv id ของ tag html ที่ต้องการรีโหลด
     * 
     * @return string html
     */
    public static function btnAdd($ezf_id, $target = '', $initdata = [], $reloadDiv = '', $modal='modal-ezform-main') {

        $data = EzfFunc::arrayEncode2String($initdata);

        return self::btnOpenForm($ezf_id, '', '<i class="glyphicon glyphicon-plus"></i> '. Yii::t('app', 'New'), [
                    'class' => 'btn btn-success ezform-main-open',
                    'data-modal' => $modal,
                    'data-url' => Url::to(['/ezforms2/ezform-data/ezform',
                        'ezf_id' => $ezf_id,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'initdata' => $data,
                        'target' => $target,
                    ]),
        ]);
    }

    /**
     * Update data
     *
     * @param int $ezf_id ezform id
     * @param int $dataid zdata id
     * @param array $initdata เซ็ตค่าให้กับตัวแปรในฟอร์ม
     * @param string $reloadDiv id ของ tag html ที่ต้องการรีโหลด
     * 
     * @return string html
     */
    public static function btnEdit($ezf_id, $dataid, $initdata = [], $reloadDiv = '', $modal='modal-ezform-main') {

        $data = EzfFunc::arrayEncode2String($initdata);

        return self::btnOpenForm($ezf_id, '', '<i class="glyphicon glyphicon-pencil"></i> '.Yii::t('app', 'Update'), [
                    'class' => 'btn btn-primary ezform-main-open',
                    'data-modal' => $modal,
                    'data-url' => Url::to(['/ezforms2/ezform-data/ezform',
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'initdata' => $data,
                    ]),
        ]);
    }
    
    /**
     * Veiw form data
     *
     * @param int $ezf_id ezform id
     * @param int $dataid zdata id
     * 
     * @return string html
     */
    public static function btnViewForm($ezf_id, $dataid, $modal='modal-ezform-main') {

        return self::btnOpenForm($ezf_id, '', '<i class="glyphicon glyphicon-eye-open"></i> '.Yii::t('ezform', 'Open Form'), [
                    'class' => 'btn btn-default ezform-main-open',
                    'data-modal' => $modal,
                    'data-url' => Url::to(['/ezforms2/ezform-data/ezform-view',
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'modal' => $modal,
                    ]),
        ]);
    }

    /**
     * Delete Data
     *
     * @param int $ezf_id ezform id
     * @param int $dataid zdata id
     * @param string $reloadDiv id ของ tag html ที่ต้องการรีโหลด
     * 
     * @return string html
     */
    public static function btnDelete($ezf_id, $dataid, $reloadDiv = '') {
        return self::btnOpenForm($ezf_id, '', '<i class="glyphicon glyphicon-trash"></i> '.Yii::t('app', 'Delete'), [
                    'class' => 'btn btn-danger ezform-delete',
                    'data-url' => Url::to(['/ezforms2/ezform-data/delete',
                        'ezf_id' => $ezf_id,
                        'dataid' => $dataid,
                        'reloadDiv' => $reloadDiv,
                    ]),
        ]);
    }

    /**
     * Show Data
     *
     * @param int $ezf_id ezform id
     * @param string $data_column gridview column
     * 
     * @return string html
     */
    public static function btnView($ezf_id, $data_column=[], $modal='modal-ezform-main') {
        return self::btnOpenForm($ezf_id, '', '<i class="glyphicon glyphicon-th-list"></i> '.Yii::t('app', 'View'), [
                    'class' => 'btn btn-info ezform-main-open',
                    'data-modal' => $modal,
                    'data-url' => Url::to(['/ezforms2/ezform-data/view',
                        'ezf_id' => $ezf_id,
                        'popup' => 1,
                        'modal' => $modal,
                        'data_column' => $data_column,
                    ]),
        ]);
    }

    public static function btnOpenForm($ezf_id, $dataid, $label, $options) {
        return Html::button($label, $options);
    }

    /**
     * Delete Data
     *
     * @param int $ezf_id ezform id
     * @param string $divid widget id
     * @param string $data_column gridview column
     * 
     * @return string html
     */
    public static function uiGrid($ezf_id, $target, $reloadDiv, $modal='modal-ezform-main', $data_column=[], $disabled=0) {
        $data_column = EzfFunc::arrayEncode2String($data_column);
        
        $url = Url::to(['/ezforms2/ezform-data/view', 'ezf_id' => $ezf_id, 'target' => $target, 'modal' => $modal, 'reloadDiv' => $reloadDiv, 'popup' => 0, 'data_column' => $data_column, 'disabled' => $disabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");

        return $html;
    }
    
    public static function uiEmr($ezf_id, $target, $reloadDiv, $modal='modal-ezform-main', $disabled=0) {
        $url = Url::to(['/ezforms2/ezform-data/emr', 'ezf_id' => $ezf_id, 'target' => $target, 'modal' => $modal, 'reloadDiv' => $reloadDiv, 'popup' => 0, 'disabled' => $disabled]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url]);
        $view = \Yii::$app->getView();
        
        $view->registerJs("
            getUiAjax('$url', '$reloadDiv');
        ");
        
        return $html;
    }

    public static function uiList($ezf_id) {
        
    }

    public static function uiSelect2($ezf_id) {
        
    }

    public static function uiSearch($ezf_id) {
        
    }

}
