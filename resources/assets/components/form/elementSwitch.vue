<template>
    <el-form-item :label="config.label">
        <el-switch v-model="form[config.name]"></el-switch>
    </el-form-item>    
</template>
<script>
    export default {
        props: ['config','form'],
    }
</script>
