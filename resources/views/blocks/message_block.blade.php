<div class="message-block">
    @include('blocks.avatar_message_block')
    <div class="message">
        <div class="author">{{ $message->user->name.' '.$message->user->family }}<span>{{ $message->created_at->format('H:m') }}</span></div>
        <div>{{ $message->body }}</div>
    </div>
</div>

