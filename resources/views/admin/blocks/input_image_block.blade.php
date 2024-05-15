<div class="panel panel-flat">
    @if (isset($head) && $head)
        <div class="panel-heading">
            <div class="panel-title">{{ $head }}</div>
        </div>
    @endif
    <div class="panel-body edit-image-preview">
        <a class="fancybox" href="{{ asset(isset($image) && $image ? $image : 'images/placeholder.jpg') }}">
            <img class="w-100" src="{{ asset(isset($image) && $image ? $image : 'images/placeholder.jpg') }}?{{ md5(rand(1,100000)*time()) }}" />
        </a>
        @include('admin.blocks.input_file_block', ['label' => '', 'name' =>  isset($name) && $name ? $name : 'image'])
    </div>
</div>
