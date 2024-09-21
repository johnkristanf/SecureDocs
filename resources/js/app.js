import './bootstrap';

import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link } from '@inertiajs/vue3'
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import { faEllipsisVertical, faFile, faFileLines, faFilePdf, faGear, faImage, faPlus, faRightFromBracket, faTable, faTrashCan } from '@fortawesome/free-solid-svg-icons';

import GuestLayout from './Layout/GuestLayout.vue'
import AuthenticatedLayout from './Layout/AuthenticatedLayout.vue'


library.add(
    faFile, 
    faTrashCan, 
    faGear, 
    faRightFromBracket, 
    faFileLines, 
    faPlus, 
    faTable, 
    faFilePdf, 
    faImage,
    faEllipsisVertical
);

createInertiaApp({

    title: (title) => title,

    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        let page =  pages[`./Pages/${name}.vue`]

        const guestPages = ['Auth/Login', 'Auth/Register', 'LandingPage'];

        if (guestPages.includes(name)) {
            page.default.layout = GuestLayout;
        } else {
            page.default.layout = AuthenticatedLayout;
        }

        return page;
    },

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(ZiggyVue)
        .component('font-awesome-icon', FontAwesomeIcon)
        .component('Head', Head)
        .component('Link', Link)
        .mount(el)
    },

    progress: {
        color: 'blue',
        includeCSS: true,
        showSpinner: true,
    },
})