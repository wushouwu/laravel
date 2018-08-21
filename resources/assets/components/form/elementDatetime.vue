<template>
    <el-form-item 
        :label="config.label"
        :label-width="config.labelWidth"
        :prop="config.name"
    >
        <el-date-picker 
            v-model="form[config.name]" 
            :type="config.pickerType||'datetime'"  
            :value-format="config.valueFormat||'yyyy-MM-dd HH:mm:ss'"
            :placeholder="config.placeholder||'选择日期时间'" 
            @change="change"
        ></el-date-picker>  
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
            change(){
                
            }
        }
    }
</script>
