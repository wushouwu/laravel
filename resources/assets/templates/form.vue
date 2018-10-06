<template>
    <el-form  
        ref="form"
        :model="form"  
        style="width:100%;height:100%;position:relative;"
        :status-icon="forms.statusIcon"
        :label-width="forms.labelWidth||'80px'"
        :inline="forms.inline"
        :label-position="forms.labelPosition"
        :show-message="forms.showMessage"
        :size="forms.size"
        :disabled="forms.disabled"        
    >            
        <div style="overflow:auto;height:100%;" class="fields">
            <component 
                v-for="(field, key,index) in fields" 
                :key="index" 
                :is="field.type" 
                :config="field" 
                :form="form"
                v-field="{field:field,form:form,query:query}"
            ></component>            
        </div>  
        <el-footer class="tools" style="height:0px;width:100%;display:flex;justify-content:center;align-items:center;position:absolute;bottom:21px;">
            <elementButton
                v-for="(tool,key) in tools"
                :key="key"
                :config="tool"
                @buttonClick="buttonClick"
            ></elementButton>
        </el-footer>              
    </el-form>
</template>  
<script>
    export default {
        props: ['query'],
        data() {
            return {
                forms:{
                    statusIcon:true,
                    labelWidth:"80px",
                    inline:false,
                    labelPosition:"right",
                    showMessage:true,
                    size:"",
                    disabled:false
                },
                fields: [],
                form: {},
                tools:[]
            }
        },
        created: function(){
            console.log(this.query);
            //表单配置
            this.my.axios({
                vue: this,
                axiosOption:{
                    url:this.query.url||'admin/table/view',
                    data:Object.assign({type:"form"},this.query)
                },
                success:function(response,option){
                    option.vue=Object.assign(option.vue,response.data.data);
                    if(option.vue.query.row && option.vue.query.row.id || option.vue.query.where){
                        //表单数据
                        let data={
                            TABLE_NAME:option.vue.query.TABLE_NAME,
                            row:option.vue.query.row
                        }
                        if(option.vue.query.row && option.vue.query.row.id){
                            data['id']=option.vue.query.row.id;                       
                        }
                        if(option.vue.query.where){
                            data['where']= option.vue.query.where;
                        }
                        option.vue.my.axios({
                            vue: option.vue,
                            axiosOption:{
                                url:'admin/table/row',
                                data:data
                            },
                            success:function(response,option){
                                option.vue.form=response.data.data;
                            }
                        });
                    }
                    
                }
            });
        },  
        directives:{
            //字段动态设置
            field:{
                bind(el,binding,vnode){
                    if(binding.value.field.script){
                        eval(binding.value.field.script)
                    }
                }
            }
        },
        methods: {
            //工具按钮点击
            buttonClick:function(event,config){
                eval(config.script);
            },       
            //保存 保存并关闭
            save: function(event,config,cancel=false){
                let vue=this;             
                this.$refs.form.validate((valid) => {
                    if (valid) {
                        vue.my.axios({
                            vue: vue,
                            axiosOption:{
                                url:config.query.url||'/admin/table/save',
                                data: Object.assign({form:vue.form},vue.query)
                            },
                            success:function(response,option){
                                if(option.vue.query.row){
                                    option.vue.$set(option.vue.query,'row',Object.assign({},option.vue.query.row,option.vue.form));
                                }
                                if(cancel){
                                    option.vue.cancel(event,config);
                                }
                            },                            
                            successMsg: '保存成功!'
                        }); 
                    } else {
                        return false;
                    }
                });                              
            },
            //取消
            cancel:function(event,config){
                this.$root.$children[0].removeTab(this.$root.$children[0].activeTab);
            },
            //配置视图
            config:function(event,config){
                this.save(event,config);
                this.cancel(event,config);
                this.$root.$children[0].addTab({
                    value:this.query.TABLE_NAME+'-config',
                    label:this.query.TABLE_COMMENT+'表-配置',
                    content: this.form.type+'ConfigVue',
                    query:Object.assign({},this.form,this.query,config.query)
                });
            }
        }
    }
</script>
<style>   
</style>
