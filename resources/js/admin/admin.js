import '../bootstrap';
import {createApp} from "vue/dist/vue.esm-bundler";
import mitt from 'mitt';
import AvatarComponent from "../components/blocks/AvatarComponent.vue";
import OrdersComponent from "../components/admin/OrdersComponent.vue";
import OrderComponent from "../components/admin/OrderComponent.vue";
import UserRatingComponent from "../components/admin/blocks/UserRatingComponent.vue";
import UsersComponent from "../components/admin/UsersComponent.vue";
import UserComponent from "../components/admin/UserComponent.vue";
import EditOrderMapComponent from "../components/admin/blocks/EditOrderMapComponent.vue";
import NoticeComponent from "../components/admin/blocks/NoticeComponent.vue";
import PartnersComponent from "../components/admin/PartnersComponent.vue";
import PartnerComponent from "../components/admin/PartnerComponent.vue";
import ActionsComponent from "../components/admin/ActionsComponent.vue";
import ActionComponent from "../components/admin/ActionComponent.vue";
import TicketsComponent from "../components/admin/TicketsComponent.vue";
import TicketComponent from "../components/admin/TicketComponent.vue";
import AnswersComponent from "../components/admin/AnswersComponent.vue";
import AnswerComponent from "../components/admin/AnswerComponent.vue";

const app = createApp({
    components: {
        UsersComponent,
        UserComponent,
        OrdersComponent,
        OrderComponent,
        AvatarComponent,
        UserRatingComponent,
        EditOrderMapComponent,
        NoticeComponent,
        PartnersComponent,
        PartnerComponent,
        ActionsComponent,
        ActionComponent,
        TicketsComponent,
        TicketComponent,
        AnswersComponent,
        AnswerComponent
    }
});

window.emitter = mitt();
app.config.globalProperties.emitter = window.emitter;
app.mount('#app');

// window.allMonths = ['Янв.', 'Фев.', 'Март', 'Апр.', 'Май', 'Июнь', 'Июль', 'Авг.', 'Сент.', 'Окт.', 'Нояб.', 'Декаб.'];
// window.statisticsData = [];
$(document).ready(function () {
    $('.styled').uniform();

    $.mask.definitions['c'] = "[1-2]";
    $('input[name=phone]').mask("+7(999)999-99-99");
    $('input[name=born]').mask("99-99-9999");

    // File input
    $(".file-styled").uniform({
        wrapperClass: 'bg-blue',
        fileButtonHtml: '<i class="icon-file-plus"></i>'
    });

    // Message modal
    const messageModal = $('#message-modal');
    if (messageModal.find('h4').html()) messageModal.modal('show');

    initCKEditor('info',300);
    initCKEditor('html',680);

    // Init avatar
    setTimeout(() => {
        window.initImages();
        window.bindFancybox();
        window.mapStepsInit();
    }, 500);

    // Enable-disable subtypes radio-buttons in edit order page
    $('input[name=order_type_id]').change(function () {
        let currentSubtypesBlock = $(this).parents('.form-group').find('.subtypes'),
            allSubtypesContainer = $('.subtypes');

        allSubtypesContainer.addClass('hidden');
        allSubtypesContainer.find('input[type=radio]').prop('checked', false);

        if (currentSubtypesBlock.length) {
            currentSubtypesBlock.removeClass('hidden');
            let subtypesRadioButtons = currentSubtypesBlock.find('input[type=radio]');
            $(subtypesRadioButtons[0]).prop('checked', true);
        }

    });

    // Remove order image in edit order page
    $('.order-photo i.icon-close2').click(function () {
        let inputId = $('input[name=id]'),
            photoContainer = $(this).parents('.order-photo'),
            photoExist = photoContainer.attr('photo_exist'),
            pos = parseInt($(this).attr('id').replace('remove-',''));
        if (inputId.length && photoExist !== undefined) {
            axios.post('/admin/delete-order-image', {
                '_token': window.tokenField,
                'id': inputId.val(),
                'pos': pos
            }).then(function () {
                photoContainer.removeAttr('photo_exist');
                photoContainer.find('.icon-file-plus2').removeClass('hidden')
            });
        }
    });

    // Single picker
    $('.daterange-single').daterangepicker({
        onSelect: function(dateText) {
            console.log(dateText);
        },
        singleDatePicker: true,
        locale: {
            format: 'DD/MM/YYYY',
            monthNames : ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
            daysOfWeek : ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
            week: moment.locale('en', {
                week: { dow: 1 }
            })
        },
    }).on('apply.daterangepicker', function(e) {
        window.emitter.emit('date-change',{name: $(e.target).attr('name'), value: $(e.target).val()});
        $(e.target).parents('.date').find('.error').html('');
    });
});

const initCKEditor = (name, height) => {
    if ($('textarea[name='+ name +']').length) {
        window.editor = CKEDITOR.replace(name,{
            height: height + 'px'
        });
    }
}
