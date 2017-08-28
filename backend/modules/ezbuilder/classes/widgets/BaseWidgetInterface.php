<?php
namespace backend\modules\ezbuilder\classes\widgets;
/**
 * newPHPClass class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 19 ส.ค. 2559 17:27:52
 * @link http://www.appxq.com/
 * @example 
 */
interface BaseWidgetInterface {
    
    /**
     * 
     * @param UrlManager $manager the URL manager
     * @param model $input 
     * @return HTML TAG
     */
    public function generateViewEditor($field, $input);
    
    /**
     * 
     * @param UrlManager $manager the URL manager
     * @param model $field 
     * @param model $input 
     * @return HTML TAG
     */
    public function generateViewItem($field, $input);
}
