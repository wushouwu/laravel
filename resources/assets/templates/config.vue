<template>
    <el-container  style="height:100%;" >
        <el-aside style="padding: 10px;width:320px;height:100%;">
            <components 
                ref="accordion" 
                :attrs="attrs" 
                :attrForm="attrForm"
                :query="query"
                @selectChange="selectChange"
            ></components>
        </el-aside>
        <el-main>
            <!-- <keep-alive> -->
                <view  
                    :is="this.type+'Config'"
                    ref="view"
                    :query="query"
                    @accordionChange="accordionChange"
                    @containersClick="accordionChange"
                    @configChange="configChange"
                    @toConfig="toConfig"
                    @del="del"
                ></view>
            <!-- </keep-alive> -->
        </el-main>
    </el-container>
</template>  
<script>
    import components from './components.vue';
    import formConfig from './formConfig.vue';
    import tableConfig from './tableConfig.vue';
    import viewConfig from './viewConfig.vue';
    import menuConfig from './menuConfig.vue';
    export default {
        components: {
            formConfig,
            tableConfig,
            viewConfig,
            menuConfig,
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
                },
                type:'',
            }
        },
        created: function(){
            if(!this.query.row){
                this.$set(this.query,'row',{type:'form'});
            }
            this.type=this.query.row.type;
        }, 
        methods: {
            selectChange(value,option){
                if(option.name=='type' ||option.name=='TABLE_NAME' ){
                    this.$set(this.query.row,option.name,value);
                }
                if(option.name=='type'){
                    this.type=value;
                }
            },            
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
            accordionChange:function(event,type){
                if(event.target==event.currentTarget ){    
                    this.$refs.accordion.activeAccordion=type;           
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
