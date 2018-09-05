<template>
    <el-card
        :shadow="config.shadow"
        class="elementCard"
        :style="'width:'+config.width+';'+'height:'+config.height+';'+config.style"
        v-resizable="config"
        @click.native="click" 
    >
        <div slot="header" v-if="'header' in config">
            <span>{{config.header}}</span>
        </div>
        <div v-for="o in 4" :key="o">
            {{'列表内容 ' + o }}
        </div>
    </el-card>
</template>
<script>
    import  'jquery-ui/themes/base/resizable.css';
    import  'jquery-ui/ui/widgets/resizable';
    export default {
        props: ['config','form'], 
        created(){   
        },
        mounted(){
            //组件添加删除按钮
            let vue=this,
                span=document.createElement('span');
            span.className="my-close";
            span.addEventListener('click',function(){
                vue.$emit('close',event,vue);
            });
            this.$el.style.position="relative";
            this.$el.appendChild(span);
        },
        computed:{

        },
        directives:{
            resizable:{
                inserted(el,binding,vnode){
                    $('.containers').children_margin();
                    el.vue=vnode.componentInstance;
                    $(el).resizable({
                        containment: 'parent',
                        grid: [ 5, 5 ],
                        minHeight: 200,
                        minWidth: 200,
                        autoHide: true,
							start: function(event,ui){
								ui.element.children_margin({children: '.ui-sortable'});								
							},
							stop: function(event,ui){
                                console.log('e',ui.element);					
                                let size=ui.element
                                    //  .children_margin({children: '.ui-sortable'})
                                    .size_px2percent()
                                //Object.assign(binding.value,size);
                                ui.element.get(0).vue.$set(binding.value,'width',size.width);
                                ui.element.get(0).vue.$set(binding.value,'height',size.height);
							}                                         
                    });
                }
            }
        },        
        methods:{            
            click: function(event){
                this.$emit('config',event,this.config,{
                    width:{
                        type:"elementText",
                        label:"宽度",
                        name:"width",
                        placeholder:"例:50%"
                    },
                    height:{
                        type:"elementText",
                        label:"高度",
                        name:"height",
                        placeholder:"例:50%"
                    },                       
                    shadow:{
                        type:"elementSelect",
                        label:"阴影",
                        name:"shadow",
                        options:[{
                            label:"总是显示",
                            value:"always",
                        },{
                            label:"浮时显示",
                            value:"hover",
                        },{
                            label:"从不显示",
                            value:"never",
                        }]
                    },
                    header:{
                        type:"elementText",
                        label:"标题",
                        name:"header"
                    }                                                  
                });
            },
            enter(event){
                this.$emit('enter',event,this.config);
            }
        }
    }
</script>
