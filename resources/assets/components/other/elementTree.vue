<template>
    <el-form-item  
        :label="config.label" 
        @click.native="click" 
        :label-width="config.labelWidth"
        :class="{'size-small-font':config.size==='small'||config.size==='mini'?true:false,'label-position-top':config.labelPositionTop}"
        class="elementTree"
    >
        <div 
            class="tree-head"
            :class="{'size-small-font':config.itemDefault.size==='small'||config.itemDefault.size==='mini'?true:false,addShow:'addShow' in config?true:false}"
        >
            <span class="label" style="">显示</span>
            <span class="label" slot="reference" style="display:inline-block;width: 65px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;vertical-align: middle;">列名</span>
            <span class="label">
                <span 
                    v-for="(label,key) in config.labels"
                    :key="key"
                    :style="(config.labelDefault&&config.labelDefault.style?config.labelDefault.style:'')+label.style"
                >{{label.label}}</span>
            </span>
            <elementButton
                :config="addButton"
                @buttonClick="config.add($event,config,form)"
            ></elementButton>            
        </div>                
        <el-tree
            ref="elementTree"
            :class="{'size-small-font':config.itemDefault.size==='small'||config.itemDefault.size==='mini'?true:false,delShow:'delShow' in config?true:false}"
            :data="config.data"
            :node-key="config.nodeKey"
            :indent="config.indent"
            :default-expand-all="config.defaultExpandAll"
            :show-checkbox="config.showCheckbox"
            :expand-on-click-node="config.expandOnClickNode"
            :default-checked-keys="config.defaultCheckedKeys"
            :default-expanded-keys="config.defaultExpandedKeys"
            :draggable="config.draggable"
            :check-strictly="config.checkStrictly"
            :allow-drop="config.allowDrop"
            :allow-drag="config.allowDrag"
            @node-click="nodeClick"
            @check="check"
            @check-change="checkChange"
            @node-drop="dataChange"
        >
            <span slot-scope="{ node, data }">
                <el-popover
                    placement="top"
                    width="250"
                    v-model="node.edit"
                    :disabled="config.popoverDisabled"
                >
                    <elementText :config="value" :form="data"></elementText>
                    <elementText :config="label" :form="data"></elementText>
                    <elementButton :config="cancelButton" @buttonClick="node.edit=false"></elementButton>
                    <span style="display:inline-block;width: 65px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;vertical-align: middle;" class="label" slot="reference">{{ node.label }}</span>
                </el-popover>
                <item
                    :is="config.item.type"
                    :config="config.item"
                    :form="node"
                ></item>
                <elementButton
                    :config="deleteButton"
                    @buttonClick="del($event,node,data)"
                    v-show="config.delShow(node,data)&&delShow"
                ></elementButton>
            </span>        
        </el-tree>
    </el-form-item>
</template>
<script>
    import draggable from 'vuedraggable';
    export default {
        components: {
            draggable,
        },
        props: ['config','form'],
        data(){
            return {
                delShow:true,
                addButton:{
                    type:"elementButton",
                    icon:"el-icon-plus",
                    buttonType:"primary",
                    plain:true,
                    circle:true,
                    size:"mini",
                    wrapper:"span"
                },
                deleteButton:{
                    type:"elementButton",
                    icon:"el-icon-minus",
                    buttonType:"danger",
                    plain:true,
                    circle:true,
                    noText:true,
                    size:"mini",
                    wrapper:"span",
                },
                cancelButton:{
                    type:"elementButton",
                    buttonType:"primary",
                    text:"退出",
                    plain:true,
                    size:"mini",
                    wrapper:"div",
                    style:"text-align:right;"
                },
                value:{
                    type:"elementSelect",
                    label:"字段",
                    name:"value",
                    allowCreate:true,
                    size:"small",
                    labelWidth:"60px",
                    options:[]
                },                
                label:{
                    type:"elementText",
                    label:"标签",
                    name:"label",
                    size:"small",
                    labelWidth:"60px"
                }
            }
        },
        created(){
        },
        mounted(){
            this.labelPositionTop();  

        },
        updated(){
           this.labelPositionTop();
        },
        watch:{

        },
        computed:{

        },
        methods:{
            labelPositionTop(){
                //labelPositionTop样式调整
                if(this.config.labelPositionTop){
                    let item__content=this.$el.querySelector('.el-form-item__content');
                    if(item__content){
                        item__content.style.cssText="marginLeft:0px;clear:both;";
                    }
                } 
                //选择
                /* for(var i in this.config.data){
                    this.$refs.elementTree.setChecked(this.config.data[i],!this.config.data[i].hide);
                }  */               
            },
            //点击节点
            nodeClick(){
                if(typeof(this.config.nodeClick)=='function'){
                    this.config.nodeClick();
                }
            }, 
            //当复选框被点击的时候触发
            check(data,node){
                //console.log(data,node);
                //data.width="0";
            },
            checkChange(data, checked, indeterminate){
                if(typeof(this.config.checkChange)=='function'){
                    this.config.checkChange(this,data, checked, indeterminate);
                }
            },
            //数据改变后渲染
            dataChange(){
                this.delShow=false;
                let vue=this;
                vue.$set(vue.form,'show',false);
                this.$nextTick(()=>{
                    vue.$set(vue.form,vue.config.name,Object.assign([],vue.config.data));
                    vue.delShow=true;
                    vue.$set(vue.form,'show',true);
                });
            },  
            //删除节点   
            del(event,node,data){
                this.$refs.elementTree.remove(node);
                this.dataChange();
            },  
            click: function(event){
                this.$emit('config',event,this.config,{
                    name:{
                        type:"elementSelect",
                        label:"字段",
                        name:"name",
                        options:[]
                    },
                    label: {
                        type:"elementText",
                        label:"字段名",
                        name:"label"
                    },
                    readonly: {
                        type:"elementSwitch",
                        label:"只读",
                        name:"readonly"
                    },
                    placeholder: {
                        type:"elementText",
                        label:"空白提示",
                        name:"placeholder"
                    }                                     
                });
            }
             
        }
    }
</script>
<style>
    .delShow .el-tree-node__content{
        height:33px;
    }
    .tree-head::before{
        content: "\E60E";
        color: transparent;
        padding:4px;
    }
</style>
