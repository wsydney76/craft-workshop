import Vue from 'vue';
import VueRouter from 'vue-router';
import VueI18n from 'vue-i18n';
import Main from './components/Main.vue';
import Archive from './components/Archive.vue';
import Entry from './components/Entry.vue';

Vue.use(VueRouter);
Vue.use(VueI18n);
const i18n = new VueI18n({
    locale: 'en',
    fallbackLocale: 'en',
    messages: {
        de: {
            'Original Title': 'Originaltitel',
            'Release Year': 'Erscheinungsjahr',
            Born: 'Geboren',
            Cast: 'Besetzung',
            Died: 'Gestorben',
            Films: 'Filme'
        },
    }
});

const router = new VueRouter({
    routes: [
        {name: 'main', path: '/', component: Main},
        {name: 'mainLang', path: '/:site', component: Main},
        {name: 'archive', path: '/:site/:slug', component: Archive},
        {name: 'entry', path: '/:site/:section/:id/:slug', component: Entry}
    ]
})

const app = new Vue({
    router,
    i18n,
    el: '#app'
});
