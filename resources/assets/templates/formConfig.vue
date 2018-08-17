<template>
    <el-container  style="height:100%;" >
        <el-aside style="padding: 10px;width:320px;height:100%;">
            <components 
                ref="accordion" 
                :attrs="attrs" 
                :attrForm="attrForm"
            ></components>
        </el-aside>
        <el-container>
            <el-form  v-model="configs.form" label-width="80px" style="width:100%;height:100%;position:relative;">            
                <div  style="overflow:auto;height:100%;">
                    <draggable  v-form 
                        v-model="configs.fields" 
                        style="width:100%;height:100%"
                        :options="{group:{ name:'view',  pull:false, put: true},preventOnFilter: true,animation: 250}"
                        @change="sort"
                        @click.native="changeAside"
                    >
                        <component 
                            :style="{border:'1px dotted '+(activeField===key?'#eee':'transparent')}"
                            v-for="(field, key,index) in configs.fields" 
                            :key="index" 
                            :is="field.type" 
                            :config="field" 
                            :form="configs.form"
                            @config="toConfig"
                            @click.native="activeField=key;"
                        >
                        </component>
                    </draggable> 
                </div>
                <draggable
                    style="width:99%;display:flex;justify-content:center;align-items:center;position:absolute;bottom:21px;border:1px dashed #eee;"
                    v-model="configs.tools" 
                    :options="{group:{ name:'view',  pull:false, put: true},preventOnFilter: true,animation: 250}"
                >
                    <elementButton
                        v-for="(tool,key,index) in configs.tools"
                        :key="key"
                        :config="tool"
                        class="buttonBottom"
                        @config="toConfig"
                    ></elementButton>
                </draggable>               
            </el-form>
        </el-container>
    </el-container>
</template>  
<script>
    import draggable from 'vuedraggable';
    import components from './components.vue';
    export default {
        components: {
            draggable,
            components
        },
        props: ['query'],
        data() {
            return {
                activeField:'',
                configs: {
                },
                fields:[],
                attrs:{},
                attrForm:{
                }
            }
        },
        created: function(){
            //表单配置
            this.my.axios({
                vue: this,
                axiosOption:{
                    url:'admin/table/view',
                    data:this.query
                },
                success:function(response,option){
                    option.vue.configs=response.data.data;
                }
            });
            //表单字段
            this.my.axios({
                vue: this,
                axiosOption:{
                    url:'admin/table/field',
                    data:this.query
                },
                success:function(response,option){
                    option.vue.fields=response.data.data;
                }
            })
        },       
        computed: {
            
        },        
        methods: {
            //排序
            sort: function(event){
                //添加设置字段
                if('added' in event){
                    //event.added.element.name="test";
                }
            },
            //切换边栏为所有组件
            changeAside:function(event){
                if(event.target==event.currentTarget ){    
                    this.$refs.accordion.activeAccordion="组件";           
                } 
            },            
            //配置字段组件
            toConfig:function(event,config,attr){
                this.$refs.accordion.activeAccordion="属性";
                if(attr.name){
                    attr.name.options=this.fields;
                }
                this.attrForm=config;
                this.attrs=attr;
            },           
            //保存配置
            save: function(event){
                my.axios({
                    vue: this,
                    axiosOption:{
                        url:'/admin/form/save',
                        data: this.configs
                    },
                    successMsg: '保存成功!'
                });               
            }
        },
        directives: {
            component:{
                inserted: function(el){
                }
            },
            form:{
                inserted:function(el){
                }
            }

        }
    }
</script>
<style>
.el-form-item.buttonBottom{
    margin-bottom:0px;
}
</style>
