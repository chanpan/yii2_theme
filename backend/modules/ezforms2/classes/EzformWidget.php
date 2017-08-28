<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class EzformWidget extends Html {
    
    /*
     * echo $form->field($model, 'ezf_name')->inline()->radioList([
						'data'=>[1,2,3,4,5,6], 
						'other'=>[
						    1=>[
							'attribute'=>'ezf_id', 
							'suffix'=>'หน่วย2'
						    ],
						    5=>[
							'attribute'=>'ezf_id', 
							'suffix'=>'หน่วย'
						    ]
						]
					]);
     */
    public static function radioList($name, $selection = null, $items = [], $options = []) {
        $inline = isset($options['inline'])?$options['inline']:0;
        $annotated = isset($options['annotated'])?$options['annotated']:0;
	$encode = !isset($options['encode']) || $options['encode'];
	$formatter = isset($options['item']) ? $options['item'] : null;
	$itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
	$lines = [];
	$index = 0;
        
	if(isset($items['data']) && !empty($items['data'])){
	    $items_other = isset($items['other'])?$items['other']:[];
	    
	    foreach ($items['data'] as $value => $label) {
		$checked = $selection !== null && (!is_array($selection) && !strcmp($value, $selection) || is_array($selection) && in_array($value, $selection));
		
                $other = isset($items_other[$value])?$items_other[$value]:NULL;
		$showVar = '';
                if($annotated){
                    if(isset($other['attribute'])){
                        $showVar = "<code>{$other['attribute']}</code>";
                    }
                }
                    
		if ($formatter !== null) {
		    $lines[] = call_user_func($formatter, $index, $label, $name, $checked, $value, $other, $showVar, $encode);
		} else {
                    
                    $other_input = ((isset($other))?Html::textInput($other['attribute'], '', ['class'=>'form-control']):'') . ((isset($other))?" {$other['suffix']}":'');
		    if($inline){
                        $lines[] = static::radio($name, $checked, array_merge($itemOptions, [
                                'labelOptions' => ['class' => 'radio-inline'],
				'value' => $value,
				'label' => '<span>'.($encode ? static::encode($label) : $label).' '.$other_input.' '.$showVar.'</span> ',
                        ]));
                    } else {
                        $lines[] = '<div class="radio">'.static::radio($name, $checked, array_merge($itemOptions, [
				'value' => $value,
				'label' => '<span>'.($encode ? static::encode($label) : $label).' '.$other_input.' '.$showVar.'</span> ',
                        ])).'</div>';
                    }
		}
		$index++;
	    }
	}

	$separator = isset($options['separator']) ? $options['separator'] : "\n";
	if (isset($options['unselect'])) {
	    // add a hidden field so that if the list box has no option being selected, it still submits a value
	    $hidden = static::hiddenInput($name, $options['unselect']);
	} else {
	    $hidden = '';
	}

	$tag = isset($options['tag']) ? $options['tag'] : 'div';
	unset($options['tag'], $options['unselect'], $options['encode'], $options['separator'], $options['item'], $options['itemOptions']);

	return $hidden . static::tag($tag, implode($separator, $lines), $options);
    }

    public static function activeCheckbox($model, $attribute, $options = [])
    {
        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);
        $value = static::getAttributeValue($model, $attribute);

        if (!array_key_exists('value', $options)) {
            $options['value'] = '1';
        }
        if (!array_key_exists('uncheck', $options)) {
            $options['uncheck'] = '0';
        }
        if (!array_key_exists('label', $options)) {
            $options['label'] = static::encode($model->getAttributeLabel(static::getAttributeName($attribute)));
        }

        $checked = "$value" === "{$options['value']}";

        if (!array_key_exists('id', $options)) {
            $options['id'] = static::getInputId($model, $attribute);
        }
        $options['model'] = $model;
        
        return static::checkbox($name, $checked, $options);
    }
    
    public static function checkbox($name, $checked = false, $options = [])
    {
        $annotated = isset($options['annotated'])?$options['annotated']:0;
        unset($options['annotated']);
        $model = isset($options['model'])?$options['model']:null;
        unset($options['model']);
        $inline = isset($options['inline'])?$options['inline']:0;
        unset($options['inline']);
        $other = isset($options['other'])?$options['other']:null;
        unset($options['other']);
        
        $options['checked'] = (bool) $checked;
        $value = array_key_exists('value', $options) ? $options['value'] : '1';
        if (isset($options['uncheck'])) {
            // add a hidden field so that if the checkbox is not selected, it still submits a value
            $hidden = static::hiddenInput($name, $options['uncheck']);
            unset($options['uncheck']);
        } else {
            $hidden = '';
        }
        if (isset($options['label'])) {
            if(isset($model)){
                $other_input = ((isset($other))?Html::activeTextInput($model, $other['attribute'], ['class'=>'form-control']):'') . ((isset($other))?" {$other['suffix']}":'');
            } else {
                $other_input = ((isset($other))?Html::textInput($other['attribute'], '', ['class'=>'form-control']):'') . ((isset($other))?" {$other['suffix']}":'');
            }
            
            $showVar = '';
            if($annotated){
                if(isset($other['attribute'])){
                    $showVar = "<code>{$other['attribute']}</code>";
                }
            }
                    
            $label = "<span>{$options['label']} $other_input $showVar</span>";
            $labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : [];
            unset($options['label'], $options['labelOptions']);
            
            if($inline){
                
                if(isset($labelOptions['class'])){
                    $labelOptions['class'] .=' checkbox-inline ';
                } else {
                    $labelOptions['class']='checkbox-inline';
                }

                $content = $hidden.static::label(static::input('checkbox', $name, $value, $options) . ' ' . $label, null, $labelOptions);
                return $content;
            } else {
                
                $content = '<div class="checkbox">'.$hidden.static::label(static::input('checkbox', $name, $value, $options) . ' ' . $label, null, $labelOptions).'</div>';
                return $content;
            }
        } else {
            return $hidden . static::input('checkbox', $name, $value, $options);
        }
    }
    
    public static function activeRadio($model, $attribute, $options = [])
    {
        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);
        $value = static::getAttributeValue($model, $attribute);

        if (!array_key_exists('value', $options)) {
            $options['value'] = '1';
        }
        
        if (!array_key_exists('label', $options)) {
            $options['label'] = static::encode($model->getAttributeLabel(static::getAttributeName($attribute)));
        }

        $checked = "$value" === "{$options['value']}";

        if (!array_key_exists('id', $options)) {
            $options['id'] = static::getInputId($model, $attribute);
        }

        return static::radio($name, $checked, $options);
    }
    
    public static function radio($name, $checked = false, $options = [])
    {
        $options['checked'] = (bool) $checked;
        $value = array_key_exists('value', $options) ? $options['value'] : '1';
        if (isset($options['uncheck'])) {
            // add a hidden field so that if the radio button is not selected, it still submits a value
            $hidden = static::hiddenInput($name, $options['uncheck']);
            unset($options['uncheck']);
        } else {
            $hidden = '';
        }
        if (isset($options['label'])) {
            $label = $options['label'];
            $labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : [];
            unset($options['label'], $options['labelOptions']);
            $content = static::label(static::input('radio', $name, $value, $options) . ' ' . $label, null, $labelOptions);
            return $hidden . $content;
        } else {
            return $hidden . static::input('radio', $name, $value, $options);
        }
    }
}

?>
