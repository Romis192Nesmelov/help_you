<template>
    <div class="form-group mt-3">
        <label class="checkbox-inline">
            <input
                class="styled"
                :id="id"
                :checked="checked"
                type="checkbox"
                :name="name"
                @input="$emit('update:checked', $event.target.checked)"
            >
            {{ label }}
        </label>
    </div>
</template>

<script>
export default {
    name: "CheckboxComponent",
    props: ['id','name','value','checked','label'],
}
</script>
