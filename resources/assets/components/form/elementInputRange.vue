<template>
    <el-form-item  
        :label="config.label" 
        :label-width="config.labelWidth"
        @click.prevent.stop.native.right="rightClick"
    >
        <el-input-number v-model="form[config.name][0]"></el-input-number>
        <el-input-number v-model="form[config.name][1]"></el-input-number>
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
        methods:{
            rightClick: function(event){
                this.$emit('config',event,this.config,{
                    name:{
                        type:"elementText",
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
            enter(event){
                this.$emit('enter',event,this.config);
            }
        }
    }
</script>
