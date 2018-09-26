<template>
    <draggable
        v-model="configs.containers" 
        class="containers draggable"    
        :options="{group:{ name:'view',  pull:false, put: true},preventOnFilter: true,animation: 250}"
        @change="sort"
        @click.native="containersClick"
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
                    "header":{
                        "title":"列表",
                        height:"67px"
                    },
                    "shadow":"always",
                    "width":"98%",
                    "height":"auto"
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
            //模块框点击
            containersClick:function(event){
                this.$emit('accordionChange',event,'模块')
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
        position: relative;
    }

</style>
