<template>
    <div class="modal fade" tabindex="-1" :id="id" :aria-labelledby="id+'Label'" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5 text-center w-100" v-if="head">{{ head }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ButtonComponent from "./ButtonComponent.vue";

export default {
    name: "ModalComponent",
    components: {ButtonComponent},
    props: {
        id: String,
        head: String|Boolean
    }
}
</script>
