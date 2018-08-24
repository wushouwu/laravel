<template>
    <el-form  :model="configs.form" status-icon label-width="80px" style="width:100%;height:100%;position:relative;">            
        <div  style="overflow:auto;height:100%;">
            <draggable
                v-model="configs.fields" 
                style="width:100%;height:100%"
                class="fields draggable"
                :options="{group:{ name:'view',  pull:false, put: true},preventOnFilter: true,animation: 250}"
                @change="sort"
                @click.native="draggableClick"
            >
                <component 
                    v-for="(field, key,index) in configs.fields" 
                    :key="index" 
                    :is="field.type" 
                    :config="field" 
                    :form="configs.form"
                    :class="{active:activeField===key}"
                    @config="toConfig"
                    @click.native="activeField=key;activeButton='';"
                    @close="del(key,'fields')"
                >
                </component>
            </draggable> 
        </div>
        <draggable
            class="tools draggable"
            style="width:99%;min-height:42px;display:flex;justify-content:center;align-items:center;position:absolute;bottom:21px;border:1px dashed #c0c4cc;"
            v-model="configs.tools" 
            :options="{group:{ name:'view',  pull:false, put: true},preventOnFilter: true,animation: 250}"
            @change="buttonChange"
        >
            <elementButton
                v-for="(tool,key) in configs.tools"
                :key="key"
                :config="tool"
                :class="{active:activeButton===key}"
                @config="toConfig"
                @click.native="activeButton=key;activeField='';"
                @close="del(key,'tools')"
            ></elementButton>
        </draggable>               
    </el-form>
</template>  
<script>
    import draggable from 'vuedraggable';
    export default {
        components: {
            draggable,
        },
        props: ['query'],
        data() {
            return {
                activeField:'',
                activeButton:'',
                configs: {
                },
                fields:[],
                attrs:{},
                attrForm:{
                }
            }
        },
        created: function(){
            let configs={
                fields:[{
                    type:'elementText',
                    label:'示例',
                    name:'text'
                }],
                tools:[{
                    "type":"elementButton",
                    "text":"保存并关闭",
                    "buttonType":"primary",
                    "name":"button",
                    "title":"保存并关闭",
                    "operator":"elementSelect",
                    "script":"this.save(event,config,true);"
                },{
                    "type":"elementButton",
                    "text":"保存",
                    "shape":"plain",
                    "buttonType":"primary",
                    "name":"button",
                    "title":"保存",
                    "operator":"elementSelect",
                    "script":"this.save(event,config);"
                },{
                    "type":"elementButton",
                    "text":"取消",
                    "buttonType":"",
                    "name":"button",
                    "title":"取消",
                    "operator":"elementSelect",
                    "script":"this.cancel(event,config);"
                }],
                form:{
                    text:'将组件拖放到此区域进行配置'
                }
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
                        if(!response.data.data.fields){
                            response.data.data.fields=[];
                        }
                        if(!response.data.data.tools){
                            response.data.data.tools=[];
                        }                    
                        option.vue.configs=response.data.data;
                    },
                    error:function(response,option){
                        option.vue.configs=configs;
                    }
                });
                //表单字段
                this.my.axios({
                    vue: this,
                    axiosOption:{
                        url:'admin/table/field',
                        data:{
                            TABLE_NAME:this.query.row.TABLE_NAME
                        }
                    },
                    success:function(response,option){
                        option.vue.fields=response.data.data;
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
            //
            draggableClick:function(event){
                this.$emit('draggableClick',event)
            },            
            //配置字段组件
            toConfig:function(event,config,attr){
                if(attr.name){
                    this.$set(attr.name,'options',this.fields);
                }            
                this.$emit('toConfig',event,config,attr)
            },
            //按钮拖放后去掉标签
            buttonChange(event){
                if('added' in event){
                    this.$delete(event.added.element,'labelWidth');
                    this.$delete(event.added.element,'label');
                }
            }
        }
    }
</script>
<style>
    /*激活的组件显示虚线框*/
    .draggable .el-form-item{
        border:1px dotted transparent;
    }
    .draggable .el-form-item.active{
        border:1px dotted #409EFF;
    }
    /*组件删除按钮*/
    .my-close{
        display: none;
    }
    .draggable .el-form-item.active .my-close{
        position:absolute;
        top:0px;
        left:0px;
        border-radius:12px;
        font-size:12px;
        border:1px solid #dddddd;
        color:red;
        width:12px;
        height:12px;
        text-align:center;
        cursor:pointer;
        line-height: 11px;
        display:inline-block;
    }
    .draggable .el-form-item.active .my-close::after{
        content:'×'
    }
</style>
