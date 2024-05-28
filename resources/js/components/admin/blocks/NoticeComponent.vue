<template>
    <ul class="nav navbar-nav">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="icon-bubbles4"></i>
                <span class="visible-xs-inline-block position-right">Сообщения</span>
                <span v-if="notices.length" class="badge bg-warning-400">{{ notices.length + tickets.length + answers.length }}</span>
            </a>

            <div class="dropdown-menu dropdown-content width-350">
                <div class="dropdown-content-heading">Сообщения</div>

                <ul class="media-list dropdown-content-body">

                    <li class="media" v-for="ticket in tickets" :key="'ticket-' + ticket.id">
                        <div class="media-left">
                            <AvatarComponent
                                :small="true"
                                :avatar_image="ticket.user.avatar"
                                :avatar_props="ticket.user.avatar_props"
                            ></AvatarComponent>
                        </div>
                        <div class="media-body">
                            <span class="text-semibold"><UserNameComponent :user="ticket.user"></UserNameComponent></span>
                            <span class="media-annotation pull-right">{{ new Date(ticket.created_at).toLocaleDateString('ru-RU') }}</span>
                            <a :href="tickets_url + '?id=' + ticket.id" class="media-heading">
                                <span v-if="ticket.status === 0">Создал новое обращение в тех.поддержку: «{{ ticket.subject }}»</span>
                                <span v-else>Закрыл заявку «{{ ticket.subject }}»</span>
                            </a>
                        </div>
                    </li>

                    <li class="media" v-for="answer in answers" :key="'answer-' + answer.id">
                        <div class="media-left">
                            <AvatarComponent
                                :small="true"
                                :avatar_image="answer.user.avatar"
                                :avatar_props="answer.user.avatar_props"
                            ></AvatarComponent>
                        </div>
                        <div class="media-body">
                            <span class="text-semibold"><UserNameComponent :user="answer.user"></UserNameComponent></span>
                            <span class="media-annotation pull-right">{{ new Date(answer.created_at).toLocaleDateString('ru-RU') }}</span>
                            <a :href="answers_url + '?parent_id=' + answer.ticket.id + '&id=' + answer.id" class="media-heading">
                                Ответил в рамках заявки: «{{ answer.ticket.subject }}»
                            </a>
                        </div>
                    </li>

                    <li class="media" v-for="notice in notices" :key="'notice-' + notice.order.id">
                        <div class="media-left">
                            <AvatarComponent
                                :small="true"
                                :avatar_image="notice.order.user.avatar"
                                :avatar_props="notice.order.user.avatar_props"
                            ></AvatarComponent>
                        </div>
                        <div class="media-body">
                            <span class="text-semibold"><UserNameComponent :user="notice.order.user"></UserNameComponent></span>
                            <span class="media-annotation pull-right">{{ new Date(notice.order.created_at).toLocaleDateString('ru-RU') }}</span>
                            <a :href="orders_url + '?id=' + notice.order.id" class="media-heading">
                                <span v-if="notice.order.status === 3">Создал новую заявку «{{ notice.order.name }}»</span>
                                <span v-else>Закрыл заявку «{{ notice.order.name }}»</span>
                            </a>
                            <div v-if="notice.order.status === 0">
                                <hr>
                                <a :href="users_url + '?id=' + notice.order.performers[0].id">Исполнитель: <UserNameComponent :user="notice.order.performers[0]"></UserNameComponent></a>
                            </div>
                        </div>
                    </li>

                </ul>

<!--                <div class="dropdown-content-footer">-->
<!--                    <a href="#" data-popup="tooltip" title="" data-original-title="All messages"><i class="icon-menu display-block"></i></a>-->
<!--                </div>-->
            </div>
        </li>

        <li class="dropdown dropdown-user">
            <a class="dropdown-toggle" data-toggle="dropdown">
                <AvatarComponent
                    :small="true"
                    :avatar_image="user.avatar"
                    :avatar_props="user.avatar_props"
                ></AvatarComponent>
                <UserNameComponent :user="user"></UserNameComponent><i class="caret"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li><a :href="logout_url"><i class="icon-switch2"></i>Выход</a></li>
            </ul>
        </li>
    </ul>
</template>

<script>
import AvatarComponent from "../../blocks/AvatarComponent.vue";
import UserNameComponent from "../../blocks/UserNameComponent.vue";

export default {
    name: "NoticeComponent",
    components: {
        AvatarComponent,
        UserNameComponent
    },
    props: {
        'incoming_notices': String,
        'incoming_tickets': String,
        'incoming_answers': String,
        'incoming_user': String,
        'orders_url': String,
        'users_url': String,
        'tickets_url': String,
        'answers_url': String,
        'logout_url': String
    },
    created() {
        let self = this;
        this.user = JSON.parse(this.incoming_user);
        this.notices = JSON.parse(this.incoming_notices);
        this.tickets = JSON.parse(this.incoming_tickets);
        this.answers = JSON.parse(this.incoming_answers);

        window.Echo.private('admin_order_event').listen('.admin_order', res => {
            if (res.notice === 'new_item' || (res.notice === 'change_item' && res.model.status === 0) ) {
                self.notices.unshift({
                    order: res.model
                });
            } else if ( (res.notice === 'change_item' && res.model.status !== 3) || res.notice === 'del_item') {
                for (let i=0;i<self.notices.length;i++) {
                    if (self.notices[i].order.id === res.model.id) {
                        self.notices.splice(i,1);
                        break;
                    }
                }
            }
        });

        window.Echo.private('admin_ticket_event').listen('.admin_ticket', res => {
            if ( res.notice === 'new_item' || (res.notice === 'change_item' && res.model.status === 1)) {
                self.tickets.unshift(res.model);
            } else if ( (res.notice === 'change_item' && res.model.read_admin === 1) || res.notice === 'del_item') {
                for (let i=0;i<self.tickets.length;i++) {
                    if (self.tickets[i].id === res.model.id) {
                        self.tickets.splice(i,1);
                        break;
                    }
                }
            }
        });

        window.Echo.private('admin_answer_event').listen('.admin_answer', res => {
            if (res.notice === 'new_item') {
                self.answers.unshift(res.model);
            } else if ( (res.notice === 'change_item' && res.model.read_admin === 1) || res.notice === 'del_item') {
                for (let i=0;i<self.answers.length;i++) {
                    if (self.answers[i].id === res.model.id) {
                        self.answers.splice(i,1);
                        break;
                    }
                }
            }
        });
    },
    data() {
        return {
            user: Object,
            notices: {},
            tickets: [],
            answers: [],
        }
    }
}
</script>
