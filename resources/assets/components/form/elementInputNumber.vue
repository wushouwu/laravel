<template>
    <el-form-item :label="config.label" :prop="config.name">
        <el-input-number v-model="form[config.name]"  :min="config.min" :max="config.max"></el-input-number>
    </el-form-item>
</template>
<script>
    export default {
        props: ['config','form'],
    }
</script>
