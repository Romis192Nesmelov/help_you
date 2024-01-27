<template>
    <div class="modal-footer justify-content-center">
        <ButtonComponent
            class="btn btn-primary w-25 mt-3"
            data-bs-dismiss='modal'
            text="Нет"
        ></ButtonComponent>
        <ButtonComponent
            class="btn btn-primary w-25 mt-3"
            text="Да"
            data-bs-dismiss='modal'
            @click="$emit('clickYes')"
        ></ButtonComponent>
    </div>
</template>

<script>
import ButtonComponent from "./ButtonComponent.vue";
export default {
    name: "ModalPairButtonsComponent",
    components: {
        ButtonComponent
    }
}
</script>
