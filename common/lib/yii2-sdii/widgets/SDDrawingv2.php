<?php
namespace appxq\sdii\widgets;

use yii\widgets\InputWidget;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\assets\drawing\DrawingAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use dosamigos\fileupload\FileUpload;
use Yii;

class SDDrawingv2 extends InputWidget
{
    public $ui_temp = '<div id="{canvasDraw}" class="canvasDraw">
			    <div class="text-center">
					<div class="drawingToo btn-toolbar" role="toolbar">
                                                <div class="btn-group" style="margin-bottom: 5px;">
							<button class="fileUpload btn btn-sm btn-default" >
							<i class="glyphicon glyphicon-picture"></i> 
							{input-upload}
							</button>
						</div>
                                                <div class="btn-group" style="margin-bottom: 5px;">
							<button class="clearDrawing btn btn-sm btn-default" type="button"><i class="iconcolor-clear_draw"></i> </button>
							<button class="fullscreen btn btn-sm btn-default" type="button"><i class="glyphicon glyphicon-fullscreen"></i> </button>
						</div>
						<div class="btn-group" data-toggle="buttons" style="margin-bottom: 5px;">
							<label class="paintTool btn-sm btn btn-default active" data-type="paint">
								<input type="radio" name="paintTool" checked> <i class="iconcolor-paint"></i>
							</label>
							<label class="paintTool btn-sm btn btn-default" data-type="text">
								<input type="radio" name="paintTool" checked> <i class="glyphicon glyphicon-font"></i>
							</label>
							<label class="paintTool btn-sm btn btn-default" data-type="eraser">
								<input type="radio" name="paintTool" checked> <i class="iconcolor-eraser"></i>
							</label>
						</div>
						<div class="btn-group" data-toggle="buttons" style="margin-bottom: 5px;">
							<label class="lineTool btn-sm btn btn-default" data-type="2">
								<input type="radio" name="lineTool" checked> <i class="iconcolor-line2"></i>
							</label>
							<label class="lineTool btn-sm btn btn-default active" data-type="4">
								<input type="radio" name="lineTool" checked> <i class="iconcolor-line4"></i>
							</label>
							<label class="lineTool btn-sm btn btn-default" data-type="6">
								<input type="radio" name="lineTool" checked> <i class="iconcolor-line6"></i>
							</label>
							<label class="lineTool btn-sm btn btn-default" data-type="8">
								<input type="radio" name="lineTool" checked> <i class="iconcolor-line8"></i>
							</label>
						</div>
						<div class="btn-group" data-toggle="buttons" style="margin-bottom: 5px;">
							<label class="colorTool btn-sm btn btn-default" data-type="ba">
								<input type="radio" name="colorTool" checked> <i class="iconcolor-black"></i>
							</label>
							<label class="colorTool btn-sm btn btn-default active" data-type="r">
								<input type="radio" name="colorTool" checked> <i class="iconcolor-red"></i>
							</label>
							<label class="colorTool btn-sm btn btn-default" data-type="b">
								<input type="radio" name="colorTool" checked> <i class="iconcolor-blue"></i>
							</label>
							<label class="colorTool btn-sm btn btn-default" data-type="y">
								<input type="radio" name="colorTool" checked> <i class="iconcolor-yellow"></i>
							</label>
						</div>
						
                                                
					</div>
			    </div>
				<div id="{canvasDraw}_box" class="canvasDraw_box" style="width: {widthDraw}px;height: {heightDraw}px;"></div>
			</div>
			<div class="text-center save-btn-drawing">
			    <button id="{saveBtn}" class="btn btn-primary disabledDisplay" name="yt0" type="button">ยืนยันการบันทึก</button>
			</div>
		    ';
	
	public $allow_bg = 0;
	public $default_bg = '';
	
	public function init()
    {
        parent::init();
        
	if(!isset($this->options['width'])){
	    $this->options['width'] = 800;
	} 
	
	if(!isset($this->options['height'])){
	    $this->options['height'] = 600;
	} 
	
	if(!isset($this->options['canvasDraw'])){
	    $this->options['canvasDraw'] = 'canvasDrawDiv';
	} 
	
	if(!isset($this->options['saveBtn'])){
	    $this->options['saveBtn'] = 'saveDrawing';
	} 
	
	if(!isset($this->options['drawingName'])){
	    $this->options['drawingName'] = 'myCanvasDrawing';
	} 
	
	if(!isset($this->options['outlineName'])){
	    $this->options['outlineName'] = '';
	} 
	
	if(!isset($this->options['outlinePath'])){
	    $this->options['outlinePath'] = '';
	} 
	
	if(!isset($this->options['outlineBg'])){
	    $this->options['outlineBg'] = '';
	} 
	
	if(!isset($this->options['saveUrl'])){
	    $this->options['saveUrl'] = Url::to(['//ezforms2/drawing/save-image']);
	} 
	
	if(!isset($this->options['bgUrl'])){
	    $inputID;
	    if ($this->hasModel()) {
		$inputID = Html::getInputId($this->model, $this->attribute);
	    } else {
		$inputID = $this->name;
	    }
	    $this->options['bgUrl'] = Url::to(['//ezforms2/drawing/bg-image', 'name'=>$inputID]);
	}
	
	$id = $this->options['id'];
	$this->options['canvasDraw'] = $this->options['canvasDraw'].'_'.$id;
	$this->options['saveBtn'] = $this->options['saveBtn'].'_'.$id;
	$this->options['drawingName'] = $this->options['drawingName'].'_'.$id;
    }
    
    public function run()
    {
	$inputID;
	$inputValue;
	if ($this->hasModel()) {
            $inputID = Html::getInputId($this->model, $this->attribute);
	    $inputValue = Html::getAttributeValue($this->model, $this->attribute);
        } else {
	    $inputID = $this->name;
	    $inputValue = $this->value;
        }
	
	
	$template = $this->ui_temp;
	
	$fileUpload = FileUpload::widget([
	    'name' => 'outline-bg',
	    'url' => $this->options['bgUrl'], 
	    'plus' => true,
            //'useDefaultButton'=>false,
	    'options' => ['accept' => 'image/*', 'class'=>'upload'],
	    'clientOptions' => [
		'maxFileSize' => 3000000
	    ],
	    // Also, you can specify jQuery-File-Upload events
	    // see: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#processing-callback-options
	    'clientEvents' => [
		'fileuploaddone' => "function(e, data) {
					var bgsize = 'auto auto';
					if(data.result.files.width > data.result.files.height){
					    bgsize = '{$this->options['width']}px auto';
					} else {
					    bgsize = 'auto {$this->options['height']}px';
					}
					$('input[name=\"outline-bg\"]').attr('data-url', data.result.files.newurl);
					$('input[name=\"outline-bg\"]').fileupload({'maxFileSize':3000000,'url':$('input[name=\"outline-bg\"]').attr('data-url')});
					
					if($('#".$inputID."').val()==''){
					    $('#".$inputID."').val(\"'',\"+data.result.files.name);
					} else {
					    var str = $('#".$inputID."').val();
					    var valueArr = str.split(',');
					    var valueStr = valueArr[0]+','+data.result.files.name;
					    $('#".$inputID."').val(valueStr);
					}

					$('#{$this->options['drawingName']}').css('background-image', 'url('+data.result.files.url+')');
					$('#{$this->options['drawingName']}').css('background-size', bgsize);
					$('#{$this->options['drawingName']}').css('background-position', 'center center');
					$('#{$this->options['drawingName']}').css('background-repeat', 'no-repeat');
				    }",
		'fileuploadfail' => "function(e, data) {
					console.log(e);
					console.log(data);
				    }",
	    ],
	]);
                                        
	$width = $this->options['width']>0?$this->options['width']:800;
        $height = $this->options['height']>0?$this->options['height']:600;
        
	echo strtr($template, [
		'{canvasDraw}' => $this->options['canvasDraw'],
		'{saveBtn}' => $this->options['saveBtn'],
		'{input-upload}' => $fileUpload,
                '{widthDraw}' => $width,
                '{heightDraw}' => $height,
	    ]);
	
	//filename,filebg
	
	if(isset($inputValue) && $inputValue!=''){
	    $fileArr = explode(',', $inputValue);
		if(count($fileArr)>1){
			$fileName = $fileArr[0];
			$fileBg = $fileArr[1];

			if(stristr($fileName, '.png') == TRUE){
                            
                            
				$idName = '';
				if ($_SERVER["REMOTE_ADDR"] == '::1' || $_SERVER["REMOTE_ADDR"] == '127.0.0.1') {
					$idName = 'mycom';
				} else {
					$idName = str_replace('.', '_', $_SERVER["REMOTE_ADDR"]);
				}
				$nowFileName = $idName . '_'.$inputID.'_tmp.png';

                                
                                
				@copy(Yii::getAlias('@storage/ezform/drawing/data/') . $fileName, Yii::getAlias('@app/web/drawing/') . $nowFileName);

				$this->options['outlineName'] = $nowFileName;
				$this->options['outlinePath'] = Yii::getAlias('@web').'/drawing';
			}
		}
	}
	//echo $this->ui_temp;
	
        $this->registerClientScript();
        if ($this->hasModel()) {
            echo Html::activeHiddenInput($this->model, $this->attribute);
        } else {
            echo Html::hiddenInput($this->name, $this->value);
        }
	
	if (isset($this->default_bg) && $this->default_bg!='') {
		
	    $fileBg = $this->default_bg;
	
	    if(stristr($fileBg, '.png') == TRUE){
			$view = $this->getView();
			list($width, $height, $type, $attr) = getimagesize(Yii::getAlias('@storage/ezform/drawing/bg/').$fileBg);
			$disable = ($this->allow_bg)?'':"$('#{$this->options['canvasDraw']} .fileUpload').addClass('disabledDisplay');";
			$view->registerJs("
				$disable
				var bgsize = 'auto auto';
				if({$width} > {$height}){
				bgsize = '{$this->options['width']}px auto';
				} else {
				bgsize = 'auto {$this->options['height']}px';
				}
				$('#{$this->options['drawingName']}').css('background-image', 'url(".Url::to(Yii::getAlias('@storageUrl').'/ezform/drawing/bg/'.$fileBg).")');
				$('#{$this->options['drawingName']}').css('background-size', bgsize);
				$('#{$this->options['drawingName']}').css('background-position', 'center center');
				$('#{$this->options['drawingName']}').css('background-repeat', 'no-repeat');
			");
			}
	}
		
	if(isset($inputValue) && $inputValue!=''){
	    $fileArr = explode(',', $inputValue);
		if(count($fileArr)>1){
			$fileName = $fileArr[0];
			$fileBg = $fileArr[1];

			if(stristr($fileBg, '.png') == TRUE){
				$view = $this->getView();
				list($width, $height, $type, $attr) = getimagesize(Yii::getAlias('@storage/ezform/drawing/bg/').$fileBg);

				$view->registerJs("
					var bgsize = 'auto auto';
					if({$width} > {$height}){
						bgsize = '{$this->options['width']}px auto';
					} else {
						bgsize = 'auto {$this->options['height']}px';
					}
					$('#{$this->options['drawingName']}').css('background-image', 'url(".Url::to(Yii::getAlias('@storageUrl').'/ezform/drawing/bg/'.$fileBg).")');
					$('#{$this->options['drawingName']}').css('background-size', bgsize);
					$('#{$this->options['drawingName']}').css('background-position', 'center center');
					$('#{$this->options['drawingName']}').css('background-repeat', 'no-repeat');
				");
			}
		}
	} 
	
	
    }

    
    public function registerClientScript() {
	$view = $this->getView();
	$inputID;
	if ($this->hasModel()) {
            $inputID = Html::getInputId($this->model, $this->attribute);
        } else {
	    $inputID = $this->name;
        }
	
	DrawingAsset::register($view);
	$view->registerJs("
//	    drawingName = '".$this->options['drawingName']."';
//	    saveBtn = '".$this->options['saveBtn']."';
//	    canvasDraw = '".$this->options['canvasDraw']."';
		
	    drawCanvas('".$this->options['drawingName']."', '".$this->options['canvasDraw']."', '".$this->options['saveBtn']."', ".$this->options['width'].", ".$this->options['height'].", '".$this->options['outlineName']."', '".$this->options['outlinePath']."', '".$this->options['outlineBg']."');

		$('#{$this->options['canvasDraw']} .fullscreen').click(function(){
			if ($.fullscreen.isFullScreen()) {
				$.fullscreen.exit();
				return false;
			} else {
				$('#{$this->options['canvasDraw']}').parent().fullscreen();
				return false;
			}
		});

	    $('#".$this->options['saveBtn']."').click(function(){
		    var canvas = document.getElementById('".$this->options['drawingName']."');
		    var img = canvas.toDataURL('image/png');
		    $.ajax({
			method: 'POST',
			url:'" . $this->options['saveUrl'] . "',
			data: {type: 'data', image: img, name: '".$inputID."', '_csrf':'".Yii::$app->request->getCsrfToken()."'},
			dataType: 'JSON',
			success: function(result, textStatus) {
			    if(result.status == 'success') {
					if($('#".$inputID."').val()==''){
						$('#".$inputID."').val(result.data+\",''\");
					} else {
						var str = $('#".$inputID."').val();
						var valueArr = str.split(',');
						var valueStr = result.data+','+valueArr[1];
						$('#".$inputID."').val(valueStr);
					}
			    } else {
				" . SDNoty::show('result.message', 'result.status') . "
			    }
			}
		    });
	    });

	");
    }
}
