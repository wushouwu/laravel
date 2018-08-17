<template>
    <el-form-item 
        :label-width="config.labelWidth"
        :label="config.label" 
        :prop="config.name" 
        @click.native="click"
        :class="{'size-small-font':config.size==='small'?true:false}"
    >
        <el-radio-group 
            v-model="form[config.name]"
            :size="config.size"
            @change="change"
        >
            <item
                v-for="(option,key,index) in config.options"
                :key="key"
                :is="option.type"
                :border="option.border"
                :label="option.value"
                :disabled="option.disabled"
                :size="option.size"
                :class="{'size-small-font':option.size==='small'?true:false}"
            >{{option.label}}</item>
        </el-radio-group>
        <component 
            v-for="(option,key,index) in config.options" 
            :key="key"
            :config="option.config"
            :is="option.config.type"
            :form="form"
            :style="config.itemStyle"
            v-if="form[config.name]==option.value"
        ></component>
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
                console.log(this.form);
                this.$emit('radioChange',value,this.config);
            }
        }   
    }
</script>
