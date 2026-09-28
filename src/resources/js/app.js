/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from 'vue';
import vClickOutside from 'v-click-outside';

import tippy, { sticky } from 'tippy.js'
import { VuePicker, VuePickerOption } from '@invisiburu/vue-picker';
import { Mentionable } from 'vue-mention';
import VueChatScroll from 'vue-chat-scroll';
import { Hooper, Slide, Navigation as HooperNavigation } from 'hooper';
import { Editor, EditorContent, EditorMenuBubble, EditorMenuBar } from 'tiptap';
import Fuse from 'fuse.js'
import {
    Blockquote,
    BulletList,
    CodeBlock,
    HardBreak,
    Heading,
    ListItem,
    OrderedList,
    TodoItem,
    TodoList,
    Bold,
    Code,
    Italic,
    Link,
    History,
    Mention,
} from 'tiptap-extensions'

import 'hooper/dist/hooper.css';
import VueSlickCarousel from 'vue-slick-carousel'
import 'vue-slick-carousel/dist/vue-slick-carousel-theme.css'

Vue.use(vClickOutside);
Vue.use(VueChatScroll);

require('./bootstrap');

window.Vue = require('vue').default;
window.draggable = require('vuedraggable');
window.Editor = Editor;

window.Blockquote = Blockquote;
window.BulletList = BulletList;
window.CodeBlock = CodeBlock;
window.HardBreak = HardBreak;
window.Heading = Heading;
window.ListItem = ListItem;
window.OrderedList = OrderedList;
window.TodoItem = TodoItem;
window.TodoList = TodoList;
window.Bold = Bold;
window.Code = Code;
window.Italic = Italic;
window.Link = Link;
window.History = History;
window.Mention = Mention;
window.sticky = sticky;
window.tippy = tippy;
window.Fuse = Fuse;

Vue.component('VueSlickCarousel', VueSlickCarousel)
Vue.component('EditorContent', EditorContent)
Vue.component('EditorMenuBubble', EditorMenuBubble)
Vue.component('EditorMenuBar', EditorMenuBar)
Vue.component('Hooper', Hooper)
Vue.component('Slide', Slide)
Vue.component('HooperNavigation', HooperNavigation)

Vue.component('VuePicker', VuePicker)
Vue.component('VuePickerOption', VuePickerOption)

Vue.component('Mentionable', Mentionable)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('my_page_main', require('./components/MyPageMain').default);
Vue.component('my-item-modal', require('./components/MyItemModal').default);
Vue.component('my-class-main', require('./components/MyClassMain').default);
Vue.component('my-class-setting', require('./components/MyClassSetting').default);
Vue.component('my-page-calendar-modal', require('./components/MyPageCalendarModal').default);
Vue.component('my-calendar', require('./components/MyCalendar').default);
Vue.component('my-calendar2', require('./components/MyCalendar2').default);
Vue.component('my-calendar-modal', require('./components/MyCalendarModal').default);
Vue.component('my-calendar-modal2', require('./components/MyCalendarModal2').default);
Vue.component('admin-log', require('./components/AdminLog').default);
Vue.component('pro-log', require('./components/ProLog').default);
Vue.component('act-log', require('./components/ActLog').default);
Vue.component('sch-log', require('./components/ScheduleLog').default);
Vue.component('my-bc', require('./components/ItemBC').default);
Vue.component('dash-top', require('./components/DashboardTop').default);
Vue.component('lecture-etc', require('./components/lecture/LectureETCMember').default);


//Lecture
//Vue.component('lecture-list', require('./components/LectureList').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    components: {
        draggable,
    }
});
