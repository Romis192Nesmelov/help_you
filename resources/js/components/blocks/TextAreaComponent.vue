<template>
    <div :id="id" class="form-group text-area">
        <label>{{ label }}</label>
        <textarea
            :name="name"
            :class="'form-control' + (error ? ' error' : '' )"
            :placeholder="placeholder"
            :minlength="min"
            :maxlength="max"
            @input="$emit('update:value', $event.target.value)"
        >{{ value }}</textarea>
        <div class="error" v-if="error">{{ error }}</div>
    </div>
</template>

<script>
export default {
    name: "TextAreaComponent",
    props: ['id', 'label','name','placeholder','value','min','max','error']
}
</script>
