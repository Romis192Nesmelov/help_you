<template>
    <div v-if="!authCheck">
        <login-component :login_url="login_url" @logged-in="loggedIn"></login-component>
        <register-component :register_url="register_url" :get_code_url="get_code_url"></register-component>
        <restore-password-component :reset_pass_url="reset_pass_url"></restore-password-component>
    </div>

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

        <div class="d-block d-lg-none">
            <a id="account-href" :href="account_change_url" v-if="authCheck">
                <AccountIconComponent :icon="account_icon"></AccountIconComponent>
            </a>
            <a id="login-href" href="#" data-bs-toggle="modal" data-bs-target="#login-modal" v-else>
                <AccountIconComponent :icon="account_icon"></AccountIconComponent>
            </a>
        </div>

        <!-- Right block begin-->
        <div id="right-button-block" :class="'buttons-block d-none d-lg-flex align-items-center justify-content-'+(authCheck ? 'between' : 'end')+(onRoot ? ' on-root' : '')">
            <a
                id="navbar-dropdown-messages"
                :class="'nav-link'+(hasMessages ? ' dropdown-toggle' : '')"
                role="button"
                :data-bs-toggle="hasMessages ? 'dropdown' : ''"
                aria-expanded="false"
                v-if="authCheck"
            >
                <i class="fa fa-bell-o">
                    <span class="dot" v-if="hasMessages"></span>
                </i>
            </a>
            <div class="dropdown-menu" aria-labelledby="navbar-dropdown-messages" v-show="authCheck && hasMessages">
                <ul id="dropdown">
                    <li v-for="(message, id) in newsMessages" :key="id">
                        Новых сообщений: <span class="counter">{{ message.count }}</span><br>
                        <a :href="chat_url+'?id='+(id.replace('order',''))">в чате по заявке «{{ message.name }}»</a>
                        <hr>
                    </li>
                    <li v-for="(news, id) in newsSubscriptions" :key="id">
                        <a :href="orders_url + '?id=' + news.id">Новая заяка «{{ news.order.name }}» от:</a><br>
                        {{ news.order.user.name + ' ' + news.order.user.family }}
                        <hr>
                    </li>
                    <li v-for="(news, id) in newsPerformers" :key="id">
                        <a :href="my_orders_url">Новый исполнитель у заявки «{{ news.order.name }}»:</a><br>
                        {{ news.order.user.name + ' ' + news.order.user.family }}
                        <hr>
                    </li>
                    <li v-for="(news, id) in newsRemovedPerformers" :key="id">
                        <a :href="my_help_url">Вам было отказано в участии</a><br>
                        в выполнении заяки «{{ news.order.name }}»
                        <hr>
                    </li>
                    <li v-for="(news, id) in newsStatusOrders" :key="id">
                        <a :href="my_orders_url">Новый статус у заявки «{{ news.order.name }}»</a>:<br>
                        «{{ ordersStatuses[news.status] }}»
                        <hr>
                    </li>
                </ul>
            </div>

            <a :href="new_order_url" v-if="authCheck && !onRoot">
                <ButtonComponent
                    class_name="btn btn-secondary"
                    icon="icon-magazine"
                    text="Создать заявку"
                ></ButtonComponent>
            </a>

            <a :href="account_change_url" v-if="authCheck">
                <ButtonComponent
                    id="account-button"
                    class_name="btn btn-secondary"
                    icon="icon-user-lock"
                    text="Личный кабинет"
                ></ButtonComponent>
            </a>
            <ButtonComponent
                id="login-button"
                class_name="btn btn-secondary"
                toggle="modal"
                target="#login-modal"
                icon="icon-user-lock"
                text="Вход/регистрация"
                v-else
            ></ButtonComponent>
        </div>
        <!-- Right block end-->
    </div>
</template>

<script>
import LoginComponent from "./LoginComponent.vue";
import RegisterComponent from "./RegisterComponent.vue";
import RestorePasswordComponent from "./RestorePasswordComponent.vue";
import LogoComponent from "./blocks/LogoComponent.vue";
import TopMenuItemComponent from "./blocks/TopMenuItemComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";
import AccountIconComponent from "./blocks/AccountIconComponent.vue";
import {Static} from "vue";

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
        this.onRoot = parseInt(this.on_root);
    },
    data() {
        return {
            authCheck: false,
            hasMessages: false,
            onRoot: Number,
            userId: 0,
            mainMenu: {},
            newsMessages: {},
            newsSubscriptions: {},
            newsPerformers: {},
            newsRemovedPerformers: {},
            newsStatusOrders: {},
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
                self.hasMessages = !$.isEmptyObject(self.newsMessages);
                self.setEmitter('my-messages',!$.isEmptyObject(self.newsMessages));
            });
        },
        getNewsOrders() {
            let self = this;
            axios.get(this.get_orders_new_url).then(function (response) {
                if (response.data.news_subscriptions.length) {
                    $.each(response.data.news_subscriptions, function (k,news) {
                        self.newsSubscriptions['subscription' + news.order_id] = news;
                    });
                    self.setEmitter('my-subscriptions',true);
                    self.hasMessages = true;
                }
                if (response.data.news_performers.length) {
                    $.each(response.data.news_performers, function (k,news) {
                        self.newsPerformers['new_performer' + news.order_id] = news;
                    });
                    self.setEmitter('my-orders',true);
                    self.hasMessages = true;
                }
                if (response.data.news_removed_performers.length) {
                    $.each(response.data.news_performers, function (k,news) {
                        self.newsRemovedPerformers['removed_performer' + news.order_id] = news;
                    });
                    self.setEmitter('my-help',true);
                    self.hasMessages = true;
                }
                if (response.data.news_status_orders.length) {
                    $.each(response.data.news_status_orders, function (k,news) {
                        self.newsStatusOrders['status' + news.order_id] = news;
                    });
                    self.setEmitter('my-orders',true);
                    self.hasMessages = true;
                }
                self.bellAlert(self.hasMessages);
            });
        },
        listenEvents() {
            let self = this,
                eventsFlag = false;

            window.Echo.private('notice_' + this.userId).listen('.notice', res => {
                switch (res.notice) {
                    case 'new_message':
                        if (self.newsMessages['order' + res.order.id]) {
                            let counter = self.newsMessages['order' + res.order.id].count;
                            counter++;
                            self.newsMessages['order' + res.order.id].count = counter;
                        } else self.getNewsMessages();

                        self.setEmitter('my-messages',true);
                        eventsFlag = true;
                        self.hasMessages = true;
                        break;
                    case 'new_order_in_subscription':
                        self.newsSubscriptions['subscription' + res.order.id] = res.order;
                        self.setEmitter('my-subscriptions',true);
                        eventsFlag = true;
                        self.hasMessages = true;
                        break;
                    case 'new_performer':
                        self.newsPerformers['new_performer' + res.order.id] = res.order;
                        self.setEmitter('my-orders',true);
                        self.hasMessages = true;
                        break;
                    case 'new_order_status':
                        self.newsStatusOrders['status' + res.order.d] = res.order;
                        self.setEmitter('my-orders',true);
                        self.hasMessages = true;
                        break;
                    case 'remove_performer':
                        self.newsRemovedPerformers['removed_performer' + res.order.id] = res.order;
                        self.setEmitter('my-help',true);
                        eventsFlag = true;
                        self.hasMessages = true;
                        break;
                }
                self.bellAlert(eventsFlag);
            });

            window.Echo.channel('order_event').listen('.order', res => {
                if (res.notice === 'remove_order') {
                    if (self.newsMessages['order' + res.order.id]) {
                        delete self.newsMessages['order' + res.order.id];
                        self.setEmitter('my-messages',!$.isEmptyObject(self.newsMessages));
                    }

                    if (self.newsSubscriptions['subscription' + res.order.id]) {
                        delete self.newsSubscriptions['subscription' + res.order.id];
                        self.setEmitter('my-subscriptions',!$.isEmptyObject(self.newsSubscriptions));
                    }

                    if (self.newsPerformers['new_performer'+res.order.id]) {
                        delete self.newsPerformers['new_performer'+res.order.id];
                        self.setEmitter('my-order',!$.isEmptyObject(self.newsPerformers));
                    }

                    if (self.newsStatusOrders['status' + res.order.d]) {
                        delete self.newsStatusOrders['status' + res.order.d];
                        self.setEmitter('my-order',!$.isEmptyObject(self.newsStatusOrders));
                    }

                    if (self.newsRemovedPerformers['removed_performer' + res.order.id]) {
                        delete self.newsRemovedPerformers['order' + res.order.id];
                        self.setEmitter('my-help',!$.isEmptyObject(self.newsRemovedPerformers));
                    }
                    self.setHasMessages();
                }
            });

            window.emitter.on('read-order', orderId => {
                if (this.newsSubscriptions['subscription' + orderId]) {
                    delete this.newsSubscriptions['subscription' + orderId];
                    self.setEmitter('my-subscriptions',!$.isEmptyObject(self.newsSubscriptions));
                    self.setHasMessages();
                }
            });

            window.emitter.on('read-message', orderId => {
                if (this.newsMessages['order' + orderId]) {
                    delete this.newsMessages['order' + orderId];
                    self.setEmitter('my-messages',!$.isEmptyObject(self.newsMessages));
                    self.setHasMessages();
                }
            });
        },
        bellAlert(eventsFlag) {
            if (eventsFlag) {
                window.bellRinging($('#right-button-block .fa.fa-bell-o'));
                // const audio = new Audio(this.bell_sound);
                // audio.muted = false;
                // audio.play();
            }
        },
        setEmitter(event, flag) {
            window.emitter.emit(event, flag);
        },
        setHasMessages() {
            self.hasMessages =
                !$.isEmptyObject(self.newsMessages) ||
                !$.isEmptyObject(self.newsSubscriptions) ||
                !$.isEmptyObject(self.newsPerformers) ||
                !$.isEmptyObject(self.newsStatusOrders) ||
                !$.isEmptyObject(self.newsRemovedPerformers);
        }
    },
    components: {
        LogoComponent,
        LoginComponent,
        RegisterComponent,
        RestorePasswordComponent,
        TopMenuItemComponent,
        ButtonComponent,
        AccountIconComponent,
    },
    props: {
        'auth': String,
        'user_id': String,
        'on_root': String,
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
        'orders_url': String,
        'active_main_menu': String,
        'get_unread_messages_url': String,
        'chat_url': String,
        'get_orders_new_url': String,
        'my_orders_url': String,
        'my_help_url': String,
        'order_statuses': String,
        'bell_sound': String
    },
}
</script>
