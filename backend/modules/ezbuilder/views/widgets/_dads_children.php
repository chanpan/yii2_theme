<?php

/**
 * _dads_children file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 18 ส.ค. 2559 14:58:22
 * @link http://www.appxq.com/
 */

//field_id : number
//field_size : number
//style_color : string
//field_item : Html
?>

<div class="view-item dads-children col-md-<?=$field_size?> " data-dad-id="<?=$field_id?>" data-dad-position="<?=$field_order?>" style="<?=$style_color?>">
    <div class="button-item">
	<button class="btn btn-default btn-sm btn-edit" data-url="<?=  yii\helpers\Url::to(['/ezbuilder/ezform-fields/update', 'id'=>$field_id])?>"><i class="fa fa-pencil"></i></button>
	<button class="btn btn-default btn-sm btn-clone" data-url="<?=  yii\helpers\Url::to(['/ezbuilder/ezform-fields/clone', 'id'=>$field_id])?>"><i class="fa fa-files-o"></i></button>
	<button class="btn btn-default btn-sm btn-delete" ><i class="fa fa-trash"></i></button>
	<button class="btn btn-default btn-sm btn-size-small" ><i class="fa fa-compress"></i></button>
	<button class="btn btn-default btn-sm btn-size" ><i class="fa fa-expand"></i></button>
    </div>
    <div class="view-box draggable dad-draggable-area">
	<?=$field_item?>
    </div>
</div>