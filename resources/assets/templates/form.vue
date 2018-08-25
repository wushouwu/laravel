<template>
    <el-form  :model="form" label-width="80px" style="width:100%;height:100%;position:relative;">            
        <div style="overflow:auto;height:100%;">
            <component 
                v-for="(field, key,index) in fields" 
                :key="index" 
                :is="field.type" 
                :config="field" 
                :form="form"
                v-field="{field:field,form:form,query:query}"
            ></component>            
        </div>  
        <el-footer class="footer-tools" style="height:0px;width:100%;display:flex;justify-content:center;align-items:center;position:absolute;bottom:21px;">
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
                fields: [],
                form: {},
                tools:[]
            }
        },
        created: function(){
            //表单配置
            this.my.axios({
                vue: this,
                axiosOption:{
                    url:'admin/table/view',
                    data:Object.assign({type:"form"},this.query)
                },
                success:function(response,option){
                    option.vue=Object.assign(option.vue,response.data.data);
                    if(option.vue.query.row && option.vue.query.row.id){
                        //表单数据
                        option.vue.my.axios({
                            vue: option.vue,
                            axiosOption:{
                                url:'admin/table/row',
                                data:{
                                    TABLE_NAME:option.vue.query.TABLE_NAME,
                                    id:option.vue.query.row.id,
                                    row:option.vue.query.row
                                }
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
                let option={
                    vue: this,
                    axiosOption:{
                        url:config.query.url||'/admin/table/save',
                        data: Object.assign({
                            form:this.form
                        },this.query)
                    },
                    successMsg: '保存成功!'
                };
                if(cancel){
                    option.success=function(response,option){
                        option.vue.cancel(event,config);
                    }
                }
                this.my.axios(option);               
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
                    name:this.query.TABLE_NAME+'-config',
                    title:this.query.TABLE_COMMENT+'表-配置',
                    content: this.form.type+'ConfigVue',
                    query:Object.assign({},this.form,this.query,config.query)
                });
            }
        }
    }
</script>
<style>   
</style>
