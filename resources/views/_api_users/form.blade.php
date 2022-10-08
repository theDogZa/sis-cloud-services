@extends('layouts.backend')
@section('content')
<!-- Page Content -->
<div class="container-fluid">
    @include('components._breadcrumb',['isSearch'=> false])
    <!-- Content Heading -->
    <h2 class="content-heading pt-2">
        <i class="{{config('theme.icon.menu_api_users')}} mr-2"></i>{{ ucfirst(__('api_users.heading')) }}
        <div class="bock-sub-menu"></div>
    </h2>
    <!-- END Content Heading -->

    <!-- Content Main -->
    <div class="block {{config('theme.layout.main_block')}}">
        <div class="block-header {{config('theme.layout.main_block_header')}}">
            <h3 class="block-title">
                <i class="{{config('theme.icon.item_form')}} mr-2"></i>
                @if(!isset($api_user))
                {{ ucfirst(__('api_users.head_title.add')) }}
                @else
                {{ ucfirst(__('api_users.head_title.edit')) }}
                @endif
                <small> </small>
            </h3>
        </div>

        <div class="block-content">
            <!-- ** Content Data ** -->
            @php
               // dd($ApiUser->id);
            @endphp
            <form action="{{ url('/api_users'.( isset($ApiUser->id) ? '/' . $ApiUser->id : '')) }}" method="POST" class="needs-validation" enctype="application/x-www-form-urlencoded" id="formUserApi" novalidate>
                {{ csrf_field() }}
                @if(isset($ApiUser))
                <input type="hidden" name="_method" value="PUT">
              
                @endif
                
                <div class="row form-group">
                    @if($arrShowField['username']==true)
                    <div class="{{config('theme.layout.form')}}">
                        <label for="username">{{ucfirst(__('api_users.username.label'))}}
                            <span class="text-danger">*</span>
                            @if(__('api_users.username.popover.title') != "")
                            @include('components._popover_info', ['title' => ucfirst(__('api_users.username.popover.title')) ,'content'=> ucfirst(__('api_users.username.popover.content'))])
                            @endif
                        </label>
                        <input type="text" class="form-control" id="username" name="username" required  value="{{ @$username }}" placeholde="{{__('api_users.username.placeholder')}}">
                        <input type="hidden" id="old_username" value="{{ @$username }}" >
                        @include('components._invalid_feedback',['required'=>'required','message'=>ucfirst(__('api_users.username.label')) ])
                    </div>
                    @endif
                   @if($arrShowField['description']==true)
                    <div class="{{config('theme.layout.form')}}">
                        <label for="description">{{ucfirst(__('api_users.description.label'))}}
                            @if(__('api_users.description.popover.title') != "")
                            @include('components._popover_info', ['title' => ucfirst(__('api_users.description.popover.title')) ,'content'=> ucfirst(__('api_users.description.popover.content'))])
                            @endif
                        </label>
                        <input type="text" class="form-control" id="description" name="description" value="{{ @$ApiUser->description }}" placeholde="{{__('api_users.description.placeholder')}}">
                    </div>
                    @endif

                    @if($arrShowField['password']==true)
                   
                    <div class="{{config('theme.layout.form')}}">
                        <label for="password">{{ucfirst(__('api_users.password.label'))}}
                            <span class="text-danger">*</span>
                            @if(__('api_users.password.popover.title') != "")
                            @include('components._popover_info', ['title' => ucfirst(__('api_users.password.popover.title')) ,'content'=> ucfirst(__('api_users.password.popover.content'))])
                            @endif
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required autocomplete="false"  value="" placeholde="{{__('api_users.password.placeholder')}}">
                        @include('components._invalid_feedback',['required'=>'required','message'=>ucfirst(__('api_users.password.label')) ])
                    </div>

                    <div class="{{config('theme.layout.form')}}">
                        <label for="password_confirmation">{{ucfirst(__('api_users.password_confirmation.label'))}}
                            <span class="text-danger">*</span>
                            @if(__('api_users.password_confirmation.popover.title') != "")
                            @include('components._popover_info', ['title' => ucfirst(__('api_users.password_confirmation.popover.title')) ,'content'=> ucfirst(__('users.password_confirmation.popover.content'))])
                            @endif
                        </label>
                        <input type="password" class="form-control" id="password_confirmation" required autocomplete="false" value="" placeholde="{{__('users.password_confirmation.placeholder')}}">
                        @include('components._invalid_feedback',['required'=>'required','message'=>ucfirst(__('api_users.password_confirmation.label')) ])
                    </div>
                    
                    @endif

                    @if($arrShowField['active']==true)
                    <div class="{{config('theme.layout.form')}}">
                        <label for="active">{{ucfirst(__('api_users.active.label'))}}
                            <span class="text-danger">*</span>
                            @if(__('api_users.active.popover.title') != "")
                            @include('components._popover_info', ['title' => ucfirst(__('api_users.active.popover.title')) ,'content'=> ucfirst(__('api_users.active.popover.content'))])
                            @endif
                        </label>
                        <div>
                            <label class="css-control css-control-lg css-control-success css-radio">
                                <input type="radio" class="css-control-input" value="1" name="active" {!! ( @$active=='1' ? 'checked' : '' ) !!}>
                                <span class="css-control-indicator"></span> {{ucfirst(__('api_users.active.text_radio.true'))}}
                            </label>
                            <label class="css-control css-control-lg css-control-danger css-radio">
                                <input type="radio" class="css-control-input" value="0" name="active" {!! ( @$active!='1' ? 'checked' : '' ) !!}>
                                <span class="css-control-indicator"></span> {{ucfirst(__('api_users.active.text_radio.false'))}}
                            </label>
                        </div>
                    </div>
                    @endif
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col">
                        @include('components._btn_back')
                        @include('components._btn_submit_form')
                        @include('components._btn_reset_form')
                    </div>
                </div>
            </form>
            <!-- END Content Data -->
        </div>
    </div>
    <!-- END Content Main -->
</div>
<!-- END Page Content -->
@endsection
@section('css_after')
<link rel="stylesheet" id="css-flatpickr" href="{{ asset('/js/plugins/flatpickr/flatpickr.min.css') }}">
@endsection
@section('js_after')
<script src="{{ asset('/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
<script>
    (function() {
      'use strict';
      window.addEventListener('load',async function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms,async function(form) {
          form.addEventListener('submit',async function(event) {
            if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            }else{
                event.preventDefault();
                event.stopPropagation();
                var message;
                var ChkUser;
                $('#username').val($('#username').val().toLowerCase())
                var username = $('#username').val(); 
                var oldUsername = $('#old_username').val(); 
                var password = $('#password').val();
                
                console.log(username);
                if(oldUsername == null){
                    ChkUser = await checkPolicyUsername(username)
                }else if(oldUsername != username){
                    ChkUser = await checkPolicyUsername(username)
                    if(!ChkUser){
                        var res = await checkIsUseUsername(username);
                        if(res != true){
                             noitMessage('Error','error',"{{ucfirst(__('api_users.message_username_inuse'))}}");
                            return;
                        }                
                    }
                }else{
                    ChkUser = 0;
                }

                if(ChkUser){

                    noitMessage('Error','error',ChkUser);
                    return;
                }

                if(password){
                    var ChkPass = await checkPolicyPassword(password,username);   
                    if(ChkPass){
                        noitMessage('Error','error',ChkPass);
                        return;
                    }
                    var password_confirmation = $('#password_confirmation').val();
                        if(password !== password_confirmation){
                            noitMessage('Error','error',"{{ucfirst(__('api_users.message_password_not_match_confirmation'))}}");  
                            return;
                        }
                }
                var title = "{{ucfirst(__('api_users.message_confirm_create.title'))}}"
                var message = "{{ucfirst(__('api_users.message_confirm_create.message'))}}"

                 var confirm = await confirmMessage(title,message,'question');
              
                if(confirm == true){ 
                    $('#formUserApi').submit();
                    return;
                }            
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();

    async function checkIsUseUsername(username){
        var url = '{{ route('chk.usernameapi') }}'
        res = $.post(url, {
                'username': username
            })
            .then(function (response) {
                var decodedResponse = atob(response);
                var obj = JSON.parse(decodedResponse);
                if (obj.code === 200) {
                    return obj.message;
                } else {
                    return false;
                }
            })
            .catch(function (err) {
                return false;
            });
        return await res;
    }

    function checkPolicyUsername(username){ 
        if (username.length < 5){
            var message = "{{ucfirst(__('api_users.message_username_min_characters'))}}"
        }else{
           if(username.toLowerCase() == 'admin' || username.toLowerCase() == 'root' || username.toLowerCase() == 'administrator') {
                var message = "{{ucfirst(__('api_users.message_username_policy_except'))}}"
           }
        }

        return message;
    }

    function checkPolicyPassword(password,username=""){

        // Should contain at least 8 characters
        if (password.length < 8){
            var message = "{{ucfirst(__('api_users.message_password_min_characters'))}}"
        }else{
            // If password contains both lower and uppercase characters, increase strength value.
            if (!password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)){
                var message = "{{ucfirst(__('api_users.message_password_policy_lower_upper_case'))}}"
              //  console.log('lower and uppercase')
            }

            // If it has numbers and characters, increase strength value.  
            if (!password.match(/([a-zA-Z])/) || !password.match(/([0-9])/)){
                var message = "{{ucfirst(__('api_users.message_password_policy_numeric_character'))}}"
              //  console.log('numbers and characters')
            }

            // If it has one special characters, increase strength value.  
            if (!password.match(/(.*[!,%,&,@,#,$,^,*])/)){
    
                var message = "{{ucfirst(__('api_users.message_password_policy_special_character'))}}"
              //  console.log('one special characters')
            }

            // Must not Match user name  
            if (password.toLowerCase() == username.toLowerCase()){
                var message = "{{ucfirst(__('api_users.message_password_policy_match_username'))}}"
            }

        }
        return message;
    }
</script>
@endsection



<!--
/** 
 * CRUD Laravel
 * Master ฺBY Kepex  =>  https://github.com/kEpEx/laravel-crud-generator
 * Modify/Update BY PRASONG PUTICHANCHAI
 * 
 * Latest Update : 06/04/2018 13:51
 * Version : ver.1.00.00
 *
 * File Create : 2021-03-08 09:54:49 *
 */
-->