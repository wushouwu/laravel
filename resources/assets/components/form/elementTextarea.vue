<template>
    <el-form-item :label="config.label">
        <el-input 
            type="textarea" 
            v-model="form[config.name]" 
            :clearable="true" 
            :readonly="config.readonly"
            :rows="config.rows"
            :autosize="config.autosize"
            :placeholder="config.placeholder||'请输入'"
        ></el-input>
    </el-form-item>
</template>
<script>
    export default {
        props: ['config','form'],
    }
</script>
