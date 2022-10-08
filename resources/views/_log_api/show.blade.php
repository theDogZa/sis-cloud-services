@extends('layouts.backend')
@section('content')
<!-- Page Content -->
<div class="container-fluid">
    @include('components._breadcrumb',['isSearch'=> false])
    <!-- Content Heading -->
    <h2 class="content-heading pt-2">
        <i class="{{config('theme.icon.menu_log_api')}} mr-2"></i>{{ ucfirst(__('log_api.heading')) }}
        <div class="bock-sub-menu"></div>
    </h2>
    <!-- END Content Heading -->

    <!-- Content Main -->
    <div class="block {{config('theme.layout.main_block')}}">
        <div class="block-header {{config('theme.layout.main_block_header')}}">
            <h3 class="block-title">
                <i class="{{config('theme.icon.item_form')}} mr-2"></i>
                {{ ucfirst(__('log_api.head_title.view')) }}
                <small> </small>
            </h3>
        </div>

        <div class="block-content">
            <!-- ** Content Data ** -->
            <div class="row form-group">
                @if($arrShowField['org_code']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="org_code">{{ucfirst(__('log_api.org_code.label'))}}
                        @if(__('log_api.org_code.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('log_api.org_code.popover.title')) ,'content'=> ucfirst(__('log_api.org_code.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="org_code" name="org_code" disabled value="{{ @$logApi->org_code }}" placeholde="{{__('log_api.org_code.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['created_at']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="created_at">{{ucfirst(__('log_api.created_at.label'))}}
                        @if(__('log_api.created_at.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('log_api.created_at.popover.title')) ,'content'=> ucfirst(__('log_api.created_at.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="created_at" name="created_at" disabled value="{{ @$logApi->created_at }}" placeholde="{{__('log_api.created_at.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['isSuccess']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="isSuccess">{{ucfirst(__('log_api.isSuccess.label'))}}
                        @if(__('log_api.isSuccess.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('log_api.isSuccess.popover.title')) ,'content'=> ucfirst(__('log_api.isSuccess.popover.content'))])
                        @endif
                    </label>
                    <div class="pt-2">
                    @include('components._badge_radio',['val'=>@$logApi->isSuccess,'tTrue'=>ucfirst(__('log_api.isSuccess.text_radio.true')), 'tFalse'=>ucfirst(__('log_api.isSuccess.text_radio.false'))])
                    </div>
                </div>
                @endif
                @if($arrShowField['created_uid']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="description">{{ucfirst(__('log_api.created_uid.label'))}}
                        @if(__('log_api.created_uid.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('log_api.created_uid.popover.title')) ,'content'=> ucfirst(__('log_api.created_uid.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="created_uid" name="created_uid" disabled value="{{ @$logApi->user->username }}" placeholde="{{__('log_api.created_uid.placeholder')}}">
                </div>
                @endif
                @if($arrShowField['edge_user']==true)
                <div class="{{config('theme.layout.view')}}">
                    <label for="description">{{ucfirst(__('log_api.edge_user.label'))}}
                        @if(__('log_api.edge_user.popover.title') != "")
                        @include('components._popover_info', ['title' => ucfirst(__('log_api.edge_user.popover.title')) ,'content'=> ucfirst(__('log_api.edge_user.popover.content'))])
                        @endif
                    </label>
                    <input type="text" class="form-control" id="edge_user" name="edge_user" disabled value="{{ @$logApi->edge_user }}" placeholde="{{__('log_api.edge_user.placeholder')}}">
                </div>
                @endif
               
              </div>
               <div class="p-2 m-2"> 
                <hr>
                <span class="ml-3 pl-1 pr-1 position-absolute bg-white" style="margin-top: -30px"><h4>{{ucfirst(__('log_api.head_title.view_sub'))}}</h4></span>
              </div>
                @if(@isset($logApi->log))
                <div class="row">
                    <div class="col p-0" id="accordion2" role="tablist" aria-multiselectable="true">  
                        @foreach ($logApi->log as $d)
                        @php 
                        $item = json_decode(($d));
                        if(@$item->status == 404) $item->code = 404;
                        if(@$item->code != 200) $item->code = @$item->status;            
                        @endphp 
                            <div class="alert {{ ( ($item->code==200) ? 'alert-success' : 'alert-danger') }} row mr-4 ml-4 mt-2 mb-0 p-0" style="min-height: 0px;" id="loop_{{$loop->iteration}}">          
                                <div class="col-auto pl-4 pt-3 pb-2" role="tab" id="accordion2_h{{$loop->iteration}}">
                                    <h3 class="alert-heading font-size-h4 font-w400 mb-2">
                                        <span class="font-w600 collapsed" data-toggle="collapse" data-parent="#accordion2" href="#accordion2_q{{$loop->iteration}}" aria-expanded="false" aria-controls="accordion2_q{{$loop->iteration}}">
                                        {{$item->apiName}}
                                        </span> 
                                    </h3>
                                    <div class="row-message">
                                        <button class="btn btn-sm  {{ ( ($item->code==200) ? 'btn-success' : 'btn-danger') }} mr-2" data-toggle="collapse" data-parent="#accordion2" href="#accordion2_q{{$loop->iteration}}" aria-expanded="true" aria-controls="accordion2_q{{$loop->iteration}}">
                                            <i class="fa fa-search-plus"></i>
                                        </button>
                                        <span class="alert-message">{{ ( ($item->code==200) ? 'success' : @$item->message) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="accordion2_q{{$loop->iteration}}" class="collapse block block-bordered mr-4 ml-4 mb-2 block-main-raw border-top-0" role="tabpanel" aria-labelledby="accordion2_h{{$loop->iteration}}">
                                <div class="block-content"><pre><code class="raw-block" id="raw-block_loop_{{$loop->iteration}}">@if(is_string(@$item->raw)){{ @$item->raw }}@endif</code></pre></div>
                            </div>  
                        @endforeach
                    </div>
                </div>
                @endif
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
<style>
pre {
color: #e83e8c;
}
pre code {
  font-size: 16px !important;
}
</style>

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