<template>
    <el-container  style="height:100%;">
        <el-aside style="width:auto;"  @mouseover.native="configs.isFullscreen=false">
            <elementMenu :config="configs" @menuSelect="menuSelect" ref="elementMenu"></elementMenu>
        </el-aside>
        <el-main @click.native="configs.isFullscreen=true">
            <el-tabs v-model="configs.activeTab" type="card" closable @tab-remove="removeTab">
            <el-tab-pane style="height:100%;"
                v-for="(item) in configs.tabs"
                :key="item.value"
                :label="item.label"
                :name="item.value"
            >
                <content :is="item.content" :query="item.query"></content>
            </el-tab-pane>
            </el-tabs>   
        </el-main>
    </el-container>
</template>  
<script>
import configVue from './config.vue';
import tableVue from './table.vue';
import formVue from './form.vue';
export default {
    components:{
        configVue,tableVue,formVue
    },
    props:['query'],
    data() {
        return {
            configs:{
                show:true,
                isFullscreen:false,
                activeTab: '',
                activeTabs:[],                
                tabs: {},
                menus:[]
            }
        }
    },
    created(){
        this.my.axios({
            vue: this,
            axiosOption:{
                url:this.query.url||'admin/table/view',
                data:Object.assign({type:"menu",view_name:"菜单"},this.query)
            },
            success:function(response,option){
                option.vue.configs=response.data.data;
                option.vue.$set(option.vue.configs,'show',true);                
            }
        });
    },
    watch:{
        //活动tab排序
        activeTab(newValue,oldValue){
            if(newValue){
                this.configs.activeTabs=this.configs.activeTabs.filter((currentValue,index,arr)=>currentValue!=newValue);
                this.configs.activeTabs.push(newValue);
            }
        }
    },
    computed:{
        activeTab(){
            return this.configs.activeTab;
        }
    },
    methods: {
        //点击菜单
        menuSelect(key, keyPath,event){
            let query=JSON.parse(event.currentTarget.getAttribute('query'));
            this.addTab({
                value:key,
                label:event.currentTarget.innerText,
                content:event.currentTarget.getAttribute('content'),
                query:query
            });
        },
        //添加tab
        addTab(option) {
            if(!(option.value in this.configs.tabs)){
                if(!option.label){
                    option.label=option.value;
                }
                this.configs.tabs[option.value]=option;  
                this.configs.activeTabs.push(option.value);
            }
            this.configs.activeTab = option.value; 
        },
        //移除tab,激活新tab
        removeTab(name) {
            let tabs = this.configs.tabs;
            let activeTab = this.configs.activeTab;
            this.configs.activeTabs=this.configs.activeTabs.filter((currentValue,index,arr)=>currentValue!=name);            
            if (activeTab === name) {
                let lastActiveTab=this.configs.activeTabs[ this.configs.activeTabs.length-1];
                activeTab =name==lastActiveTab?'':lastActiveTab;
                //激活菜单
                let $activeTab=this.$el.querySelector('#'+activeTab);
                if($activeTab){
                    this.$refs.elementMenu.$refs.elementMenu.open(activeTab.substring(0,activeTab.lastIndexOf('-')));
                    $activeTab.click();
                }
                

            }
            this.configs.activeTab = activeTab;
            Vue.delete( this.configs.tabs, name )
        }
    }
}
</script>
<style>
    /*菜单栏*/
    .el-menu:not(.el-menu--collapse) {
        width: 300px;
    }
    /* 菜单图标 */    
    .el-submenu [class^=fa],
    .el-menu-item [class^=fa]{
        vertical-align: middle;
        margin-right: 5px;
        width: 24px;
        text-align: center;
        font-size: 18px;
    } 
    .el-tabs{
        display:flex;
        flex-flow:column;
        height:100%;
    }
    .el-tabs__content{
        flex: 1;
    }  
    /*时间选择框大小*/
    .el-date-editor.el-input, .el-date-editor.el-input__inner{
        width:100%;
    } 
    /*按钮组件边距处理*/ 
    .tools .el-form-item.elementButton{
        margin:5px;
    }
    /*表单组件边距处理*/
    .fields>.el-form-item{
        margin:0px 5px 20px 5px;
    } 
    form>.el-form-item{
        margin-right:10px;
    }       
    /*size为small时label字体大小处理*/
    .size-small-font [class*="label"]{
        font-size:13px;
    } 
    /*可拖动组件的光标*/
    .draggable label,.draggable button{
        cursor:move;
    }
    /*激活的组件显示虚线框*/
    .draggable .el-form-item,draggable .elementCard{
        border:1px dotted transparent;
    }
    .draggable .el-form-item.active,.draggable .elementCard.active{
        border:1px dotted #409EFF;
    }
    /*组件删除按钮*/
    .my-close{
        display: none;
    }
    .draggable .active .my-close{
        position:absolute;
        top:0px;
        left:0px;
        border-radius:12px;
        font-size:12px;
        border:1px solid #dddddd;
        color:red;
        width:12px;
        height:12px;
        text-align:center;
        cursor:pointer;
        line-height: 11px;
        display:inline-block;
    }
    .draggable .active .my-close::after{
        content:'×'
    }  
    /* table错位问题 */
    body .el-table th.gutter{
        display: table-cell!important;
    }  
    /* 有背景分页圆形 */
    .el-pagination.is-background .el-pager li,.el-pagination.is-background button[type="button"]{
        border-radius:14px;
    } 
    /* 卡片处理 */       
    .elementCard{
        display:inline-block;
        vertical-align: top;
        margin: 1% 0px 0px 1%; 
    }
    /* 卡片头处理 */    
    .elementCard .el-card__header{
        display:flex;
        align-items:center;
        text-indent: 18px;
        height:57.8px;
        padding:0px;
    }
    .containers .elementCard{
        cursor:move;
    }
</style>
