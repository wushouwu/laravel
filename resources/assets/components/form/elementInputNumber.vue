<template>
    <el-form-item 
        :label="config.label"
        :label-width="config.labelWidth"
        :prop="config.name" 
        :class="{'size-small-font':config.size==='small'||config.size==='mini'?true:false}"
    >
        <el-input-number 
            v-model="form[config.name]"  
            :min="config.min" 
            :max="config.max"
            :size="config.size"
            :controls-position="config.controlsPosition"
            @change="change"
        ></el-input-number>
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
                this.$emit('NumberChange',value,this.config);
                this.$emit('e',value,this.config);
            }
        }
    }
</script>
