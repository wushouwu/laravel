<template>
    <el-form  
        :model="configs.form"  
        style="width:100%;height:100%;position:relative;"
        :status-icon="configs.forms.statusIcon"
        :label-width="configs.forms.labelWidth||'80px'"
        :inline="configs.forms.inline"
        :label-position="configs.forms.labelPosition"
        :show-message="configs.forms.showMessage"
        :size="configs.forms.size"
        :disabled="configs.forms.disabled"
    >            
        <draggable
            v-model="configs.fields" 
            class="fields draggable"
            :options="{group:{ name:'view',  pull:false, put: true},preventOnFilter: true,animation: 250}"
            @change="sort"
            @click.native="fieldsClick"
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
        <draggable
            class="tools draggable"
            v-model="configs.tools" 
            :options="{group:{ name:'view',  pull:false, put: true},preventOnFilter: true,animation: 250}"
            @change="buttonChange"
            @click.native="toolsClick"
        >
            <elementButton
                v-for="(tool,key) in configs.tools"
                :key="key"
                :config="tool"
                :form="tool"
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
                    forms:{
                        statusIcon:true,
                        labelWidth:"80px",
                        inline:false,
                        labelPosition:"right",
                        showMessage:true,
                        size:"",
                        disabled:false
                    },
                    fields:[],
                    tools:[],
                    form:{}
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
                        option.vue.configs=Object.assign(option.vue.configs,response.data.data);
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
            //点击字段框 事件
            fieldsClick:function(event){
                this.$emit('fieldsClick',event)
            },
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
    /* 表单字段框 */
    .fields.draggable{
        overflow: auto;
        width:auto;
        height:100%;       
        border:1px dashed #c0c4cc;
    }
    /* 表单工具框 */
    .tools.draggable{
        width:80%;
        min-height:42px;
        display:flex;
        justify-content:center;
        align-items:center;
        position:absolute;
        bottom:21px;
        margin-left: 10%;
        border:1px dashed #c0c4cc;
        cursor:pointer;
    }
</style>
