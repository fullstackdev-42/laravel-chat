@extends('layouts.app')
@section('title')
    {{ __('messages.blogs') }}
@endsection
@section('page_css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/blog.css') }}"/>
@endsection
@section('content')
    <div class="blog-container">
        <div class="animated fadeIn main-table">
            @include('flash::message')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card full-height">
                        <div class="card-header page-header d-flex align-items-sm-center align-items-start flex-sm-row flex-column">
                            <div class="d-inline-block align-items-center justify-content-between">
                                <div class="pull-left page__heading me-3 my-2">
                                    {{ __('messages.blogs') }}
                                </div>
                            </div>
                            <div class="d-inline-block filter-container align-self-sm-center align-self-end ms-auto">
                                @if(count($centers) > 0 || count($groups) > 0 || count($users) > 0)
                                    <button type="button"
                                        class="my-2 btn btn-primary mr-20 filter-container__btn ms-sm-0 ms-auto"
                                        data-bs-toggle="modal"
                                        data-bs-target="#blog_search_modal"> {{ __('messages.Filter') }} </button>
                                @endif
                                @if(Auth::user()->hasRole('Member'))
                                    <button type="button"
                                        class="my-2 pull-right btn btn-primary new-blog-btn filter-container__btn ms-sm-0 ms-auto"
                                        data-bs-toggle="modal"
                                        data-bs-target="#create_blog_modal">{{ __('messages.new_blog') }}</button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="blog-list-container">
                                <div id="content-loader">
                                    <div class="loader-demo-box">
                                        <div class="jumping-dots-loader">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="blog-list" data-filter_type="", data-filter_id="">
                                    @include('blog.blog-list', ['blogs' => $blogs])
                                </div>
                                <div class="load-more-blogs">
                                    <div class="loader-demo-box">
                                        <div class="jumping-dots-loader">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="create_blog_modal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.new_blog') }}</h4>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form method="POST" action="{{ route('blogs/create-blog') }}" enctype="multipart/form-data" id="blog-form">
                                @csrf
                                <div class="form-group">
                                    <label for="upload-blog-image-btn" class="btn btn-primary form-control"> Choose Blog Image </label>
                                    <input type="file" id="upload-blog-image-btn" name="blog-image" accept="image/*" hidden/>
                                    <img id="blog-image-preview" class="mt-3">
                                </div>
                                <div class="form-group">
                                    <textarea id="description" class="form-control user__bio login-group__input" name="description" rows="5" placeholder="{{ __('messages.describe_blog') }}"></textarea>
                                </div>
                                <!-- Submit Field -->
                                <div class="form-group">
                                    {{ Form::button(__('messages.save') , ['type'=>'submit','class' => 'btn btn-primary','id'=>'createBtnSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> " .__('messages.processing')]) }}
                                    <button type="button" class="btn btn-secondary ms-1" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(count($centers) > 0 || count($groups) > 0 || count($users) > 0)
        <div id="blog_search_modal" class="modal fade" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            {{ __('messages.Filter') }}
                            @if(count($centers) > 0)
                            {{ __('messages.centers') }}
                            @endif
                            @if(count($groups) > 0)
                            / {{ __('messages.group.groups') }}
                            @endif
                            @if(count($users) > 0)
                            / {{ __('messages.users') }}
                            @endif
                        </h4>
                        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="blog-filter-wrapper">
                                    <div class="blog-search-wrapper">
                                        <div class="blog-search clearfix">
                                            <i class="fa fa-search"></i>
                                            <input type="search" placeholder="Search" class="blog-search-input" id="searchBlogInput">
                                        </div>
                                    </div>
                                    <ul class="nav nav-tabs blog-tab-nav mb-1 border-bottom-0" id="blogTabs">
                                        @if(count($centers) > 0)
                                            <li class="nav-item">
                                                <a data-bs-toggle="tab" data-type="centers" class="nav-link login-group__sub-title" href="#"> {{ __('messages.centers') }} </a>
                                            </li>
                                        @endif
                                        @if(count($groups) > 0)
                                            <li class="nav-item">
                                                <a data-bs-toggle="tab" data-type="groups" class="nav-link login-group__sub-title" href="#"> {{ __('messages.group.groups') }} </a>
                                            </li>
                                        @endif
                                        @if(count($users) > 0)
                                            <li class="nav-item">
                                                <a data-bs-toggle="tab" data-type="users" class="nav-link login-group__sub-title" href="#"> {{ __('messages.users') }} </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <div class="filter-list-wrapper">
                                        @if(count($centers) > 0)
                                            <ul class="list-group-item centers" style="display: none;">
                                                @foreach ($centers as $center)
                                                    <li data-type="center" data-id="{{ $center->id }}">
                                                        <img class="blogger-avatar" src="{{ getUserImageInitial($center->id, $center->name) }}">
                                                        <span> {{ $center->name }} </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        @if(count($groups) > 0)
                                            <ul class="list-group-item groups" style="display: none;">
                                                @foreach ($groups as $group)
                                                    <li data-type="group" data-id="{{ $group->id }}">
                                                        @if(isset($group->photo_url) && !empty($group->photo_url))
                                                            <img class="blogger-avatar" src="{{ $group->photo_url }}">
                                                        @else
                                                            <img class="blogger-avatar" src="{{ getUserImageInitial($group->id, $group->name) }}">
                                                        @endif
                                                        <span> {{ $group->name }} </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        @if(count($users) > 0)
                                            <ul class="list-group-item users" style="display: none;">
                                                @foreach ($users as $user)
                                                    <li data-type="user" data-id="{{ $user->id }}">
                                                        @if(isset($user->photo_url) && !empty($user->photo_url))
                                                            <img class="blogger-avatar" src="{{ $user->photo_url }}">
                                                        @endif
                                                        <span>
                                                            {{ $user->name }}
                                                            @if($user->id == Auth::user()->id)
                                                                (Me)
                                                            @endif
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('scripts')
    <!--custom js-->
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(window).on('load', function(){
            $("#content-loader").fadeOut();
        });
        $(document).ready(() => {
            $("#upload-blog-image-btn").change(function() {
                $("#upload-blog-image-btn").removeClass("is-invalid");
                $("#upload-blog-image-btn").next().remove("span");
                const file = this.files[0];
                if (file) {
                    const fileType = file["type"];
                    const validImageTypes = ['image/gif', 'image/jpeg', 'image/png', 'image/webp', 'image/bmp'];
                    if (validImageTypes.includes(fileType)) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $("#blog-image-preview").attr("src", e.target.result);
                        }
                        reader.readAsDataURL(file);
                    } else {
                        $("#blog-image-preview").attr("src", "");
                        $("#upload-blog-image-btn").val('');
                        $("#upload-blog-image-btn").addClass("is-invalid");
                        $("#upload-blog-image-btn").after('<span class="invalid-feedback" role="alert"><strong> Accepted blog image formats include gif, jpg, jpeg, webp, bmp and png. </strong></span>');
                    }
                } else {
                    $("#blog-image-preview").attr("src", "");
                    $("#upload-blog-image-btn").val('');
                    $("#upload-blog-image-btn").addClass("is-invalid");
                    $("#upload-blog-image-btn").after('<span class="invalid-feedback" role="alert"><strong> Please choose a blog image. </strong></span>');
                }
            });

            $("form#blog-form").submit(function(e) {
                e.preventDefault();
                var has_error = false;
                //form validation
                $(".is-invalid").removeClass("is-invalid");
                $(".invalid-feedback").remove();
                try {
                    const file = $("#upload-blog-image-btn")[0].files[0];
                    if (file) {
                        const fileType = file["type"];
                        const validImageTypes = ['image/gif', 'image/jpeg', 'image/png', 'image/webp', 'image/bmp'];
                        if (!validImageTypes.includes(fileType)) {
                            $("#blog-image-preview").attr("src", "");
                            $("#upload-blog-image-btn").val('');
                            $("#upload-blog-image-btn").addClass("is-invalid");
                            $("#upload-blog-image-btn").after('<span class="invalid-feedback" role="alert"><strong> Accepted blog image formats include gif, jpg, jpeg, webp, bmp and png. </strong></span>');
                            has_error = true;
                        }
                    } else {
                        $("#blog-image-preview").attr("src", "");
                        $("#upload-blog-image-btn").val('');
                        $("#upload-blog-image-btn").addClass("is-invalid");
                        $("#upload-blog-image-btn").after('<span class="invalid-feedback" role="alert"><strong> Please choose a blog image. </strong></span>');
                        has_error = true;
                    }
                } catch(err) {
                    $("#blog-image-preview").attr("src", "");
                    $("#upload-blog-image-btn").val('');
                    $("#upload-blog-image-btn").addClass("is-invalid");
                    $("#upload-blog-image-btn").after('<span class="invalid-feedback" role="alert"><strong> Please choose a blog image. </strong></span>');
                    has_error = true;
                }
                if(!has_error) {
                    var formData = new FormData(this);
                    $.ajax({
                        url: "{{ route('blogs/create-blog') }}",
                        type: 'POST',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            if(response.status) {
                                $(".no-blogs").remove();
                                var html = response.html;
                                $('.blog-list').prepend(html);
                                displayToastr('Success', 'success', response.message);
                                $('#create_blog_modal').modal('hide');
                                $('#blog-form').trigger("reset");
                                $('#blog-image-preview').attr('src', '');
                            } else {
                                displayToastr('Error', 'error', response.message);
                            }
                        }
                    });
                } else {
                    console.log("ddd");
                }
            });

            $(window).scroll(function() {//detect page scroll
                if($(window).scrollTop() + $(window).height() >= $(document).height()) {
                    var last_blog_id = $(".blog-list .blog").last().data("blog_id");
                    if(last_blog_id) {
                        var filter_type = $(".blog-list").data("filter_type");
                        var filter_id = $(".blog-list").data("filter_id");
                        $.ajax({
                            url: "{{ route('blogs/expand-blogs') }}",
                            type: 'POST',
                            data: {
                                type: filter_type,
                                id: filter_id,
                                last_blog_id: last_blog_id
                            },
                            success: function (response) {
                                if(response.status) {
                                    if(response.html != "") {
                                        $(".load-more-blogs").show();
                                        $(".blog-list").append(response.html);
                                        setTimeout(
                                            function() {
                                                $(".load-more-blogs").hide();
                                            }, 1000
                                        );
                                    }
                                }
                            }
                        });
                    }
                }
            });

            $(document).on("click", ".archive-blog-btn", function(e) {
                e.preventDefault();
                var that = this;
                var blog_id = $(this).data("blog_id");
                $.ajax({
                    url: "{{ route('blogs/delete-blog') }}",
                    type: 'POST',
                    data: {
                        blog_id: blog_id
                    },
                    success: function (response) {
                        if(response.status) {
                            $(that).closest(".blog").fadeOut(300, function() {$(this).remove();});
                            displayToastr('Success', 'success', response.message);
                        } else {
                            displayToastr('Error', 'error', response.message);
                        }
                    }
                });
            });

            function generateSearchList() {
                $("#searchBlogInput").val("");
                $(".filter-list-wrapper .list-group-item").hide();
                var active_tab = $(".blog-tab-nav .nav-item a.nav-link.active").data("type");
                
                if(active_tab == "centers") {
                    $(".filter-list-wrapper .list-group-item.centers").show();
                } else if(active_tab == "groups") {
                    $(".filter-list-wrapper .list-group-item.groups").show();
                } else if(active_tab == "users") {
                    $(".filter-list-wrapper .list-group-item.users").show();
                }
            }

            $('#blog_search_modal').on('shown.bs.modal', function() {
                $(".blog-tab-nav .nav-item a.nav-link").removeClass("active");
                $(".blog-tab-nav .nav-item:first a.nav-link").addClass("active");
                generateSearchList();
            });

            $(document).on("click", ".blog-tab-nav .nav-item a.nav-link", function(e) {
                generateSearchList();
            });

            $("#searchBlogInput").on("input", function() {
                var active_tab = $(".blog-tab-nav .nav-item a.nav-link.active").data("type");
                var search_keyword = $(this).val().toUpperCase();
                if(active_tab == "centers") {
                    var lists = $(".filter-list-wrapper .list-group-item.centers li span");
                } else if(active_tab == "groups") {
                    var lists = $(".filter-list-wrapper .list-group-item.groups li span");
                } else if(active_tab == "users") {
                    var lists = $(".filter-list-wrapper .list-group-item.users li span");
                }
                lists.each(function(index) {
                    if($(this).text().toUpperCase().indexOf(search_keyword) >= 0) {
                        $(this).parent().show();
                    } else {
                        $(this).parent().hide();
                    };
                });
            });

            $(document).on("click", ".list-group-item li", function(e) {
                e.preventDefault();
                $('#blog_search_modal').modal('hide');
                $("#content-loader").show();

                var type = $(this).data("type");
                var id = $(this).data("id");
                $.ajax({
                    url: "{{ route('blogs/filter-blogs') }}",
                    type: 'POST',
                    data: {
                        type: type,
                        id: id
                    },
                    success: function (response) {
                        if(response.status) {
                            $(".blog-list").data({"filter_type": type, "filter_id": id});
                            $(".blog-list").html(response.html);
                            $("#content-loader").fadeOut();
                        }
                    }
                });
            });
        });
    </script>
@endsection
