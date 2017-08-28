<?php
namespace appxq\sdii\widgets;

use yii\widgets\InputWidget;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfHelper;

class DataTable extends InputWidget {

    public $template = '<div id="{id}" class="panel panel-{theme}">
                            <div class="panel-heading">
                                <h3 class="panel-title">{icon}{text} {btnadd}</h3>
                            </div>
                            <div class="panel-body">
                                {gridview}
                            </div>
                        </div>';
    public $path;
    public $specific = [];
    public $tag = 'div';
    public $iconTemp = '<i class="fa {icon}"></i> ';
    public $icon = '';
    public $theme = 'default';
    public $ezf_id;
    public $ezf_field_id;
    public $column;
    public $modal_size = 'modal-xxl';
    
    public function init() {
	if (!isset($this->path)) {
	    $icon = '';
	    if(isset($this->specific['icon']) && $this->specific['icon']!=''){
		$icon = strtr($this->iconTemp, ['{icon}'=>$this->specific['icon']]);
	    }
            $disabled = 0;
	    if(isset($this->options['disabled']) && $this->options['disabled']){
                $disabled = 1;
            }
            
	    $this->options['style'] = isset($this->specific['color'])?"color: {$this->specific['color']};":'';
            
            if (isset($this->model)) {
                $target = Html::getAttributeValue($this->model, 'id');
            } else {
                $target = 0;
            }
            
	    $this->path = [
		'{gridview}' => EzfHelper::uiGrid($this->ezf_id, $target, 'datawidget-'.$this->ezf_field_id, 'modal-'.$this->ezf_field_id, $this->column, $disabled),
                '{btnadd}' => !$disabled?EzfHelper::btnAdd($this->ezf_id, $target, [], 'datawidget-'.$this->ezf_field_id, 'modal-'.$this->ezf_field_id):'',
                '{text}' => $this->value,
                '{theme}' => $this->theme,
		'{icon}' => $icon,
                '{id}'=>'data-main-'.$this->ezf_field_id
	    ];
	}
    }

    /**
     * @inheritdoc
     */
    public function run() {
        $view = $this->getView();
        
	$options = $this->options;
	$submodal = '<div id="modal-'.$this->ezf_field_id.'" class="fade modal" role="dialog"><div class="modal-dialog '.$this->modal_size.'"><div class="modal-content"></div></div></div>';
        
	echo Html::tag($this->tag, strtr($this->template, $this->path), $options);
        $inputMain = "ez{$this->ezf_id}-id";
        
        $view->registerJs("
        var hasForm = $( 'body' ).has( '#$inputMain' ).length;
        if(hasForm){
            $('#ezform-{$this->options['data-ezfid']} div[item-id={$this->ezf_field_id}]').hide();
        } else {
            $('#ezf-modal-box').append('$submodal');
        }    
            
        $('#modal-{$this->ezf_field_id}').on('hidden.bs.modal', function(e){
            var hasmodal = $('body .modal').hasClass('in');
            if(hasmodal){
                $('body').addClass('modal-open');
            } 
        });

        $('.ezform-main-open').on('click', function(){
            var url = $(this).attr('data-url');
            var modal = $(this).attr('data-modal');
            modal_{$this->ezf_field_id}(url);
        });
        
        function modal_{$this->ezf_field_id}(url) {
            $('#modal-{$this->ezf_field_id} .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#modal-{$this->ezf_field_id}').modal('show')
            .find('.modal-content')
            .load(url);
        }

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
        
        ");
    }

}
