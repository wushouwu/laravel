<template>
    <draggable
        v-model="configs.containers" 
        class="containers draggable"
        :options="{group:{ name:'view',  pull:false, put: true},preventOnFilter: true,animation: 250}"
        @change="sort"
        @click.native="fieldsClick"
    >
        <component 
            v-for="(container, key) in configs.containers" 
            :key="key" 
            :is="container.type" 
            :config="container" 
            :class="{active:activeContainer===key}" 
            @click.native="activeContainer=key;"
            @config="toConfig"
            @close="del(key,'containers')"
        >
        </component>
    </draggable>
</template>  
<script>
    import draggable from 'vuedraggable';
    export default {
        components: {
            draggable
        },
        props: ['query'],
        data() {
            return {
                activeContainer:'',
                configs: {
                    containers:[]
                }
            }
        },
        created: function(){
            let configs={
                containers:[{
                    "type":"elementCard",
                    "header":"列表",
                    "shadow":"always",
                    "width":"98%"
                }]
            };
            if(this.query.row){
                //表单信息                
                this.my.axios({
                    vue: this,
                    axiosOption:{
                        url:'admin/table/view',
                        data:this.query.row
                    },
                    success:function(response,option){
                        option.vue.configs=Object.assign(option.vue.configs,response.data.data);
                    },
                    error:function(response,option){
                        option.vue.configs=configs;
                    }
                });                
            }else{
                this.configs=configs;
            }
        }, 
        watch:{
            configs:{
                handler(newValue,oldValue){
                    this.$emit('configChange',newValue);
                },
                deep:true
            },
            type(newValue,oldValue){
                if(newValue=='form'){
                    this.$emit('configChange',this.configs);
                }
            }
        },
        computed:{
            type(){
                return this.query.row.type;
            } 
        },
        methods: {
            //排序
            sort: function(event){
                //添加字段时设置字段属性
                if('added' in event){
                }
            },
            //删除组件
            del(key,type){
                this.$delete(this.configs[type],key);
                this.$emit('del')   
            },
            //点击字段框 事件
            fieldsClick:function(event){
                this.$emit('fieldsClick',event)
            },
            //form配置
            toolsClick(event){
                if(event.target==event.currentTarget ){ 
                    this.activeField='';this.activeButton=''                
                    this.$emit('toConfig',event,this.configs.forms,[{
                        "type":"elementSwitch",
                        "label":"只读",
                        "name":"disabled"
                    },{
                        "type":"elementSelect",
                        "label":"尺寸",
                        "name":"size",
                        "options":[{
                            label:"默认",
                            value:""
                        },{
                            label:"中等",
                            value:"medium"
                        },{
                            label:"小型",
                            value:"small"
                        },{
                            label:"超小",
                            value:"mini"
                        }]
                    },{
                        "type":"elementSwitch",
                        "label":"单行模式",
                        "name":"inline"
                    },{
                        "type":"elementSelect",
                        "label":"标签对齐",
                        "name":"labelPosition",
                        "options":[{
                            label:"右边",
                            value:"right"
                        },{
                            label:"顶部",
                            value:"top"
                        },{
                            label:"左边",
                            value:"left"
                        }]                        
                    },{
                        "type":"elementText",
                        "label":"标签宽度",
                        "name":"labelWidth",
                        "placeholder":"例: 80px"
                    },{
                        "type":"elementSwitch",
                        "label":"校验信息",
                        "name":"showMessage"
                    },{
                        "type":"elementSwitch",
                        "label":"反馈图标",
                        "name":"statusIcon"
                    }])                                
                }                 
            },                
            //配置字段组件
            toConfig:function(event,config,attr){            
                this.$emit('toConfig',event,config,attr)
            }
        }
    }
</script>
<style>
    /* 框 */
    .containers.draggable{
        overflow: auto;
        width:auto;
        height:100%;       
        border:1px dashed #c0c4cc;
    }

</style>
