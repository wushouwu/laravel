<template>
    <el-container  style="height:100%;" >
        <el-aside style="padding: 10px;width:320px;height:100%;">
            <components 
                ref="accordion" 
                :attrs="attrs" 
                :attrForm="attrForm"
                :query="query"
            ></components>
        </el-aside>
        <el-container>
            <el-form  v-model="configs.form" label-width="80px" style="width:100%;height:100%;position:relative;">            
                <div  style="overflow:auto;height:100%;">
                    <draggable
                        v-model="configs.fields" 
                        style="width:100%;height:100%"
                        class="draggable"
                        :options="{group:{ name:'view',  pull:false, put: true},preventOnFilter: true,animation: 250}"
                        @change="sort"
                        @click.native="changeAside"
                    >
                        <component 
                            :style="{border:'1px dotted '+(activeField===key?'#409EFF':'transparent')}"
                            v-for="(field, key,index) in configs.fields" 
                            :key="index" 
                            :is="field.type" 
                            :config="field" 
                            :form="configs.form"
                            @config="toConfig"
                            @click.native="activeField=key;activeButton='';"
                        >
                        </component>
                    </draggable> 
                </div>
                <draggable
                    class="footer-tools draggable"
                    style="width:99%;min-height:42px;display:flex;justify-content:center;align-items:center;position:absolute;bottom:21px;border:1px dashed #c0c4cc;"
                    v-model="configs.tools" 
                    :options="{group:{ name:'view',  pull:false, put: true},preventOnFilter: true,animation: 250}"
                    @change="buttonChange"
                >
                    <elementButton
                        v-for="(tool,key,index) in configs.tools"
                        :key="key"
                        :config="tool"
                        :style="{border:'1px dotted '+(activeButton===key?'#409EFF':'transparent')}"
                        @config="toConfig"
                        @click.native="activeButton=key;activeField='';"
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
                activeButton:'',
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
                    data:this.query.row
                },
                success:function(response,option){
                    if(!response.data.data.fields){
                        response.data.data.fields=[];
                    }
                    if(!response.data.data.tools){
                        response.data.data.tools=[];
                    }                    
                    option.vue.configs=response.data.data;
                },
                error:function(response,option){
                    option.vue.configs={
                        fields:[{
                            type:'elementText',
                            label:'示例',
                            name:'text'
                        }],
                        tools:[{
                            "buttonType":"primary",
                            "shape":"plain",
                            "text":"示例按钮"
                        }],
                        form:{
                            text:'将组件拖放到此区域进行配置'
                        }
                    }
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
        methods: {
            //排序
            sort: function(event,b){
                //添加字段时设置字段属性
                if('added' in event){
                    //this.$refs.accordion.configs.components=this.$refs.accordion.initComponents;
                    //event.added.element.name=event.added.newIndex;
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
            buttonChange(event){
                if('added' in event){
                    this.$delete(event.added.element,'labelWidth');
                    this.$delete(event.added.element,'label');
                }
            }
        }
    }
</script>
<style>
</style>
