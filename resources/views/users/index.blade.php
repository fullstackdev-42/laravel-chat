@extends('layouts.app')
@section('title')
    {{ __('messages.users') }}
@endsection
@section('page_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/dataTable.min.css') }}"/>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ mix('assets/css/admin_panel.css') }}">
@endsection
@section('content')
    <div class="container-fluid page__container">
        <div class="animated fadeIn main-table">
            @include('flash::message')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div
                            class="card-header page-header flex-wrap align-items-sm-center align-items-start flex-sm-row flex-column">
                            <div class="user-header d-flex align-items-center justify-content-between">
                                <div class="pull-left page__heading me-3 my-2">
                                    {{ __('messages.users') }}
                                </div>
                                <button type="button"
                                        class="my-2 pull-right btn btn-primary filter-container__btn ms-sm-0 ms-auto d-sm-none d-block"
                                        data-bs-toggle="modal"
                                        data-bs-target="#create_user_modal">{{ __('messages.new_user') }}</button>
                            </div>
                            <div class="filter-container user-filter align-self-sm-center align-self-end ms-auto">
                                <div class="me-2 my-2 user-select2 ms-sm-0 ms-auto">
                                    {!!Form::select('drp_users', \App\Models\User::FILTER_ARRAY, null, ['id' => 'filter_user','class'=>'form-control','placeholder' => __('messages.placeholder.select_status_all'),'style'=>'min-width:150px;'])  !!}
                                </div>
                                {{-- <div class="me-sm-2 my-2 user-select2 ms-sm-0 ms-auto">
                                    {!!Form::select('privacy_filter', \App\Models\User::PRIVACY_FILTER_ARRAY, null, ['id' => 'privacy_filter', 'class'=>'form-control','placeholder' => __('messages.placeholder.select_privacy'), 'style'=>'min-width:150px;'])  !!}
                                </div> --}}
                                <button type="button"
                                        class="my-2 pull-right btn btn-primary new-user-btn filter-container__btn ms-sm-0 ms-auto"
                                        data-bs-toggle="modal"
                                        data-bs-target="#create_user_modal">{{ __('messages.new_user') }}</button>
                            </div>
                        </div>
                        <div class="card-body">
                            @include('users.table')
                            <div class="pull-right me-3">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('users.create')
    @include('users.edit')
    @include('users.templates.action_icons')
@endsection
@section('page_js')
    <script type="text/javascript" src="{{ asset('js/dataTable.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let defaultImageAvatar = "{{ getDefaultAvatar() }}"
    </script>

    <script>
        $(document).ready(function () {
            $('#filter_user').select2({
                minimumResultsForSearch: -1,
            });
            $('#privacy_filter').select2({
                minimumResultsForSearch: -1,
            });

            let tbl = $('#users_table').DataTable({
                processing: true,
                serverSide: true,
                'bStateSave': true,
                'order': [[1, 'asc']],
                ajax: {
                    url: route('users.index'),
                    data: function (data) { 
                        data.filter_user = $('#filter_user').find('option:selected').val();
                        data.privacy_filter = $('#privacy_filter').find('option:selected').val();
                    },
                },
                columnDefs: [
                    {
                        'targets': [0],
                    },
                    {
                        'targets': [6, 7, 8],
                        'orderable': false,
                        'className': 'text-center',
                        'width': '10%',
                    },
                    {
                        'targets': [5, 7],
                        visible: false,
                    }
                ],
                columns: [
                    {
                        data: function (row) {
                            return `<div class="d-flex align-items-center"> <div class="symbol symbol-circle symbol-50px overflow-hidden mr-3"> <a href="javascript:void(0)"> <div> <img src="${row.photo_url}" alt="User Image" class="user-avatar-img"> </div> </a> </div> <div class="d-flex flex-column"> <a href="javascript:void(0)" class="mb-1 user-name-data">${htmlSpecialCharsDecode(row.name)}</a></div> </div> `
                        }, name: 'name',
                    },
                    {
                        data: 'user_no',
                        name: 'user_no',
                    },
                    {
                        data: function (data) {
                            let role_name = getRoleName(data.roles);
                            return htmlSpecialCharsDecode(role_name);
                        },
                        name: 'email',
                    },
                    {
                        data: function (data) {
                            let name = getRoleName(data.centers);
                            return htmlSpecialCharsDecode(name);
                        },
                        name: 'email',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: function (data) {
                            let name = getRoleName(data.groups);
                            return htmlSpecialCharsDecode(name);
                        },
                        name: 'email',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: function (data) {
                            return (data.privacy) ? '<span class="public-badge py-1 px-2">Public</span>' : '<span class="private-badge py-1 px-2">Private</span>';
                        },
                        name: 'privacy',
                        'searchable': false,
                    },
                    {
                        data: function (row) {
                            let checked = row.is_active == 0 ? '' : 'checked';
                            return ' <label class="switch switch-label switch-outline-primary-alt align-middle">' +
                                '<input name="is_active" data-id="' + row.id +
                                '" class="switch-input is-active" type="checkbox" value="1" ' +
                                checked + '>' +
                                '<span class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>' +
                                '</label>';
                        }, name: 'id',
                    },
                    {
                        data: function (row) {
                            return `<a title="" href="${route('user-impersonate-login',row.id)}" class="btn btn-primary btn-sm">
                                    Impersonate
                                    </a>`;
                        }, name: 'id',
                    },
                    {
                        data: function (row) {
                            let helpers = {
                                isArchive: isArchive,
                            };
                            let template = $.templates('#tmplAddChatUsersList');
                            return template.render(row, helpers);
                        }, name: 'id',
                    },
                ],
                drawCallback: function () {
                    this.api().state.clear();
                },
                'fnInitComplete': function () {
                    $('#filter_user').change(function () {
                        tbl.ajax.reload()
                    });
                    $('#privacy_filter').change(function () {
                        tbl.ajax.reload()
                    });
                },
            });

            window.isArchive = function(deletedAt) {
                return (deletedAt != null) ? 1 : 0;
            }

            window.getRoleName = function(roles) {
                let roleName = '';
                $.each(roles, (index, val) => {
                    roleName = val.name;
                    return false;
                });
                return roleName;
            }

            $('#createUserForm').on('submit', function (event) {
                event.preventDefault();
                let loadingButton = jQuery(this).find('#createBtnSave');
                loadingButton.button('loading');
                $.ajax({
                    url: route('users.store'),
                    type: 'post',
                    data: new FormData($(this)[0]),
                    processData: false,
                    contentType: false,
                    success: function (result) {
                        if (result.success) {
                            displayToastr('Success', 'success', result.message);
                            $('#create_user_modal').modal('hide');
                            $('#users_table').DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function (result) {
                        displayToastr('Error', 'error', result.responseJSON.message);
                    },
                    complete: function () {
                        loadingButton.button('reset');
                    },
                });
            });

            $('#editUserForm').on('submit', function (event) {
                event.preventDefault();
                let loadingButton = jQuery(this).find('#editBtnSave');
                loadingButton.button('loading');
                let id = $('#edit_user_id').val();
                $.ajax({
                    url: route('user.update',id),
                    type: 'post',
                    data: new FormData($(this)[0]),
                    processData: false,
                    contentType: false,
                    success: function (result) {
                        if (result.success) {
                            displayToastr('Success', 'success', result.message);
                            $('#edit_user_modal').modal('hide');
                            $('#users_table').DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function (result) {
                        displayToastr('Error', 'error', result.responseJSON.message);
                    },
                    complete: function () {
                        loadingButton.button('reset');
                    },
                });
            });

            $(document).on('click', '.edit-btn', function () {
                let userId = $(this).data('id');
                renderData(route('users.edit',userId));
            });

            window.renderData = function (url) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    // cache: false,
                    success: function (result) {
                        if (result.success) {
                            let user = result.data.user;
                            $('#edit_user_id').val(user.id);
                            $('#edit_name').val(htmlSpecialCharsDecode(user.name));
                            $('#edit_email').val(user.email);
                            $('#edit_phone').val(user.phone);
                            $('#edit_is_active').val(user.is_active);
                            $('#edit_role_id').val(user.role_id);
                            $('#edit_upload-photo-img').attr('src', user.photo_url);
                            $('#edit_about').val(htmlSpecialCharsDecode(user.about));
                            $('#edit_user_modal').modal('show');
                            if (user.gender == 1) {
                                $('#edit_male').prop('checked', true);
                            }
                            if (user.gender == 2) {
                                $('#edit_female').prop('checked', true);
                            }

                            if (user.privacy == 1) {
                                $('#editPrivacyPublic').prop('checked', true);
                            } else {
                                $('#editPrivacyPrivate').prop('checked', true);
                            }
                        }
                    },
                    error: function (error) {
                        displayToastr('Error', 'error', error.responseJSON.message);
                    },
                });
            };

            const swalDelete = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-danger mr-2 btn-lg',
                    cancelButton: 'btn btn-secondary btn-lg',
                },
                buttonsStyling: false,
            });

            // open delete confirmation model
            $(document).on('click', '.delete-btn', function (event) {
                let userId = $(this).data('id');
                deleteItem(route('users.destroy',userId), '#users_table', Lang.get('messages.placeholder.user'));
            });

            function deleteItem (url, tableId, header, callFunction = null) {
                swalDelete.fire({
                    title: Lang.get('messages.placeholder.are_you_sure'),
                    html: Lang.get('messages.placeholder.want_to_delete_this') +'"'+ header +'"'+ ' ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    input: 'text',
                    inputPlaceholder: Lang.get('messages.placeholder.write_delete_user'),
                    inputValidator: (value) => {
                        if (value !== "delete") {
                            return Lang.get('messages.placeholder.need_to_write_delete')
                        }
                    }
                }).then((result) => {
                    if (result.value) {
                        deleteItemAjax(url, tableId, header, callFunction = null);
                    }
                });
            }

            $(document).on('click', '.archive-btn', function () {
                let userId = $(this).data('id');
                archiveItem(route('archive-user',userId), '#users_table', Lang.get('messages.placeholder.user'));
            });

            function archiveItem (url, tableId, header, callFunction = null) {
                swalDelete.fire({
                    title: Lang.get('messages.placeholder.are_you_sure'),
                    input: 'text',
                    inputPlaceholder: Lang.get('messages.placeholder.confirm_archive'),
                    html: Lang.get('messages.placeholder.want_to_archive')+' "'+ header +'" '+ Lang.get('messages.placeholder.after_archive'),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Archive',
                    inputValidator: (value) => {
                        if (value !== "archive") {
                            return Lang.get('messages.placeholder.you_need_to')
                        }
                    }
                }).then((result) => {
                    if (result.value) {
                        archiveItemAjax(url, tableId, header, callFunction = null);
                    }
                });
            }

            window.archiveItemAjax = function (url, tableId, header, callFunction = null) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function (obj) {
                        if (obj.success) {
                            $(tableId).DataTable().ajax.reload(null, false);
                        }
                        displayToastr('Success', 'success',obj.message);
                    },
                    error: function (data) {
                        displayToastr('Error', 'error', data.responseJSON.message);
                    },
                });
            };

            $(document).on('click', '.restore-btn', function (event) {
                let userId = $(this).data('id');
                restoreItem(route('user.restore-user'), '#users_table', Lang.get('messages.placeholder.user'), userId);
            });

            function restoreItem (url, tableId, header, userId) {
                swal.fire({
                    title: Lang.get('messages.placeholder.are_you_sure'),
                    html: Lang.get('messages.placeholder.want_to_restore') +'"'+ header + '"?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Restore',
                }).then((result) => {
                    if (result.value) {
                        restoreItemAjax(url, tableId, header, userId);
                    }
                });
            }

            window.restoreItemAjax = function (url, tableId, header, userId) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {'id': userId},
                    dataType: 'json',
                    success: function (obj) {
                        if (obj.success) {
                            $(tableId).DataTable().ajax.reload(null, false);
                        }
                        displayToastr('Success', 'success',
                            header + ' has been restored.');
                    },
                    error: function (data) {
                        displayToastr('Error', 'error', data.responseJSON.message);
                    },
                });
            };

            $('#create_user_modal').on('hidden.bs.modal', function () {
                resetModalForm('#createUserForm', '#validationErrorsBox');
                $('#upload-photo-img').attr('src', defaultImageAvatar);
            });
            $('#edit_user_modal').on('hidden.bs.modal', function () {
                resetModalForm('#editUserForm', '#editValidationErrorsBox');
            });

            function resetModalForm (formId, validationBox) {
                $(formId)[0].reset();
                $(validationBox).hide();
            }

            function printErrorMessage (selector, errorMessage) {
                $(selector).show().html('');
                $(selector).append('<div>' + errorMessage + '</div>');
            }

            // listen user activation deactivation change event
            $(document).on('change', '.is-active', function (event) {
                const userId = $(event.currentTarget).data('id');
                activeDeActiveUser(userId);
            });

            // activate de-activate user
            window.activeDeActiveUser = function (id) {
                $.ajax({
                    url: route('active-de-active-user',id),
                    method: 'post',
                    cache: false,
                    success: function (result) {
                        if (result.success) {
                            displayToastr('Success', 'success', result.message);
                            $('#users_table').DataTable().ajax.reload(null, false);
                        }
                    },
                });
            };

            // Email verified
            $(document).on('change', '.email-verified', function (event) {
                const userId = $(event.currentTarget).data('id');
                $.ajax({
                    url: route('user.email-verified',userId),
                    method: 'post',
                    cache: false,
                    success: function (result) {
                        if (result.success) {
                            displayToastr('Success', 'success', result.message);
                            $('#users_table').DataTable().ajax.reload(null, false);
                        }
                    },
                });
            });

            window.validatePasswordConfirmation = function () {
                let passwordConfirmation = $('#confirm_password').val();
                if (passwordConfirmation === '') {
                    displayToastr('Error', 'error',
                        'The password confirmation field is required.');
                    return false;
                }
                return true;
            };

            window.validateMatchPasswords = function () {
                let passwordConfirmation = $('#confirm_password').val();
                let password = $('#password').val();
                if (passwordConfirmation !== password) {
                    displayToastr('Error', 'error',
                        'The password and password confirmation did not match.');
                    return false;
                }
                return true;
            };

            window.validatePassword = function () {
                let password = $('#password').val();
                if (password === '') {
                    displayToastr('Error', 'error', 'The password field is required.');
                    return false;
                }
                return true;
            };
        });

    </script>
    <script src="{{ mix('assets/js/admin/users/edit_user.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom-datatables.js') }}"></script>
@endsection

