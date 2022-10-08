@extends('layouts.backend')
@section('content')
<!-- Page Content -->
<div class="container">
    @include('components._breadcrumb')
    <!-- Content Heading -->
    <h2 class="content-heading pt-2">
        <i class="{{config('theme.icon.menu_apicloud')}} mr-2"></i>{{ ucfirst(__('openTenants.heading')) }}
        <div class="bock-sub-menu"></div>
    </h2>
    <!-- END Content Heading -->

    <!-- Content Main -->
    <div class="block {{config('theme.layout.main_block')}}">
        <div class="block-header {{config('theme.layout.main_block_header')}}">
            <h3 class="block-title">
                <i class="{{config('theme.icon.item_form')}} mr-2"></i>
                {{ ucfirst(__('openTenants.head_title.add')) }}
                <small> </small>
            </h3>
        </div>

        <div class="block-content container p-4">
            <!-- ** Content Data ** -->
            <div class="row mb-4 ml-4 mr-2">
                <div class="col">
                    <form class="form-inline float-right">
                        <label class="mr-3" for="Organization">{{ ucfirst(__('openTenants.title_form_send')) }} :</label>
                        <input type="text" class="form-control form-control-lg mb-2 mr-sm-2 mb-sm-0" id="Organization" name="Organization" placeholder="Organization..">
                        <input type="hidden" id="code" name="code" value="{{$code}}">
                         @permissions('create.openTenants')
                        <button type="button" id="btn-submit" class="btn btn-primary min-width-125">
                         <i class=" fa fa-send mr-1"></i>
                           
                            {{ ucfirst(__('openTenants.btn_form_send')) }}
                           
                        </button>
                         @endpermissions
                    </form>
                </div>
            </div>
            <div id="accordion2" role="tablist" aria-multiselectable="true">  
            @foreach ($collection as $key => $item)
               @php
                   $item = (object)$item;
               @endphp
                <div class="alert alert-secondary row mr-4 ml-4 mt-2 mb-0 p-0" style="min-height: 76px;" id="loop_{{$loop->iteration}}">          
                    <div class="col-auto pl-4 pt-3 pb-2" role="tab" id="accordion2_h{{$loop->iteration}}">
                        <h3 class="alert-heading font-size-h4 font-w400 mb-2">{{$item->name}}</h3>
                        <div class="row-message" style="display: none;">
                            <button class="btn btn-sm btn-secondary mr-2" data-toggle="collapse" data-parent="#accordion2" href="#accordion2_q{{$loop->iteration}}" aria-expanded="true" aria-controls="accordion2_q{{$loop->iteration}}">
                                <i class="fa fa-search-plus"></i>
                            </button>
                            <span class="alert-message">{!! $item->des !!}</span>
                        </div>
                    </div>
                    <div class="col-auto ml-auto text-right border-left border-3x border-white pt-3 text-center" style="min-width: 89px;">
                        <i class="icon-status fa fa-4x fa-minus"></i>
                    </div>
                </div>
                <div id="accordion2_q{{$loop->iteration}}" class="collapse block block-bordered mr-4 ml-4 mb-2 block-main-raw border-top-0" role="tabpanel" aria-labelledby="accordion2_h{{$loop->iteration}}">
                    <div class="block-content"><pre><code class="raw-block" id="raw-block_loop_{{$loop->iteration}}"></code></pre></div>
                </div>
             @endforeach
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

<script>
    var requestid;
    var openTenantsAgain;
@permissions('create.openTenantsAgain') 
    openTenantsAgain = true
@endpermissions

    $(document).ready(async function(){
        var Code = $('#code').val();
        if(Code == ''){
            await noitMessage("{{ucfirst(__('openTenants.message_code_required.title'))}}",'error',"{{ucfirst(__('openTenants.message_code_required.message'))}}");
            window.location.href = '{{ route('home') }}'    
        }
    });

   $(function($) {
        $('#btn-submit').click(async function() {

            requestid = new Date().getTime()+'_'+Math.random().toString(36).substring(2);

            var orgCode = $('#Organization').val();
            var Code = $('#code').val();

            if(Code == ''){
                noitMessage("{{ucfirst(__('openTenants.message_code_required.title'))}}",'error',"{{ucfirst(__('openTenants.message_code_required.message'))}}");
                return;
            }

            if(orgCode==""){
                noitMessage("{{ucfirst(__('openTenants.message_orgcode_required.title'))}}",'error',"{{ucfirst(__('openTenants.message_orgcode_required.message'))}}");
                return;
            }else{
                var isGet = await chkIsGetOrgCode(orgCode);
                if(isGet !== null){
                    //var title = "{{ucfirst(__('openTenants.message_confirm_send.title'))}}"
                    var title = "&nbsp;"
                    var icon = 'warning'
                    if(openTenantsAgain ===true){
                        var message =  '"' + orgCode + '" '+"{{ucfirst(__('openTenants.message_confirm_send.message_isuse_admin'))}}" 
                        var con = await confirmMessage(title,message,icon);
                    }else{
                        var message =  '"' + orgCode + '" '+"{{ucfirst(__('openTenants.message_confirm_send.message_isuse'))}}" 
                        var con = false
                        noitMessage(title,icon,message);
                    }

                }else{
                    var title = "&nbsp;"
                    var message = "{{ucfirst(__('openTenants.message_confirm_send.message_notisuse'))}}"+ '"' + orgCode + '"'
                    var icon = 'success'
                     var con = await confirmMessage(title,message,icon);
                }
                // var con = await confirmMessage(title,message,icon);
                if(con==true){

                    $('#btn-submit').attr('disabled','disabled');
                    resetBlock();

                    var token = await getToken(Code,orgCode);
                    if(token === false){ $('#btn-submit').removeAttr('disabled'); return;}
                    var urlToken = await getUrlByOrgCode(token,orgCode);
                    if(urlToken === false){ $('#btn-submit').removeAttr('disabled'); return;}
                    var urlVdcRollup = await getUrlByVdcRollup(token,urlToken);
                    if(urlVdcRollup === false){ $('#btn-submit').removeAttr('disabled'); return;}
                    var urlOVDC = await getUrlByOVDC(token,urlVdcRollup,orgCode);
                    if(urlOVDC === false){ $('#btn-submit').removeAttr('disabled'); return;}
                    var urlNetworkProfile = await getUrlByNetworkProfile(token,urlOVDC);
                    if(urlNetworkProfile === false){ $('#btn-submit').removeAttr('disabled'); return;}
                    var dataPrimaryEdgeCluster = await getPrimaryEdgeCluster(token,urlNetworkProfile);
                    if(dataPrimaryEdgeCluster === false){ $('#btn-submit').removeAttr('disabled'); return;}
                    var dataPrimaryEdgeCluster = await putPrimaryEdgeCluster(token,urlNetworkProfile);
                    if(dataPrimaryEdgeCluster === false){ $('#btn-submit').removeAttr('disabled'); return;}
                    var dataPrimaryEdgeCluster = await getCheckPrimaryEdgeCluster(token,urlNetworkProfile);
                    $('#btn-submit').removeAttr('disabled');
                }
            }        
        });
   });

   async function chkIsGetOrgCode(orgCode){
        var arr = {};
        arr['orgCode'] = orgCode;
        
        res = $.post("/api/v1/chkcode/"+orgCode)
        .then(function(response) {
            var decodedResponse = atob(response);
            var obj = JSON.parse(decodedResponse);
            if(obj.code === 200){
               
                return obj.data;
            }else{
               
                return false;
            }
        })
        .catch(function(err) {
           
            return false;
        });

        return await res;
  
    }

    async function getToken(code,orgCode){
        var arr = {};
        arr['requestid'] = requestid;
        arr['code'] = code;
        arr['orgCode'] = orgCode;
        var token = await callApi(1,arr,'1');
        return await token;
    }

    async function getUrlByOrgCode(token,orgCode){
        var arr = {};
        arr['requestid'] = requestid;
        arr['token'] = token;
        arr['orgCode'] = orgCode;
        var url = await callApi(2,arr,'2');
        return await url;
    }

    async function getUrlByVdcRollup(token,url){
        
        var arr = {};
        arr['requestid'] = requestid;
        arr['token'] = token;
        arr['url'] = url;
        var url = await callApi(3,arr,'3');
        return await url;
    }

    async function getUrlByOVDC(token,url,orgCode){
        
        var arr = {};
        arr['requestid'] = requestid;
        arr['token'] = token;
        arr['url'] = url;
        arr['orgCode'] = orgCode;
        var url = await callApi(4,arr,'4');
        return await url;
    }

    async function getUrlByNetworkProfile(token,url){
        
        var arr = {};
        arr['requestid'] = requestid;
        arr['token'] = token;
        arr['url'] = url;
        var url = await callApi(5,arr,'5');
        return await url;
    }

    async function getPrimaryEdgeCluster(token,url){
        
        var arr = {};
        arr['requestid'] = requestid;
        arr['token'] = token;
        arr['url'] = url;
        var url = await callApi(6,arr,'6');
        return await url;
    }

     async function putPrimaryEdgeCluster(token,url){
        
        var arr = {};
        arr['requestid'] = requestid;
        arr['token'] = token;
        arr['url'] = url;
        var url = await callApi(7,arr,'7');
        return await url;
    }

    async function getCheckPrimaryEdgeCluster(token,url){
        
        var arr = {};
        arr['requestid'] = requestid;
        arr['token'] = token;
        arr['url'] = url;
        var url = await callApi(8,arr,'8');
        return await url;
    }

    async function callApi(url = '',data = [],block = ''){
        var block = 'loop_'+block;
        var res = false;
        
        changeBlock(block,'loading')
       
        res = $.post("/api/v1/call/"+url,{data})
        .then(function(response) {
            var decodedResponse = atob(response);
            var obj = JSON.parse(decodedResponse);
            if(obj.code === 200){
                changeBlock(block,'success','Success',obj.raw);
                return obj.data;
            }else{
                changeBlock(block,'error',obj.message,obj.raw);
                return false;
            }
        })
        .catch(function(err) {
            changeBlock(block,'error',err)
            return false;
        });

        return await res;
    }

    function changeBlock(id,status,message="",raw=""){
        var newStatus = '';
        var newicon = '';
        var oldStatus = 'alert-secondary';
        var oldIcon = 'fa-minus';
        var idBlock = '#'+id;
        var idIcon = '#'+id+' .icon-status';
        var idMessage = '#'+id+' .alert-message';
        var idRowMessage = '#'+id+' .row-message';
        var idBlockRaw = '#raw-block_'+id;
        var idbtn = '#'+id+' .btn';
        var newBtn = '';
        var oldBtn = 'btn-secondary';
        
        if(status == 'success'){
            newStatus = 'alert-success';
            oldStatus = 'alert-warning';
            newicon = 'fa-check animated bounceIn';
            oldIcon = 'fa-circle-o-notch fa-spin';
            newBtn = 'btn-success';
           $(idRowMessage).show();           
        }else if(status == 'loading'){
            newStatus = 'alert-warning';
            newicon = 'fa-circle-o-notch fa-spin';
            var raw = "";
        }else if(status == 'error'){
            newStatus = 'alert-danger animated shake';
            oldStatus = 'alert-warning';
            newicon = 'fa-close animated bounceIn';
            oldIcon = 'fa-circle-o-notch fa-spin';
            newBtn = 'btn-danger';
           $(idRowMessage).show();
        }
       
        $(idbtn).removeClass(oldBtn).addClass(newBtn);
        $(idBlock).removeClass(oldStatus).addClass(newStatus);
        $(idIcon).removeClass(oldIcon).addClass(newicon);
        $(idMessage).html(message);
        $(idBlockRaw).text(raw);
        
    }

    function resetBlock(){

        var idBlock = '.alert';
        var idIcon = '.alert .icon-status';
        var newStatus = 'alert-secondary';
        var newicon = 'fa-minus';
        var oldStatus1 = 'alert-success';
        var oldIcon1 = 'fa-check animated bounceIn';
        var oldStatus2 = 'alert-danger animated shake';
        var oldIcon2 = 'fa-close animated bounceIn';
        var idMessage = ".alert .alert-message";
        var idBlockRaw = '.raw-block';
        var idRowMessage = '.row-message';
        var idbtn = '.row-message .btn ';

        var newBtn = 'btn-secondary';
        var oldBtn1 = 'btn-success';
        var oldBtn2 = 'btn-danger';
       
        var message = "";
        $(idbtn).removeClass(oldBtn1).removeClass(oldBtn2).addClass(newBtn);
        $(idBlock).removeClass(oldStatus1).removeClass(oldStatus2).addClass(newStatus);
        $(idIcon).removeClass(oldIcon1).removeClass(oldIcon2).addClass(newicon);
        $(idMessage).html("");
        $(idBlockRaw).html("");
        $(idRowMessage).hide();
        $('.block-main-raw').removeClass('show');

    }
</script>
@endsection