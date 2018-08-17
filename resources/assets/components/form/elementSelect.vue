<template>
    <el-form-item 
        :label="config.label" 
        :prop="config.name" 
        @click.native="click"
        :class="{'size-small-font':config.size==='small'?true:false}"
    >
        <el-select 
            v-model="form[config.name]" 
            :filterable="config.filterable" 
            :placeholder="config.placeholder||'请选择'"
            :size="config.size"
            @change="change"
        >
            <el-option 
                v-for="opiton in config.options" 
                value-key="value"
                :key="opiton.value" 
                :label="opiton.label" 
                :value="opiton.value"
            ></el-option>
        </el-select>
    </el-form-item>
</template>
<script>
    export default {
        props: ['config','form'], 
        data(){
            return {
                tables:[],
                fields:[]
            }
        },
        methods: {
            click: function(event){
                //配置
                if('tableField' in this.config){
                    if(this.tables.length){ 
                        //所属表所有字段
                        if(this.config.tableField.table){
                            this.my.axios({
                                vue: this,
                                axiosOption:{
                                    url: 'admin/table/field',
                                    data:{
                                        TABLE_NAME:this.config.tableField.table
                                    }
                                },
                                success:function(response,option){
                                    option.vue.fields=response.data.data;
                                    option.vue.emit(event);
                                }
                            });
                        }else{
                            this.emit(event);
                        }                                         
                    }else{
                        //所有表
                        this.my.axios({
                            vue: this,
                            axiosOption:{
                                url: 'admin/table/tables'
                            },
                            success:function(response,option){
                                option.vue.tables=response.data.data;
                                option.vue.emit(event);
                            }
                        });
                    }
                }

            },  
            emit(event){
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
                    source:{
                        type:"elementRadio",
                        name:"source",
                        label:"选项来源",
                        radioChange:`
                            if(value=="options"){
                                this.$set(this.attrs.options,'hide',false);
                                this.$set(this.attrs.tableField,'hide',true);
                                this.$set(this.attrs.options,'form',this.attrForm.options);
                            }else{
                                this.$set(this.attrs.options,'hide',true);
                                this.$set(this.attrs.tableField,'hide',false);                           
                            }
                        `,
                        options:[{
                            value:'options',
                            label:'自定义',
                            type:'el-radio'
                        },{
                            value:'tableField',
                            label:'表格字段值',
                            type:'el-radio'
                        }]
                    },
                    tableField:{
                        hide:this.config.source=='tableField'?false:true,
                        type:"elementComponents",
                        name:"options",
                        labelWidth:"0px",
                        form:this.config.tableField,
                        formWatch:`
                            this.$set(this.form,'tableField',newValue);
                            if(newValue.table && newValue.fieldLabel && newValue.fieldValue){
                                this.my.axios({
                                    vue: this,
                                    axiosOption:{
                                        url: 'admin/table/table',
                                        data:{
                                            TABLE_NAME:newValue.table,
                                            fields:'\`'+newValue.fieldLabel+'\` as label,\`'+newValue.fieldValue+'\` as value',
                                            pageSize: 10
                                        }
                                    },
                                    success:function(response,option){
                                        option.vue.$set(option.vue.form,'options',response.data.data);
                                        
                                    }
                                });
                            }
                        `,
                        options:[{
                            type:"elementSelect",
                            name:"table",
                            label:"表格",
                            size:'small',
                            options:this.tables,
                            event:`
                                this.config.form.fieldLabel='';
                                this.config.form.fieldValue='';
                                this.form.tableField.fieldLabel='';
                                this.form.tableField.fieldValue='';                                
                                this.my.axios({
                                    vue: this,
                                    axiosOption:{
                                        url: 'admin/table/field',
                                        data:{
                                            TABLE_NAME:event
                                        }
                                    },
                                    success:function(response,option){
                                        option.vue.config.options[1].options=response.data.data;
                                        option.vue.config.options[2].options=response.data.data;
                                        if(response.data.data.length){
                                            option.vue.config.form.fieldLabel=response.data.data[0].value;
                                            option.vue.config.form.fieldValue=response.data.data[0].value;
                                            option.vue.form.tableField.fieldLabel=response.data.data[0].value;
                                            option.vue.form.tableField.fieldValue=response.data.data[0].value;                                            
                                        }
                                        
                                    }
                                });
                            `
                        },{
                            type:"elementSelect",
                            name:"fieldLabel",
                            label:"显示值",
                            size:'small',
                            options:this.fields,
                        },{
                            type:"elementSelect",
                            name:"fieldValue",
                            label:"保存值",
                            size:'small',
                            options:this.fields,
                        }]
                    },
                    options:{
                        type:"elementAddDelete",
                        hide:this.config.source=='options'?false:true,
                        name:"options",
                        labelWidth:'0px',
                        labels:[{label:'显示值'},{label:'保存值'}],
                        itemStyle:"width:40%;display:inline-block;margin-right:2%",
                        options:Array(this.config.options.length).fill([{
                            type:"elementText",
                            inline:true,
                            size:'mini',
                            name:"label"
                        },{
                            type:"elementText",
                            inline:true,
                            size:'mini',
                            name:"value"
                        }]),
                        form:this.config.options
                    }
                });
            },
            change(value){
                this.$emit('selectChange',value,this.config);
                //所有组件统一事件
                this.$emit('e',value,this.config);
            }
        }   
    }
</script>
<style>
</style>
