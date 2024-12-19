<div class="blog" data-blog_id="{{ $blog->id }}">
    <div class="blog-header">
        @if(isset($blog->photo_url) && !empty($blog->photo_url))
            <img class="blogger-avatar" src="{{ asset('uploads/users/'.$blog->photo_url) }}">
        @else
            @if($blog->gender)
                <img class="blogger-avatar" src="{{ asset('assets/icons/male.png') }}">
            @else
                <img class="blogger-avatar" src="{{ asset('assets/icons/female.png') }}">
            @endif
        @endif
        <span class="blogger-name"> {{ $blog->name }} </span>
        <span class="blog-timestamp"> {{ timeDiff($blog->created_at) }} </span>
        <span title="Archive" class="archive-blog-btn" data-blog_id="{{ $blog->id }}">
            <i class="fa fa-archive action-icon"></i>
        </span>
    </div>
    <div class="blog-body">
        <div class="blog-description">
            {{ $blog->description }}
        </div>
        <img class="blog-image" src="{{ asset('uploads/blogs/'.$blog->blog_image) }}">
    </div>
</div>