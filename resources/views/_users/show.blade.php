@extends('layouts.backend')
@section('content')
<!-- Page Content -->
<div class="container-fluid">
    @include('components._breadcrumb',['isSearch'=> false])
    <!-- Content Heading -->
    <h2 class="content-heading pt-2">
        <i class="{{config('theme.icon.menu_users')}} mr-2"></i>{{ ucfirst(__('users.heading')) }}
        <div class="bock-sub-menu"></div>
    </h2>
    <!-- END Content Heading -->

    <!-- Content Main -->
    <div class="block {{config('theme.layout.main_block')}}">
        <div class="block-header {{config('theme.layout.main_block_header')}}">
            <h3 class="block-title">
                <i class="{{config('theme.icon.item_form')}} mr-2"></i>
                {{ ucfirst(__('users.head_title.view')) }}
                <small> </small>
            </h3>
        </div>

        <div class="block-content">
            <!-- ** Content Data ** -->
            <div class="row form-group">
                @if($arrShowField['username']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="username">{{ucfirst(__('users.username.label'))}}
                        @if(__('users.username.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('users.username.popover.title')) ,'content'=> ucfirst(__('users.username.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="username" name="username" disabled value="{{ @$user->username }}" placeholde="{{__('users.username.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['first_name']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="first_name">{{ucfirst(__('users.first_name.label'))}}
                        @if(__('users.first_name.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('users.first_name.popover.title')) ,'content'=> ucfirst(__('users.first_name.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="first_name" name="first_name" disabled value="{{ @$user->first_name }}" placeholde="{{__('users.first_name.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['last_name']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="last_name">{{ucfirst(__('users.last_name.label'))}}
                        @if(__('users.last_name.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('users.last_name.popover.title')) ,'content'=> ucfirst(__('users.last_name.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="last_name" name="last_name" disabled value="{{ @$user->last_name }}" placeholde="{{__('users.last_name.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['email']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="email">{{ucfirst(__('users.email.label'))}}
                        @if(__('users.email.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('users.email.popover.title')) ,'content'=> ucfirst(__('users.email.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="email" name="email" disabled value="{{ @$user->email }}" placeholde="{{__('users.email.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['email_verified_at']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="email_verified_at">{{ucfirst(__('users.email_verified_at.label'))}}
                        @if(__('users.email_verified_at.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('users.email_verified_at.popover.title')) ,'content'=> ucfirst(__('users.email_verified_at.popover.content'))])
                        @endif
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control input-time  js-flatpickr-enabled flatpickr-input" disabled id="email_verified_at" name="email_verified_at" value="{{@$user->email_verified_at}}">
                    </div>
                </div>
                @endif
                @if($arrShowField['password']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="password">{{ucfirst(__('users.password.label'))}}
                        @if(__('users.password.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('users.password.popover.title')) ,'content'=> ucfirst(__('users.password.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="password" name="password" disabled value="{{ @$user->password }}" placeholde="{{__('users.password.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['auth_code']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="auth_code">{{ucfirst(__('users.auth_code.label'))}}
                        @if(__('users.auth_code.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('users.auth_code.popover.title')) ,'content'=> ucfirst(__('users.auth_code.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="auth_code" name="auth_code" disabled value="{{ @$user->auth_code }}" placeholde="{{__('users.auth_code.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['active']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="active">{{ucfirst(__('users.active.label'))}}
                        @if(__('users.active.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('users.active.popover.title')) ,'content'=> ucfirst(__('users.active.popover.content'))])
                        @endif
                    </label>
                    <div>
                        <label class="css-control css-control-lg css-control-success css-radio">
                            <input type="radio" class="css-control-input" value="1" name="active" disabled {!! ( @$user->active=='1' ? 'checked' : '' ) !!}>
                            <span class="css-control-indicator"></span> {{ucfirst(__('users.active.text_radio.true'))}}
                        </label>
                        <label class="css-control css-control-lg css-control-danger css-radio">
                            <input type="radio" class="css-control-input" value="0" name="active" disabled {!! ( @$user->active!='1' ? 'checked' : '' ) !!}>
                            <span class="css-control-indicator"></span> {{ucfirst(__('users.active.text_radio.false'))}}
                        </label>
                    </div>
                </div>
                @endif
                @if($arrShowField['activated']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="activated">{{ucfirst(__('users.activated.label'))}}
                        @if(__('users.activated.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('users.activated.popover.title')) ,'content'=> ucfirst(__('users.activated.popover.content'))])
                        @endif
                    </label>
                    <div>
                        <label class="css-control css-control-lg css-control-success css-radio">
                            <input type="radio" class="css-control-input" value="1" name="activated" disabled {!! ( @$user->activated=='1' ? 'checked' : '' ) !!}>
                            <span class="css-control-indicator"></span> {{ucfirst(__('users.activated.text_radio.true'))}}
                        </label>
                        <label class="css-control css-control-lg css-control-danger  css-radio">
                            <input type="radio" class="css-control-input" value="0" name="activated" disabled {!! ( @$user->activated!='1' ? 'checked' : '' ) !!}>
                            <span class="css-control-indicator"></span> {{ucfirst(__('users.activated.text_radio.false'))}}
                        </label>
                    </div>
                </div>
                @endif
                @if($arrShowField['remember_token']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="remember_token">{{ucfirst(__('users.remember_token.label'))}}
                        @if(__('users.remember_token.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('users.remember_token.popover.title')) ,'content'=> ucfirst(__('users.remember_token.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="remember_token" name="remember_token" disabled value="{{ @$user->remember_token }}" placeholde="{{__('users.remember_token.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['last_login']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="last_login">{{ucfirst(__('users.last_login.label'))}}
                        @if(__('users.last_login.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('users.last_login.popover.title')) ,'content'=> ucfirst(__('users.last_login.popover.content'))])
                        @endif
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control input-time  js-flatpickr-enabled flatpickr-input" disabled id="last_login" name="last_login" value="{{@$user->last_login}}">
                    </div>
                </div>
                @endif
                @if($arrShowField['last_login']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="user_map">{{ucfirst(__('users.user_map.label'))}}
                        @if(__('users.user_map.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('users.user_map.popover.title')) ,'content'=> ucfirst(__('users.user_map.popover.content'))])
                        @endif
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control input-time  js-flatpickr-enabled flatpickr-input" disabled id="user_map" name="user_map" value="{{ @$user->UserMap->ApiUser->username }}">
                    </div>
                </div>
                @endif
                
            </div>
            <hr>
                <div class="row mb-3">
                    <div class="col">
                        @include('components._btn_back')
                    
                    </div>
                </div>
            <!-- END Content Data -->
        </div>
       
    </div>
    <!-- END Content Main -->
</div>
<!-- END Page Content -->
@endsection
@section('css_after')

@endsection
@section('js_after')

@endsection



<!--
/** 
 * CRUD Laravel
 * Master ฺBY Kepex  =>  https://github.com/kEpEx/laravel-crud-generator
 * Modify/Update BY PRASONG PUTICHANCHAI
 * 
 * Latest Update : 18/09/2020 10:51
 * Version : ver.1.00.00
 *
 * File Create : 2020-09-18 17:11:34 *
 */
-->