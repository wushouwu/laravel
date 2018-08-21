<template>
    <el-form-item 
        :label="config.label"
        :label-width="config.labelWidth"
        :class="{'size-small-font':config.size==='small'||config.size==='mini'?true:false}"
    >
        <el-switch v-model="form[config.name]"></el-switch>
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
        }        
    }
</script>
