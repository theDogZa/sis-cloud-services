@extends('layouts.backend')
@section('content')
<!-- Page Content -->
<div class="container-fluid">
    @include('components._breadcrumb')
    <!-- Content Heading -->
    <h2 class="content-heading pt-2">
        <i class="{{config('theme.icon.menu_config')}} mr-2"></i>{{ ucfirst(__('config.heading')) }}
        <div class="bock-sub-menu"></div>
    </h2>
    <!-- END Content Heading -->

    <!-- Content Main -->
    <div class="block {{config('theme.layout.main_block')}}">
        <div class="block-header {{config('theme.layout.main_block_header')}}">
            <h3 class="block-title">
                <i class="{{config('theme.icon.item_form')}} mr-2"></i>
                {{ ucfirst(__('config.head_title.view')) }}
                <small> </small>
            </h3>
        </div>

        <div class="block-content">
            <!-- ** Content Data ** -->
            <div class="row form-group">
                @if($arrShowField['code']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="code">{{ucfirst(__('config.code.label'))}}
                        @if(__('config.code.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('config.code.popover.title')) ,'content'=> ucfirst(__('config.code.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="code" name="code" disabled value="{{ @$ApiConfig->code }}" placeholde="{{__('config.code.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['type']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="type">{{ucfirst(__('config.type.label'))}}
                        @if(__('config.type.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('config.type.popover.title')) ,'content'=> ucfirst(__('config.type.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="type" name="type" disabled value="{{ @$ApiConfig->type }}" placeholde="{{__('config.type.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['name']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="name">{{ucfirst(__('config.name.label'))}}
                        @if(__('config.name.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('config.name.popover.title')) ,'content'=> ucfirst(__('config.name.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="name" name="name" disabled value="{{ @$ApiConfig->name }}" placeholde="{{__('config.name.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['des']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="des">{{ucfirst(__('config.des.label'))}}
                        @if(__('config.des.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('config.des.popover.title')) ,'content'=> ucfirst(__('config.des.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="des" name="des" disabled value="{{ @$ApiConfig->des }}" placeholde="{{__('config.des.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['val']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="val">{{ucfirst(__('config.val.label'))}}
                        @if(__('config.val.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('config.val.popover.title')) ,'content'=> ucfirst(__('config.val.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="val" name="val" disabled value="{{ @$ApiConfig->val }}" placeholde="{{__('config.val.placeholder')}}">
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
 * File Create : 2020-09-22 15:47:51 *
 */
-->