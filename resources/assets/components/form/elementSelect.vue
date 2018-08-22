<template>
    <el-form-item 
        :label="config.label"
        :label-width="config.labelWidth"
        :prop="config.name" 
        :rules="config.rules"
        @click.native="click"
        :class="{'size-small-font':config.size==='small'||config.size==='mini'?true:false}"
    >
        <el-select 
            v-model="form[config.name]" 
            :allow-create="config.allowCreate"
            :multiple="config.multiple"
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
        created(){
           //默认验证
           this.$set(this.config,'rules',Object.assign([{
                required:false,
                trigger:[],
                message:"字段不能为空",
            }],this.config.rules));
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
                        itemDefault:{
                            labelWidth:'0px',
                        },
                        options:[{
                            value:'options',
                            label:'自定义',
                            type:'el-radio',
                            config:{
                                type:"elementAddDelete",
                                name:"options",
                                labels:[{label:'显示值'},{label:'保存值'}],
                                itemDefault:{
                                    style:"width:40%;display:inline-block;margin-right:2%",
                                    size:"mini"
                                },
                                options:Array(this.config.options.length).fill([{
                                    type:"elementText",
                                    name:"label"
                                },{
                                    type:"elementText",
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
                                itemDefault:{
                                    size:"small"
                                },
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
                                    options:this.fields,
                                },{
                                    type:"elementSelect",
                                    name:"fieldValue",
                                    label:"保存值",
                                    options:this.fields,
                                }]
                            }
                        }]
                    },
                    rules:{
                        type:"elementComponents",
                        label:"验证规则",
                        name:"rules",
                        itemDefault:{
                            size:"small",
                            //style:"border-bottom:1px solid #eee;margin-bottom:10px;padding-bottom:10px;"
                        },
                        labelPositionTop:true,
                        options:[{
                            type:"elementComponents",
                            labelWidth:"0px",
                            name:"0",
                            itemDefault:{
                                size:"small"
                            },
                            options:[{
                                type:"elementSwitch",
                                label:"必填",
                                name:"required",
                            },{
                                type:"elementSelect",
                                label:"触发",
                                name:"trigger",
                                multiple:true,
                                options:[{
                                    label:"失去焦点",
                                    value:"blur"
                                },{
                                    label:"改变",
                                    value:"change"
                                }]
                            },{
                                type:"elementText",
                                label:"提示信息",
                                name:"message",
                            }]
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
