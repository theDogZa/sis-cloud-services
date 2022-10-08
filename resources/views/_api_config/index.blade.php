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
                <i class="{{config('theme.icon.item_list')}} mr-2"></i>
                {{ ucfirst(__('config.head_title.list')) }}
                <small> ( {{$collection->total() }} {{ ucfirst(__('core.data_total_records')) }} ) </small>
            </h3>
        </div>

        <div class="block-content">
            <!-- ** Content Data ** -->
            <table class="table {{config('theme.layout.table_list_item')}}">
                <thead class="{{config('theme.layout.table_list_item_head')}}">
                    <tr>
                        <th>{{ucfirst(__('core.th_iteration_number'))}}</th>
                        @if($arrShowField['code']==true)
                        <th>
                            @sortablelink('code',__('config.code.th'))
                        </th>
                        @endif
                        @if($arrShowField['type']==true)
                        <th>
                            @sortablelink('type',__('config.type.th'))
                        </th>
                        @endif
                        @if($arrShowField['name']==true)
                        <th>
                            @sortablelink('name',__('config.name.th'))
                        </th>
                        @endif
                        @if($arrShowField['des']==true)
                        <th>
                            @sortablelink('des',__('config.des.th'))
                        </th>
                        @endif
                        @if($arrShowField['val']==true)
                        <th>
                            @sortablelink('val',__('config.val.th'))
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
                        @if(!empty( $arrShowField['code'] ))
                        <td>
                            {!! @$item->slug !!}
                        </td>
                        @endif
                        @if(!empty( $arrShowField['type'] ))
                        <td>
                            {!! @$item->type !!}
                        </td>
                        @endif
                        @if(!empty( $arrShowField['name'] ))
                        <td>
                            {!! @$item->name !!}
                        </td>
                        @endif
                        @if(!empty( $arrShowField['des'] ))
                        <td>
                            {!! @$item->des !!}
                        </td>
                         @endif
                         @if(!empty( $arrShowField['val'] ))
                        <td>
                            {!! @$item->val !!}
                        </td>
                         @endif
                        <td class="text-center">
                            <div class="btn-group">
                                @permissions('read.config')
                                @include('components.button._view')
                                @endpermissions
                                @permissions('update.config')
                                @include('components.button._edit')
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
@include('components._notify_message')
@endsection

@section('css_after')

@endsection
@section('js_after')

<script>
    $(function($) {
        
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