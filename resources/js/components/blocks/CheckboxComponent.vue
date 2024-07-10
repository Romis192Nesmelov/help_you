<template>
    <div class="form-check form-switch mt-3">
        <label class="checkbox-inline">
            <input
                class="form-check-input"
                :id="id"
                type="checkbox"
                :name="name"
                :checked="checked"
                @input="$emit('update:checked', $event.target.checked)"
            >
            <label class="form-check-label" :for="id">{{ label }}</label>
        </label>
    </div>
</template>

<script>
export default {
    props: ['id','name','value','checked','label'],
    name: "CheckboxComponent",
}
</script>
