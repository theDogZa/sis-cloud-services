<?php

return [

    /*
    |--------------------------------------------------------------------------
    | api_users Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the api_users library to build
    | the simple api_users links. You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */

/* breadcrumd */
    'controllername' => 'EDGE Users ',
    'index' => ' Lists ',
    'create' => ' Add ',
    'edit' => ' Edit ',
    'show' => ' View ',
    'listUser' => 'Users Map',

    'heading' => 'EDGE users',
    'head_title' => [
        'list' => 'List EDGE users',
        'add' => 'Add EDGE user',
        'edit' => 'Edit EDGE user',
        'view' => 'view EDGE user',
        'search' => 'search',
    ],
    'username' => [
        'th' => 'Username',
        'label' => 'Username',
        'placeholder' => 'Username',
        'popover' => [
            'title' => 'Policy ',
            'content' => '- Should contain at least 5 characters.<br>
                        - Except ! admin, root, administrator<br>
                        - Should contain at lower case only'
        ],
    ],
    'description' => [
        'th' => 'Description',
        'label' => 'Description',
        'placeholder' => 'description',
        'popover' => [
            'title' => '',
            'content' => ''
        ],
    ],
    'current_password' => [
        'label' => 'current password',
        'placeholder' => '*******',
        'popover' => [
            'title' => '',
            'content' => ''
        ],
    ],
    'password' => [
        'th' => 'Password',
        'label' => 'Password',
        'placeholder' => 'Password',
        'popover' => [
            'title' => 'Policy',
            'content' => '- Should contain at least 8 characters.<br>
                          - Should contain at least one numerical character.<br>
                          - Must not Match user name.<br>
                          - Should contain at least one lower case and one upper case character.<br>
                          - Should contain at least one special characters such as !, @, #, $, %, &, ^ and *'
        ],
    ],
    'new_password' => [
        'th' => 'New password',
        'label' => 'New password',
        'placeholder' => 'New password',
        'popover' => [
            'title' => 'Policy',
            'content' => '- Should contain at least 8 characters.<br>
                          - Should contain at least one numeric character.<br>
                          - Must not Match user name.<br>
                          - Should contain at least one lower case and one upper case character.<br>
                          - Should contain at least one special characters such as !, @, #, $, %, &, ^ and *'
        ],
    ],
    'password_confirmation' => [
        'th' => 'Confirmation password',
        'label' => 'Confirmation password',
        'placeholder' => 'Confirmation password',
        'popover' => [
            'title' => '',
            'content' => ''
        ],
    ],
    'active' => [
        'th' => 'Active',
        'label' => 'Active',
        'placeholder' => 'Active',
        'popover' => [
            'title' => '',
            'content' => ''
        ],
        'text_radio' => [
            'all' => 'All',
            'true' => 'Active',
            'false' => 'Disabled'
        ],
    ],
    'created_at' => [
        'th' => 'Createdat',
        'label' => 'Createdat',
        'placeholder' => 'Createdat',
        'popover' => [
            'title' => '',
            'content' => ''
        ],
    ],
    'updated_at' => [
        'th' => 'Updatedat',
        'label' => 'Updatedat',
        'placeholder' => 'Updatedat',
        'popover' => [
            'title' => '',
            'content' => ''
        ],
    ],

    'label.change_password'=> 'Change Password',

    'message_password_not_match_confirmation' => 'The password confirmation does not match. !',

    'message_username_inuse' => ' has already been taken. !',
    
    'message_username_min_characters' => 'The username must be at least 5 characters. !',
    'message_username_policy' => 'The username does not meet the password policy',
    'message_username_policy_except' => 'The username must not use admin, root, administrator',


    'message_password_min_characters' => 'The password must be at least 8 characters. !',
    'message_password_policy' => 'The password does not meet the password policy',

    'message_password_policy_numeric_character' => 'The password Should contain at least one numeric character.',
    'message_password_policy_lower_upper_case' => 'The password Should contain at least one lower case and one upper case character.',
    'message_password_policy_special_character' => 'The password Should contain at least one special characters.',
    'message_password_policy_match_username' => 'The password Must not Match user name.',


    'message_new_password_min_characters' => 'The New password must be at least 8 characters. !',
    'message_new_password_policy' => 'The New password does not meet the password policy',
   
    'message_password_error' => 'The Password is incorrect !',
    
    'message_confirm_create' => [
        'title' =>  'create user. ',
        'message' =>  'Please confirm create user. !',
    ],
    'message_confirm_update' => [
        'title' =>  'edit user. ',
        'message' =>  'Please confirm edit user. !',
    ],
    

];



/** 
 * CRUD Laravel
 * Master ฺBY Kepex  =>  https://github.com/kEpEx/laravel-crud-generator
 * Modify/Update BY PRASONG PUTICHANCHAI
 * 
 * Latest Update : 06/04/2018 13:51
 * Version : v.10000
 *
 * File Create : 2021-03-08 09:54:49 *
 */