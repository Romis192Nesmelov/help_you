<template>
    <ModalComponent id="complete-modal" head="Ваша заявка успешно зарегистрирована! Сейчас Вы будете перенаправлены на страницу предпросмотра заяки.">
        <img class="w-100" :src="wizardImages[4]" />
    </ModalComponent>

    <div class="col-12 col-lg-6">
        <div class="rounded-block tall">
            <h1 class="mb-4">{{ stepsHeadsH1[currentStep-1] }}</h1>
            <div id="progress-bar" v-show="currentStep > 1">
                <label>Заявка сформирована на:</label>
                <div class="progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" :style="'width:'+ ((currentStep - 1) * 25) + '%'">{{ ((currentStep - 1) * 25) + '%' }}</div>
                </div>
            </div>
            <h2>{{ stepsHeadsH2[currentStep-1] }}</h2>
            <div class="inputs-step" v-show="currentStep === 1">
                <div class="radio-group" v-for="orderType in orderTypes" :key="'orderType' + orderType.id">
                    <div class="form-check mb-1">
                        <input
                            :id="'order-type-' + orderType.id"
                            name="order_type"
                            :value="orderType.id"
                            class="form-check-input"
                            type="radio"
                            v-model="selectedOrderType"
                        >
                        <label class="form-check-label" :for="'order-type-' + orderType.id">{{ orderType.name }}</label>
                    </div>
                    <div class="sub-types-block" v-if="orderType.subtypes_active.length && orderType.id === selectedOrderType">
                        <div class="form-check mb-1 small" v-for="orderSubType in orderType.subtypes_active" :key="'orderType' + orderSubType.id">
                            <input
                                :id="'order-sub-type-' + orderSubType.id"
                                name="order_sub_type"
                                :value="orderSubType.id"
                                class="form-check-input"
                                type="radio"
                                v-model="selectedOrderSubType"
                            >
                            <label class="form-check-label" :for="'order-sub-type-' + orderSubType.id">{{ orderSubType.name }}</label>
                        </div>
                    </div>
                </div>
                <h2 class="mt-4">Название заявки</h2>
                <InputComponent
                    type="text"
                    name="name"
                    min="3"
                    max="50"
                    placeholder="Введите название заявки"
                    :error="errors['name']"
                    v-model:value="orderName"
                    @change="errors['name']=null"
                ></InputComponent>
            </div>
            <div class="inputs-step" v-show="currentStep === 2">
                <InputComponent
                    type="number"
                    name="need_performers"
                    min="1"
                    max="20"
                    :error="errors['need_performers']"
                    v-model:value="needPerformers"
                    @change="errors['need_performers']=null"
                ></InputComponent>
                <h2 class="mt-4">Добавьте изображения</h2>
                <div class="row">
                    <OrderImagesComponent
                        :errors="errors"
                        :photo1="photo1"
                        :photo2="photo2"
                        :photo3="photo2"
                        :input_image_hover="input_image_hover"
                    ></OrderImagesComponent>
                </div>
            </div>
            <div class="inputs-step" v-show="currentStep === 3">
                <OrderMapComponent
                    :incoming_address="address"
                    :yandex_api_key="yandex_api_key"
                    :get_place_mark="false"
                    @disabled-buttons="disableButtons"
                    @set-place-mark="setPlaceMark"
                    ref="OrderMapComponent"
                ></OrderMapComponent>
            </div>
            <div class="inputs-step" v-show="currentStep === 4">
                <TextAreaComponent
                    label="Краткое описание (до 200 символов)"
                    name="description_short"
                    max="200"
                    :value="descriptionShort"
                    v-model:value="descriptionShort"
                    :error="errors['description_short']"
                    @change="errors['description_short']=null"
                ></TextAreaComponent>
                <TextAreaComponent
                    label="Полное описание (до 1000 символов)"
                    name="description_full"
                    max="1000"
                    :value="descriptionFull"
                    v-model:value="descriptionFull"
                    :error="errors['description_full']"
                    @change="errors['description_full']=null"
                ></TextAreaComponent>
            </div>
            <div class="bottom-block">
                <div class="d-flex align-items-center justify-content-center justify-content-md-end">
                    <ButtonComponent
                        v-if="currentStep > 1"
                        class="btn btn-secondary link-cover"
                        text="Назад"
                        :disabled="disabledButtons"
                        @click="prevStep"
                    ></ButtonComponent>
                    <ButtonComponent
                        class="btn btn-secondary link-cover"
                        text="Далее"
                        :disabled="disabledButtons"
                        @click="nextStep"
                    ></ButtonComponent>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="rounded-block three-quarter d-none d-md-flex align-items-center justify-content-center">
            <img class="right-image" :src="wizardImages[currentStep - 1]" v-show="currentStep !== 3">
            <div id="map-steps" v-show="currentStep === 3"></div>
        </div>
        <div class="rounded-block one-quarter">
            <p>{{ stepsDescriptions[currentStep - 1] }}</p>
        </div>
    </div>
</template>

<script>
import OrderMapComponent from "./blocks/OrderMapComponent.vue";
import ModalComponent from "./blocks/ModalComponent.vue";
import InputComponent from "./blocks/InputComponent.vue";
import TextAreaComponent from "./blocks/TextAreaComponent.vue";
import CheckboxComponent from "./blocks/CheckboxComponent.vue";
import OrderImagesComponent from "./blocks/OrderImagesComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";

export default {
    name: "EditOrderComponent",
    components: {
        OrderMapComponent,
        ModalComponent,
        InputComponent,
        TextAreaComponent,
        CheckboxComponent,
        OrderImagesComponent,
        ButtonComponent,
    },
    created() {
        let self = this;

        if (this.step) this.currentStep = parseInt(this.step);
        this.wizardImages = JSON.parse(this.images);
        this.orderTypes = JSON.parse(this.order_types);
        this.sessionSteps = JSON.parse(this.session);
        this.selectedOrderType = 1;
        this.selectedOrderSubType = 1;

        if (this.order) {
            let order = JSON.parse(this.order);

            this.orderId = order.id;
            this.currentStep = 1;
            this.stepsHeadsH1[0] = 'Редактирование заявки №:' + order.id;
            this.selectedOrderType = parseInt(order.order_type_id);
            this.selectedOrderSubType = parseInt(order.subtype_id);
            this.orderName = order.name;
            this.needPerformers = order.need_performers;

            for (let i=1;i<=3;i++) {
                this.photo = null;
            }

            $.each(order.images, function (k,image) {
                self['photo' + image.position] = image.image;
            });

            window.singlePoint = [order.latitude, order.longitude];
            this.address = order.address;
            this.descriptionShort = order.description_short;
            this.descriptionFull = order.description_full;
        }

        if (this.sessionSteps.length) {
            this.currentStep = this.sessionSteps.length + 1;
            this.selectedOrderType = parseInt(this.sessionSteps[0].order_type_id);
            this.selectedOrderSubType = parseInt(this.sessionSteps[0].subtype_id);
            this.orderName = this.sessionSteps[0].name;

            if (this.sessionSteps.length > 1) {
                this.needPerformers = this.sessionSteps[1].need_performers;
            }

            if (this.sessionSteps.length > 2) {
                window.singlePoint = [this.sessionSteps[2].latitude, this.sessionSteps[2].longitude];
                this.address = this.sessionSteps[2].address;
            }
        }

        window.emitter.on('remove-order-photo', pos => {
            let self = this;
            if (this.orderId && this['photo' + pos]) {
                axios.post(this.delete_order_image_url, {
                    '_token': window.tokenField,
                    'id': self.orderId,
                    'pos': pos
                }).then(function (response) {
                    self['photo' + pos] = null;
                });
            }
        });
    },
    props: {
        'next_step_url': String,
        'prev_step_url': String,
        'order_preview_url': String,
        'delete_order_image_url': String,
        'images': String,
        'order_types': String,
        'session': String,
        'order': String,
        'yandex_api_key': String,
        'input_image_hover': String
    },
    data() {
        return {
            disabledButtons: false,
            sessionSteps: Array,
            currentStep: 1,
            wizardImages: Array,
            orderId: null,
            orderTypes: Array,
            selectedOrderType: 1,
            orderSubTypes: Array,
            selectedOrderSubType: null,
            orderName: '',
            needPerformers: 1,
            photo1: null,
            photo2: null,
            photo3: null,
            // geoObjectCollection: null,
            // featureMemberIndex: 0,
            address: '',
            // addresses: [],
            descriptionShort: '',
            descriptionFull: '',
            stepsHeadsH1: [
                'Создайте новую заявку!',
                'Выберите количество помощников',
                'Укажите адрес оказания помощи',
                'Дополнительная информация'
            ],
            stepsHeadsH2: [
                'Вид помощи',
                'Количество исполнителей',
                'Адрес оказания помощи',
                'Укажите необходимую информацию'
            ],
            stepsDescriptions: [
                'Выберите требуемый вам вид помощи и нажмите «Далее»',
                'Выберите количество исполнителей и нажмите кнопку «Далее»',
                'Укажите адрес оказания помощи и нажмите кнопку «Далее»',
                'Напишите дополнительную информацию о необходимой помощи и нажмите кнопку «Далее»'
            ],
            errors: {
                name: null,
                need_performers: null,
                photo1: null,
                photo2: null,
                photo3: null,
                address: null,
                description_short: null,
                description_full: null,
            },
        }
    },
    methods: {
        nextStep() {
            if (this.currentStep === 3) {
                if (!this.$refs.OrderMapComponent.address) this.$refs.OrderMapComponent.addressError = 'Укажите адрес!';
                else this.$refs.OrderMapComponent.getApiPlaceMark();
            } else {
                let self = this,
                    formData = new FormData();

                this.disabledButtons = true;

                formData.append('_token', window.tokenField);
                formData.append('order_type_id', this.selectedOrderType);
                if (this.selectedOrderSubType) formData.append('subtype_id', this.selectedOrderSubType);
                formData.append('name', this.orderName);
                formData.append('need_performers', this.needPerformers);
                if (this.descriptionShort) formData.append('description_short', this.descriptionShort);
                if (this.descriptionFull) formData.append('description_full', this.descriptionFull);

                formData.append('photo1', $('input[name=photo1]')[0].files[0]);
                formData.append('photo2', $('input[name=photo2]')[0].files[0]);
                formData.append('photo3', $('input[name=photo3]')[0].files[0]);
                if (this.orderId) formData.append('id', this.orderId);

                axios.post(this.next_step_url, formData)
                    .then(function (response) {
                        if (self.currentStep !== 4) {
                            self.currentStep++;
                            self.disabledButtons = false;
                        } else {
                            setTimeout(function () {
                                window.location.href = self.order_preview_url;
                            }, 3000);
                            $('#complete-modal').modal('show').on('hidden.bs.modal', () => {
                                window.location.href = self.order_preview_url;
                            });
                        }
                    })
                    .catch(function (error) {
                        // console.log(error);
                        self.disabledButtons = false;
                        $.each(error.response.data.errors, (name,error) => {
                            self.errors[name] = error[0];
                        });
                    });
            }
        },
        prevStep() {
            let self = this;
            this.disabledButtons = true;
            axios.get(this.prev_step_url + '?id=' + (this.orderId ? this.orderId : ''))
                .then(function (response) {
                    self.currentStep--;
                    self.disabledButtons = false;
                })
                .catch(function (error) {
                console.log(error);
            });
        },
        disableButtons(flag) {
            this.disabledButtons = flag;
        },
        setPlaceMark(address) {
            this.address = address;
            let fields = {
                '_token': window.tokenField,
                'address': this.address,
                'latitude': window.singlePoint[0],
                'longitude': window.singlePoint[1]
            };

            if (this.orderId) fields['id'] = this.orderId;

            axios.post(this.next_step_url, fields)
                .then(() => {
                    setTimeout(() => {
                        this.currentStep++;
                        this.disabledButtons = false;
                    }, 1500);
                });
        },
    }
}
</script>
