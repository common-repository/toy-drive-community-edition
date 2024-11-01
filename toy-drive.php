<?php

/**
 * Toy Drive
 *
 * @package     ToyDrive
 * @author      Michael Morris
 * @copyright   2021 Magic Box Software LLC
 * @license     GPL-3.0
 *
 * @wordpress-plugin
 * Plugin Name: Toy Drive Community Edition
 * Plugin URI:  https://toydrives.org
 * Description: Collect toy requests online. View requests and export as a spreadsheet. Limit the number of requested toys if desired. Simple administration. Easy Setup.
 * Version:     1.1.3
 * Requires PHP: 7.0
 * Requires at least: 4.7
 * Author:      Magic Box Software LLC
 * Author URI:  https://magicboxsoftware.com
 * Text Domain: toy-drive
 * License:     GPL v3
 * License URI: https://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

/**
 * The submission form shortcode
 * @param mixed[] $atts
 * @return string
 */

function toydrive_get_allowed_form_html(){
    return [
        'h1' => [],
        'h2' => [],
        'h3' => [],
        'h4' => [],
        'a' => [
            'id' => [],
            'href' => [],
            'class' => [],
            'style' => []
        ],
        'div' =>[
            'id' => [],
            'class' => [],
            'style' => []
        ],
        'span' => [
            'id' => [],
            'class' => [],
            'style' => []
        ],
        'img' => [
            'src' => [],
            'class' => [],
            'style' => [],
            'id' => []
        ],
        'form' => [
            'id' => [],
            'data-response' => []
        ],
        'input' => [
            'type' => [],
            'name' => [],
            'style' => [],
            'class' => [],
            'required' => [],
            'value' => [],
            'placeholder' => []
        ],
        'select' => [
            'name' => [],
            'style' => [],
            'class' => [],
            'required' => []
        ],
        'option' => [
            'value' => []
        ],
        'table' => [
            'style' => [],
            'class' => [],
            'id' => []
        ],
        'tr' => [
            'class' => [],
            'style' => []
        ],
        'td' => [
            'class' => [],
            'style' => []
        ],
        'button' => [
            'id' => [],
            'class' => [],
            'style' => [],
            'type' => []
        ],
        'script' => [],
        'br' => []
    ];
}

function toydrive_get_allowed_response_html(){
    return [
        'h1' => [],
        'h2' => [],
        'h3' => [],
        'h4' => [],
        'br' => [],
        'div' => [
            'id' => [],
            'class' => [],
            'style' => []
        ]
    ];
}

function ToyDrive_start_form($atts){
    if(get_option('td_force_closed')){
        return esc_html(get_option('td_closed_msg'));
    }
    if(file_exists(plugin_dir_path(__FILE__).'/data/kids.xls')){
        $xls = file(plugin_dir_path(__FILE__).'/data/kids.xls');
        
        if(count($xls) > 1 && get_option('td_max_kids') != '0'){
            $total_kids = 0;
            for($i = 1; $i < count($xls); $i++){
                $kids = (count(explode(',', $xls[$i])) - 6) / 3;
                $total_kids = $total_kids + $kids;
                //return $total_kids;
            }
            if($total_kids >= get_option('td_max_kids')){
                return esc_html(get_option('td_closed_msg'));;
            }
        }
    }
    ob_start();
    ?>
    <div id="ToyDrive_form_wrapper" style="max-width:375px;width:100%;margin:0 auto;">
        <div style="text-align:center"><h2><?php echo esc_html(get_option('td_form_title'));?></h2></div>
    
        <form id="signup-form" data-response="<?php echo wp_kses(get_option('td_form_response'), toydrive_get_allowed_response_html());?>">
            <div class="toydrive-fieldset td-parent">
                <h4>Parent/Guardian/Other Adult</h4>
                <div style="padding:10px 20px;">Name<br><input name="parent_name" required style="width:100%;" /></div>
                <div style="padding:10px 20px;">Street Address<br><input name="street" required style="width:100%;" /></div>
                <div style="padding:10px 20px;">City<br><input name="city" required style="width:100%;" /></div>
                <div style="padding:10px 20px;">Zip<br><input name="zip" style="width:100%;" /></div>
                <div style="padding:10px 20px;">Email<br><input type="email" name="email" style="width:100%;" /></div>
                <div style="padding:10px 20px;">Phone<br><input type="tel" name="phone" required style="width:100%;" /></div>
            </div>
            <div id="kids">
                <div class="toydrive-fieldset td-child">
                    <h4>Child 1</h4>
                    <table>
                        <tr style="background: transparent;">
                            <td style="width:100px;">
                                <div style="padding:10px;">Name<br><input type="text" name="cname_1" style="width:100%;" class="child-name" /></div>
                            </td>
                            <td style="width: 75px;">
                                <div style="padding:10px;">Age<br><input type="number" name="age_1" style="width:100%;" class="child-age" required /></div>
                            </td>
                            <td>
                                <div>Gender<br><select name="sex_1" style="padding:10px;margin:10px 0px;border-color: #ccc;"><option value="F">Girl</option><option value="M">Boy</option></select></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div style="padding:10px 20px;text-align:center;"><button id="add-child">Add Another Child</button></div>
            <div style="padding:20px 20px;text-align:center;">
                <button id="td-submit-button" type="submit" style="padding:20px;margin:0 auto;font-size:24px;">
                    Submit <span id="td-form-spinner" style="display: none;"><img style="width:24px;" src="<?php echo plugins_url('css/spinner.gif',__FILE__ ); ?>" /></span>
                </button>
        </form>

    </div>

    <?php
    return wp_kses(ob_get_clean(), toydrive_get_allowed_form_html());
}

add_shortcode('ToyDrive_form', 'ToyDrive_start_form');

/**
 * Injects the script and styles for the submission form
 */
function toydrive_frontend(){
    wp_enqueue_script( 'td-form', plugins_url('scripts/signup.js',__FILE__ ), [], '1.0', true);
    wp_enqueue_style( 'td-style', plugins_url('css/signup.css',__FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'toydrive_frontend' );

if(strpos($_SERVER['REQUEST_URI'], 'admin-ajax') !== false){
    include_once(__DIR__.'/include/api.php');
}


/**
 * Renders the main admin page
 */
function ToyDrive_show_admin_page(){
    ob_start();
    ?>
<h2>ToyDrive Administration</h2>
<div style="padding:20px">
    {download_link}
</div>
<div style="padding:20px;">
    Shortcode: [ToyDrive_form] - Paste the shortcode where you want the signup form to appear.
</div>
<form id="toydrive_admin">
    <div style="padding:20px">
        Form Title - Shown above the signup form<br>
        <input type="text" name="td_form_title" placeholder="Our Toy Drive Signup" value="{form_title}" style="width:100%" />
    </div>
    <div style="padding:20px">
        Form Submission Response - HTML is OK<br>
        <input type="text" name="td_form_response" placeholder="Thank you" value="{sub_response}" style="width:100%" />
    </div>
    <div style="padding:20px">
        Backup Email - A copy of each submission will be sent to this address if specified<br>
        <input type="email" name="td_backup_email" value="{backup_email}" style="width:100%" />
    </div>
    <div style="padding:20px">
        Max children - Will stop after family that hits this number so there may be a few extra = 0 for unlimited<br>
        <input type="number" name="td_max_kids" value="{max_kids}" />
    </div>
    <div style="padding:20px">
        Submissions closed message<br>
        <input type="text" name="td_closed_msg" value="{closed_msg}" style="width:100%" />
    </div>
    <div style="padding:20px">
        <input type="checkbox" name="td_force_closed" value="1" {forced_closed} /> Force closed
    </div>
    <div style="padding:20px">
        <input type="submit" value="Update" />
    </div>
</form>
<?php
    $template = ob_get_clean();
    if(file_exists(plugin_dir_path(__FILE__).'/data/kids.xls')){
        $download_link = '<a href="/wp-admin/admin-ajax.php?action=toydrive_download_spreadsheet">Download Spreadsheet</a> - <a style="cursor: pointer;" id="delete_datafile">Delete Spreadsheet</a>';
        $download_link .= ' - <a style="cursor:pointer;" id="view_datafile">View Spreadsheet</a>';
    }
    else{
        $download_link = 'You do not have any submissions yet<br><br>When you do a download link will appear here';
    }
    $template = str_replace('{download_link}', $download_link, $template);
    
    $template = str_replace('{form_title}', get_option('td_form_title') ? esc_attr(get_option('td_form_title')) : '', $template);
    $template = str_replace('{sub_response}', get_option('td_form_response') ? esc_attr(get_option('td_form_response')) : 'Thank you. Your submission was received', $template);
    $template = str_replace('{backup_email}', get_option('td_backup_email') ? esc_attr(get_option('td_backup_email')) : '', $template);
    $template = str_replace('{max_kids}', get_option('td_max_kids') ? esc_attr(get_option('td_max_kids')) : '0', $template);
    $template = str_replace('{closed_msg}', get_option('td_closed_msg') ? esc_attr(get_option('td_closed_msg')) : 'We are sorry but submissions are now closed', $template);
    $template = str_replace('{forced_closed}', get_option('td_force_closed') ? 'checked' : '', $template);
    ob_start();
    ?>
<script>
    const td_form = document.getElementById('toydrive_admin');
    td_form.onsubmit = async function(e){
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('action', 'toydrive_save_admin');
        const resp = await fetch(ajaxurl, {method: 'POST', body: fd});
        const result = await resp.json();
        if(result.success){
            alert('Settings Updated');
        }
        else{
            alert('Settings Not Saved');
        }
        return false;
    }
    const delete_link = document.getElementById('delete_datafile');
    if(delete_link){
        delete_link.onclick = async function(e){
            e.preventDefault();
            if(confirm('Are you sure you want to delete the data file?')){
                const fd2 = new FormData();
                fd2.append('action', 'toydrive_unlink_datafile');
                const resp = await fetch(ajaxurl, {method: 'POST', body: fd2});
                const result = await resp.text();
                if(result.success){
                    alert('Datafile deleted');
                }
                else{
                    alert('Datafile Not Deleted');
                }
                return false;
            }
            return false;
        };
    }
    const view_link = document.getElementById('view_datafile');
    if(view_link){
        view_link.onclick = function(e){
            e.preventDefault();
            Papa.parse("<?php echo home_url(); ?>/wp-admin/admin-ajax.php?action=toydrive_download_spreadsheet&ex=" + Date.now(), {
            download: true,
            complete: function(results) {
                    Heiho(results.data, {header: true});
            }
    });
        }
    }
</script>
<?php
    $template .= ob_get_clean();
    echo wp_kses($template, toydrive_get_allowed_form_html());
}

/**
 * Renders the admin menu link
 */
function ToyDrive_menu_page(){
    add_menu_page( 'Toy Drive Admin', 'Toy Drive', 'manage_options', 'ToyDrive', 'ToyDrive_show_admin_page', plugins_url('toylogo16.png',__FILE__ ), 90 );
}
add_action( 'admin_menu', 'ToyDrive_menu_page' );

/**
 * Add action links to the plugins admin page
 * @param array $links
 * @return string[]
 */
function toydrive_add_plugin_actions($links){
    $links[] = '<a href="/wp-admin/admin.php?page=ToyDrive">Settings</a>';
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'toydrive_add_plugin_actions' );

/**
 * Injects the admin scripts and styles
 * @param string $hook
 * @return null
 */
function toydrive_admin_css_and_js($hook) {
    if (!isset($_GET['page']) || 'ToyDrive' != $_GET['page'] ) {
        return;
    }
    wp_enqueue_style('spreadsheet-css', plugins_url('css/heiho.css',__FILE__ ));
    wp_enqueue_script('spreadsheet-js', plugins_url('scripts/heiho.js',__FILE__ ));
    wp_enqueue_script('csv-parse-js', plugins_url('scripts/csvparser.min.js',__FILE__ ));
}
add_action('admin_enqueue_scripts', 'toydrive_admin_css_and_js');