<template>
    <el-form-item  :label="config.label" @click.native="click" :style="config.style">
        <el-input 
            v-model="form[config.name]"
            :size="config.size"
            :clearable="true" 
            @keyup.enter.native="enter"
            :placeholder="config.placeholder||'请输入'"
            :readonly="config.readonly"
            :disabled="config.disabled"
        ></el-input>
    </el-form-item>
</template>
<script>
    export default {
        props: ['config','form'],   
        methods:{
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
                    },
                    readonly: {
                        type:"elementSwitch",
                        label:"只读",
                        name:"readonly"
                    },
                    placeholder: {
                        type:"elementText",
                        label:"空白提示",
                        name:"placeholder"
                    }                                     
                });
            },
            enter(event){
                this.$emit('enter',event,this.config);
            }
        }
    }
</script>
