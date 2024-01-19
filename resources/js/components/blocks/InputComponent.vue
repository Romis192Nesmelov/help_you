<template>
    <div class="form-group has-label mt-2">
        <i :class="icon" v-if="icon.length"></i>
        <label>{{ label }}</label>
        <input
            :type="type"
            :name="name"
            :value="value"
            :class="'form-control has-icon' + (error ? ' error' : '' )"
            :placeholder="placeholder"
            :minlength="min"
            :maxlength="max"
            @input="$emit('update:value', $event.target.value)"
        >
        <div class="error" v-if="error">{{ error }}</div>
    </div>
</template>

<script>
export default {
    name: "InputComponent",
    props: ['label','icon','type','name','placeholder','value','min','max','error']
}
</script>
