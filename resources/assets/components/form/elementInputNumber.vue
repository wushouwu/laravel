<template>
    <el-form-item :label="config.label" :prop="config.name">
        <el-input-number 
            v-model="form[config.name]"  
            :min="config.min" 
            :max="config.max"
            :size="config.size"
            :controls-position="config.controlsPosition"
            :class="{'size-small-font':config.size==='small'?true:false}"
            @change="change"
        ></el-input-number>
    </el-form-item>
</template>
<script>
    export default {
        props: ['config','form'],
        methods: {
            click: function(event){
                this.$emit('config',event,this.config,{
                    name:{
                        type:"elementSelect",
                        label:"字段",
                        name:"name",
                        options:[]
                    },
                    label: {
                        type:"elementText",
                        label:"字段名",
                        name:"label"
                    }
                });
            },            
            change(value){
                this.$emit('NumberChange',value,this.config);
                this.$emit('e',value,this.config);
            }
        }
    }
</script>
