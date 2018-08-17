<template>
    <el-form-item 
        :label="config.label"
        @click.native="click"
    >
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
        methods:{
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
                    },
                    readonly: {
                        type:"elementSwitch",
                        label:"只读",
                        name:"readonly"
                    },
                    placeholder: {
                        type:"elementTextarea",
                        label:"空白提示",
                        name:"placeholder"
                    },
                    autosize:{
                        type:"elementComponents",
                        label:"自动尺寸",
                        name:"autosize",
                        form:this.config.autosize,                     
                        options:[{
                            name:"minRows",
                            label:"最小行数",
                            type:"elementInputNumber",
                            size:"mini",
                            script:"console.log(this.form,this.config)"

                        },{
                            name:"maxRows",
                            label:"最大行数",
                            type:"elementInputNumber",
                            size:"mini"
                        }]
                    }                                 
                });
            },
            enter(event){
                this.$emit('enter',event,this.config);
            }
        }
    }
</script>
