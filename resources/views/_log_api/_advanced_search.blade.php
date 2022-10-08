<div class="overlay"></div>
<div class="col position-relative" style="z-index: 101">
    <div class="position-absolute block block-rounded block-search mr-2" id="block-search" style="display: none; margin-top: -67px !important;">
        <form action="{{url()->current()}}" method="get" class="form-search" enctype="application/x-www-form-urlencoded">
            <div class="block-header {{config('theme.layout.main_block_search_header')}}">
                <h3 class="block-title">
                    <i class="{{config('theme.icon.advanced_search')}}"></i> {{ ucfirst(__('roles.head_title.search')) }}
                </h3>
                <div class="block-options"></div>
            </div>
            <div class="block-content">
                <div class="row">
                    @if($arrShowField['org_code']==true)
                    <div class="form-group {{config('theme.layout.search')}}">
                        <label for="org_code">{{ucfirst(__('log_api.org_code.label'))}}</label>
                        <input type="text" class="form-control" id="org_code" name="org_code" value="{{@$search->org_code}}">
                    </div>
                    @endif
                     @if($arrShowField['created_uid']==true)
                    <div class="form-group {{config('theme.layout.search')}}">
                        <label for="created_uid">{{ucfirst(__('log_api.created_uid.label'))}}</label>
                        <input type="text" class="form-control" id="created_uid" name="created_uid" value="{{@$search->created_uid}}">
                    </div>
                    @endif
                    @if($arrShowField['created_at']==true)
                    <div class="form-group {{config('theme.layout.search')}}">
                        <label for="created_at">{{ucfirst(__('log_api.created_at.label'))}}</label>
                        <div class="input-daterange input-group js-datepicker-enabled" data-date-format="dd-mm-yyyy" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                            <input type="text" class="form-control input-datetime bg-white js-flatpickr-enabled flatpickr-input" id="created_at_start" name="created_at_start" value="{{@$search->created_at_start}}" placeholder="From" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                            <div class="input-group-prepend input-group-append">
                                <span class="input-group-text font-w600">to</span>
                            </div>
                            <input type="text" class="form-control input-datetime bg-white js-flatpickr-enabled flatpickr-input" id="created_at_end" name="created_at_end" value="{{@$search->created_at_end}}" placeholder="To" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                        </div>
                    </div>
                    @endif
                    @if($arrShowField['isSuccess']==true)
                    <div class="form-group {{config('theme.layout.search')}}">
                        <label for="isSuccess">{{ucfirst(__('log_api.isSuccess.label'))}}</label>
                        <div>
                            <label class="css-control css-control-lg css-control-primary css-radio">
                                <input type="radio" class="css-control-input chk_radio_all" id="chk_radio_all" value="" name="isSuccess" {!! ( @$search->isSuccess != 'Y' && @$search->isSuccess!='N' ? 'checked' : '') !!} >
                                <span class="css-control-indicator"></span> {{ucfirst(__('log_api.isSuccess.text_radio.all'))}}
                            </label>
                            <label class="css-control css-control-lg css-control-success css-radio">
                                <input type="radio" class="css-control-input" value="1" name="isSuccess" {!! ( @$search->isSuccess=='1' ? 'checked' : '') !!}>
                                <span class="css-control-indicator"></span> {{ucfirst(__('log_api.isSuccess.text_radio.true'))}}
                            </label>
                            <label class="css-control css-control-lg css-control-danger css-radio">
                                <input type="radio" class="css-control-input" value="0" name="isSuccess" {!! ( @$search->isSuccess=='0' ? 'checked' : '') !!} >
                                <span class="css-control-indicator"></span> {{ucfirst(__('log_api.isSuccess.text_radio.false'))}}
                            </label>
                        </div>
                    </div>
                    @endif
                </div>
                <hr>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            @include('components.button._submin_search')
                            @include('components.button._reset',['class'=>'btn-sm btn-reset-search'])
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>