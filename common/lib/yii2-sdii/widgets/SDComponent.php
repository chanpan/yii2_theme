<?php
namespace appxq\sdii\widgets;
/**
 * SDComponent class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 25 พ.ย. 2558 13:08:20
 * @link http://www.appxq.com/
 * @example 
 */
use Yii;
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\helpers\Url;

class SDComponent extends Select2 {

    public $ezf_id;
    public $ezf_field_id;
    public $modal_size = 'modal-xxl';
    
    public function init() {
	parent::init();
    }
    
    protected function renderInput()
    {
        if ($this->pluginLoading) {
            $this->_loadIndicator = '<div class="kv-plugin-loading loading-' . $this->options['id'] . '">&nbsp;</div>';
            Html::addCssStyle($this->options, 'display:none');
        }
        $input = $this->getInput('dropDownList', true);
        
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }

        $disabled = 0;
        if(isset($this->options['disabled']) && $this->options['disabled']){
            $disabled = 1;
        }
        
        echo '<div class="form-group">';
        echo '<div class="input-group" id="comp-'.$this->ezf_field_id.'" >';
        echo $this->_loadIndicator . $this->embedAddon($input);
        echo '<span id="btn-box-'.$this->ezf_field_id.'" class="input-group-btn">';  
        if(!$disabled){
            echo Html::button('<i class="glyphicon glyphicon-cog"></i> ', ['data-container'=>'body', 'data-toggle'=>'popover', 'data-placement'=>'right', 'data-content'=>'Load...', 'class'=>'btn btn-default btn-cong', 'data-active'=>0, 'data-url'=>Url::to(['/ezforms2/select2/check-comp', 'ezf_field_id'=>$this->ezf_field_id, 'ezf_id'=>$this->ezf_id, 'modal'=>"modal-{$this->ezf_field_id}", 'dataid'=>'']), 'data-id'=>$value]).' ';
        }
        echo '</span>';
        echo '</div>';
        echo '</div> ';
        
        $this->registerJs();
    }
    
    protected function registerJs()
    {
        $view = $this->getView();
        $submodal = '<div id="modal-'.$this->ezf_field_id.'" class="fade modal" role="dialog"><div class="modal-dialog '.$this->modal_size.'"><div class="modal-content"></div></div></div>';
        
        $inputId = $this->id;
        if ($this->hasModel()) {
            $inputId = Html::getInputId($this->model, $this->attribute);
        } 
        $inputMain = "ez{$this->ezf_id}-id";
        $view->registerJs("
        var hasForm = $( 'body' ).has( '#$inputMain' ).length;
        if(hasForm){
            $('#comp-{$this->ezf_field_id} .btn-cong').hide();
        } else {
            $('#ezf-modal-box').append('$submodal');
        }  
            
        $('#comp-{$this->ezf_field_id} #$inputId').change(function(){
            if($('.btn-cong').attr('data-active')==1){
            var url = $('.btn-cong').attr('data-url');
            var dataid = $('#$inputId').val();
            if(dataid!=''){
                url = url+dataid;
            } 
            
            $('#btn-box-{$this->ezf_field_id}').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#btn-box-{$this->ezf_field_id}').load(url);
                
            }
        });
        
        $('#comp-{$this->ezf_field_id}').on('click', '.btn-cong', function(){
            var url = $(this).attr('data-url');
            var dataid = $('#$inputId').val();
            if(dataid!=''){
                url = url+dataid;
            } 
            
            $('#btn-box-{$this->ezf_field_id}').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#btn-box-{$this->ezf_field_id}').load(url);
            
        });
        
        $('#comp-{$this->ezf_field_id}').on('click', '.btn-edit', function(){
            var url = $(this).attr('data-url');
            var dataid = $(this).attr('data-id');
            if(dataid!=''){
                modal_{$this->ezf_field_id}(url+dataid);
            } else {
                modal_{$this->ezf_field_id}(url);
            }
        });
        
        $('#comp-{$this->ezf_field_id}').on('click', '.btn-add', function(){
            var url = $(this).attr('data-url');
            modal_{$this->ezf_field_id}(url);
        });
        
        $('#modal-{$this->ezf_field_id}').on('hidden.bs.modal', function(e){
            var hasmodal = $('body .modal').hasClass('in');
            if(hasmodal){
                $('body').addClass('modal-open');
            }
        });

        function modal_{$this->ezf_field_id}(url) {
            $('#modal-{$this->ezf_field_id} .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#modal-{$this->ezf_field_id}').modal('show')
            .find('.modal-content')
            .load(url);
        }

        ");
    }
}
