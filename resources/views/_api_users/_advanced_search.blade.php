<div class="overlay"></div>
<div class="col position-relative" style="z-index: 101">
    <div class="position-absolute block block-rounded block-search mr-2" id="block-search" style="display: none; margin-top: -67px !important;">
        <form action="{{url()->current()}}" method="get" class="form-search" enctype="application/x-www-form-urlencoded">
            <div class="block-header {{config('theme.layout.main_block_search_header')}}">
                <h3 class="block-title">
                    <i class="{{config('theme.icon.advanced_search')}}"></i> {{ ucfirst(__('api_users.head_title.search')) }}
                </h3>
                <div class="block-options"></div>
            </div>
            <div class="block-content">
                <div class="row">
                    @if($arrShowField['username']==true)
                    <div class="form-group {{config('theme.layout.search')}}">
                        <label for="username">{{ucfirst(__('api_users.username.label'))}}</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{@$search->username}}">
                    </div>
                    @endif
                    @if($arrShowField['description']==true)
                    <div class="form-group {{config('theme.layout.search')}}">
                        <label for="description">{{ucfirst(__('api_users.description.label'))}}</label>
                        <input type="text" class="form-control" id="description" name="description" value="{{@$search->description}}">
                    </div>
                    @endif
                    @if($arrShowField['active']==true)
                    <div class="form-group {{config('theme.layout.search')}}">
                        <label for="active">{{ucfirst(__('api_users.active.label'))}}</label>
                        <div>
                            <label class="css-control css-control-lg css-control-primary css-radio">
                                <input type="radio" class="css-control-input chk_radio_all" id="chk_radio_all" value="" name="active" {!! ( @$search->active != 'Y' && @$search->active!='N' ? 'checked' : '') !!} >
                                <span class="css-control-indicator"></span> {{ucfirst(__('api_users.active.text_radio.all'))}}
                            </label>
                            <label class="css-control css-control-lg css-control-success css-radio">
                                <input type="radio" class="css-control-input" value="1" name="active" {!! ( @$search->active=='1' ? 'checked' : '') !!}>
                                <span class="css-control-indicator"></span> {{ucfirst(__('api_users.active.text_radio.true'))}}
                            </label>
                            <label class="css-control css-control-lg css-control-danger css-radio">
                                <input type="radio" class="css-control-input" value="0" name="active" {!! ( @$search->active=='0' ? 'checked' : '') !!} >
                                <span class="css-control-indicator"></span> {{ucfirst(__('api_users.active.text_radio.false'))}}
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