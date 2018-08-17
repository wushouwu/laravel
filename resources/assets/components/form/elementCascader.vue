<template>
    <el-form-item  
        :label="config.label" 
        @click.native="click" 
        :style="config.style"
        :label-width="config.labelWidth"
    >
        <el-cascader
            :options="config.options"
            @active-item-change="activeItemChange"
        ></el-cascader>
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
                        type:"elementText",
                        label:"空白提示",
                        name:"placeholder"
                    }                                     
                });
            },
            activeItemChange(value){
                let vue=this;
                this.config.options.find((item,index)=>{
                    if(value[0] === item.value && !item.children.length){
                        vue.my.axios({
                            vue:vue,
                            axiosOption:{
                                url:'admin/table/field',
                                data:{
                                    TABLE_NAME:item.value
                                }
                            },
                            success:function(response,option){
                                option.vue.config.options[index].children=response.data.data;
                            }            
                        });
                    }
                    return value[0] === item.value;
                });                
                this.$emit('cascaderActiveChange',value,this.config);
            }
        }
    }
</script>
