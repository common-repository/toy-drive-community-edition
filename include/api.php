<?php

function toydrive_handle_form(){
    $header_row = [
        'Name',
        'Street',
        'City',
        'Zip',
        'Phone',
        'Email',
        'Name 1',
        'Gender 1',
        'Age 1',
        'Name 2',
        'Gender 2',
        'Age 2',
        'Name 3',
        'Gender 3',
        'Age 3',
        'Name 4',
        'Gender 4',
        'Age 4',
        'Name 5',
        'Gender 5',
        'Age 5',
        'Name 6',
        'Gender 6',
        'Age 6',
        'Name 7',
        'Gender 7',
        'Age 7',
        'Name 8',
        'Gender 8',
        'Age 8',
        'Name 9',
        'Gender 9',
        'Age 9',
        'Name 10',
        'Gender 10',
        'Age 10'
    ];
    
    $xls_file_path = dirname(__DIR__).'/data/kids.xls';
    if(!file_exists($xls_file_path) || !count(file($xls_file_path))){
        $fp = fopen($xls_file_path, 'a');
        fputcsv($fp, $header_row);
        fclose($fp);
    }
    $fields = [
        sanitize_text_field($_POST['parent_name']),
        sanitize_text_field($_POST['street']),
        sanitize_text_field($_POST['city']),
        sanitize_text_field($_POST['zip']),
        sanitize_text_field($_POST['phone']),
        sanitize_email(sanitize_text_field($_POST['email'])),
        sanitize_text_field($_POST['cname_1']),
        sanitize_text_field($_POST['sex_1']),
        sanitize_text_field($_POST['age_1'])
        ];
    for($i = 2; $i < 50; $i++){
        if(isset($_POST['age_'.$i]) && '' != $_POST['age_'.$i]){
            $fields[] = isset($_POST['cname_'.$i]) ? sanitize_text_field($_POST['cname_'.$i]) : '';
            $fields[] = sanitize_text_field($_POST['sex_'.$i]);
            $fields[] = sanitize_text_field($_POST['age_'.$i]);
        }
        else{
            break;
        }
    }
    $fp = fopen($xls_file_path, 'a');
    fputcsv($fp, $fields);
    fclose($fp);
    
    if(get_option('td_backup_email')){
        wp_mail(sanitize_email(get_option('td_backup_email')) ,'ToyDrive Request', wp_json_encode($fields));
    }
    
    wp_send_json_success();
}

add_action( 'wp_ajax_toydrive_handle_form', 'toydrive_handle_form' );
add_action( 'wp_ajax_nopriv_toydrive_handle_form', 'toydrive_handle_form' );

function toydrive_save_admin(){
    if(!current_user_can('manage_options')){
        wp_send_json_error(null, 403);
    }
    
    update_option('td_form_title', isset($_POST['td_form_title']) ? sanitize_text_field($_POST['td_form_title']) : '');
    update_option('td_form_response', isset($_POST['td_form_response']) ? wp_kses($_POST['td_form_response'], 'post') : '');
    update_option('td_backup_email', isset($_POST['td_backup_email']) ? sanitize_email(sanitize_text_field($_POST['td_backup_email'])) : '');
    update_option('td_max_kids', isset($_POST['td_max_kids']) ? sanitize_text_field($_POST['td_max_kids']) : '0');
    update_option('td_closed_msg', isset($_POST['td_closed_msg']) ? sanitize_text_field($_POST['td_closed_msg']) : 'We are sorry but submissions are now closed');
    $forced_closed = isset($_POST['td_force_closed']) ? '1' : false;
    if($forced_closed){
        update_option('td_force_closed', '1');
    }
    else{
        delete_option('td_force_closed');
    }
    wp_send_json_success();
}
add_action( 'wp_ajax_toydrive_save_admin', 'toydrive_save_admin' );

function toydrive_unlink_datafile(){
    if(!current_user_can('manage_options')){
        wp_send_json_error(null, 403);
    }
    $xls_file_path = dirname(__DIR__).'/data/kids.xls';
    unlink($xls_file_path);
    wp_send_json_success();
}
add_action( 'wp_ajax_toydrive_unlink_datafile', 'toydrive_unlink_datafile' );

function toydrive_download_spreadsheet(){
    if(!current_user_can('manage_options')){
        wp_send_json_error(null, 403);
    }
    $xls_file_path = dirname(__DIR__).'/data/kids.xls';;
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='. basename($xls_file_path));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($xls_file_path));
    readfile($xls_file_path);
    wp_die();
}
add_action( 'wp_ajax_toydrive_download_spreadsheet', 'toydrive_download_spreadsheet' );