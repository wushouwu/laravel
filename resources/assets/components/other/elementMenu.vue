<template>
    <el-menu    
        ref="elementMenu"
        :default-active="config.activeTab" 
        :collapse="config.isFullscreen"             
        :unique-opened="'uniqueOpened' in config?config.uniqueOpened:true"
        @select="select"
        style="height:100%;"
    >
        <template 
            ref="elementSubmenu"
            v-for="(menu,key) in config.menus"
        >
            <elementSubmenu
                v-if="menu.children&&menu.children.length"
                :config="menu"
                :key="key"
                :id="'menu'+key"
            >
            </elementSubmenu>
            <el-menu-item
                v-else
                :key="key"
                :index="String(key)"
                :id="'menu'+key"                       
                :content="menu.content" 
                :query="JSON.stringify(menu.query)"                    
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
                            >
                            </elementSubmenu>
                            <el-menu-item
                                v-else
                                :index="id+'-'+key"
                                :id="id+'-'+key"                       
                                :content="menu.content" 
                                :query="JSON.stringify(menu.query)"                    
                            >
                                <i :class="menu.icon"></i>
                                <span slot="title">{{menu.label}}</span>
                            </el-menu-item>
                        </template>
                    </el-submenu>                
                `,
                props:['config','id']
            }           
        },
        props: ['config'],
        data(){
            return {
 
            }
        },      
        created(){
        },
        mounted(){ 

        },
        updated(){
        },
        watch:{

        },
        computed:{

        },
        methods:{ 
            select(key, keyPath){
                if(this.config.menuSelect){
                    this.config.menuSelect(key, keyPath,event)
                }else{
                    this.$emit('menuSelect',key, keyPath,event);
                }
            }
        }
    }
</script>
<style>

</style>
