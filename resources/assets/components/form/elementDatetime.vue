<template>
    <el-form-item :label="config.label" :prop="config.name">
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
        methods:{
            change(){
                
            }
        }
    }
</script>
