<template>
    <el-collapse v-model="activeAccordion" accordion>
        <el-collapse-item title="视图信息" name="视图">
            <el-form :model="row" ref="rowForm"  label-width="80px" style="width:100%;height:100%">
                <component 
                    v-for="(config, key,index) in configs.form.fields" 
                    :key="index" 
                    :is="config.type" 
                    :config="config" 
                    :form="row"
                    @selectChange="selectChange"
                ></component> 
                <elementButton
                    :config="{name:'save',text:'保存',buttonType:'primary'}"
                    style="text-align:center;"
                    @buttonClick="save"
                ></elementButton>            
            </el-form>
        </el-collapse-item>         
        <el-collapse-item title="组件" name="组件">
            <el-form :model="form" label-width="80px" style="width:100%;height:100%">
                <draggable 
                    v-model="configs.components" 
                    :options="{group:{ name:'view',  pull:'clone', put: false },preventOnFilter: true,handle: 'label',animation: 250}" 
                    style="width:100%;height:100%"
                    class="draggable"
                    @remove="remove('components')"
                    @update="update('components')"
                >
                    <component 
                        v-for="(config, key,index) in configs.components" 
                        :key="index" 
                        :is="config.type" 
                        :config="config" 
                        :form="form"
                    ></component>
                </draggable>
            </el-form>
        </el-collapse-item>  
        <el-collapse-item title="模块" name="模块">
            <el-form :model="form" label-width="80px" style="width:100%;height:100%">
                <draggable 
                    v-model="configs.containers" 
                    :options="{group:{ name:'view',  pull:'clone', put: false },preventOnFilter: true,handle: '.el-card',animation: 250}" 
                    style="width:auto;height:100%;padding:10px;"
                    class="draggable"
                    @remove="remove('containers')"
                    @update="update('containers')"
                >
                    <component 
                        v-for="(config, key,index) in configs.containers" 
                        :key="index" 
                        :is="config.type" 
                        :config="config" 
                        :form="form"
                    ></component>
                </draggable>
            </el-form>
        </el-collapse-item>               
        <el-collapse-item title="属性" name="属性">
            <el-form :model="attrForm" label-width="80px" style="width:100%;height:100%">
                <component 
                    v-for="(attr, key,index) in attrs" 
                    :key="index" 
                    :is="attr.type" 
                    :config="attr" 
                    :form="attrForm"
                    @e="e"
                ></component>
            </el-form>
        </el-collapse-item>
    </el-collapse>
</template>    
<script>
    import draggable from 'vuedraggable'
    export default {
        components: {
            draggable
        },
        props:['attrs','attrForm','query'],
        data() {
            return {
                row:{
                    TABLE_NAME:'',
                    desc:'',
                    json:'',
                    type:'form',
                    view_name:'',
                    id:''
                },
                activeAccordion:"视图",
                initComponents:`[{
                    "type": "elementText",
                    "label": "文本",
                    "name": "text"
                },{
                    "type": "elementSelect",
                    "label": "选项",
                    "name": "select",
                    "source":"options",
                    "options": [{
                        "value": "1",
                        "label": "选项1"
                    }, {
                        "value": "2",
                        "label": "选项2"
                    }],
                    "tableField":{
                        "table":"",
                        "fieldLabel":"",
                        "fieldValue":""
                    }
                },{
                    "type": "elementDatetime",
                    "label": "日期时间",
                    "name": "datetime"
                },{
                    "type": "elementInputNumber",
                    "label": "数字",
                    "name": "inputNumber"
                },{
                    "type": "elementButton",
                    "labelWidth":"80px",
                    "label": "按钮",
                    "text": "按钮",
                    "shape":"plain",
                    "buttonType":"primary",
                    "name": "button"
                },{
                    "type": "elementSwitch",
                    "label": "开关",
                    "name": "switch"
                },{
                    "type": "elementTextarea",
                    "label": "多行文本",
                    "name": "textarea",
                    "autosize":{"minRows":3,"maxRows":5}
                },{
                    "type": "elementEditor",
                    "label": "编辑器",
                    "name": "editor",
                    "style":"padding-right: 1px;",
                    "readonly":false,
                    "editorType":"multy",                    
                    "options":{
                        "minWidth":0,
                        "width":"100%",
                        "height":296,
                        "resizeType":1
                    }
                }]`,
                initContainers:`[{
                    "type":"elementCard",
                    "header":{
                        "title":"列表",
                        "height":"57.8px"
                    },
                    "shadow":"always",
                    "width":"98%",
                    "height":"auto"
                }]`,
                configs: {
                    components:[],
                    containers:[],
                    form:[]
                },
                "form": {
                }
            }
        },
        created(){
            this.configs.components=JSON.parse(this.initComponents);
            this.configs.containers=JSON.parse(this.initContainers);
            //表单配置
            this.my.axios({
                vue: this,
                axiosOption:{
                    url:'admin/table/view',
                    data:{
                        TABLE_NAME:'view',
                        view_name:this.query.row&&this.query.row.id?'视图-编辑':'视图-添加',
                        type:'form'
                    }
                },
                success:function(response,option){
                    option.vue.$set(option.vue.configs,'form',response.data.data);
                }
            }); 
            //表单数据
            if(this.query.row){
                if(this.query.row.id){
                    this.my.axios({
                        vue: this,
                        axiosOption:{
                            url:'admin/table/row',
                            data:{
                                TABLE_NAME:'view',
                                id: this.query.row.id,
                            }
                        },
                        success:function(response,option){
                            option.vue.$set(option.vue,'row',response.data.data);
                        }
                    });
                }else{
                    if(this.query.row.type){
                        this.row.type=this.query.row.type;
                    }
                    this.row.TABLE_NAME=this.query.row.TABLE_NAME;
                }
            }
        },
        watch:{
            json:{
                handler(newValue, oldValue){
                    this.$set(this.row,'json',JSON.stringify(newValue,null,4));
                },
                deep:true
            }
        },
        computed:{
            json(){
                return this.$parent.$parent.$parent.configs;
            }
        },
        methods:{  
            //处理源组件被修改的问题
            remove(type){
                this.configs[type]=JSON.parse(this[this.camelCase('init-'+type)]);
            }, 
            update(type){
               this[this.camelCase('init-'+type)]=JSON.stringify(this.configs[type]);
            },
            selectChange(value,option){
                this.$emit('selectChange',value,option);
            },         
            e(event,option){
                switch(option.config.name){
                    default:
                        eval(option.config.script);
                }
            },
            //保存配置
            save: function(event){
                let vue=this;             
                this.$refs.rowForm.validate((valid) => {
                    if (valid) {
                        vue.my.axios({
                            vue: vue,
                            axiosOption:{
                                url:'/admin/table/save',
                                data: {form:vue.row,TABLE_NAME:'view'}
                            },
                            success:function(response,option){
                            },
                            successMsg: '保存成功!'
                        });
                    } else {
                        return false;
                    }
                });               
            }         
        }
    }
</script>