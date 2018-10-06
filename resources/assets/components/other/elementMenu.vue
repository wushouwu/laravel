<template>
    <el-menu    
        ref="elementMenu"
        :default-active="config.activeTab" 
        :collapse="config.isFullscreen"             
        :unique-opened="'uniqueOpened' in config?config.uniqueOpened:true"
        style="height:100%;"
        @select="select"
        @click.prevent.native.right="rightClick"
    >
        <template 
            ref="elementSubmenu"
            v-for="(menu,key) in config.menus"
            v-if="config.show"
        >
            <elementSubmenu
                v-if="menu.children&&menu.children.length"
                :config="menu"
                :key="key"
                :id="'menu'+key"
                @itemRightClick='itemRightClick'
            >
            </elementSubmenu>
            <el-menu-item
                v-if="(!menu.children || menu.children&&!menu.children.length) && !menu.hide"
                :key="key"
                :index="String(key)"
                :id="'menu'+key"                       
                :content="menu.content" 
                :query="JSON.stringify(menu.query)" 
                @click.prevent.stop.native.right="itemRightClick($event,menu)"
            >
                <i :class="menu.icon"></i>
                <span slot="title">{{menu.label}}</span>
            </el-menu-item>
        </template> 
    </el-menu>
</template>

<script>
    export default {
        components: {
            elementSubmenu:{
                name:"elementSubmenu",
                template:`
                    <el-submenu
                        v-if="config.children&&config.children.length"
                        :index="id"
                        :id="id"
                        @click.prevent.stop.native.right="itemRightClick($event,config)" 
                    >
                        <template slot="title">
                            <i :class="config.icon"></i>
                            <span slot="title">{{config.label}}</span>
                        </template>
                        <template 
                            v-for="(menu,key) in config.children"
                        >
                            <elementSubmenu
                                v-if="menu.children&&menu.children.length"
                                :config="menu"
                                :key="key"
                                :id="id+'-'+key"
                                @itemRightClick="itemRightClick"
                            >
                            </elementSubmenu>
                            <el-menu-item
                                v-if="(!menu.children || menu.children&&!menu.children.length) && !menu.hide"
                                :index="id+'-'+key"
                                :id="id+'-'+key"                       
                                :content="menu.content" 
                                :query="JSON.stringify(menu.query)" 
                                @click.prevent.stop.native.right="itemRightClick($event,menu)"                     
                            >
                                <i :class="menu.icon"></i>
                                <span slot="title">{{menu.label}}</span>
                            </el-menu-item>
                        </template>
                    </el-submenu>                
                `,
                props:['config','id'],
                methods:{
                    itemRightClick(event,menu){
                        this.$emit('itemRightClick',event,menu);
                    }
                }
            }           
        },
        props: ['config'],
        data(){
            return {
                role:[],
                dept:[],
                user:[]
            }
        },
        methods:{ 
            rightClick: function(event){
                this.$emit('config',event,this.config,{
                    uniqueOpened:{
                        type:"elementSwitch",
                        label:"单菜单展开",
                        name:"uniqueOpened",
                        size:"small"
                    },                                     
                    menus:{
                        type:"elementTree",
                        labelWidth:"0px",
                        name:"menus",
                        expandOnClickNode:false,
                        itemDefault:{
                            size:"small"
                        },
                        delShow:(node,data)=>(!data.children || data.children && !data.children.length),
                        add:function(event,config,form,vue){                           
                            config.data.push({
                                label:"点击设置"	
                            });
                            form.menus.push({
                                label:"点击设置",
                            });
                            vue.dataChange();                            
                        },
                        defaultCheckedKeys:[],
                        checkChange:function(vue,data, checked, indeterminate){
                            vue.$set(data,'hide',!checked);
                        },
                        allowDrop:(draggingNode, dropNode, type)=>dropNode.data.value&&type=='inner'?false:true,
                        draggable:true,
                        showCheckbox:true,
                        data:Object.assign([],this.config.menus),
                        labelDefault:{
                            style:"padding: 4px;"
                        },
                        labels:[],
                        item:{
                            type:"elementComponents",
                            name:"data",
                            wrapper:"span",
                            itemDefault:{
                                style:"margin-left:10px;"
                            },
                            options:[]
                        }
                    }                                                                     
                });
            },            
            select(key, keyPath){
                if(this.config.menuSelect){
                    this.config.menuSelect(key, keyPath,event)
                }else{
                    this.$emit('menuSelect',key, keyPath,event);
                }
            },
            itemRightClick(event,menu){
                if(!menu.priv){
                    this.$set(menu,'priv',{role:[],dept:[],user:[]});
                }
                if(!menu.query){
                    this.$set(menu,'query',{});
                }
                if(!menu.content){
                    this.$set(menu,'content','');
                }                
                let target=event.currentTarget;//处理currentTarget丢失的问题
                if(!this.role.length||!this.user.length){
                    this.my.axiosAll({
                        vue:this,
                        axiosOption:[{
                            url: 'admin/table/table',
                            data:{
                                TABLE_NAME:'roles',
                                fields:'`name` as label,`id` as value',
                            }
                        },{
                            url: 'admin/table/table',
                            data:{
                                TABLE_NAME:'users',
                                fields:'`name` as label,`id` as value',
                            }
                        }],
                        success:function (response,option) {
                            option.vue.$set(option.vue,'role',response[0].data.data);
                            option.vue.$set(option.vue,'user',response[1].data.data);
                            event.myTarget=target;
                            option.vue.emit(event,menu);
                        }
                    });                    
                }else{
                    this.emit(event,menu);
                }
            },
            emit(event,menu){
                let vue=this;
                this.$set(this.config,'show',false);
                this.$nextTick(()=>{
                    vue.$emit('config',event,menu,{
                        label:{
                            type:"elementText",
                            label:"标题",
                            name:"label"
                        },
                        icon:{
                            type:"elementText",
                            label:"图标",
                            name:"icon"
                        },
                        content:{
                            type:"elementSelect",
                            label:"模板",
                            name:"content",
                            options:[{
                                label:"表格",
                                value:'tableVue'
                            },{
                                label:"表单",
                                value:'formVue'
                            },{
                                label:"视图",
                                value:'viewVue'
                            },{
                                label:"配置",
                                value:'configVue'
                            }]
                        },
                        query:{
                            type:"elementAddDelete",
                            labelPositionTop:true,
                            name:"query",
                            label:"参数",
                            labels:[{
                                label:"参数名"
                            },{
                                label:"参数值"
                            }],
                            itemDefault:{
                                size:"mini",
                                style:"width:40%;display:inline-block;margin-right:2%",
                            },
                            options:menu.query//Object.assign({},this.config.query)
                        },
                        priv:{
                            type:"elementComponents",
                            name:"priv",
                            label:"权限",
                            labelPositionTop:true,
                            itemDefault:{
                                size:"small"
                            },                        
                            options:[{
                                type:"elementSelect",
                                name:"role",
                                label:"角色",
                                transfer:true,
                                multiple:true,
                                source:"options",                        
                                options:this.role,
                                config:{
                                    type:"elementTransfer",
                                    wrapper:"div",
                                    name:"role",
                                    data:this.role,
                                    titles:['未选','已选'],
                                    filterable:true 
                                }
                            },{ 
                                type:"elementSelect",
                                name:"dept",
                                label:"部门",
                                transfer:true,
                                multiple:true, 
                                source:"tableField",
                                tableField:{
                                    table:"roles",
                                    fieldLabel:"name",
                                    fieldValue:"id"
                                },
                                options:[{label: "a", value: 1}],
                                config:{
                                    type:"elementTransfer",
                                    wrapper:"div",
                                    name:"dept",
                                    data:[{
                                        value: "1",
                                        label: "aa"
                                    },{
                                        key: "2",
                                        value: "bb"
                                    }],
                                    titles:['未选','已选'],
                                    filterable:true 
                                }
                            },{
                                type:"elementSelect",
                                name:"user",
                                label:"用户",
                                transfer:true,
                                multiple:true,
                                source:"options",                        
                                options:this.user,
                                config:{
                                    type:"elementTransfer",
                                    wrapper:"div",
                                    name:"user",
                                    data:this.user,
                                    titles:['未选','已选'],
                                    filterable:true 
                                }
                            }]
                        }                                                                                                                  
                    });                    
                    vue.$set(vue.config,'show',true);
                });                
            }
        }
    }
</script>
<style>

</style>
