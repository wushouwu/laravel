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
        <el-main>
            <keep-alive>
                <view  
                    :is="this.query.row.type+'Config'"
                    ref="view"
                    :query="query"
                    @draggableClick="accordionChange"
                    @configChange="configChange"
                    @toConfig="toConfig"
                    @del="del"
                ></view>
            </keep-alive>
        </el-main>
    </el-container>
</template>  
<script>
    import components from './components.vue';
    import formConfig from './formConfig.vue';
    import tableConfig from './tableConfig.vue';
    export default {
        components: {
            formConfig,
            tableConfig,
            components
        },
        props: ['query'],
        data() {
            return {
                activeField:'',
                activeButton:'',
                configs:[],
                fields:[],
                attrs:{},
                attrForm:{
                }
            }
        },
        created: function(){
            console.log(this.query)
        }, 
        methods: {
            //排序
            sort: function(event){
                //添加字段时设置字段属性
                if('added' in event){
                }
            },
            //删除组件
            del(){
                this.$refs.accordion.activeAccordion="组件";
                this.attrForm={};
                this.attrs={};    
            },
            //切换边栏为所有组件
            accordionChange:function(event){
                if(event.target==event.currentTarget ){    
                    this.$refs.accordion.activeAccordion="组件";           
                } 
            }, 
            //配置改变时
            configChange(configs){
                this.configs=configs;
            },                    
            //配置字段组件
            toConfig:function(event,config,attr){
                this.$refs.accordion.activeAccordion="属性";
                this.attrForm=config;
                this.attrs=attr;
            }
        }
    }
</script>
