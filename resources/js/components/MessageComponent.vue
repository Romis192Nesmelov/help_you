<template>
    <ModalComponent id="message-modal" head="Сообщение">
        <h4 class="text-center p-4">{{ message }}</h4>
    </ModalComponent>
</template>

<script>
import ModalComponent from "./blocks/ModalComponent.vue";

export default {
    name: "MessageComponent",
    components: {
        ModalComponent
    },
    data() {
        return {
            message: ''
        }
    },
}
</script>
