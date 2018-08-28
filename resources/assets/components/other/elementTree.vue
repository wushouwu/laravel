<template>
    <el-form-item  
        :label="config.label" 
        @click.native="click" 
        :label-width="config.labelWidth"
        :class="{'size-small-font':config.size==='small'||config.size==='mini'?true:false,'label-position-top':config.labelPositionTop}"
        class="elementTree"
    >
        <elementButton
            :config="addButton"
            @buttonClick="config.add($event,config,form)"
        ></elementButton>
        <el-tree
            ref="tree"
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
            @node-click="config.nodeClick"
            @node-drop="nodeDrop"
        >       
            <span slot-scope="{ node, data }">
                <span class="label">{{ node.label }}</span>
                <span>
                    <elementButton
                        :config="deleteButton"
                        @buttonClick="del($event,node,data)"
                        v-show="config.delShow(node,data)"
                    ></elementButton>
                </span>
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
                addButton:{
                    type:"elementButton",
                    icon:"el-icon-plus",
                    buttonType:"primary",
                    plain:true,
                    circle:true,
                    size:"mini"
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
                }
            }
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
                        item__content.style.cssText="marginLeft:0px;";//clear:both;
                    }
                }
            },           
            del(event,node,data){
                this.$refs.tree.remove(node);
            },
            nodeDrop:function(dragNode,dropNode,type,event){
                if(type=='inner'){
                    this.$set(dropNode.data,'value','');
                }
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
</style>
