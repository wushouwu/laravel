<template>
    <el-form :model="form" label-width="80px" style="width:100%;height:100%">
        <draggable v-model="configs" :options="{group:{ name:'form',  pull:'clone', put: false },preventOnFilter: true,handle: 'label',animation: 250}" style="width:100%;height:100%">
            <component v-for="(config, key,index) in configs" :key="index" :is="config.type" :config="config" :form="form"></component>
        </draggable>
    </el-form>
</template>    
<script>    
    import draggable from 'vuedraggable'
    export default {
        components: {
            draggable
        },
        data() {
            return {
                configs: [{
                    "type": "elementText",
                    "label": "文本",
                    "name": "text"
                },{
                    "type": "elementSelect",
                    "label": "选项",
                    "name": "select",
                    "options": [{
                        "value": "1",
                        "label": "黄金糕"
                    }, {
                        "value": "2",
                        "label": "双皮奶"
                    }, {
                        "value": "3",
                        "label": "蚵仔煎"
                    }, {
                        "value": "4",
                        "label": "龙须面"
                    }, {
                        "value": "5",
                        "label": "北京烤鸭"
                    }]
                },{
                    "type": "elementSwitch",
                    "label": "开关",
                    "name": "switch"
                },{
                    "type": "elementTextarea",
                    "label": "开关",
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
                "form": {
                }
            }
        }
    }
</script>