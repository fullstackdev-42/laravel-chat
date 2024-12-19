@extends('layouts.app')
@section('title')
    {{__('messages.group.groups')}}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ mix('assets/css/jquery.toast.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/dataTable.min.css') }}"/>
    <link rel="stylesheet" href="{{ mix('assets/css/admin_panel.css') }}">
@endsection
@section('content')
    <div class="container-fluid page__container">
        <div class="animated fadeIn main-table">
            @include('flash::message')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header page-header flex-wrap">
                            <div class="pull-left page__heading me-3 my-2">
                                {{ __('messages.group.groups') }}
                            </div>
                            <a href="{{ route('group.create') }}"
                               class="my-2 pull-right btn btn-primary ms-auto">{{ __('messages.group.create_group') }}</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-borderless table-responsive-sm table-responsive-lg table-responsive-md table-responsive-xl" id="groups-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.group.center') }}</th>
                                        <th>{{ __('messages.group.number') }}</th>
                                        <th>{{ __('messages.name') }}</th>
                                        <th>{{ __('messages.group.image') }}</th>
                                        <th>{{ __('messages.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <div class="pull-right me-3">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page_js')
    <script type="text/javascript" src="{{ asset('js/dataTable.min.js') }}"></script>
    <script src="{{ mix('assets/js/jquery.toast.min.js') }}"></script>
@endsection
@section('scripts')
    <script type="text/javascript" src="{{ asset('assets/js/custom-datatables.js') }}"></script>
    <script>
        let token = '{{ csrf_token() }}'
        let AuthUserRoleId = "{{ isset(getLoggedInUser()->roles) ? getLoggedInUser()->roles->first()->id : '' }}"
    </script>
    
    <script src="{{ mix('assets/js/custom.js') }}"></script>

    <script>
        $(document).ready(function () {
            let roleTable = $('#groups-table').DataTable({
                processing: true,
                serverSide: true,
                'order': [[2, 'asc']],
                ajax: {
                    url: route('group.index'),
                },
                columnDefs: [
                    {
                        'targets': [-1, -2],
                        'orderable': false,
                    },
                ],
                columns: [
                    {
                        data: 'center.name',
                        name: 'center.name',
                    },
                    {
                        data: 'number',
                        name: 'number',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'photo_url',
                        name: 'photo_url',
                        render: function(data) {
                            return `<img src="${data}" style="border-radius: 50%; height: 40px; min-width: 40px; width: 40px;" />`
                        }
                    },
                    {
                        data: function (row) {
                            return `<div class="d-flex justify-content-center align-items-center"> <a title="Edit" class="index__btn btn btn-ghost-success btn-sm edit-btn mr-1" href="${route('group.edit',row.id)}">
                                <i class="cui-pencil action-icon"></i></a>
                                <button title="Delete" class="index__btn btn btn-ghost-danger btn-sm delete-btn" data-id="${row.id}"><i class="cui-trash action-icon"></i></button>
                                <div>
                                `;
                        },
                        name: 'id',
                    },
                ],
            });


            const swalDelete = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-danger mr-2 btn-lg',
                    cancelButton: 'btn btn-secondary btn-lg',
                },
                buttonsStyling: false,
            });

            // open delete confirmation model
            $(document).on('click', '.delete-btn', function (event) {
                let roleId = $(this).data('id');
                swalDelete.fire({
                    title: Lang.get('messages.placeholder.are_you_sure'),
                    html: Lang.get('messages.placeholder.delete_group'),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: route('group.destroy',roleId),
                            type: 'DELETE',
                            dataType: 'json',
                            success: function (obj) {
                                displayToastr(
                                    'Success', 'success', 'Group is deleted successfully.',
                                );
                                if (obj.success) {
                                    if ($('#groups-table').DataTable().data().count() == 1) {
                                        $('#groups-table').DataTable().page('previous').draw('page');
                                    } else {
                                        $('#groups-table').DataTable().ajax.reload(null, false);
                                    }
                                }
                            },
                            error: function (data) {
                                displayToastr('Error', 'error',
                                    data.responseJSON.message);
                            },
                        });
                    }
                });
            });
        });

    </script>

@endsection

