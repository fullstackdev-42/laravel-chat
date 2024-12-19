@extends('layouts.app')
@section('title')
    {{ __('messages.edit_group') }}
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
                                {{ __('messages.edit_group') }}
                            </div>
                        </div>
                        <div class="card-body py-sm-3 py-1">
                            @include('coreui-templates::common.errors')
                            {{ Form::model($group, ['route' => ['group.update', $group->id], 'method' => 'post', 'id' => 'editGroupForm']) }}
                            @csrf
                            @method('PUT')
                            <div class="row mb-sm-0 mb-1">
                                <div class="form-group col-sm-12">
                                    <div class="alert alert-danger form-group col-sm-12" style="display: none" id="editValidationErrorsBox"></div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12 login-group__sub-title">
                                    {{ Form::label('center_id', __('messages.group.center').':') }}<span class="red">*</span>
                                    <input type="hidden" name="center_id" value="{{ $group->center_id }}" />
                                    <select id="center_id" class="form-control login-group__input" required disabled>
                                        @foreach ($centers as $center)
                                            <option value="{{$center->id}}" {{$group->center_id == $center['id'] ? 'selected' : '' }}>{{$center->name}} ({{$center->code}})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6 col-sm-12 login-group__sub-title">
                                    {{ Form::label('name', __('messages.name').':') }}
                                    {{ Form::text('name', null, ['class' => 'form-control login-group__input', 'id' => 'name', 'required', 'disabled', 'placeholder'=>__('messages.name')]) }}
                                </div>

                                <div class="col-12 d-flex edit-profile-image mb-3">
                                    <div class="ps-0 edit-profile-btn">
                                        {!! Form::label('photo', __('messages.group_icon').':', ['class' => 'login-group__sub-title']) !!}
                                        <label class="edit-profile__file-upload btn-primary mb-0"> {{__('messages.group.choose_file')}}
                                            {!! Form::file('photo',['id'=>'groupImage','class' => 'd-none', 'accept' => 'image/*']) !!}
                                        </label>
                                    </div>
                                    <div class="mt-2 profile__inner mw-unset w-auto m-auto">
                                        <div class=" preview-image-video-container text-center chat-profile__img-wrapper mt-0">
                                            <img id='groupPhotoPreview' class="" src="{{asset('assets/images/group-img.png')}}"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-12 col-sm-12 login-group__sub-title">
                                    {{ Form::label('description', __('messages.group.description').':') }}
                                    {{ Form::textarea('description', null, ['class' => 'form-control login-group__input', 'id' => 'description','placeholder'=>__('messages.enter_group_desc'), 'rows' => 4]) }}
                                </div>
                                
                                <div class="text-start form-group col-sm-12">
                                    {{ Form::button(__('messages.save') , ['type'=>'submit','class' => 'btn btn-primary','id'=>'submit','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> " .__('messages.processing')]) }}
                                    <a type="button" href="{{ route('centers.index') }}" id="btnCancelEdit"
                                       class="btn btn-secondary close_edit_center ms-1">{{ __('messages.cancel') }}
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
            $(document).on('change', '#groupImage', function () {
                let ext = $(this).val().split('.').pop().toLowerCase()
                if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                    $(this).val('')
                    $('#groupValidationErrorsBox').
                        html(
                            'The profile image must be a file of type: jpeg, jpg, png.').
                        show()
                } else {
                    displayPhoto(this, '#groupPhotoPreview')
                }

                setTimeout(function () {
                    $('#groupValidationErrorsBox').hide()
                }, 3000)
            });
        });

        window.displayPhoto = function (input, selector) {
            let displayPreview = true;
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    let image = new Image();
                    image.src = e.target.result;
                    image.onload = function () {
                        $(selector).attr('src', e.target.result);
                        displayPreview = true;
                    };
                };
                if (displayPreview) {
                    reader.readAsDataURL(input.files[0]);
                    $(selector).show();
                }
            }
        };
        
    </script>
    <script src="{{ mix('assets/js/custom.js') }}"></script>
@endsection