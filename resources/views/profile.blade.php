@extends('layouts.app')
@section('title')
    {{ __('messages.edit_profile') }}
@endsection
@section('content')
    <div class="page-container">
        <div class="profile bg-white">
            <h1 class="page__heading text-center mb-4">{{ __('messages.edit_profile') }}</h1>
            {!! Form::open(['id'=>'editProfileForm','files'=>true]) !!}
            <div class="profile__inner m-auto">
                <div class="edit-profile-card w-100 mb-5 position-relative">
                    <div class="profile__img-wrapper mb-4">
                        <img src="{{ getLoggedInUser()->photo_url }}" alt="" id="upload-photo-img">
                    </div>
                    <div class="text-center mb-4 edit-profile-card__btn">
                        <label class="btn profile__update-label">{{ __('messages.upload_photo') }}
                            <input id="upload-photo" class="d-none" name="photo" type="file" accept="image/*">
                        </label>
                        @if(!(Str::contains(getLoggedInUser()->getOriginal('photo_url'),'ui-avatars.com')) && !(Str::contains(getLoggedInUser()->getOriginal('photo_url'),'assets')))
                            <label>
                                <button class="btn btn-danger mb-2 remove-profile-img ms-1">{{__('messages.remove_profile')}}</button>
                            </label>
                        @endif
                    </div>
                </div>
                <div class="alert alert-danger w-100" style="display: none" id="editProfileValidationErrorsBox"></div>
                
                <div class="form-group bordered-input w-100">
                    <label for="about" class="mb-2 login-group__sub-title">{{ __('messages.bio').':' }}</label>
                    <textarea
                            class="profile__email login-group__input form-control" id="about" rows="3"
                            name="about" placeholder="{{ __('messages.bio')}}">{{ (htmlspecialchars_decode(Auth::user()->about))??'' }}</textarea>
                </div>
                
                
                @php
                    $currentUser = getLoggedInUser();
                @endphp
                <div class="form-group mb-4 login-group__sub-title w-100">
                    {!! Form::label('name', __('messages.full_name').':' )!!}
                    <input type="text"
                           class="form-control login-group__input"
                           value="{{ $currentUser->name }}"
                           placeholder="{{ __('messages.full_name') }}" id="name" readonly>
                </div>

                <div class="form-group mb-4 login-group__sub-title w-100">
                    {!! Form::label('age', __('messages.age').':' )!!}
                    <input type="number"
                           class="form-control login-group__input"
                           value="{{ $currentUser->age }}"
                           placeholder="{{ __('messages.age') }}" id="age" readonly>
                </div>

                <div class="form-group mb-4 login-group__sub-title w-100">
                    {!! Form::label('gender', __('messages.gender').':' )!!}
                    <input type="text"
                           class="form-control login-group__input"
                           value="{{ $currentUser->gender == \App\Models\User::MALE ? "Male" : "Female" }}"
                           placeholder="{{ __('messages.gender') }}" id="gender" readonly>
                </div>

                <div class="form-group bordered-input w-100">
                    <label for="phone" class="mb-2 login-group__sub-title">
                        {{ __('messages.phone').':' }}</label>
                    <input type="tel" class="profile__phone form-control login-group__input" id="phone"
                           aria-describedby="User phone no"
                           placeholder="{{ __('messages.phone_number') }}"
                           value="{{ $currentUser->phone }}" readonly />
                </div>

                <div class="form-group login-group__sub-title w-100">
                    {{ Form::label('role_id', __('messages.account_type').':') }}
                    
                    <input type="text" class="profile__phone form-control login-group__input" id="role"
                           aria-describedby="User phone no"
                           value="{{ $currentUser->role_name }}" readonly />
                </div>
                
                @php
                    $centers = $currentUser->centers;
                    $groups = $currentUser->groups;
                @endphp

                <div class="form-group login-group__sub-title w-100">
                    {{ Form::label('center', __('messages.group.center').':') }}
                    
                    <input type="text" class="profile__phone form-control login-group__input" id="center"
                           aria-describedby="User Center"
                           value="{{ count($centers) > 0 ? $centers[0]->name : '' }}" readonly />
                </div>

                <div class="form-group login-group__sub-title w-100">
                    {{ Form::label('group', __('messages.group.group').':') }}
                    
                    <input type="text" class="profile__phone form-control login-group__input" id="group"
                           aria-describedby="User Group"
                           value="{{ count($groups) > 0 ? $groups[0]->name : '' }}" readonly />
                </div>
                
                <div class="d-flex w-100">
                    {!! Form::button(__('messages.save') , ['type'=>'submit','class' => 'btn btn-primary me-2 primary-btn','id'=>'btnEditSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> " .__('messages.processing')]) !!}
                    <a class="btn btn-secondary" id="cancelGroupModal"
                       href="{{url('conversations')}}">{{ __('messages.cancel') }}</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection
