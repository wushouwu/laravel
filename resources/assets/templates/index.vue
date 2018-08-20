<template>
    <el-container  style="height:100%;">
        <el-aside style="width:auto;"  @mouseover.native="isFullscreen=false">
            <el-menu    
                ref="menu"
                :default-active="activeTab" 
                :collapse="isFullscreen"             
                :unique-opened="true"
                @select="menuSelect"
                style="height:100%;"
            >
                <el-submenu index="1" >
                    <template slot="title">
                        <i class="el-icon-menu"></i>
                        <span slot="title">导航一</span>
                    </template>
                    <el-submenu index="1-1">
                        <span slot="title">选项1</span>
                        <el-menu-item index="1-1-1" ref="1-1-1" content="viewVue" :query="{tableName:'hello'}">选项1</el-menu-item>
                    </el-submenu>
                </el-submenu>
                <el-submenu index="2">
                    <template slot="title">
                        <i class="el-icon-document"></i>
                        <span slot="title">导航二</span>
                    </template>
                    <el-submenu index="2-1">
                        <span slot="title">导航二选项1</span>
                        <el-menu-item index="2-1-1" ref="2-1-1" content="tableVue" :query="{table:'../table'}">导航二选项1</el-menu-item>
                    </el-submenu>
                </el-submenu>
                <el-submenu index="3">
                    <template slot="title">
                        <i class="el-icon-setting"></i>
                        <span slot="title">配置</span>
                    </template>
                    <el-menu-item index="3-1" ref="3-1" content="tableVue" :query="{}">表格</el-menu-item>
                    <el-menu-item index="3-2" ref="3-2" content="viewVue" :query="{}">视图</el-menu-item>
                    <el-menu-item index="3-3" ref="3-3" content="formConfigVue" :query="{}">表单</el-menu-item>
                </el-submenu>
            </el-menu>
        </el-aside>
        <el-main @click.native="isFullscreen=true">
            <el-tabs v-model="activeTab" type="card" closable @tab-remove="removeTab">
            <el-tab-pane style="height:100%;"
                v-for="(item, index) in tabs"
                :key="item.name"
                :label="item.title"
                :name="item.name"
            >
                <content :is="item.content" :query="item.query"></content>
            </el-tab-pane>
            </el-tabs>   
        </el-main>
    </el-container>
</template>  
<script>
import formConfigVue from './formConfig.vue';
import tableVue from './table.vue';
import formVue from './form.vue';
export default {
    components:{
        formConfigVue,tableVue,formVue
    },
    data() {
        return {
            isFullscreen:false,
            activeTab: '3-1',
            activeTabs:['3-1'],
            tabs: {'3-1': {
                title: '表格',
                name: '3-1',
                content: 'tableVue',
                query:{TABLE_NAME:'INFORMATION_SCHEMA.TABLES',view_name:'table'}
            }},
        }
    },
    watch:{
        //活动tab排序
        activeTab(newValue,oldValue){
            if(newValue){
                this.activeTabs=this.activeTabs.filter((currentValue,index,arr)=>currentValue!=newValue);
                this.activeTabs.push(newValue);
            }
        }
    },
    methods: {
        //点击菜单
        menuSelect(key, keyPath){
            let refs=this.$refs[key];
            this.addTab({
                name:key,
                title:refs.$el.innerText,
                content:refs.$attrs.content,
                query:refs.$attrs.query
            });
        },
        //添加tab
        addTab(option) {
            if(!(option.name in this.tabs)){
                this.tabs[option.name]=option;  
                this.activeTabs.push(option.name);
            }
            this.activeTab = option.name; 
        },
        //移除tab,激活新tab
        removeTab(name) {
            let tabs = this.tabs;
            let activeTab = this.activeTab;
            this.activeTabs=this.activeTabs.filter((currentValue,index,arr)=>currentValue!=name);            
            if (activeTab === name) {
                let lastActiveTab=this.activeTabs[ this.activeTabs.length-1];
                activeTab =name==lastActiveTab?'':lastActiveTab;
                //激活菜单
                if(activeTab in this.$refs){
                    this.$refs.menu.open(this.$refs[activeTab].$parent.index);
                    this.$refs[activeTab].$el.click();
                }           
            }
            this.activeTab = activeTab;
            Vue.delete( this.tabs, name )
        }
    }
}
</script>
<style>
    /*菜单栏*/
    .el-menu:not(.el-menu--collapse) {
        width: 300px;
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
    /*按钮组件下边距处理*/ 
    .tools .el-form-item.elementButton{
        margin:5px;
    }
    /*表单组件边距处理*/
    form .el-form-item{
        margin-right:10px;
        margin-bottom:20px;
    }  
    /*size为small时label字体大小处理*/
    .size-small-font,.size-small-font [class*="label"]{
        font-size:13px;
    } 
    /*可拖动组件的光标*/
    .draggable label,.footer-tools.draggable button{
        cursor:move;
    }
</style>
