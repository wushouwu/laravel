<template>
    <el-form-item  :label="config.label" @click.native="click">
        <el-input-number v-model="form[config.name][0]"></el-input-number>
        <el-input-number v-model="form[config.name][1]"></el-input-number>
    </el-form-item>
</template>
<script>
    export default {
        props: ['config','form'],
        methods:{
            click: function(event){
                this.$emit('config',event,this.config,{
                    name:{
                        type:"elementText",
                        label:"字段",
                        name:"name"
                    },
                    label: {
                        type:"elementText",
                        label:"字段名",
                        name:"label"
                    }
                });
            },
            enter(event){
                this.$emit('enter',event,this.config);
            }
        }
    }
</script>
