<?php
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfFunc;
use appxq\sdii\utils\SDUtility;

/**
 * _textinput_validations file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 19 ส.ค. 2559 19:02:45
 * @link http://www.appxq.com/
 * @example  
 */
$validateTmp = isset($input['input_validate'])?SDUtility::string2Array($input['input_validate']):[];
$validate = isset($model['ezf_field_validate'])?$model['ezf_field_validate']:$validateTmp;
?>

<div class="well" style="padding: 15px; background-color: #fcf8e3; border-color: #faebcc;">
    <h4 style="margin-top: 0px;">Validations</h4>
    
    <div id="validations-form">
	<?php
	//init data
	$validate = EzfFunc::mergeValidate($validate);
	$addArr = [];
	$html = '';
	$btn = [];
        
	foreach ($validate as $row => $data) {
	    $addArr = ArrayHelper::merge($addArr, [$data[0]]);
	    if(in_array($data[0], ['string', 'number', 'integer', 'file', 'date', 'email', 'url', 'boolean', 'appxq\sdii\validators\CitizenIdValidator'])){
                
                $widgetName = $data[0];
                if($widgetName=='appxq\sdii\validators\CitizenIdValidator'){
                    $widgetName = 'citizenIdValidator';
                }
                
		$html .= $this->renderAjax('/validations/_'.$widgetName, [
		    'row'=>$row,
		    'data'=>$data,
		]);
	    } else {
		$val_name = explode('\\', $data[0]);
		$getname = $val_name[count($val_name)-1];
		$getview = '/validations/_'.lcfirst($getname);
		$btn[] = ['name'=>$getname, 'view'=>$getview];
		$html .= $this->renderAjax($getview, [
		    'row'=>$row,
		    'data'=>$data,
		]);
	    }
	    
	}
        
	echo $html;
	?>
    </div>
    
    <div class="btn-group" role="group">
	    <?= Html::button('String', ['class'=>'btn btn-default btn-add-validations', 'data-view'=>'/validations/_string'])?>
	    <?= Html::button('Number', ['class'=>'btn btn-default btn-add-validations', 'data-view'=>'/validations/_number'])?>
	    <?= Html::button('Integer', ['class'=>'btn btn-default btn-add-validations', 'data-view'=>'/validations/_integer'])?>
	    <?= Html::button('File Type', ['class'=>'btn btn-default btn-add-validations', 'data-view'=>'/validations/_file'])?>
	    <?= Html::button('Date Format', ['class'=>'btn btn-default btn-add-validations', 'data-view'=>'/validations/_date'])?>
	    <?= Html::button('Email', ['class'=>'btn btn-default btn-add-validations', 'data-view'=>'/validations/_email'])?>
	    <?= Html::button('URL', ['class'=>'btn btn-default btn-add-validations', 'data-view'=>'/validations/_url'])?>
	    <?= Html::button('Boolean', ['class'=>'btn btn-default btn-add-validations', 'data-view'=>'/validations/_boolean'])?>
            <?= Html::button('Citizen ID', ['class'=>'btn btn-default btn-add-validations', 'data-view'=>'/validations/_citizenIdValidator'])?>
	    <?php
		foreach ($btn as $value) {
		    echo Html::button($value['name'], ['class'=>'btn btn-default btn-add-validations', 'data-view'=>$value['view']]);
		}
	    ?>
	</div>
</div>

<?php  $this->registerJs("
$('.btn-add-validations').click(function(){
    var view = $(this).attr('data-view');
    var row = $('#validations-form .form-group').length;
    
    getValidations(view, row);
});

$('#validations-form').on('click', '.btn-del-validations', function() {
    $(this).parent().remove();
});

function getValidations(view, row){
    $.ajax({
	method: 'POST',
	url:'".yii\helpers\Url::to(['/ezbuilder/ezform-fields/view-validations'])."',
	data: {view:view, row:row, model:".\yii\helpers\Json::encode($model)."},
	dataType: 'JSON',
	success: function(result, textStatus) {
	    if(result.status == 'success') {
		$('#validations-form').append(result.html);
	    } else {
		". SDNoty::show('result.message', 'result.status') ."
	    }
	}
    });
}
");?>