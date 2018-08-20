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
    </el-form-item>
</template>
<script>
    export default {
        props: ['config','form'],
        mounted(){
            //组件添加删除按钮
            let vue=this,
                span=document.createElement('span');
            span.className="my-close";
            span.addEventListener('click',function(){
                vue.$emit('close',event,vue);
            });
            this.$el.style.position="relative";
            this.$el.appendChild(span);
        },         
        methods: {
            click: function(event){
                this.$emit('config',event,this.config,{
                    name:{
                        type:"elementSelect",
                        label:"字段",
                        name:"name",
                        allowCreate:true,
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
                this.$emit('radioChange',value,this.config);
                this.$emit('e',value,this.config);
            }
        }   
    }
</script>
