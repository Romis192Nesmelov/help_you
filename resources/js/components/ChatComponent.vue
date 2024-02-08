<template>
    <ModalComponent id="user-data-modal" head="Данные пользователя">
        <UserPropertiesComponent
            :user="myCompanion"
            :avatar_coof=0.35
            :use_rating=true
            :allow_change_rating=false
        ></UserPropertiesComponent>
        <hr>
        <h5 class="text-center">Информация о пользователе:</h5>
        <p class="text-center">{{ myCompanion.info_about ? myCompanion.info_about : 'Информация отсутствует' }}</p>
    </ModalComponent>

    <ModalComponent id="order-data-modal" head="Данные о задаче">
        <div id="selected-points">
            <UserPropertiesComponent
                :user="chatOrder.user"
                :avatar_coof=0.35
                :use_rating=true
                :allow_change_rating=false
            ></UserPropertiesComponent>
            <OrderCarouselImagesComponent
                :images="chatOrder.images"
            ></OrderCarouselImagesComponent>
            <OrderPropertiesComponent
                :name="chatOrder.name"
                :type="chatOrder.order_type.name"
                :subtype="chatOrder.sub_type.name"
                :address="chatOrder.address"
                :description_short="chatOrder.description_short"
            ></OrderPropertiesComponent>
        </div>
    </ModalComponent>

    <div class="col-12 col-lg-8">
        <div class="rounded-block tall white pt-4">
            <div class="d-flex justify-content-start align-items-center mb-3">
                <a :href="chats_url"><i title="Сообщения" class="icon-arrow-left8 fs-4 me-3"></i></a>
                <div>
                    <a data-bs-toggle="modal" data-bs-target="#user-data-modal">
                        <strong>
                            <UserNameComponent :user="myCompanion"></UserNameComponent>
                        </strong>
                    </a>
                    <div class="fs-lg-6 fs-sm-7">
                        <a data-bs-toggle="modal" data-bs-target="#order-data-modal">«{{ chatOrder.name }}» – {{ chatOrder.order_type.name }} от {{ chatDate }}</a>
                    </div>
                </div>
            </div>
            <div class="content-block simple">
                <div id="messages">
                    <div v-for="(message, k) in messages">
                        <div class="date-block" v-if="newDate(k)">
                            <span class="date">{{ getDate(message.created_at) }}</span>
                        </div>
                        <div :class="'message-row' + (message.user.id === userId ? ' my-self' : '')">
                            <div class="attached-image" v-if="message.image">
                                <a :href="'/' + message.image" class="fancybox"><img :src="'/' + message.image" /></a>
                                <MessageComponent :message="message"></MessageComponent>
                            </div>
                            <MessageComponent :message="message" v-else></MessageComponent>
                        </div>
                    </div>
                    <div class="message-row my-self" v-if="previewImage">
                        <div class="attached-image preview">
                            <i class="icon-close2" @click="detachImage"></i>
                            <a :href="previewImage" class="fancybox"><img :src="previewImage" /></a>
                            <div class="error" v-if="errors['image']">{{ errors['image'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="chat-input">
                    <div class="chat-attach-file">
                        <i class="icon-camera"></i>
                        <input type="file" name="image" @change="attachingFile">
                    </div>
                    <TextAreaComponent
                        id="message"
                        name="message"
                        icon="icon-circle-right2"
                        :max=255
                        v-model:value="message"
                        :error="errors['message']"
                        @icon-click="sendMessage"
                        @keydown.enter.prevent.exact="sendMessage"
                        @change="errors['message']=null"
                    ></TextAreaComponent>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ModalComponent from "./blocks/ModalComponent.vue";
import UserNameComponent from "./blocks/UserNameComponent.vue";
import UserPropertiesComponent from "./blocks/UserPropertiesComponent.vue";
import OrderCarouselImagesComponent from "./blocks/OrderCarouselImagesComponent.vue";
import OrderPropertiesComponent from "./blocks/OrderPropertiesComponent.vue";
import MessageComponent from "./blocks/MessageComponent.vue";
import TextAreaComponent from "./blocks/TextAreaComponent.vue";

export default {
    name: "ChatComponent",
    components: {
        ModalComponent,
        UserNameComponent,
        UserPropertiesComponent,
        OrderCarouselImagesComponent,
        OrderPropertiesComponent,
        MessageComponent,
        TextAreaComponent
    },
    created() {
        let self = this;
        this.userId = parseInt(this.user_id);
        this.chatOrder = JSON.parse(this.order);
        this.messages = this.chatOrder.messages;
        this.myCompanion = JSON.parse(this.companion);
        this.chatDate = this.getDate(this.chatOrder.created_at);
        window.enablePointImagesCarousel();
        window.bindFancybox();
        this.scrollBottom();

        window.Echo.private('chat_' + this.chatOrder.id).listen('.chat', res => {
            if (res.message.user.id !== this.userId) {
                self.messages.push(res.message);
                self.scrollBottom();
                if (res.message.image) self.bindLastFancyBox();

                axios.post(this.read_message_url, {
                    _token: window.tokenField,
                    order_id: self.chatOrder.id
                }).catch(function (error) {console.log(error);});
            }
        });

        window.Echo.channel('order_event').listen('.order', res => {
            if (res.order.id === this.chatOrder.id) {
                if (res.notice === 'remove_order' || res.notice === 'new_order_status') {
                    window.location.href = self.chats_url;
                }
            }
        });

        window.Echo.private('notice_' + this.userId).listen('.notice', res => {
            if (res.notice === 'remove_performer') {
                window.location.href = self.chats_url;
            }
        });
    },
    props: {
        'user_id': String,
        'new_message_url': String,
        'read_message_url': String,
        'chats_url': String,
        'order': String,
        'companion': String,
    },
    data() {
        return {
            userId: Number,
            chatOrder: Number,
            chatDate: String,
            myCompanion: Object,
            message: '',
            messages: [],
            previewImage: null,
            image: null,
            errors: {
                error: {
                    message: null,
                    image: null
                }
            }
        }
    },
    methods: {
        getUserAge(born) {
            return window.getUserAge(born);
        },
        getDate(createdAt) {
            return new Date(createdAt).toLocaleDateString('ru-RU');
        },
        newDate(k) {
            if (k) {
                let lastDate = new Date(this.messages[k-1].created_at).getDate(),
                    currentDate = new Date(this.messages[k].created_at).getDate();
                return lastDate !== currentDate;
            } else return true;
        },
        attachingFile() {
            this.errors['image'] = null;
            let self = this,
                reader = new FileReader();

            this.image = $('input[name=image]')[0].files[0];

            if (this.image.type === 'image/jpeg' || this.image.type === 'image/png') {
                reader.onload = function (e) {
                    self.previewImage = e.target.result;
                    self.scrollBottom();
                    self.bindLastFancyBox();
                };
                reader.readAsDataURL(self.image);
            }
        },
        detachImage() {
            this.errors['image'] = null;
            this.previewImage = null;
            this.image = null;
            $('input[name=image]').val('');
        },
        sendMessage() {
            if (this.message || this.image) {
                let self = this,
                    formData = new FormData();

                formData.append('_token', window.tokenField);
                formData.append('order_id', this.chatOrder.id);
                formData.append('body', this.message);

                if (this.image) formData.append('image', this.image);

                axios.post(this.new_message_url, formData)
                    .then(function (response) {
                        self.message = '';
                        self.previewImage = null;
                        self.image = null;
                        $('input[name=image]').val('');
                        self.messages.push(response.data);
                        self.scrollBottom();
                        if (response.data.image) self.bindLastFancyBox();
                    })
                    .catch(function (error) {
                        console.log(error);
                        $.each(error.response.data.errors, (name,error) => {
                            self.errors[name] = error[0];
                        });
                    });
            }
        },
        scrollBottom() {
            setTimeout(() => {
                window.scrollBottomMessages();
            },500);
        },
        bindLastFancyBox() {
            setTimeout(() => {
                $('a.fancybox').last().fancybox(window.fancyBoxSettings);
            },300);
        }
    }
}
</script>
