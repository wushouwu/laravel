<template>
    <el-form-item 
        :label="config.label" 
        :label-width="config.labelWidth"
        :prop="config.name" 
    >
        <el-dropdown 
            split-button 
            :type="config.buttonType" 
            @click="click" 
            @command="itemClick"
        >
            {{config.text}}<i :class="config.icon"></i>
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item 
                    v-for="(option,key,index) in config.options" 
                    :key="key"
                    :command="key"
                >{{option.text}}</el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>
    </el-form-item>
</template>
<script>
    export default {
        props: ['config'],
        mounted(){
            //组件添加删除按钮
            let vue=this,
                span=document.createElement('span');
            span.className="my-close";
            span.addEventListener('click',function(){
                vue.$emit('close',event,vue);
            });
            this.$el.style.position="relative";
            this.$el.appendChild(span);
        },         
        methods:{
            click: function(event){
                this.$emit('buttonClick',event,this.config);
            },
            itemClick:function(command){
                this.$emit('buttonItemClick',event,command);
            }
        }
    }
</script>
<style>
    /*对齐问题处理*/
	.el-dropdown .el-button{
		height:40px;
        vertical-align: top;
	}
</style>
