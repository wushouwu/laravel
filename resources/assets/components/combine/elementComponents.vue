<template>
    <el-form-item 
        :label="config.label" 
        :label-width="config.labelWidth"
        :prop="config.name" 
    >
        <component 
            v-for="(option,key,index) in config.options"
            :key="key"
            :is="option.type"
            :config="option"
            :form="config.form"
            @e="e"
        ></component>
    </el-form-item>
</template>
<script>
    export default {
        props: ['config','form'],
        watch:{
            configForm:{
        　　　　handler(newValue, oldValue) {
        　　　　　　eval(this.config.formWatch);
        　　　　},
        　　　　deep: true
            }
        },
        computed:{
            configForm(){
                return this.config.form;
            }
        },
        methods:{
            e(event,config){
                switch(config.name){
                    default:
                        eval(config.event);
                }
                this.$emit('componentEvent',event,config);
            }
        }  
    }
</script>
