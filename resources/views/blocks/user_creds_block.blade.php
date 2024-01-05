<div class="w-100 d-flex mb-3 align-items-center">
    <div class="d-flex align-items-center justify-content-center">
        <div class="avatar cir" style="{!! avatarProps(isset($user) ? $user->avatar : null, isset($user) ? $user->avatar_props : null, 0.35) !!}"></div>
        <div class="ms-3">
            <div class="fs-lg-6 fs-sm-7 user-name">
                @if (isset($user))
                    @include('blocks.user_name_block', ['user' => $user])
                @endif
            </div>
            <div class="fs-lg-6 fs-sm-7 user-age">
                @if (isset($user))
                    {{ getUserAge($user) }}
                @endif
            </div>
            @if (isset($rating))
                <div class="mt-2">
                    @include('blocks.rating_line_block',['rating' => $rating])
                </div>
            @endif
        </div>
    </div>
</div>
