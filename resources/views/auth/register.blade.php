@extends('layouts.auth_layout')
@section('title')
    {{ __('messages.register') }}
@endsection
@section('meta_content')
    - {{ __('messages.register') }} {{ __('messages.to') }} {{getAppName()}}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ mix('assets/css/simple-line-icons.css')}}">
@endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="p-4 account-container w-100">
                <div class="card p-sm-4 p-3 login-group border-0">
                    @if($errors->any())
                        <div class="alert alert-danger text-center mt-2">{{$errors->first()}}</div>
                    @endif
                    <div class="card-body p-1">
                        <form method="post" action="{{ url('/register') }}" id="registerForm">
                            {{ csrf_field() }}
                            <h1 class="login-group__title mb-2">{{ __('messages.register') }}</h1>
                            <p class="text-muted login-group__sub-title mb-3">{{ __('messages.create_your_account') }}</p>
                            <div class="form-group mb-4 login-group__sub-title">
                                {!! Form::label('name', __('messages.full_name').':' )!!}<span class="red">*</span>
                                <input type="text"
                                       class="form-control login-group__input"
                                       name="name" value="{{ old('name') }}"
                                       placeholder="{{ __('messages.full_name') }}" id="name" required>
                            </div>

                            <div class="form-group mb-4 login-group__sub-title">
                                {!! Form::label('age', __('messages.age').':' )!!}
                                <input type="number"
                                       class="form-control login-group__input"
                                       name="age" value="{{ old('age') }}"
                                       placeholder="{{ __('messages.age') }}" id="age" required>
                            </div>

                            <div class="form-group w-100">
                                <div class="form-group w-100">
                                    <label class="mb-2 login-group__sub-title">{{ __('messages.gender').':' }}</label>
                                    <div class="d-flex login-group__input align-items-center">
                                        <div class="custom-control custom-radio mx-2">
                                            <input type="radio" class="custom-control-input" id="male" name="gender"
                                                   value="{{ \App\Models\User::MALE }}" checked >
                                            <label class="custom-control-label" for="male">{{ __('messages.male') }}</label>
                                        </div>
                                        <div class="custom-control custom-radio mx-2">
                                            <input type="radio" class="custom-control-input" id="female" name="gender"
                                                   value="{{ \App\Models\User::FEMALE }}" >
                                            <label class="custom-control-label" for="female">{{ __('messages.female') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group bordered-input w-100">
                                <label for="phone" class="mb-2 login-group__sub-title">
                                    {{ __('messages.phone').':' }}</label>
                                <input type="tel" class="profile__phone form-control login-group__input" id="phone"
                                       aria-describedby="User phone no"
                                       placeholder="{{ __('messages.phone_number') }}"
                                       name="phone"
                                       value="" />
                            </div>

                            <div class="form-group login-group__sub-title">
                                {{ Form::label('role_id', __('messages.account_type').':') }}
                                
                                <select name="role_id" id="role_id" class="form-control login-group__input" required>
                                    @foreach ($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="center-container" class="form-group login-group__sub-title">
                                {{ Form::label('center_id', __('messages.group.center').':') }}
                                
                                <select name="center_id" id="center_id" class="form-control login-group__input" required>
                                    @foreach ($centersWithGroups as $center)
                                        <option value="{{$center['id']}}">{{$center['name']}} ({{$center['code']}})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="group-container" class="form-group login-group__sub-title">
                                <label for="group_id" class="mb-2 login-group__sub-title">
                                    {{ __('messages.group_number').':' }} <i class="fa fa-question-circle ms-2 question-type-open cursor-pointer"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Please select 1 if only one centre"></i></label>
                                <select name="group_id" id="group_id" class="form-control login-group__input" required>
                                    
                                </select>
                            </div>

                            <div class="form-group mb-4 login-group__sub-title">
                                {!! Form::label('password', __('messages.password').':' )!!}<span class="red">*</span>
                                <i class="fa fa-question-circle ms-2 question-type-open cursor-pointer"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="Minimum 8 character required, White space is not allowed"></i>
                                <input type="password"
                                       class="form-control login-group__input"
                                       name="password" placeholder="{{ __('messages.password') }}" id="password"
                                       onkeypress="return avoidSpace(event)" required>
                            </div>
                            <div class="form-group mb-4 login-group__sub-title">
                                {!! Form::label('confirm_password', __('messages.confirm_password').':' )!!}<span
                                    class="red">*</span>
                                <i class="fa fa-question-circle ms-2 question-type-open cursor-pointer"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="Minimum 8 character required, White space is not allowed"></i>
                                <input type="password" name="password_confirmation"
                                       class="form-control login-group__input"
                                       placeholder="{{ __('messages.confirm_password') }}" id="password_confirmation"
                                       onkeypress="return avoidSpace(event)" required>
                            </div>
                            <button type="button" id="processBtn"
                                    class="btn btn-primary btn-block btn-flat mb-4 login-group__register-btn">{{ __('messages.create_account') }}</button>                                    
                            <a href="{{ url('/login') }}"
                               class="text-center back-to-login__btn text-decoration-none">{{ __('messages.already_have_membership') }}</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmInforModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title group-modal-title">{{ __('messages.confirm_information') }}</h4>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Full Name</td>
                                        <td id="confirm_fullName"></td>
                                    </tr>
                                    <tr>
                                        <td>Age</td>
                                        <td id="confirm_age"></td>
                                    </tr>
                                    <tr>
                                        <td>Gender</td>
                                        <td id="confirm_gender"></td>
                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td id="confirm_phone"></td>
                                    </tr>
                                    <tr>
                                        <td>Account Type</td>
                                        <td id="confirm_accountType"></td>
                                    </tr>
                                    <tr>
                                        <td>Center</td>
                                        <td id="confirm_center"></td>
                                    </tr>
                                    <tr id="confirm_group_container">
                                        <td>Group Number</td>
                                        <td id="confirm_group"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-12 text-start">
                            <button id="confirmBtn" type="button" class="btn btn-primary ms-1">{{ __('messages.confirm') }}</button>
                            <button type="button" class="btn btn-secondary ms-1" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection
@section('page_js')
    <script>
        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        const centersWithGroups = @json($centersWithGroups);

        $(document).ready(function() {

            $(document).on('change', '#role_id', function(event) {
                const role = $('#role_id option:selected').text();

                if(role == 'Center Admin') {
                    $('#group-container').hide();
                    $('#group_id').props('disabled', true);
                } else {
                    $('#group-container').show();
                    $('#group_id').props('disabled', false);
                }
            });
            
            $(document).on('change', '#center_id', function() {
                const centerId = this.value;
                const centerWithGroups = centersWithGroups.find((item) => item.id == centerId);
                const groups = centerWithGroups.groups;

                let options = '';
                options = groups.map((item) => {
                    return `<option value="${item.id}">${item.number} - ${item.name}</option>`;
                });

                $('#group_id').html(options);
            });

            $('#center_id').trigger('change');
 
            $(document).on('click', '#processBtn', function (event) {
                event.preventDefault();
                if (!validateName() || !validatePassword() ||
                    !validatePasswordConfirmation() || !validateMatchPasswords()) {
                    return false
                }

                $('#confirm_fullName').text($('#name').val());
                $('#confirm_age').text($('#age').val());

                var gender = $('#male').prop('checked') ? 'male' : 'female';
                $('#confirm_gender').text(gender);
                
                $('#confirm_phone').text($('#phone').val());
                $('#confirm_accountType').text($('#role_id option:selected').text());

                if($('#role_id option:selected').text() == 'Center Admin') {
                    $('#confirm_group_container').hide();
                } else {
                    $('#confirm_group_container').show();
                }
                
                $('#confirm_center').text($('#center_id option:selected').text());
                $('#confirm_group').text($('#group_id option:selected').text());

                $('#confirmInforModal').modal('show');
            });

            $(document).on('click', '#confirmBtn', function(event) {
                $('#registerForm').submit()
            });
        });
    </script>
@endsection
