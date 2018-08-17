<template>
    <el-collapse v-model="activeAccordion" accordion>
        <el-collapse-item title="组件" name="组件">
            <el-form :model="form" label-width="80px" style="width:100%;height:100%">
                <draggable v-model="configs.form" :options="{group:{ name:'view',  pull:'clone', put: false },preventOnFilter: true,handle: 'label',animation: 250}" style="width:100%;height:100%">
                    <component v-for="(config, key,index) in configs.form" :key="index" :is="config.type" :config="config" :form="form"></component>
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
                    v-show="!attr.hide"
                    @radioChange="radioChange"
                    @cascaderActiveChange="cascaderActiveChange"
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
        props:['attrs','attrForm'],
        data() {
            return {
                activeAccordion:'组件',
                configs: {
                    form: [{
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
                            "table":'',
                            "fieldLabel":'',
                            "fieldValue":''
                        }
                    },{
                        "type": "elementSwitch",
                        "label": "开关",
                        "name": "switch"
                    },{
                        "type": "elementTextarea",
                        "label": "多行文本",
                        "name": "textarea"
                    },{
                        "type": "elementDatetime",
                        "label": "日期时间",
                        "name": "datetime"
                    },{
                        "type": "elementInputNumber",
                        "label": "数字",
                        "name": "inputNumber",
                        "min": 0,
                        "max": 10
                    }],
                    table:[{
                        "type": "elementTable",
                        "label": "文本",
                        "name": "text",
                        'fields':[{
                            "label": "日期",
                            "name": "date",
                            "width": 90,
                        },{
                            "label": "姓名",
                            "name": "name",
                            "width": 90
                        },{
                            "label": "地址",
                            "name": "province",
                        }],  
                    },
                    ]
                },
                "form": {
                }
            }
        },
        methods:{
            radioChange(value,config){
                eval(config.radioChange);
            },
            cascaderActiveChange(value,config){
                eval(config.cascaderActiveChange);
            },
            e(event,config){
                switch(config.name){
                    default:
                        eval(config.event);
                }
            }
        }
    }
</script>