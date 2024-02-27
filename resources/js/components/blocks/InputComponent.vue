<template>
    <div class="form-group has-label mt-2">
        <i :class="icon" v-if="icon"></i>
        <label v-if="label">{{ label }}</label>
        <input
            :type="type"
            :name="name"
            :value="value"
            :class="'form-control' + (icon ? ' has-icon': '') + (error ? ' error' : '' )"
            :placeholder="placeholder"
            :minlength="min"
            :maxlength="max"
            :min="min"
            :max="max"
            @input="$emit('update:value', $event.target.value)"
        >
        <div class="error" v-if="error">{{ error }}</div>
    </div>
</template>

<script>
export default {
    name: "InputComponent",
    props: {
        'label': String,
        'icon': String,
        'type': String,
        'name': String,
        'placeholder':String,
        'value': String|Number,
        'min': String|Number,
        'max': String|Number,
        'error': String
    }
}
</script>
