<?php
/**
 * @package VM_split
 * @version 0.1
 */
/*
Plugin Name: VM_split
Plugin URI: http://visermort.ru/
Description: Demo plugin. Splits value of field as xxx,yyyyyy,zzzzzzz to array and shows on page like list &lt;ul&gt; &lt;li&gt;xxx&lt;/li&gt;&lt;li&gt;yyyyy&lt;li&gt;&lt;li&gt;zzzzzzz&lt;li&gt; &lt;ul&gt;
Author: visermort
Version: 0.1
Author URI: http://visermort.ru/
Text Domain: vm_split
Domain Path: /languages/
*/



//функция вывода страницы настроек плагина
function vm_split_settings(){
    ?>
    <div class="wrap">
        <h2>VM split</h2>

            <p><?php echo __('Executed by ', 'vm_split');?> Shortcode.</p>
            <div class="manual">
                <h4><?php echo __('Sample using', 'vm_split');?>:</h4>
                <p>
                    $listVar = get_post_meta( $post->ID , 'someField', true);
                </p>
                <p>
                    echo do_shortcode ('[vm_slpit dat="'.$listVar.'" ulclass="list-class" liclass="item-class" action="strtoupper" ]');
                </p>
                <h4><?php echo __('Attributes', 'vm_split');?>:</h4>
                <ul>
                    <li>
                        'dat'  - <?php echo __('data variable', 'vm_split');?>;
                    </li>
                    <li>
                        'ulclass'  - <?php echo __('class attribute for &lt;ul&gt;, default ', 'vm_split');?> "ulclass";
                    </li>
                    <li>
                        'liclass'  - <?php echo __('class attribute for &lt;li&gt;, default ', 'vm_split');?> "liclass";
                    </li>
                    <li>
                        'action' - <?php echo __(' function to update each item; if empty, item will not be changed - function of PHP, WordPress or you function;', 'vm_split');?>
                    </li>
                </ul>


        </div>

        <form method="post" action="options.php">
            <?php settings_fields( 'vm_group' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo __('Split by pattern', 'vm_split');?> </th>
                    <td><input type="text" name="vm_split_pattern" value="
                    <?php echo (get_option('vm_split_pattern')? get_option('vm_split_pattern') : ';'); ?>" /></td>
                </tr>

            <p class="submit">
                <input type="submit" class="button-primary" value=" <?php echo __('Save Changes', 'vm_split');?>" />
            </p>

        </form>
    </div>
<?php }


//регистриреум настройки
function register_mysettings() {
    register_setting( 'vm_group', 'vm_split_pattern' );
}


//функция создаёт страницу в админ панели и регистрирует сохранение настроек
function catalog_admin_menu(){
    add_options_page( __('VM split', 'vm_split'), __('VM split', 'vm_split'), 8, basename(__FIle__), 'VM_split_settings');

    //call register settings function
    add_action( 'admin_init', 'register_mysettings' );
}


// в админ меню вызывает функцию

add_action('admin_menu', 'catalog_admin_menu');


//функция вывода
function vm_print($attr){
    //получаем атрибуты
    $attr= shortcode_atts(
        array(
            'dat' => null, //данные
            'ulclass' => 'ulclass', //класс списка по умолчанию
            'liclass' => 'liclass',  //кдасс элемента по умолчанию
            'action' => null,
        ),
        $attr,
        'vm_slpit' );

    extract( $attr );

    //разбиваем значение поля на массив подстрок
    if ($dat) {
        $list = explode(get_option('vm_split_pattern'), $dat);
    } else {
        $list = null;
    }
    //собственноо вывод массива строк в список, классы  - из атрибутов
    $ret = '<ul class="'.$ulclass.'">';
    if ($list) {
        foreach ($list as $item) {
            //если задано действие, то оно вызывается
            if ($action) {
                $item = $action($item);
            }
            $ret .= '<li class="'.$liclass.'">'.$item.' </li>';
        }
    }
    $ret .='</ul>';

    return  $ret;
}


//добавляем шорткод
add_shortcode('vm_slpit', 'vm_print');


//делаем интернационализацию
function load_plugin_text_vm_split() {
    load_plugin_textdomain( 'vm_split', FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
}

add_action( 'plugins_loaded', 'load_plugin_text_vm_split' );
