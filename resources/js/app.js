/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

import ActiveUsersChatBox from './components/ActiveUsersChatBox.vue'
Vue.component('activeusers-chatbox', ActiveUsersChatBox)

import NewChatBox from './components/NewChatBoxComponent.vue'
Vue.component('NewChatBox', NewChatBox)

import VueChatScroll from 'vue-chat-scroll'
Vue.use(VueChatScroll)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    components: {
        ActiveUsersChatBox,
        NewChatBox
    },
    // router,
});

