<template>
    <button
        :id="id"
        type="button"
        :class="bclass"
        :data-bs-toggle="toggle"
        :data-bs-target="dtarget"
        :disabled="disabled"
        :data-bs-dismiss="dismiss"
        :data-dismiss="dismiss"
    >
        <i :class="icon"></i>
        <span>{{ btext }}</span>
    </button>
</template>

<script>
export default {
    props: ['id','bclass','toggle','dtarget','dismiss','disabled','icon','btext'],
    name: "ButtonComponent"
}
</script>
