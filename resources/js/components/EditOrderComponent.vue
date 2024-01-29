<template>
    <ModalComponent id="complete-modal" head="Ваша заявка успешно зарегистрирована! Сейчас Вы будете перенаправлены на страницу предпросмотра заяки">
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
                    <div class="col-lg-4 col-md-4 col-sm-12" v-for="count in 3" :key="'order-image-' + count">
                        <div :id="'photo' + count" :class="'order-photo' + (errors['photo'+count] ? ' error' : '')" :style="this['photo' + count] ? `background-image:url(/${this['photo' + count]});` : ''">
                            <i class="icon-file-plus2" v-show="!this['photo' + count]"></i>
                            <img class="hover-image" :src="input_image_hover" />
                            <i :class="'icon-close2' + (!this['photo' + count] ? ' d-none' : '')"></i>
                            <input type="file" :name="'photo' + count">
                        </div>
                    </div>
                </div>
            </div>
            <div class="inputs-step" v-show="currentStep === 3">
                <InputComponent
                    type="text"
                    name="address"
                    :error="errors['address']"
                    v-model:value="address"
                    @change="errors['address']=null"
                ></InputComponent>
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
                        @click="prevStep"
                    ></ButtonComponent>
                    <ButtonComponent
                        class="btn btn-secondary link-cover"
                        text="Далее"
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
import ModalComponent from "./blocks/ModalComponent.vue";
import InputComponent from "./blocks/InputComponent.vue";
import TextAreaComponent from "./blocks/TextAreaComponent.vue";
import CheckboxComponent from "./blocks/CheckboxComponent.vue";
import ButtonComponent from "./blocks/ButtonComponent.vue";

export default {
    name: "EditOrderComponent",
    components: {
        ModalComponent,
        InputComponent,
        TextAreaComponent,
        CheckboxComponent,
        ButtonComponent,
    },
    created() {
        if (this.step) this.currentStep = parseInt(this.step);
        this.wizardImages = JSON.parse(this.images);
        this.orderTypes = JSON.parse(this.order_types);
        this.sessionSteps = JSON.parse(this.session);

        if (this.order) {
            let order = JSON.parse(this.order),
                self = this;

            this.orderId = order.id;
            this.currentStep = 1;
            this.stepsHeadsH1[0] = 'Редактирование заявки №:' + order.id;
            this.selectedOrderType = order.order_type_id;
            this.selectedOrderSubType = order.subtype_id;
            this.orderName = order.name;
            this.needPerformers = order.need_performers;

            $.each(order.images, function (k,image) {
                self['photo' + (k + 1)] = image.image;
            });

            window.singlePoint = [order.latitude, order.longitude];
            this.address = order.address;
            this.latitude = order.latitude;
            this.longitude = order.longitude;
            this.descriptionShort = order.description_short;
            this.descriptionShort = order.description_full;
        }

        if (this.sessionSteps.length) {
            this.currentStep = this.sessionSteps.length + 1;
            this.selectedOrderType = this.sessionSteps[0].order_type_id;
            this.selectedOrderSubType = this.sessionSteps[0].subtype_id;
            this.orderName = this.sessionSteps[0].name;

            if (this.sessionSteps.length > 1) {
                this.needPerformers = this.sessionSteps[1].need_performers;
            }

            if (this.sessionSteps.length > 2) {
                window.singlePoint = [this.sessionSteps[1].latitude, this.sessionSteps[1].longitude];
                this.address = this.sessionSteps[1].address;
                this.latitude = this.sessionSteps[1].latitude;
                this.longitude = this.sessionSteps[1].longitude;
            }

            if (this.sessionSteps.length > 3) {
                this.descriptionShort = this.sessionSteps[2].description_short;
                this.descriptionShort = this.sessionSteps[2].description_full;
            }
        }

        window.emitter.on('remove-order-photo', pos => {
            let self = this;
            if (this.orderId) {
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
            point: Array,
            address: '',
            addressError: null,
            latitude: Number,
            longitude: Number,
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
            let self = this;
            if (this.currentStep === 3) {
                if (!this.address) this.errors['address'] = 'Укажите адрес!';
                else {
                    this.address = this.address.indexOf('Москва') >= 0 ? this.address : 'Москва, ' + this.address;

                    axios.get('https://geocode-maps.yandex.ru/1.x/?apikey=' + self.yandex_api_key + '&geocode=' + self.address + '&format=json')
                        .then(function (response) {
                            let geoObjectCollection = response.data.response.GeoObjectCollection;

                            if (parseInt(geoObjectCollection.metaDataProperty.GeocoderResponseMetaData.found) === 1) {
                                self.address = geoObjectCollection.featureMember[0].GeoObject.name;

                                if (window.placemark) window.myMap.geoObjects.remove(window.placemark);
                                let coordinates = geoObjectCollection.featureMember[0].GeoObject.Point.pos.split(' ');
                                window.singlePoint = [parseFloat(coordinates[1]), parseFloat(coordinates[0])];
                                let newPlaceMark = window.getPlaceMark(window.singlePoint, {});

                                window.myMap.geoObjects.add(newPlaceMark)
                                window.zoomAndCenterMap();

                                let fields = {
                                    '_token': window.tokenField,
                                    'address': self.address,
                                    'latitude': window.singlePoint[0],
                                    'longitude': window.singlePoint[1]
                                };

                                if (self.orderId) {
                                    fields['id'] = self.orderId;
                                }

                                axios.post(self.next_step_url, fields)
                                    .then(function (response) {
                                        setTimeout(() => {
                                            self.currentStep++;
                                        }, 1500);
                                    });
                            } else {
                                self.errors['address'] = 'Уточните адресс, он может быть не верным!';
                            }
                        });
                }
            } else {
                let self = this,
                    formData = new FormData();

                formData.append('_token', window.tokenField);
                formData.append('order_type_id', this.selectedOrderType);
                if (this.selectedOrderSubType) formData.append('subtype_id', this.selectedOrderSubType);
                formData.append('name', this.orderName);
                formData.append('need_performers', this.needPerformers);
                if (this.descriptionShort) formData.append('description_short', this.descriptionShort);
                if (this.descriptionShort) formData.append('description_full', this.descriptionShort);

                formData.append('photo1', $('input[name=photo1]')[0].files[0]);
                formData.append('photo2', $('input[name=photo2]')[0].files[0]);
                formData.append('photo3', $('input[name=photo3]')[0].files[0]);
                if (this.orderId) formData.append('id', this.orderId);

                axios.post(this.next_step_url, formData)
                    .then(function (response) {
                        if (self.currentStep !== 4) {
                            self.currentStep++;
                        } else {
                            setTimeout(function () {
                                window.location.href = self.order_preview_url;
                            }, 3000);
                            $('#complete-modal').modal('show').on('hidden.bs.modal', () => {
                                window.location.href = self.order_preview_url;
                            })
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                        $.each(error.response.data.errors, (name,error) => {
                            self.errors[name] = error[0];
                        });
                    });
            }
        },
        prevStep() {
            let self = this;
            axios.get(this.prev_step_url + '?id=' + (this.orderId ? this.orderId : ''))
                .then(function (response) {
                    self.currentStep--;
                })
                .catch(function (error) {
                console.log(error);
            });
        },
    }
}
</script>
