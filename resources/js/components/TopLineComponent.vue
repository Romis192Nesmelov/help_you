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
        <div id="right-button-block" :class="'buttons-block d-none d-lg-flex align-items-center justify-content-'+(authCheck ? 'between' : 'end')+(on_root ? ' on-root' : '')">
            <a
                id="navbar-dropdown-messages"
                :class="'nav-link'+(countMessages ? ' dropdown-toggle' : '')"
                role="button"
                :data-bs-toggle="countMessages ? 'dropdown' : ''"
                aria-expanded="false"
                v-if="authCheck"
            >
                <i class="fa fa-bell-o">
                    <span class="dot" v-if="countMessages"></span>
                </i>
            </a>
            <div class="dropdown-menu" aria-labelledby="navbar-dropdown-messages" v-show="authCheck && countMessages">
                <ul id="dropdown">
                    <li v-for="(counter, id) in newsMessages" :key="id">
                        Новых сообщений: <span class="counter">{{ counter }}</span><br>
                        <a :href="chat_url+'?order_id='+(id.replace('order',''))">в чате по заявке №{{ id.replace('order','') }}</a>
                        <hr>
                    </li>
                    <li v-for="(news, id) in newsSubscriptions" :key="id">
                        <a :href="my_subscriptions_url">Новая заяка №{{ news.id }} от:</a><br>
                        {{ news.user.name+' '+news.user.family }}
                        <hr>
                    </li>
                    <li v-for="(news, id) in newsPerformers" :key="id">
                        <a :href="my_orders_url">Новый исполнитель у заявки №:{{ news.order_id }}:</a><br>
                        {{ news.user.name+' '+news.user.family }}
                        <hr>
                    </li>
                    <li v-for="(news, id) in newsRemovedPerformers" :key="id">
                        <a :href="my_help_url">Вам было отказано в участии</a><br>
                        в выполнении заяки №{{ news.order_id }}
                        <hr>
                    </li>
                    <li v-for="(news, id) in newsStatusOrders" :key="id">
                        <a :href="my_orders_url">Новый статус у заявки №:{{ news.order_id }}</a>:<br>
                        «{{ ordersStatuses[news.status] }}»
                        <hr>
                    </li>
                </ul>
            </div>

            <a :href="new_order_url" v-if="authCheck && !on_root">
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
            ordersStatuses: []
        }
    },
    methods: {
        countMessages() {
            return (
                !isEmptyObject(this.newsMessages) ||
                !isEmptyObject(this.newsSubscriptions) ||
                !isEmptyObject(this.newsPerformers) ||
                !isEmptyObject(this.newsRemovedPerformers) ||
                !isEmptyObject(this.newsStatusOrders)
            );
        },
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
                window.emitter.emit('my-messages', !$.isEmptyObject(self.newsMessages));
            });
        },
        getNewsOrders() {
            let self = this,
                otherEventsFlag = false,
                ordersEventFlag = false;

            axios.get(this.get_orders_new_url).then(function (response) {
                if (response.data.news_subscriptions.length) {
                    $.each(response.data.news_subscriptions, function (k,subscription) {
                        if (subscription.unread_orders.length) {
                            $.each(subscription.unread_orders, function (k,unreadOrder) {
                                self.newsSubscriptions['subscription'+unreadOrder.order_id] = unreadOrder.order;
                            });
                        }
                    });
                    otherEventsFlag = true;
                    window.emitter.emit('my-subscriptions', !$.isEmptyObject(self.newsSubscriptions));
                }
                if (response.data.news_performers.length) {
                    $.each(response.data.news_performers, function (k,news) {
                        self.newsPerformers['new_performer'+news.order_id] = news;
                    });
                    ordersEventFlag = true;
                }
                if (response.data.news_removed_performers.length) {
                    $.each(response.data.news_performers, function (k,news) {
                        self.newsRemovedPerformers['removed_performer'+news.order_id] = news;
                    });
                    window.emitter.emit('my-help', !$.isEmptyObject(self.newsRemovedPerformers));
                    otherEventsFlag = true;
                }
                if (response.data.news_status_orders.length) {
                    $.each(response.data.news_status_orders, function (k,news) {
                        self.newsStatusOrders['status'+news.order_id] = news;
                    });
                    ordersEventFlag = true;
                }
                self.eventOrderChange(ordersEventFlag);
                self.bellAlert(otherEventsFlag, ordersEventFlag);
            });
        },
        listenEvents() {
            let self = this,
                otherEventsFlag = false,
                ordersEventFlag = false;

            window.Echo.private('notice_' + this.userId).listen('.notice', res => {
                switch (res.notice) {
                    case 'new_message':
                        let messageKey = 'order'+res.order.id;
                        if (self.newsMessages[messageKey]) {
                            let counter = self.newsMessages[messageKey];
                            counter++;
                            self.newsMessages[messageKey] = counter;
                        } else {
                            self.newsMessages[messageKey] = 1;
                        }
                        window.emitter.emit('my-messages', !$.isEmptyObject(self.newsMessages));
                        otherEventsFlag = true;
                        break;
                    case 'new_order_in_subscription':
                        self.newsSubscriptions['subscription'+res.order.id] = res.order;
                        window.emitter.emit('my-subscriptions', !$.isEmptyObject(self.newsSubscriptions));
                        otherEventsFlag = true;
                        break;
                    case 'new_performer':
                        self.newsPerformers['new_performer'+res.order.id] = res.order;
                        ordersEventFlag = true;
                        break;
                    case 'new_order_status':
                        self.newsStatusOrders['status'+res.order.d] = res.order;
                        if (!res.order.status) this.deleteOrderNotice(res.order.id);
                        ordersEventFlag = true;
                        break;
                    case 'remove_performer':
                        self.newsRemovedPerformers['removed_performer'+res.order.id] = res.order;
                        window.emitter.emit('my-help', !$.isEmptyObject(self.newsRemovedPerformers));
                        otherEventsFlag = true;
                        break;
                    case 'delete_order':
                        this.deleteOrderNotice(res.order.id);
                        ordersEventFlag = true;
                        break;
                }
                self.eventOrderChange(ordersEventFlag);
                self.bellAlert(otherEventsFlag, ordersEventFlag);
            });
        },
        eventOrderChange(ordersEventFlag) {
            if (ordersEventFlag) {
                window.emitter.emit('my-orders',
                    !$.isEmptyObject(this.newsPerformers) ||
                    !$.isEmptyObject(this.newsStatusOrders)
                );
            }
        },
        deleteOrderNotice(orderId) {
            let self = this;
            $.each({
                'order':self.newsMessages,
                'subscription:':self.newsSubscriptions,
                'performer':self.newsPerformers,
                'status':self.newsStatusOrders
            }, function (key,news) {
                let newsKey = key + orderId;
                if (news[newsKey]) delete news[newsKey];
            });
        },
        bellAlert(otherEventsFlag, ordersEventFlag) {
            if (otherEventsFlag || ordersEventFlag) {
                let counter = 0,
                    degrees = 15;

                const bellIcon = $('#right-button-block .fa.fa-bell-o'),
                    audio = new Audio(this.bell_sound),
                    bellRinging = setInterval(() => {
                        degrees *= -1;
                        bellRing(degrees);
                        counter++;
                        if (counter > 5) {
                            clearInterval(bellRinging);
                            bellRing(0);
                        }
                    }, 200);

                audio.muted = false;
                audio.play();

                const bellRing = (degrees) => {
                    bellIcon.css({'-webkit-transform' : 'rotate('+ degrees +'deg)',
                        '-moz-transform' : 'rotate('+ degrees +'deg)',
                        '-ms-transform' : 'rotate('+ degrees +'deg)',
                        'transform' : 'rotate('+ degrees +'deg)'});
                }
            }
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
        'order_statuses': String,
        'bell_sound': String
    },
}
</script>
