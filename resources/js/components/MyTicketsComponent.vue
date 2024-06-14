<template>
    <ModalComponent id="ticket-close-confirm-modal" head="Закрыть запрос">
        <h4 class="text-center">Вы действительно хотите закрыть этот запрос?</h4>
        <ModalPairButtonsComponent @click-yes="closeTicket"></ModalPairButtonsComponent>
    </ModalComponent>

    <ModalComponent id="new-ticket-modal" head="Новое обращение в техподдержку">
        <div class="row">
            <div class="col-12 col-lg-9 col-md-8">
                <InputComponent
                    label="Тема обращения"
                    type="text"
                    name="subject"
                    v-model:value="subject"
                    :error="errors.subject"
                ></InputComponent>
                <TextAreaComponent
                    label="Описание проблемы"
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
                ></InputImageComponent>
            </div>
            <div class="modal-footer justify-content-center">
                <ButtonComponent
                    class="btn btn-primary w-25 mt-3"
                    data-bs-dismiss='modal'
                    text="Закрыть"
                ></ButtonComponent>
                <ButtonComponent
                    class="btn btn-primary w-25 mt-3"
                    text="Отправить"
                    data-bs-dismiss='modal'
                    @click="newTicket"
                ></ButtonComponent>
            </div>
        </div>
    </ModalComponent>

    <div id="my-tickets" class="col-12 col-lg-8 right-block">
        <div class="rounded-block tall">
            <div class="row">
                <div class="col-12 col-lg-6 col-md-8 d-flex align-items-center">
                    <h2>Мои обращения в техподдержку</h2>
                </div>
                <div class="col-12 col-lg-6 col-md-4 d-flex align-items-center justify-content-end">
                    <ButtonComponent
                        class="w-70 btn btn-primary"
                        icon="icon-new-tab2"
                        text="Создать обращение"
                        data-bs-dismiss='modal'
                        target="#new-ticket-modal"
                    ></ButtonComponent>
                </div>
            </div>
            <div class="h-100 without-tabs d-flex flex-column justify-content-between" v-if="tickets.length">
                <div class="row pt-2">
                    <div class="col-lg-6 col-sm-12 mb-1" v-for="ticket in tickets" :key="ticket.id">
                        <div class="item-in-list text-center p-3">
                            <div class="fs-6"><small>Дата создания: {{ new Date(ticket.created_at).toLocaleDateString('ru-RU') }}</small></div>
                            <hr class="mt-1 mb-1">
                            <div class="h6 fw-bold mb-0">«{{ cropContent(ticket.subject,30) }}»</div>
                            <hr class="mt-1 mb-1">
                            <div :class="'status-label ' + (ticket.status ? 'bg-success' : 'bg-danger')">{{ ticket.status ? 'Закрыто' : 'В работе' }}</div>
                            <hr class="mt-1 mb-0">
                            <div class="fs-6">
                                <small>
                                    <span>Всего сообщений:<b>{{ ticket.answers }}</b></span> | <span>Новых сообщений:<b>{{ ticket.new_answers }}</b></span>
                                </small>
                            </div>
                            <hr class="mt-0 mb-2">
                            <div class="w-100 d-flex justify-content-center">
                                <a class="w-50 me-1" :href="my_tickets_url + '?id=' + ticket.id">
                                    <ButtonComponent
                                        class="btn btn-primary w-100 m-auto"
                                        text="Перейти к ответам"
                                    ></ButtonComponent>
                                </a>
                                <ButtonComponent
                                    v-if="!ticket.status"
                                    class="btn btn-primary w-50 m-auto"
                                    text="Закрыть запрос"
                                    @click="closeTicketConfirm(ticket.id)"
                                ></ButtonComponent>
                            </div>
                        </div>
                    </div>
                </div>
                <PaginatorComponent
                    :links="links"
                    tab_key="tickets"
                    @paginate="changePage"
                ></PaginatorComponent>
            </div>
            <NoDataComponent v-else></NoDataComponent>
        </div>
    </div>
</template>

<script>
import ModalComponent from "./blocks/ModalComponent.vue";
import ModalPairButtonsComponent from "./blocks/ModalPairButtonsComponent.vue";
import InputComponent from "./blocks/InputComponent.vue";
import InputImageComponent from "./admin/blocks/InputImageComponent.vue";
import TextAreaComponent from "./blocks/TextAreaComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";
import NoDataComponent from "./blocks/NoDataComponent.vue";
import PaginatorComponent from "./blocks/PaginatorComponent.vue";

export default {
    name: "MyTicketsComponent",
    components: {
        ModalComponent,
        ModalPairButtonsComponent,
        InputComponent,
        InputImageComponent,
        TextAreaComponent,
        ButtonComponent,
        NoDataComponent,
        PaginatorComponent
    },
    created() {
        let self = this;

        this.getTickets(this.get_tickets_url);
        this.userId = parseInt(this.user_id);
        window.Echo.private('ticket_' + this.userId).listen('.ticket', res => {
            if (res.notice === 'new_item' || res.notice === 'del_item') self.getTickets(self.get_tickets_url);
            else {
                let key = self.findTicket(res.ticket.id);
                if (key) self.tickets[key] = self.getTicketProps(red.ticket)
            }
        });
    },
    data() {
        return {
            userId: Number,
            tickets: [],
            links: [],
            subject: '',
            text: '',
            closingTicket: 0,
            errors: {
                image: null,
                subject: null,
                text: null,
            },
        }
    },
    props: {
        'user_id': String,
        'my_tickets_url': String,
        'get_tickets_url': String,
        'new_ticket_url': String,
        'close_ticket_url': String
    },
    methods: {
        getTickets(url) {
            let self = this;
            axios.get(url)
                .then(function (response) {
                    self.tickets = [];
                    $.each(response.data.tickets.data, function (k,ticket) {
                        self.tickets.push(self.getTicketProps(ticket));
                    });
                    self.links = response.data.tickets.links;
                }).catch(function (error) {
                    console.log(error);
                });
        },
        newTicket() {
            let self = this,
                formData = new FormData(),
                inputImage = $('input[name=image]');

            formData.append('_token', window.tokenField);
            if (inputImage.val()) formData.append('image', inputImage[0].files[0]);
            formData.append('subject', this.subject);
            formData.append('text', this.text);

            axios.post(this.new_ticket_url, formData)
                .then(function (response) {
                    self.getTickets(self.get_tickets_url);
                    window.showMessage(response.data.message);
                }).catch(function (error) {
                    console.log(error);
                });
        },
        closeTicketConfirm(ticketId) {
            this.closingTicket = ticketId;
            $('#ticket-close-confirm-modal').modal('show');
        },
        closeTicket() {
            let self = this;
            axios.post(this.close_ticket_url, {
                _token: window.tokenField,
                ticket_id: this.closingTicket
            }).then(function (response) {
                for (let i=0;i<self.tickets.length;i++) {
                    if (self.tickets[i].id === self.closingTicket) {
                        self.tickets[i].status = 1;
                        break;
                    }
                }
                window.showMessage(response.data.message);
            }).catch(function (error) {
                // console.log(error);
            });
        },
        cropContent(string,max) {
            return window.cropContent(string,max);
        },
        changePage(params) {
            this.getTickets(params.url)
        },
        findTicket(ticketId) {
            let key = 0;
            for (let i=0;i<this.tickets.length;i++) {
                if (this.tickets[i].id === ticketId) {
                    key = i;
                    break;
                }
            }
            return key;
        },
        getTicketProps(ticket) {
            let newAnswers = 0;
            for (let i=0;i<ticket.answers.length;i++) {
                if (!ticket.answers[i].read_owner) newAnswers++;
            }
            return {
                id:ticket.id,
                status:ticket.status,
                subject:ticket.subject,
                created_at:ticket.created_at,
                answers:ticket.answers.length,
                new_answers:newAnswers
            };
        }
    }
}
</script>
