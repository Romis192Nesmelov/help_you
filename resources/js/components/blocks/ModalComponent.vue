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
                <div class="modal-footer d-flex justify-content-center" v-if="show_footer">
                    <ButtonComponent
                        v-if="yes_button_text"
                        class="btn btn-primary"
                        :text="yes_button_text"
                    ></ButtonComponent>
                    <ButtonComponent
                        v-if="no_button_text"
                        class="btn btn-primary w-25 mt-3"
                        :text="no_button_text"
                    ></ButtonComponent>
                    <ButtonComponent
                        v-if="show_close_button"
                        class="btn btn-primary w-50 m-auto mt-3"
                        text="Закрыть"
                    ></ButtonComponent>
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
        head: String|Boolean,
        show_close_button: Boolean,
        show_footer: Boolean,
        yes_button_text: String|Boolean,
        no_button_text: String|Boolean,
    }
}
</script>
