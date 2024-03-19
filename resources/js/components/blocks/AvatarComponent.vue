<template>
    <div
        :class="`avatar cir ${this.small ? 'small' : ''} ${this.avatar_error ? 'error' : ''}`"
        :style="`
            background-image:url(/${this.avatar_image ? (this.avatar_image + '?' + getRandomString()) : 'images/def_avatar.svg'});
            ${this.avatar_props && this.avatar_props['background-position-x'] ? 'background-position-x:'+(this.avatar_props['background-position-x'] * this.avatar_coof)+'px' : ''};
            ${this.avatar_props && this.avatar_props['background-position-y'] ? 'background-position-y:'+(this.avatar_props['background-position-y'] * this.avatar_coof)+'px' : ''};
            ${this.avatar_props && this.avatar_props['background-size'] ? 'background-size:'+(this.avatar_props['background-size']) : ''};
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
        'small': Boolean,
        'avatar_image': String|NaN,
        'avatar_props': Object|NaN,
        'avatar_coof': Number,
        'avatar_error': String|NaN,
        'allow_change_avatar': Number,
        'input_image_hover': String|NaN,
    },
    methods: {
        getRandomString() {
            let result = '';
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            const charactersLength = characters.length;
            let counter = 0;
            while (counter < 10) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
                counter += 1;
            }
            return result;
        }
    }
}
</script>
