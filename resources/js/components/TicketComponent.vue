<template>
    <ModalComponent id="new-answer-modal" :head="'Новый ответ на запрос «' + ticket.subject + '»'">
        <div class="row">
            <div class="col-12 col-lg-9 col-md-8">
                <TextAreaComponent
                    label="Ответ"
                    name="text"
                    :value="text"
                    v-model:value="text"
                    :error="errors.text"
                    @change="errors.text=null"
                ></TextAreaComponent>
            </div>
            <div class="col-12 col-lg-3 col-md-4 d-flex align-items-end pt-3">
                <InputImageComponent
                    name="image"
                    placeholder_image="/images/input_image_hover.svg"
                    :error="errors.image"
                    @change="errors.image=null"
                ></InputImageComponent>
            </div>
            <div class="modal-footer justify-content-center mt-3">
                <ButtonComponent
                    class="btn btn-primary w-25 mt-3"
                    data-bs-dismiss='modal'
                    text="Закрыть"
                ></ButtonComponent>
                <ButtonComponent
                    class="btn btn-primary w-25 mt-3"
                    text="Отправить"
                    :disabled="disabledSubmit"
                    @click="newAnswer"
                ></ButtonComponent>
            </div>
        </div>
    </ModalComponent>

    <div id="ticket-answers" class="col-12 col-lg-8 right-block">
        <div class="rounded-block tall pt-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-3">
                    <a :href="back_url"><i class="icon-arrow-left52 nav-link p-0 fs-2 me-3"></i></a>
                    <h2 class="mb-0">{{ cropContent(ticket.subject,55) }}</h2>
                </div>
                <ButtonComponent
                    v-if="!ticket.status"
                    class="btn btn-primary mt-sm-3 mb-sm-3"
                    icon="icon-megaphone"
                    text="Написать ответ"
                    data-bs-dismiss='modal'
                    target="#new-answer-modal"
                ></ButtonComponent>
            </div>
            <div class="h-100 without-tabs d-flex flex-column justify-content-between">
                <div class="content-block simple">
                    <div id="messages" class="answers">
                        <div v-for="(answer, k) in answers">
                            <div :class="'message-row' + (answer.user.id === userId ? ' my-self' : '')">
                                <div class="message-block">
                                    <AnswerComponent :answer="answer"></AnswerComponent>
                                    <div class="w-25">
                                        <a v-if="answer.image" :href="'/' + answer.image" class="fancybox">
                                            <img class="w-100" :src="'/' + answer.image" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ModalComponent from "./blocks/ModalComponent.vue";
import InputImageComponent from "./admin/blocks/InputImageComponent.vue";
import TextAreaComponent from "./blocks/TextAreaComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";
import AnswerComponent from "./blocks/AnswerComponent.vue";

export default {
    name: "TicketComponent",
    components: {
        ModalComponent,
        InputImageComponent,
        TextAreaComponent,
        ButtonComponent,
        AnswerComponent
    },
    created() {
        let self = this;
        this.userId = parseInt(this.user_id);
        this.ticket = JSON.parse(this.incoming_ticket);
        this.answers = this.ticket.answers;
        this.answers.unshift({
            id: 0,
            image: this.ticket.image,
            text: this.ticket.text,
            created_at: this.ticket.created_at,
            user: this.ticket.user
        });
        window.bindFancybox();
        self.scrollBottom();

        window.Echo.private('ticket_' + this.userId).listen('.ticket', res => {
            if (res.notice === 'del_item') window.location.href = this.back_url;
            else {
                self.answers[0] = {
                    image: res.ticket.image,
                    text: res.ticket.text,
                    user: res.ticket.user
                }
            }
        });

        window.Echo.private('answer_' + this.userId).listen('.answer', res => {
            if (res.answer.ticket.id === self.ticket.id) {
                if (res.notice === 'new_item') {
                    self.answers.push(res.answer);
                    if (res.answer.image) window.bindFancybox();
                    self.scrollBottom();
                } else if (res.notice === 'edit_item') {
                    let key = window.findSomething(self.answers, res.answer.id);
                    self.answers[key] = res.answer;
                } else {
                    let key = window.findSomething(self.answers, res.answer.id);
                    self.answers.splice(key, 1);
                }
            }
        });
    },
    data() {
        return {
            userId: Number,
            ticket: {
                image: null,
                subject: '',
                text: '',
            },
            answers: [],
            text: '',
            disabledSubmit: false,
            errors: {
                image: null,
                text: null,
            },
        }
    },
    props: {
        'user_id': String,
        'incoming_ticket': String,
        'new_answer_url': String,
        'back_url': String
    },
    methods: {
        newAnswer() {
            let self = this,
                formData = new FormData(),
                inputImage = $('input[name=image]');

            this.disabledSubmit = true;

            formData.append('_token', window.tokenField);
            formData.append('ticket_id', this.ticket.id);
            if (inputImage.val()) formData.append('image', inputImage[0].files[0]);
            formData.append('text', this.text);

            axios.post(this.new_answer_url, formData)
                .then(function (response) {
                    $('#new-answer-modal').modal('hide');
                    window.showMessage(response.data.message);
                    self.disabledSubmit = false;
                }).catch(function (error) {
                    $.each(error.response.data.errors, (name,error) => {
                        self.errors[name] = error[0];
                        self.disabledSubmit = false;
                    });
                });
        },
        findAnswer(answerId) {
            let key = false;
            for (let i=0;i<this.answers.length;i++) {
                if (this.answers[i].id === answerId) {
                    key = i;
                    break;
                }
            }
            return key;
        },
        cropContent(string,max) {
            return window.cropContent(string,max);
        },
        scrollBottom() {
            setTimeout(() => {
                window.scrollBottomMessages();
            },500);
        },
        bindLastFancyBox() {
            setTimeout(() => {
                $('a.fancybox').last().fancybox(window.fancyBoxSettings);
            },300);
        },
    }
}
</script>
