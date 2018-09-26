<template>
    <el-card
        :shadow="config.shadow"
        class="elementCard"
        :style="'width:'+config.width+';'+'height:'+config.height+';'+config.style"
        :body-style="config.bodyStyle"
        v-resizable="config"
        @click.native="click" 
    >
        <div 
            slot="header" 
            v-if="config.header && config.header.title"
        >
            <span>{{config.header.title}}</span>
        </div>
        <div v-for="o in 4" :key="o">
            {{'列表内容 ' + o }}
        </div>
    </el-card>
</template>
<script>
    function resizable(el,binding){
        $(el).resizable({
            containment: 'parent',
            grid: [ 3, 3 ],
            stop: function(event,ui){					
                let size=ui.element.size_px2percent()
                Object.assign(binding.value,size);
            }                                         
        })
        .find('.el-card__header')
            .resizable({
                containment: 'parent',
                handles:"s",
                stop: function(event,ui){					
                    let height=ui.element.css('height');
                    binding.value.header.height=height;
                }                                         
            });
    }
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
            this.headerHeightSet();
        },
        watch:{
            headerHeight(newValue,oldValue){
                let vue=this;
                this.$nextTick(()=>{
                    vue.headerHeightSet();
                });
            }
        },
        computed:{
            headerHeight(){
                console.log('a');
                return this.config.header.height;
            }
        },
        directives:{
            resizable:{
                inserted(el,binding,vnode){
                    resizable(el,binding);
                },               
                update(el,binding,vnode){
                    $(el).resizable( "destroy");
                    resizable(el,binding);                   
                }
            }
        },        
        methods:{
            //头部高度
            headerHeightSet(){
                if(this.config.header.height){
                    let card__header=this.$el.querySelector('.el-card__header');
                    if(card__header){
                        card__header.style.height=this.config.header.height;
                    }

                }
            },
            click: function(event){
                this.$emit('config',event,this.config,{
                    header:{
                        type:"elementComponents",
                        label:"头部",
                        name:"header",
                        labelPositionTop:true,
                        itemDefault:{
                            size:"small"
                        },
                        options:[{
                            type:"elementText",
                            label:"标题",
                            name:"title"
                        },{
                            type:"elementText",
                            label:"高度",
                            name:"height"
                        }]
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
                    width:{
                        type:"elementText",
                        label:"宽度",
                        name:"width",
                        placeholder:"例:50%,可拖动模块右边框设置"
                    },
                    height:{
                        type:"elementText",
                        label:"高度",
                        name:"height",
                        placeholder:"例:50%,可拖动模块下边框设置"
                    },                                                                      
                });
            },
            enter(event){
                this.$emit('enter',event,this.config);
            }
        }
    }
</script>
