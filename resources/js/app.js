/**
 * @author Elias Champi
 * @copyright (c) 2023 Elias Champi
 * @license GPL-3.0-or-later
 */
import mitt from "mitt";
import { createPinia } from "pinia";
import { createApp } from "vue";
import App from "./App.vue";
import initializeComponents from "./core/components.js";
import initializeProperties from "./core/properties.js";
import "./core/validation";
import { CanDirective } from "./directives/can.js";
import { YearDirective } from "./directives/year";
import router from "./router/index.js";

//app
const app = createApp(App);

//core
initializeComponents(app);
initializeProperties(app);

//router
app.use(router);

//store
const pinia = createPinia();
app.use(pinia);

//directive
app.directive("can", CanDirective);
app.directive("year", YearDirective);

//emitter
const emitter = mitt();
app.provide("emitter", emitter);

//start
app.mount("#app");
