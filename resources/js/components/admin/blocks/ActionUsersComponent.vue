<template>
    <select id="action-users" class="form-select" name="users_ids[]" multiple>
        <option :selected="activeUsers.indexOf(user.id) !== -1" v-for="(user, k) in users" :key="'user-' + k" :value="user.id">
            <UserNameComponent :user="user"></UserNameComponent>
        </option>
    </select>
</template>

<script>
import UserNameComponent from "../../blocks/UserNameComponent.vue";

export default {
    name: "ActionUsersComponent",
    components: {
        UserNameComponent
    },
    props: {
        'incoming_users': String,
        'action_users': String
    },
    created() {
        let self = this;
        this.users = JSON.parse(this.incoming_users);
        this.activeUsers = JSON.parse(this.action_users);

        window.Echo.private('admin_incentive_event').listen('.admin_incentive', res => {
            self.activeUsers = res.ids;
        });
    },
    data() {
        return {
            users: [],
            activeUsers: []
        }
    }
}
</script>
