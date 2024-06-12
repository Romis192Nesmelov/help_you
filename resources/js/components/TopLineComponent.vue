<template>
    <div v-if="!authCheck">
        <LoginComponent :login_url="login_url" @logged-in="loggedIn"></LoginComponent>
        <RegisterComponent :register_url="register_url" :get_code_url="get_code_url"></RegisterComponent>
        <RestorePasswordComponent :reset_pass_url="reset_pass_url"></RestorePasswordComponent>
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
<!--                    <top-menu-item-component-->
<!--                        v-if="authCheck"-->
<!--                        key="messages"-->
<!--                        :is_active=false-->
<!--                        add_class="d-block d-lg-none"-->
<!--                        :url="messages_url"-->
<!--                        name="Сообщения"-->
<!--                    ></top-menu-item-component>-->
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
                    <top-menu-item-component
                        v-if="authCheck"
                        :is_active=false
                        key="orders"
                        add_class="d-block d-lg-none"
                        :url="orders_url"
                        name="Оказать помощь"
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
                :class="'ms-2 me-2'+(hasMessages ? ' dropdown-toggle' : '')"
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
                    <li v-for="(news, id) in newsIncentives" :key="id">
                        <a :href="incentives_url + '?id=' + news.action.id">У вас новая награда:</a><br>
                        Акция «{{ news.action.name }}»
                        <hr>
                    </li>
                    <li v-for="(message, id) in newsMessages" :key="id">
                        Новых сообщений: <span class="counter">{{ message.count }}</span><br>
                        <a :href="chat_url+'?id=' + message.order.id">в чате по заявке «{{ message.order.name }}»</a>
                        <hr>
                    </li>
                    <li v-for="(news, id) in newsSubscriptions" :key="id">
                        <a :href="orders_url + '?id=' + news.order.id">Новая заяка «{{ news.order.name }}» от:</a><br>
                        {{ news.order.user.name + ' ' + news.order.user.family }}
                        <hr>
                    </li>
                    <li v-for="(news, id) in newsPerformers" :key="id">
                        <a :href="my_orders_url">Новый исполнитель у заявки «{{ news.order.name }}»:</a><br>
                        {{ news.user.name + ' ' + news.user.family }}
                        <hr>
                    </li>
                    <li v-for="(news, id) in newsRemovedPerformers" :key="id">
                        <a :href="my_help_url">Вам было отказано в участии</a><br>
                        в выполнении заяки «{{ news.order.name }}»
                        <hr>
                    </li>
                    <li v-for="(news, id) in newsStatusOrders" :key="id">
                        <a :href="my_orders_url">Новый статус у заявки «{{ news.order.name }}»</a>:<br>
                        «{{ ordersStatuses[news.order.status] }}»
                        <hr>
                    </li>
                </ul>
            </div>

            <a class="ms-2 me-2" :href="new_order_url" v-if="authCheck && !onRoot">
                <i class="icon-add-to-list"></i>
<!--                <ButtonComponent-->
<!--                    class_name="btn btn-secondary"-->
<!--                    icon="icon-magazine"-->
<!--                    text="Создать заявку"-->
<!--                ></ButtonComponent>-->
            </a>

            <a class="ms-2 me-2" :href="orders_url" v-if="authCheck && !onRoot">
                <i class="icon-map"></i>
            </a>

            <a :href="account_change_url" v-if="authCheck">
                <ButtonComponent
                    id="account-button"
                    class_name="btn btn-secondary ms-3"
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
            this.getNews();
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
            newsIncentives: [],
            newsMessages: [],
            newsSubscriptions: [],
            newsPerformers: [],
            newsRemovedPerformers: [],
            newsStatusOrders: [],
            ordersStatuses: []
        }
    },
    methods: {
        loggedIn(id) {
            this.userId = id;
            this.authCheck = true;
            this.getNewsMessages();
            this.getNews();
            this.listenEvents();
        },
        getNewsMessages() {
            let self = this;
            axios.get(this.get_unread_messages_url).then(function (response) {
                if (response.data.unread.length) {
                    self.newsMessages = response.data.unread;
                    self.hasMessages = true;
                    self.setEmitter('my-messages',true);
                }
            });
        },
        getNews() {
            let self = this;
            axios.get(this.get_news_url).then(function (response) {
                if (response.data.news_incentive.length) {
                    self.newsIncentives = response.data.news_incentive;
                    self.setEmitter('incentives',true);
                    self.hasMessages = true;
                }
                if (response.data.news_subscriptions.length) {
                    self.newsSubscriptions = response.data.news_subscriptions;
                    self.setEmitter('my-subscriptions',true);
                    self.hasMessages = true;
                }
                if (response.data.news_performers.length) {
                    self.newsPerformers = response.data.news_performers;
                    self.setEmitter('my-orders',true);
                    self.hasMessages = true;
                }
                if (response.data.news_removed_performers.length) {
                    self.newsRemovedPerformers = response.data.news_removed_performers;
                    self.setEmitter('my-help',true);
                    self.hasMessages = true;
                }
                if (response.data.news_status_orders.length) {
                    self.newsStatusOrders = response.data.news_status_orders;
                    self.setEmitter('my-orders',true);
                    self.hasMessages = true;
                }
                self.bellAlert(self.hasMessages);
            });
        },
        listenEvents() {
            let self = this,
                eventsFlag = false,
                key = false;

            window.Echo.private('incentive_' + this.userId).listen('.incentive', res => {
                if (res.notice === 'remove_incentive') {
                    key = self.findIncentive(res.incentive.id);
                    if (key !== false) {
                        self.newsIncentives.splice(key,1);
                        self.setEmitter('incentives',self.newsIncentives.length);
                        self.setHasMessages();
                    }
                } else if (res.notice === 'new_incentive') {
                    self.newsIncentives.push(res);
                    self.setEmitter('incentives',true);
                    self.hasMessages = true;
                    self.bellAlert(true);
                }
            });

            window.Echo.private('notice_' + this.userId).listen('.notice', res => {
                switch (res.notice) {
                    case 'new_message':
                        key = this.findOrder(self.newsMessages, res.order.id);
                        if (key !== false) {
                            let counter = self.newsMessages[key].count;
                            counter++;
                            self.newsMessages[key].count = counter;
                        } else {
                            self.newsMessages.push({
                                'order':res.order,
                                'count':1
                            });
                        }
                        self.setEmitter('my-messages',true);
                        eventsFlag = true;
                        self.hasMessages = true;
                        break;
                    case 'new_order_in_subscription':
                        self.newsSubscriptions.push(res);
                        self.setEmitter('my-subscriptions',true);
                        eventsFlag = true;
                        self.hasMessages = true;
                        break;
                    case 'new_performer':
                        self.newsPerformers.push(res);
                        self.setEmitter('my-orders',true);
                        self.hasMessages = true;
                        break;
                    case 'new_order_status':
                        self.newsStatusOrders.push(res);
                        self.setEmitter('my-orders',true);
                        self.hasMessages = true;
                        break;
                    case 'remove_performer':
                        self.newsRemovedPerformers.push(res);
                        self.setEmitter('my-help',true);
                        eventsFlag = true;
                        self.hasMessages = true;
                        break;
                }
                self.bellAlert(eventsFlag);
            });

            window.Echo.channel('order_event').listen('.order', res => {
                if (res.notice === 'remove_order') {
                    key = self.findOrder(self.newsMessages, res.order.id);
                    if (key !== false) {
                        self.newsMessages.splice(key,1);
                        self.setEmitter('my-messages',self.newsMessages.length);
                    }

                    key = self.findOrder(self.newsSubscriptions, res.order.id);
                    if (key !== false) {
                        self.newsSubscriptions.splice(key,1);
                        self.setEmitter('my-subscriptions',self.newsSubscriptions.length);
                    }

                    key = self.findOrder(self.newsPerformers, res.order.id);
                    if (key !== false) {
                        self.newsPerformers.splice(key,1);
                        self.setEmitter('my-orders',self.newsPerformers.length);
                    }

                    key = self.findOrder(self.newsStatusOrders, res.order.id);
                    if (key !== false) {
                        self.newsStatusOrders.splice(key,1);
                        self.setEmitter('my-orders',self.newsStatusOrders.length);
                    }

                    key = self.findOrder(self.newsRemovedPerformers, res.order.id);
                    if (key !== false) {
                        self.newsRemovedPerformers.splice(key,1);
                        self.setEmitter('my-help',self.newsRemovedPerformers.length);
                    }
                    self.setHasMessages();
                }
            });

            //TODO: TicketEvent listener and corresponding emitter

            window.emitter.on('read-order', orderId => {
                key = self.findOrder(self.newsSubscriptions, orderId);
                if (key !== false) {
                    self.newsSubscriptions.splice(key,1);
                    self.setEmitter('my-subscriptions',self.newsSubscriptions.length);
                    self.setHasMessages();
                }
            });

            window.emitter.on('read-message', orderId => {
                key = self.findOrder(self.newsMessages, orderId);
                if (key !== false) {
                    self.newsMessages.splice(key,1);
                    self.setEmitter('my-messages',self.newsMessages.length);
                    self.setHasMessages();
                }
            });

            window.emitter.on('read-unread-by-my-orders', orderId => {
                self.newsPerformers = [];
                self.newsStatusOrders = [];
                self.setEmitter('my-orders',false);
            });

            window.emitter.on('read-unread-by-my-help', orderId => {
                self.newsPerformers = [];
                self.newsRemovedPerformers = [];
                self.setEmitter('my-help',false);
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
            this.hasMessages =
                this.newsMessages.length ||
                this.newsSubscriptions.length ||
                this.newsPerformers.length ||
                this.newsStatusOrders.length ||
                this.newsRemovedPerformers.length ||
                this.newsIncentives.length;
        },
        findOrder(newsArr, orderId) {
            let key = false;
            $.each(newsArr, function (k,news) {
                if (news.order.id === orderId) {
                    key = k;
                    return false;
                }
            });
            return key;
        },
        findIncentive(incentiveId) {
            let key = false;
            $.each(this.newsIncentives, function (k,news) {
                if (news.id === incentiveId) {
                    key = k;
                    return false;
                }
            });
            return key;
        },
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
        'get_news_url': String,
        'my_orders_url': String,
        'my_help_url': String,
        'incentives_url': String,
        'order_statuses': String
    },
}
</script>
