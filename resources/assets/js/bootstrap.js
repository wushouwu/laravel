
window._ = require('lodash');
window.Popper = require('popper.js').default;

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');
	//调整子元素margin
	$.fn.extend({
        children_margin:function(option){
            var default_option={
                toSide: '.elementCard',
                children: '*',
                margin: 1
            }
            var option=$.extend(default_option,option);
            if($(this).hasClass(option.toSide)){
                var w_percent2px=$(this).width()*option.margin/100;
                var h_percent2px=$(this).height()*option.margin/100;	
                option.margin=option.margin+'%';
                $(this).children(option.children).each(function(index,dom){  
                    var position=$(dom).position();
                    var left=position.left>w_percent2px?option.margin:'0px';
                    var top=position.top>h_percent2px?option.margin:'0px';
                    $(dom).css({margin:top+' 0px 0px '+left});
                });	
            }else{                
                option.margin=option.margin+'%';
                $(this).children(option.children).each(function(index,dom){
                    $(dom).css({margin:option.margin+' 0px 0px '+option.margin});
                });	
            }
            return this;	
        },
        size_px2percent:function(parent,size){
            var obj=$(this);
            var parent=parent||obj.parent();
            var p_width=parent.width();
            var p_height=parent.height();
            var size=size||{width:obj.width(),height:obj.height()};
            var width=size.width/p_width*100+'%';
            var height=size.height/p_height*100+'%';
            var res={width:width,height:height};
            obj.css(res);
            return res;
        }        
    })
    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;
window.axios.defaults.timeout = 30000;
/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });
