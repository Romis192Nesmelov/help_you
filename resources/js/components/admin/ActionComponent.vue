<template>
    <div class="col-lg-3 col-md-4 col-sm-12">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">Партнер акции</h5>
            </div>
            <div class="panel-body">
                <select v-model="obj.partner_id" class="select form-control">
                    <option v-for="(partner, k) in partners" :value="partner.id" :key="'partner' + k">{{ partner.name }}</option>
                </select>
            </div>
        </div>
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">Пользователи в акции</h5>
            </div>
            <div class="panel-body">
                <select v-model="activeUsers" id="action-users" class="form-select" multiple>
                    <option v-for="(user, k) in users" :key="'user-' + k" :value="user.id">
                        <UserNameComponent :user="user"></UserNameComponent>
                    </option>
                </select>
            </div>
        </div>
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">Рейтинг акции</h5>
            </div>
            <div class="panel-body">
                <div class="form-group no-margin-bottom" v-for="(name, rating) in ['Один','Два']">
                    <input :id="'action_rating_' + rating" :value="rating" type="radio" v-model="obj.rating">
                    <label class="ml-10 form-check-label" :for="'action_rating_' + rating">{{ name }}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-md-8 col-sm-12">
        <div class="panel panel-flat">
            <div class="panel-body">
                <InputComponent
                    label="Название акции"
                    type="text"
                    name="name"
                    min="3"
                    max="50"
                    placeholder="Название акции"
                    :error="errors.name"
                    v-model:value="obj.name"
                    @change="errors.name=null"
                ></InputComponent>
                <TextAreaComponent
                    label="Содержание акции"
                    name="html"
                    max="50000"
                    :value="obj.html"
                    v-model:value="obj.html"
                    :error="errors.html"
                    @keyup="errors.html=null"
                ></TextAreaComponent>
                <ButtonComponent
                    class_name="btn btn-primary mt-3 pull-right"
                    text="Сохранить"
                    :disabled=disabledSubmit
                    @click="save"
                ></ButtonComponent>
            </div>
        </div>
    </div>
</template>

<script>
import UserComponent from "./UserComponent.vue";
import UserNameComponent from "../blocks/UserNameComponent.vue";

export default {
    name: "ActionComponent",
    extends: UserComponent,
    components: {
        UserComponent,
        UserNameComponent,
    },
    props: {
        'incoming_users': String,
        'incoming_partners': String
    },
    created() {
        let self = this;
        this.users = JSON.parse(this.incoming_users);
        this.partners = JSON.parse(this.incoming_partners);

        window.emitter.on('date-change', (res) => {
            self.obj[res.name] = res.value;
        });

        window.Echo.private('admin_incentive_event').listen('.admin_incentive', res => {
            self.activeUsers = res.ids;
        });

        window.Echo.private('admin.admin_action_event').listen('.admin_action', res => {
            if (res.notice === 'change_item' && res.model.id === self.objId) {
                let newStart = self.convertTime(res.model.start),
                    newEnd = self.convertTime(res.model.end);

                $('input[name=start]').val(newStart);
                $('input[name=end]').val(newEnd);
            }
        });

        setTimeout(() => {
            $('input[name=start]').val('14/07/1976');
        }, 2000);
    },
    data() {
        return {
            obj: {
                name: '',
                html: '',
                rating: 1,
                partner_id: 1
            },
            users: [],
            activeUsers: [],
            partners: [],
        }
    },
    watch: {
        obj(newObj) {
            let self = this;
            $.each(newObj.users, function(k,user) {
                self.activeUsers.push(user.id);
            });
            this.obj.start = this.convertTime(this.obj.start);
            this.obj.end = this.convertTime(this.obj.end);
        }
    },
    methods: {
        convertTime(date) {
            return new Date(date).toLocaleDateString('ru-RU').replaceAll('.','/');
        },
        getDate(date) {
            let dateArr = date.split('/'),
                newDate = dateArr[1] + '.' + dateArr[0] + '.' + dateArr[2];
            return new Date(newDate).getTime();
        },
        save() {
            if (this.getDate(this.obj.start) >= this.getDate(this.obj.end)) {
                $('.date .error').html('Время начала акции не может быть больше или равно времени окончания!');
                this.disabledSubmit = false;
            } else {
                this.obj.html = window.editor.getData();
                let formData = this.preparingFields();
                formData.append('users_ids', this.activeUsers);
                this.saving(formData);
            }
        }
    },
}
</script>
