<template>
    <el-form-item 
        :label="config.label" 
        :prop="config.name" 
        @click.native="click"
        :class="{'size-small-font':config.size==='small'?true:false}"
    >
        <el-select 
            v-model="form[config.name]" 
            :allow-create="config.allowCreate"
            :filterable="config.filterable||config.allowCreate" 
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
                        allowCreate:true,
                        options:[]
                    },
                    label: {
                        type:"elementText",
                        label:"字段名",
                        name:"label"
                    },
                    source:{
                        type:"elementRadioChange",
                        name:"source",
                        label:"选项来源",
                        script:`
                            if(event=="options"){
                                this.$set(this.attrForm,'options',this.attrForm.options.slice(0,this.attrs.source.options[0].config.options.length));
                            }else{   
                                let tableField=this.attrForm.tableField;
                                if(tableField.table && tableField.fieldLabel && tableField.fieldValue){
                                    this.my.axios({
                                        vue: this,
                                        axiosOption:{
                                            url: 'admin/table/table',
                                            data:{
                                                TABLE_NAME:tableField.table,
                                                fields:'\`'+tableField.fieldLabel+'\` as label,\`'+tableField.fieldValue+'\` as value',
                                                pageSize: 10
                                            }
                                        },
                                        success:function(response,option){
                                            option.vue.$set(option.vue.attrForm,'options',response.data.data);
                                            
                                        }
                                    });
                                }                                                  
                            }
                        `,
                        options:[{
                            value:'options',
                            label:'自定义',
                            type:'el-radio',
                            config:{
                                type:"elementAddDelete",
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
                                }])
                            },
                        },{
                            value:'tableField',
                            label:'表格字段值',
                            type:'el-radio',
                            config:{
                                type:"elementComponents",
                                name:"tableField",
                                labelWidth:"0px",
                                formWatch:`
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
                                    script:`
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
                            }
                        }]
                    }
                });
            },
            change(value){
                this.$emit('selectChange',value,this.config);
                this.$emit('e',value,this.config);
            }
        }   
    }
</script>
<style>
</style>
