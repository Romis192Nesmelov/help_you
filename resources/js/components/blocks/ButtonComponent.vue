<template>
    <button
        :id="id"
        type="button"
        :class="class_name"
        :data-bs-toggle="target ? 'modal' : ''"
        :data-bs-target="target"
        :disabled=disabled
        :data-bs-dismiss="dismiss ? 'modal' : ''"
        :data-dismiss="dismiss"
    >
        <i v-if="icon" :class="icon"></i>
        <span>{{ text }}</span>
    </button>
</template>

<script>
export default {
    name: "ButtonComponent",
    props: {
        'id': String|Boolean,
        'class_name': String,
        'target': String|Boolean,
        'dismiss': Boolean,
        'disabled': Boolean,
        'icon': String|Boolean,
        'text': String
    }
}
</script>
