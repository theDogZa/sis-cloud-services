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
                @if(!isset($ApiConfig))
                {{ ucfirst(__('config.head_title.add')) }}
                @else
                {{ ucfirst(__('config.head_title.edit')) }}
                @endif
                <small> </small>
            </h3>
        </div>

        <div class="block-content">
            <!-- ** Content Data ** -->
            <form action="{{ url('/config'.( isset($ApiConfig) ? '/' . $ApiConfig->id : '')) }}" method="POST" class="needs-validation" enctype="application/x-www-form-urlencoded" id="form" novalidate>
                {{ csrf_field() }}
                @if(isset($ApiConfig))
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="row form-group">
                    @if($arrShowField['code']==true)
                    <div class="{{config('theme.layout.form')}}">
                        <label for="code">{{ucfirst(__('config.code.label'))}}
                            <span class="text-danger">*</span>
                            @if(__('config.code.popover.title') != "")
                            @include('components._popover_info', ['title' => ucfirst(__('config.code.popover.title')) ,'content'=> ucfirst(__('config.code.popover.content'))])
                            @endif
                        </label>
                        <input type="text" class="form-control" id="code" name="code" required  value="{{ @$code }}" placeholde="{{__('config.code.placeholder')}}">
                        @include('components._invalid_feedback',['required'=>'required','message'=>ucfirst(__('config.code.label')) ])
                    </div>
                    @endif
                     @if($arrShowField['type']==true)
                    <div class="{{config('theme.layout.form')}}">
                        <label for="type">{{ucfirst(__('config.type.label'))}}
                            <span class="text-danger">*</span>
                            @if(__('config.type.popover.title') != "")
                            @include('components._popover_info', ['title' => ucfirst(__('config.type.popover.title')) ,'content'=> ucfirst(__('config.type.popover.content'))])
                            @endif
                        </label>
                        <input type="text" class="form-control" id="type" name="type" required  value="{{ @$type }}" placeholde="{{__('config.type.placeholder')}}">
                        @include('components._invalid_feedback',['required'=>'required','message'=>ucfirst(__('config.type.label')) ])
                    </div>
                    @endif
                    @if($arrShowField['name']==true)
                    <div class="{{config('theme.layout.form')}}">
                        <label for="name">{{ucfirst(__('config.name.label'))}}
                            <span class="text-danger">*</span>
                            @if(__('config.name.popover.title') != "")
                            @include('components._popover_info', ['title' => ucfirst(__('config.name.popover.title')) ,'content'=> ucfirst(__('config.name.popover.content'))])
                            @endif
                        </label>
                        <input type="text" class="form-control" id="name" name="name" required  value="{{ @$name }}" placeholde="{{__('config.name.placeholder')}}">
                        @include('components._invalid_feedback',['required'=>'required','message'=>ucfirst(__('config.name.label')) ])
                    </div>
                    @endif
                    @if($arrShowField['des']==true)
                    <div class="{{config('theme.layout.form')}}">
                        <label for="des">{{ucfirst(__('config.des.label'))}}
                            @if(__('config.des.popover.title') != "")
                            @include('components._popover_info', ['title' => ucfirst(__('config.des.popover.title')) ,'content'=> ucfirst(__('config.des.popover.content'))])
                            @endif
                        </label>
                        <input type="text" class="form-control" id="des" name="des"   value="{{ @$des }}" placeholde="{{__('config.des.placeholder')}}">
                        @include('components._invalid_feedback',['required'=>'','message'=>ucfirst(__('config.des.label')) ])
                    </div>
                    @endif
                    @if($arrShowField['val']==true)
                    <div class="{{config('theme.layout.form')}}">
                        <label for="val">{{ucfirst(__('config.val.label'))}}
                            @if(@$isRequest) <span class="text-danger">*</span> @endif
                            @if(__('config.val.popover.title') != "")
                            @include('components._popover_info', ['title' => ucfirst(__('config.val.popover.title')) ,'content'=> ucfirst(__('config.val.popover.content'))])
                            @endif
                        </label>
                         <input type="text" class="form-control" id="val" name="val" @if(@$isRequest) required @endif value="{{ @$val }}" placeholde="{{__('config.val.placeholder')}}">
                        @include('components._invalid_feedback',['required'=>'','message'=>ucfirst(__('config.val.label')) ])
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
      window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();
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
 * File Create : 2020-09-22 15:47:51 *
 */
-->