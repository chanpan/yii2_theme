<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use yii\helpers\Html;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
/**
 * Description of EzfStarterWidget
 *
 * @author appxq
 */
class EzfStarterWidget extends \yii\base\Widget {

    public $key = 'AIzaSyCq1YL-LUao2xYx3joLEoKfEkLXsEVkeuk';
    public $options;
    //popup ezform, delete ezform, grid ezform, addon ezform
    public function init() {
        parent::init();

        $this->initOptions();
        
        echo Html::beginTag('div', ['id'=>'ezf-main-box']) . "\n";
            echo Html::beginTag('div', ['id'=>'ezf-main-app']) . "\n";
            
    }

    public function run() {
            echo "\n" . Html::endTag('div');// ezf-main-app
            echo ModalForm::widget([
                'id' => 'modal-ezform-main',
                'size' => 'modal-xxl',
                'tabindexEnable' => false,
            ]);
            echo Html::beginTag('div', ['id'=>'ezf-modal-box']) . "\n";
            echo "\n" . Html::endTag('div');// ezf-modal-box
        echo "\n" . Html::endTag('div'); // ezf-main-box
        
        $this->registerMap();
        $this->registerEzform();
        $this->registerJs();
    }
    
    protected function initOptions() {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        
    }
    
    protected function registerMap()
    {
        $view = $this->getView();
        
        //$op['sensor'] = $this->sensor;
	if($this->key!=''){
	    $op['key'] = $this->key;
	}
	$op['language'] = 'th';
	
	$q = array_filter($op);

        $view->registerJsFile('https://maps.google.com/maps/api/js?'.http_build_query($q), [
	    'position'=>\yii\web\View::POS_HEAD,
	    'depends'=>'yii\web\YiiAsset',
	]);
    }
    
    protected function registerEzform()
    {
        $view = $this->getView();
        
        \backend\modules\ezforms2\assets\EzfAsset::register($view);
        \backend\modules\ezforms2\assets\EzfGenAsset::register($view);
    }
    
    protected function registerJs()
    {
        $view = $this->getView();
        $view->registerJs("
            
        $('#modal-ezform-main').on('hidden.bs.modal', function (e) {
            $('#ezf-modal-box').html('');
        });
        
        $('#ezf-main-app').on('click', '.ezform-main-open', function(){
            var url = $(this).attr('data-url');
            var modal = $(this).attr('data-modal');
            modalEzformMain(url, modal);
        });
        
        $('#ezf-main-app').on('click', '.ezform-delete', function(){
            var url = $(this).attr('data-url');
            var url_reload = $(this).attr('data-url-reload');
            
            yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function(){
                $.post(
                        url, {'_csrf':'".Yii::$app->request->getCsrfToken()."'}
                ).done(function(result){
                        if(result.status == 'success'){
                                ". SDNoty::show('result.message', 'result.status') ."
                                var urlreload =  $('#'+result.reloadDiv).attr('data-url');
                                if(urlreload){
                                    getUiAjax(urlreload, result.reloadDiv);
                                }
                        } else {
                                ". SDNoty::show('result.message', 'result.status') ."
                        }
                }).fail(function(){
                        ". SDNoty::show("'" . "Server Error'", '"error"') ."
                        console.log('server error');
                });
            });
        });
        
        function getUiAjax(url, divid) {
            $.ajax({
                method: 'POST',
                url: url,
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#'+divid).html(result);
                }
            });
        }
            
        function modalEzformMain(url, modal) {
            $('#'+modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#'+modal).modal('show')
            .find('.modal-content')
            .load(url);
        }

        ");
    }

}
