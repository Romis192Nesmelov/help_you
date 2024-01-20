<template>
    <div
        :class="`avatar cir${this.avatar_error ? ' error' : ''}`"
        :style="`
            background-image:url(/${this.avatar_image ? this.avatar_image : 'images/def_avatar.svg'});
            ${this.avatar_props['background-position-x'] ? 'background-position-x:'+(this.avatar_props['background-position-x'] * this.avatar_coof)+'px' : ''};
            ${this.avatar_props['background-position-y'] ? 'background-position-y:'+(this.avatar_props['background-position-y'] * this.avatar_coof)+'px' : ''};
            ${this.avatar_props['background-size'] ? 'background-size:'+(this.avatar_props['background-size']) : ''};
        `"
    >
        <img v-if="allow_change_avatar" :src="input_image_hover" />
        <input v-if="allow_change_avatar" type="file" name="avatar" @change="$emit('onloadAvatar')">
    </div>
</template>
<script>
export default {
    name: "AvatarComponent",
    props: {
        'avatar_image': String|NaN,
        'avatar_props': Object|NaN,
        'avatar_coof': Number,
        'avatar_error': String,
        'allow_change_avatar': Number,
        'input_image_hover': String,
    },
}
</script>
