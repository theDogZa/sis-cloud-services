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
                {{ ucfirst(__('api_users.head_title.view')) }}
                <small> </small>
            </h3>
        </div>

        <div class="block-content">
            <!-- ** Content Data ** -->
            <ul class="nav nav-tabs nav-tabs-block js-tabs-enabled" data-toggle="tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#btabs-static-detail">Detail</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#btabs-static-user">Users Map</a>
                    </li>
                </ul>
                <div class="block-content tab-content">
                    <div class="tab-pane active show " id="btabs-static-detail" role="tabpanel">

                        <div class="row form-group">
                            @if($arrShowField['username']==true)
                            <div class="{{config('theme.layout.view')}}">
                                <label for="username">{{ucfirst(__('api_users.username.label'))}}
                                    @if(__('api_users.username.popover.title') != "")
                                    @include('components._popover_info', ['title' => ucfirst(__('api_users.username.popover.title')) ,'content'=> ucfirst(__('api_users.username.popover.content'))])
                                    @endif
                                </label>
                                <input type="text" class="form-control" id="username" name="username" disabled value="{{ @$ApiUser->username }}" placeholde="{{__('api_users.username.placeholder')}}">
                            </div>
                            @endif
                            @if($arrShowField['description']==true)
                            <div class="{{config('theme.layout.view')}}">
                                <label for="description">{{ucfirst(__('api_users.description.label'))}}
                                    @if(__('api_users.description.popover.title') != "")
                                    @include('components._popover_info', ['title' => ucfirst(__('api_users.description.popover.title')) ,'content'=> ucfirst(__('api_users.description.popover.content'))])
                                    @endif
                                </label>
                                <input type="text" class="form-control" id="description" name="description" disabled value="{{ @$ApiUser->description }}" placeholde="{{__('api_users.description.placeholder')}}">
                            </div>
                            @endif
                            @if($arrShowField['password']==true)
                            
                            <div class="{{config('theme.layout.view')}}">
                                <label for="password">{{ucfirst(__('api_users.password.label'))}}
                                    @if(__('api_users.password.popover.title') != "")
                                    @include('components._popover_info', ['title' => ucfirst(__('api_users.password.popover.title')) ,'content'=> ucfirst(__('api_users.password.popover.content'))])
                                    @endif
                                </label>
                                <input type="password" class="form-control" id="password" name="password" disabled value="password" placeholde="{{__('api_users.password.placeholder')}}">
                            </div>
                            @endif
                            @if($arrShowField['active']==true)
                            <div class="{{config('theme.layout.view')}}">
                                <label for="active">{{ucfirst(__('api_users.active.label'))}}
                                    @if(__('api_users.active.popover.title') != "")
                                    @include('components._popover_info', ['title' => ucfirst(__('api_users.active.popover.title')) ,'content'=> ucfirst(__('api_users.active.popover.content'))])
                                    @endif
                                </label>
                                <div>
                                    <label class="css-control css-control-lg css-control-success css-radio">
                                        <input type="radio" class="css-control-input" value="1" name="active" disabled {!! ( @$ApiUser->active=='1' ? 'checked' : '' ) !!}>
                                        <span class="css-control-indicator"></span> {{ucfirst(__('api_users.active.text_radio.true'))}}
                                    </label>
                                    <label class="css-control css-control-lg css-control-danger css-radio">
                                        <input type="radio" class="css-control-input" value="0" name="active" disabled {!! ( @$ApiUser->active!='1' ? 'checked' : '' ) !!}>
                                        <span class="css-control-indicator"></span> {{ucfirst(__('api_users.active.text_radio.false'))}}
                                    </label>
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>
                    <div class="tab-pane" id="btabs-static-user" role="tabpanel">
                        <div class="row m-1 mt-4 pb-3 form-group border rounded" style="margin-bottom: 20px !important;">  
                            <div class="col-12 p-3">
                                <h4 style="margin-top:-30px;">
                                    <span class="pl-4 pr-4 bg-white">{{ ucfirst(__('users_roles.head_title.sub')) }}</span>
                                </h4>
                            </div> 
                            @foreach ($UsersMap as $key => $item)
                            <div class="col-3 mb-4 pl-4 pb-4">
                                <span class="pl-2 position-absolute" style="margin-top: -10px">
                                    <b>{{$item->username}}</b><br>
                                    <small>{{$item->first_name}}&nbsp;{{$item->last_name}}</small>
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
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
 * File Create : 2021-03-08 09:54:49 *
 */
-->