/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import $ from "jquery";
import { createRouter, createWebHistory } from 'vue-router';

window.$ = $;

Number.prototype.toBalance = function(dp) {
    return Number(Math.round(parseFloat((this / 100) + 'e' + dp)) + 'e-' + dp);
}

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
    app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
});

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

const router = new createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'home',
            component: app._context.components.HomePage
        },
        {
            path: '/crates',
            name: 'crates',
            component: app._context.components.Crates
        },
        {
            path: '/crate/:id',
            name: 'open_crate',
            component: app._context.components.OpenCrate,
        },
        {
            path: '/profile',
            name: 'profile',
            component: app._context.components.Profile,
            props: true,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/crate-battles',
            name: 'crate_battles',
            component: app._context.components.CrateBattles,
        },
        {
            path: '/coinflip',
            name: 'coinflip',
            component: app._context.components.CoinflipList,
        },
        {
            path: '/coinflip/:id',
            name: 'coinflip_play',
            component: app._context.components.Coinflip,
        },
    ],
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record =>  record.meta.requiresAuth)) {
        if (!localStorage.getItem("isLoggedIn")) {
            next({ name: 'home' });
        } else {
            next();
        }
    } else {
      next();
    }
});

app.use(router);

app.mount('#app');




$(function(){
    $(".nav_balance").on("updateBalance", function(event, amount){
        let updateAmount = Number(amount).toBalance(2);
        let $balancePopup = $(".balance-popup");

        $balancePopup.find(".balance-text").text(updateAmount.toLocaleString());

        showBalancePopup(updateAmount, updateAmount > 0);

        let updatedBalance = Number($(this).text().replace(",", "")) + updateAmount;
        $(this).text(updatedBalance.toLocaleString());
    });
});

function showBalancePopup(amount, positive) {
    let $popupDiv = null;
    if(positive) {
        $popupDiv = $("<div>", {
            "class": "d-flex balance-popup",
            "style": "z-index: 999; position: fixed; left: 50%; top: 3%; transform: translate(-50%, -9%); opacity: 0.2; border: 1px #085a34 solid; border-radius: 4px; padding: 0.2rem; background-color: #198754;"
        });
    } else {
        $popupDiv = $("<div>", {
            "class": "d-flex balance-popup",
            "style": "z-index: 999; position: fixed; left: 50%; top: 3%; transform: translate(-50%, -9%); opacity: 0.2; border: 1px #b60f1f solid; border-radius: 4px; padding: 0.2rem; background-color: #DC3545;"
        });
    }

    $popupDiv.append($("<img>", {"src": "/storage/radianite.png", "class": "currency-icon"}));
    $popupDiv.append($("<div>", {"class": "balance-text", "text": amount.toLocaleString()}));

    $(".nav-balance").append($popupDiv);

    setTimeout(function(){
        $popupDiv.css({
            'transition-timing-function':'linear',
            'transition-duration':'0.5s',
            'transform':'translate(-50%, 71%)',
            'opacity': '1'
        });
    }, 20);

    setTimeout(resetBalancePopup, 650);
}

function resetBalancePopup() {
    $(".nav-balance .balance-popup").remove();
}


