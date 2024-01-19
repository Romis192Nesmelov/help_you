<template>
    <div v-if="!authCheck">
        <login-component :login_url="login_url" @logged-in="loggedIn"></login-component>
        <register-component :register_url="register_url" :get_code_url="get_code_url"></register-component>
        <restore-password-component :reset_pass_url="reset_pass_url"></restore-password-component>
    </div>
    <message-component></message-component>

    <div id="top-line" class="w-100 d-flex align-items-center justify-content-between">
        <a class="d-none d-lg-block" :href="home_url">
            <div class="logo-block d-none d-lg-flex align-items-center justify-content-between">
                <logo-component :image="logo_image"></logo-component>
                <img class="logo-text" :src="logo_text_image" />
            </div>
        </a>

        <nav id="main-nav" class="main-nav navbar navbar-expand-lg navbar-light">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-main-nav" aria-controls="navbar-main-nav" aria-expanded="false" aria-label="Переключатель навигации">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbar-main-nav">
                <ul class="navbar-nav">
                    <top-menu-item-component
                        v-if="authCheck"
                        key="messages"
                        :is_active=false
                        add_class="d-block d-lg-none"
                        :url="messages_url"
                        name="messages"
                    ></top-menu-item-component>

                    <top-menu-item-component
                        v-for="menu in this.mainMenu"
                        :key="menu.key"
                        :is_active="active_main_menu === menu.key"
                        add_class=""
                        :url="menu.url"
                        :name="menu.name"
                    ></top-menu-item-component>

                    <top-menu-item-component
                        v-if="authCheck"
                        :is_active=false
                        key="new_order"
                        add_class="d-block d-lg-none"
                        :url="new_order_url"
                        name="Создать заявку"
                    ></top-menu-item-component>
                </ul>
            </div>
        </nav>

        <div class="d-block d-lg-none">
            <a :href="home_url">
                <logo-component :image="logo_image"></logo-component>
            </a>
        </div>
        <right-block-component
            :auth_check="authCheck"
            :user_id="userId"
            :on_root="on_root"
            :account_icon="account_icon"
            :account_change_url="account_change_url"
            :new_order_url="new_order_url"
            :my_subscriptions_url="my_subscriptions_url"
            :news_messages="newsMessages"
            :chat_url="chat_url"
            :my_orders_url="my_orders_url"
            :my_help_url="my_help_url"
            :news_subscriptions="newsSubscriptions"
            :news_performers="newsPerformers"
            :news_removed_performers="newsRemovedPerformers"
            :news_status_orders="newsStatusOrders"
            :new_orders_approved="newOrdersApproved"
            :orders_statuses="ordersStatuses"
        ></right-block-component>
    </div>
</template>

<script>
import LoginComponent from "./LoginComponent.vue";
import RegisterComponent from "./RegisterComponent.vue";
import RestorePasswordComponent from "./RestorePasswordComponent.vue";
import MessageComponent from "./MessageComponent.vue";
import LogoComponent from "./blocks/LogoComponent.vue";
import TopMenuItemComponent from "./blocks/TopMenuItemComponent.vue";
import RightBlockComponent from "./RightBlockComponent.vue";

export default {
    name: "TopLineComponent",
    created() {
        this.authCheck = !isNaN(parseInt(this.auth));
        this.userId = parseInt(this.user_id);
        this.mainMenu = JSON.parse(this.main_menu);
        this.ordersStatuses = JSON.parse(this.order_statuses);

        if (this.authCheck) {
            this.getNewsMessages();
            this.getNewsOrders();
            this.listenEvents();
        }
    },
    data() {
        return {
            authCheck: false,
            userId: 0,
            mainMenu: {},
            newsMessages: {},
            newsSubscriptions: {},
            newsPerformers: {},
            newsRemovedPerformers: {},
            newsStatusOrders: {},
            newOrdersApproved: {},
            ordersStatuses: []
        }
    },
    methods: {
        loggedIn(id) {
            this.userId = id;
            this.authCheck = true;
            this.getNewsMessages();
            this.getNewsOrders();
            this.listenEvents();
        },
        getNewsMessages() {
            let self = this;
            axios.get(this.get_unread_messages_url).then(function (response) {
                self.newsMessages = response.data.unread;
            });
        },
        getNewsOrders() {
            let self = this;
            axios.get(this.get_orders_new_url).then(function (response) {
                if (response.data.news_subscriptions.length) {
                    $.each(response.data.news_subscriptions, function (k,subscription) {
                        if (subscription.unread_orders.length) {
                            $.each(subscription.unread_orders, function (k,unreadOrder) {
                                self.newsSubscriptions['subscription'+unreadOrder.order_id] = unreadOrder.order;
                            });
                        }
                    });
                }
                if (response.data.news_performers.length) {
                    $.each(response.data.news_performers, function (k,news) {
                        self.newsPerformers['new_performer'+news.order_id] = news;
                    });
                }
                if (response.data.news_removed_performers.length) {
                    $.each(response.data.news_performers, function (k,news) {
                        self.newsRemovedPerformers['removed_performer'+news.order_id] = news;
                    });
                }
                if (response.data.news_status_orders.length) {
                    $.each(response.data.news_status_orders, function (k,news) {
                        self.newsStatusOrders['status'+news.order_id] = news;
                    });
                }
            });
        },
        listenEvents() {
            let self = this;
            window.Echo.private('notice_' + this.userId).listen('.notice', res => {
                if (res.notice === 'new_message') {
                    let messageKey = 'order'+res.order.id;
                    if (self.newsMessages[messageKey]) {
                        let counter = self.newsMessages[messageKey];
                        counter++;
                        self.newsMessages[messageKey] = counter;
                    } else {
                        self.newsMessages[messageKey] = 1;
                    }
                } else if (res.notice === 'new_order_in_subscription') {
                    self.newsSubscriptions['subscription'+res.order.id] = res.order;
                } else if (res.notice === 'new_performer') {
                    self.newsPerformers['new_performer'+res.order.id] = res.order;
                } else if (res.notice === 'order_approved') {
                    self.newOrdersApproved['status'+res.order.d] = res.order;
                } else if (res.notice === 'new_order_status') {
                    self.newsStatusOrders['status'+res.order.d] = res.order;
                    if (!res.order.status) this.deleteOrderNotice(res.order.id);
                } else if (res.notice === 'remove_performer') {
                    self.newsRemovedPerformers['removed_performer'+res.order.id] = res.order;
                } else if (res.notice === 'delete_order') {
                    this.deleteOrderNotice(res.order.id);
                }
            });
        },
        deleteOrderNotice(orderId) {
            let orderKeyNewMessages = 'order'+orderId,
                orderKeySubscriptions = 'subscription'+orderId,
                orderKeyNewPerformer = 'performer'+orderId,
                orderKeyOrderApproved = 'status'+orderId,
                orderKeyOrderStatus = 'status'+orderId;

            if (self.newsMessages[orderKeyNewMessages]) delete self.newsMessages[orderKeyNewMessages];
            if (self.newsSubscriptions[orderKeySubscriptions]) delete self.newsSubscriptions[orderKeySubscriptions];
            if (self.newsPerformers[orderKeyNewPerformer]) delete self.newsPerformers[orderKeyNewPerformer];
            if (self.newOrdersApproved[orderKeyOrderApproved]) delete self.newOrdersApproved[orderKeyOrderApproved];
            if (self.newsStatusOrders[orderKeyOrderStatus]) delete self.newsStatusOrders[orderKeyOrderStatus];
        }
    },
    components: {
        LogoComponent,
        LoginComponent,
        RegisterComponent,
        RestorePasswordComponent,
        MessageComponent,
        TopMenuItemComponent,
        RightBlockComponent
    },
    props: {
        'auth': String,
        'user_id': String,
        'on_root': Boolean,
        'home_url': String,
        'login_url': String,
        'register_url': String,
        'get_code_url': String,
        'reset_pass_url': String,
        'logo_image': String,
        'logo_text_image': String,
        'main_menu': String,
        'account_icon': String,
        'new_order_url': String,
        'account_change_url': String,
        'messages_url': String,
        'my_subscriptions_url': String,
        'active_main_menu': String,
        'get_unread_messages_url': String,
        'chat_url': String,
        'get_orders_new_url': String,
        'my_orders_url': String,
        'my_help_url': String,
        'order_statuses': String
    },
}
</script>
