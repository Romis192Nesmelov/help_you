<template>
    <ModalComponent id="confirm-delete-modal" head="Подтверждение удаления">
        <h2 class="w-100 text-center">{{ delete_phrase }}</h2>
        <div class="modal-footer">
            <button @click="$emit('clickYes')" type="button" class="btn btn-primary" data-bs-dismiss="modal" data-dismiss="modal">
                <i class="icon-checkmark4"></i><span>Да</span>
            </button>

            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-dismiss="modal">
                <i class="icon-cancel-circle2"></i><span>Нет</span>
            </button>
        </div>
    </ModalComponent>
</template>

<script>
import ModalComponent from "../../blocks/ModalComponent.vue";
import ButtonComponent from "../../blocks/ButtonComponent.vue";

export default {
    name: "DeleteItemModalComponent",
    components: {
        ModalComponent,
        ButtonComponent
    },
    props: {
        'delete_phrase': String
    }
}
</script>
