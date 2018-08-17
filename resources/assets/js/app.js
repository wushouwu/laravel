
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import ElementUI from 'element-ui';    //引入element－ui
import 'element-ui/lib/theme-chalk/index.css'; //引入element－ui所需的css样式资源文件
//import locale from 'element-ui/lib/locale/lang/en'
//Vue.use(ElementUI, { locale })
Vue.use(ElementUI);    //把引入的ElementUI装入我们的Vue
//引入基础组件
import camelCase from 'lodash/camelCase';
const requireComponent = require.context(
    // 其组件目录的相对路径
    '../components',
    // 是否查询其子目录
    true,
    // 匹配基础组件文件名的正则表达式
    /element[A-Z]\w+\.vue$/
)
requireComponent.keys().forEach(fileName => {
    // 获取组件配置
    const componentConfig = requireComponent(fileName)

    // 获取组件的 PascalCase 命名
    const componentName =camelCase(
        // 剥去文件名开头的 `'./` 和结尾的扩展名
            fileName.replace(/^\.\/\w+(.*)\.\w+$/, '$1')
    )
    // 全局注册组件
    Vue.component(
        componentName,
        // 如果这个组件选项是通过 `export default` 导出的，
        // 那么就会优先使用 `.default`，
        // 否则回退到使用模块的根。
        componentConfig.default || componentConfig
    )
})
//路由
import index from '../templates/index.vue';
import table from '../templates/table.vue';
import components from '../templates/components.vue';
import VueRouter from 'vue-router'; 
Vue.use(VueRouter);
const router = new VueRouter({
  routes: [
    //{ path: '/view', component: view ,props:(route)=>({query:route.query})},
    //{ path: '/table', component: table},
    { path: '/', component: index},    
    //{ path: '/components', component: components},
  ]
});

import {my} from '../js/my.js';
Vue.prototype.my=my;
Vue.prototype.camelCase=camelCase;
const app = new Vue({
    router
}).$mount('#app')