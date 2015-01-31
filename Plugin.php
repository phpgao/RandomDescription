<?php

/**
 * 随机站点描述
 *
 * @package RandomDescription
 * @author  老高@PHPer
 * @version 1.0
 * @link http://www.phpgao.com/
 */
class RandomDescription_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->beforeRender = array('RandomDescription_Plugin', 'change_description');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {

    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {

        $description = new Typecho_Widget_Helper_Form_Element_Textarea('description', NULL, Helper::options()->description, _t('需要变化的文本,一行一个'), _t('留空取当前站点描述'));
        $form->addInput($description);

        $last_string = new Typecho_Widget_Helper_Form_Element_Hidden('last_string', NULL, NULL);
        $form->addInput($last_string);

        $last_time = new Typecho_Widget_Helper_Form_Element_Hidden('last_time', NULL, time());
        $form->addInput($last_time);

        $interval = new Typecho_Widget_Helper_Form_Element_Text('interval', NULL, 0, _t('变更间隔'), _t('单位 : 秒'));
        $form->addInput($interval);

    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    public static function change_description($class)
    {

        $class->random = htmlspecialchars(self::get_random_string());
    }

    public static function get_random_string()
    {
        $config = Helper::options()->plugin('RandomDescription');
        if (empty($config->description)) {
            return Helper::options()->description;
        }
        if (time() - $config->last_time > $config->interval) {
            $string = self::gen_string_from($config->description);
            $time = array('last_time' => time(), 'last_string' => $string);
            Helper::configPlugin('RandomDescription', $time);
            return $string;
        } else {
            return $config->last_string;
        }
    }

    public static function gen_string_from($description)
    {
        $str_array = explode("\n", $description);
        $key = array_rand($str_array);
        return $str_array[$key];
    }
}
