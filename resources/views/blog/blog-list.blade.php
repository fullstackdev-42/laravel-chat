@if(count($blogs) == 0)
    <div class="no-blogs"> No blogs yet </div>
@else
    @foreach ($blogs as $key => $blog)
        @include('blog.blog', ['key' => $key, 'blog' => $blog])
    @endforeach
@endif