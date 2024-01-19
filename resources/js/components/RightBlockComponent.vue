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
        <a class="nav-link dropdown-toggle" id="navbar-dropdown-messages" role="button" data-bs-toggle="dropdown" aria-expanded="false" v-if="auth_check">
            <i class="fa fa-bell-o"></i>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbar-dropdown-messages" v-if="auth_check">
            <ul id="dropdown"></ul>
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
    props: {
        'auth_check': Boolean,
        'on_root': Boolean,
        'account_icon': String,
        'new_order_url': String,
        'account_change_url': String,
    },
}
</script>
