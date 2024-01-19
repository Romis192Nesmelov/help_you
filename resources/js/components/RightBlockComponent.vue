<template>
    <div class="d-block d-lg-none">
        <a id="account-href" :href="account_change_url" v-if="auth_check">
            <AccountIconComponent :icon="account_icon"></AccountIconComponent>
        </a>
        <a id="login-href" href="#" data-bs-toggle="modal" data-bs-target="#login-modal" v-else>
            <AccountIconComponent :icon="account_icon"></AccountIconComponent>
        </a>
    </div>

    <div id="right-button-block" :class="'buttons-block d-none d-lg-flex align-items-center justify-content-'+(auth_check ? 'between' : 'end')+(on_root ? ' on-root' : '')">
        <a
            id="navbar-dropdown-messages"
            :class="'nav-link'+(
                news_messages.length ||
                news_subscriptions.length ||
                news_performers.length ||
                news_removed_performers.length ||
                news_removed_performers ||
                news_status_orders.length
                ? ' dropdown-toggle' : ''
                )"
            role="button"
            :data-bs-toggle="(
                news_messages.length ||
                news_subscriptions.length ||
                news_performers.length ||
                news_removed_performers.length ||
                news_removed_performers ||
                news_status_orders.length
                ? 'dropdown' : ''
                )"
            aria-expanded="false"
            v-if="auth_check"
        >
            <i class="fa fa-bell-o">
                <span class="dot" v-if="(
                    news_messages.length ||
                    news_subscriptions.length ||
                    news_performers.length ||
                    news_removed_performers.length ||
                    news_removed_performers ||
                    news_status_orders.length
                )"></span>
            </i>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbar-dropdown-messages" v-if="auth_check && (
            news_messages.length ||
            news_subscriptions.length ||
            news_performers.length ||
            news_removed_performers.length ||
            news_removed_performers ||
            news_status_orders.length
        )">
            <ul id="dropdown">
                <li v-for="(counter, id) in this.news_messages" :key="'message_in_'+id">
                    Новых сообщений: <span class="counter">{{ counter }}</span><br>
                    <a :href="chat_url+'?order_id='+(id.replace('order',''))">в чате по заявке №{{ id.replace('order','') }}</a>
                    <hr>
                </li>
                <li v-for="order in news_subscriptions" :key="'subscription'+order.id">
                    <a :href="my_subscriptions_url">Новая заяка от:</a><br>
                    {{ order.user.name+' '+order.user.family }}
                    <hr>
                </li>
                <li v-for="news in this.news_performers" :key="'new_performers'+news.order_id">
                    <a :href="my_orders_url">Новый исполнитель у заявки №:{{ news.order_id }}:</a><br>
                    {{ news.user.name+' '+news.user.family }}
                    <hr>
                </li>
                <li v-for="news in this.news_removed_performers" :key="'new_removed_performers'+news.order_id">
                    <a :href="my_help_url">Вам было отказано в участии</a><br>
                    в выполнении заяки №{{ news.order_id }}
                    <hr>
                </li>
                <li v-for="news in this.news_status_orders" :key="'new_status_order'+news.order_id">
                    <a :href="my_orders_url">Новый статус у заявки №:{{ news.order_id }}</a>:<br>
                    «{{ this.orders_statuses[news.status] }}»
                    <hr>
                </li>
            </ul>
        </div>

        <a :href="new_order_url" v-if="auth_check && !on_root">
            <ButtonComponent
                class_name="btn btn-secondary"
                icon="icon-magazine"
                text="Создать заявку"
            ></ButtonComponent>
        </a>

        <a :href="account_change_url" v-if="auth_check">
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
</template>

<script>
import ButtonComponent from "./blocks/ButtonComponent.vue";
import AccountIconComponent from "./blocks/AccountIconComponent.vue";

export default {
    name: "RightBlockComponent",
    components: {
        AccountIconComponent, ButtonComponent
    },
    data() {
        return {
            statuses: ['Закрыта','Выполняется',]
        }
    },
    props: {
        'auth_check': Boolean,
        'on_root': Boolean,
        'account_icon': String,
        'new_order_url': String,
        'account_change_url': String,
        'news_messages': Object,
        'chat_url': String,
        'my_subscriptions_url': String,
        'my_orders_url': String,
        'my_help_url': String,
        'news_subscriptions': Object,
        'news_performers': Array,
        'news_removed_performers': Array,
        'news_status_orders': Array,
        'orders_statuses': Array
    },
}
</script>
