@extends('layouts.app')
@section('title')
    {{ __('messages.new_center') }}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ mix('assets/css/admin_panel.css') }}">
@endsection
@section('content')
    <div class="container-fluid page__container">
        <div class="animated fadeIn main-table">
            @include('flash::message')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header page-header">
                            <div class="pull-left page__heading">
                                {{ __('messages.new_center') }}
                            </div>
                        </div>
                        <div class="card-body py-sm-3 py-1">
                            @include('coreui-templates::common.errors')
                            {{ Form::open(['id'=>'createCenterForm', 'route' => 'centers.store', 'method' => 'post']) }}
                                {{ csrf_field() }}
                                <div class="row mb-sm-0 mb-1">
                                    <div class="form-group col-sm-12">
                                        <div class="alert alert-danger" style="display: none" id="validationErrorsBox"></div>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12 login-group__sub-title">
                                        {{ Form::label('name', __('messages.name').':') }}<span class="red">*</span>
                                        {{ Form::text('name', null, ['class' => 'form-control login-group__input', 'id' => 'name', 'required','placeholder'=>__('messages.name')]) }}
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12 login-group__sub-title">
                                        {{ Form::label('code', __('messages.code').':') }}<span class="red">*</span>
                                        {{ Form::text('code', null, ['class' => 'form-control login-group__input', 'id' => 'code', 'required','placeholder'=>__('messages.code')]) }}
                                    </div>

                                    <div class="form-group col-md-12 col-sm-12 login-group__sub-title">
                                        {{ Form::label('center_remark', __('messages.remark').':') }}
                                        {{ Form::textarea('remark', null, ['class' => 'form-control login-group__input', 'id' => 'center_remark','placeholder'=>__('messages.remark'), 'rows' => 4]) }}
                                    </div>

                                    <div class="text-start form-group col-sm-12">
                                        {{ Form::button(__('messages.save') , ['type'=>'submit','class' => 'btn btn-primary primary-btn','id'=>'submit','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> " .__('messages.processing')]) }}
                                        <a type="button" href="{{ route('roles.index') }}" id="cancel"
                                           class="btn btn-secondary close_create_role ms-1">{{ __('messages.cancel') }}
                                        </a>
                                    </div>
                                </div>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        
        $(document).ready(function() {
            $('#createCenterForm').on('submit', function (event) {
                
                let name = $('#name').val();
                let emptyName = name.trim().replace(/ \r\n\t/g, '') === '';
                
                if (emptyName) {
                    event.preventDefault();
                    displayToastr('Error', 'error', 'Name field is not contain only white space');
                    return 
                }
                
                let loadingButton = jQuery(this).find('#submit');
                loadingButton.button('loading');

                return true;
            });

            $('#name').on('keyup', function(event) {
                let value = $(this).val();

                let initials = value
                .split(' ')
                .map(function(word) {
                    return word.charAt(0).toUpperCase();
                })
                .join('');

                $('#code').val(initials);
            });
        });
        
        
    </script>
    <script src="{{ mix('assets/js/custom.js') }}"></script>
@endsection
