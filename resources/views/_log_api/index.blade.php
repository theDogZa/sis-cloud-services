@extends('layouts.backend')
@section('content')
<!-- Page Content -->
<div class="container">
    @include('components._breadcrumb',['isSearch'=> true])
    <!-- Content Heading -->
    <h2 class="content-heading pt-2">
        <i class="{{config('theme.icon.menu_log_api')}} mr-2"></i>{{ ucfirst(__('log_api.heading')) }}
        <div class="bock-sub-menu"> </div>
        @if(View::exists('_log_api._advanced_search'))
        @include('_log_api._advanced_search')
        @endif
    </h2>
    <!-- END Content Heading -->

    <!-- Content Main -->
    <div class="block {{config('theme.layout.main_block')}}">
        <div class="block-header {{config('theme.layout.main_block_header')}}">
            <h3 class="block-title">
                <i class="{{config('theme.icon.item_list')}} mr-2"></i>
                {{ ucfirst(__('log_api.head_title.list')) }}
                <small> ( {{$collection->total() }} {{ ucfirst(__('core.data_total_records')) }} ) </small>
            </h3>
        </div>

        <div class="block-content">
            <!-- ** Content Data ** -->
            <table class="table {{config('theme.layout.table_list_item')}}">
                <thead class="{{config('theme.layout.table_list_item_head')}}">
                    <tr>
                        <th>{{ucfirst(__('core.th_iteration_number'))}}</th>
                        @if($arrShowField['org_code']==true)
                        <th>
                            @sortablelink('org_code',__('log_api.org_code.th'))
                        </th>
                        @endif
                        @if($arrShowField['isSuccess']==true)
                        <th class="text-center">
                            @sortablelink('isSuccess',__('log_api.isSuccess.th'))
                        </th>
                        @endif
                        @if($arrShowField['created_at']==true)
                        <th class="text-center">
                            @sortablelink('updated_at',__('log_api.created_at.th'))
                        </th>
                        @endif
                        @if($arrShowField['created_uid']==true)
                        <th class="text-center">
                            @sortablelink('created_uid',__('log_api.created_uid.th'))
                        </th>
                        @endif
                        @if($arrShowField['edge_user']==true)
                        <th class="text-center">
                            @sortablelink('created_uid',__('log_api.edge_user.th'))
                        </th>
                        @endif
                        <th width="10px;" class="text-center">{{ucfirst(__('core.th_actions'))}}</th>
                    </tr>
                </thead>
                <tbody>
                    @if($collection->total())
                    @foreach ($collection as $item)
                    <tr id="row_{{$item->id}}">
                        <td>{!! $loop->iteration+(($collection->currentPage()-1)*$collection->perPage()) !!}</td>
                        @if(!empty( $arrShowField['org_code'] ))
                        <td>
                            {!! @$item->org_code !!}
                        </td>
                        @endif                
                        @if(!empty( $arrShowField['isSuccess'] ))
                        <td class="text-center">
                            @include('components._badge_radio',['val'=>@$item->isSuccess,'tTrue'=>ucfirst(__('log_api.isSuccess.text_radio.true')), 'tFalse'=>ucfirst(__('log_api.isSuccess.text_radio.false'))])
                        </td>
                        @endif
                        @if(!empty( $arrShowField['created_at'] ))
                        <td  class="text-center">
                            {!! @$item->updated_at !!}
                        </td>
                        @endif
                        @if(!empty( $arrShowField['created_uid'] ))
                        <td  class="text-center">
                            {!! @$item->user->username !!}
                        </td>
                        @endif
                        @if(!empty( $arrShowField['edge_user'] ))
                        <td  class="text-center">
                            {!! @$item->edge_user !!}
                        </td>
                        @endif
                        <td class="text-center">
                            <div class="btn-group">
                                @permissions('read.log_api')
                                @include('components.button._view')
                                @endpermissions
                                @permissions('del.log_api')
                                @include('components.button._del')
                                @endpermissions
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="7" class="text-center">{{ ucfirst(__('core.no_records')) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <!-- END Content Data -->
            @include('components._pagination', ['data' => $collection])
        </div>
    </div>
    <!-- END Content Main -->
</div>
<!-- END Page Content -->
@include('components._form_del',['action'=> 'log_api'])
@include('components._notify_message')
@endsection
@section('css_after')
<link rel="stylesheet" id="css-flatpickr" href="{{ asset('/js/plugins/flatpickr/flatpickr.min.css') }}">
@endsection
@section('js_after')
<script src="{{ asset('/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
<script>
    $(function($) {
        $(".input-datetime").flatpickr({
            allowInput: true,
            enableTime: true,
            time_24hr: true,
            altInputClass: 'flatpickr-alt',
            // minTime: "16:00",
            // maxTime: "22:30",
            onReady: function(dateObj, dateStr, instance) {
                $('.flatpickr-calendar').each(function() {
                    var $this = $(this);
                    if ($this.find('.flatpickr-ok').length < 1) {
                        $this.append('<div class="flatpickr-ok btn btn-sm btn-rounded btn-success min-width-125 mb-10">OK</div>');
                        $this.find('.flatpickr-ok').on('click', function() {
                            instance.close();
                        }); 
                    }

                    if ($this.find('.flatpickr-clear').length < 1) {
                        $this.append('<div class="flatpickr-clear btn btn-sm btn-rounded btn-warning min-width-125 mb-10">Clear</div>');
                        $this.find('.flatpickr-clear').on('click', function() {
                            instance.clear();
                            instance.close();
                        });
                    }
                });
            }
        });
        $('.input-datetime').keypress(function() {
            return false;
        });
    });
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