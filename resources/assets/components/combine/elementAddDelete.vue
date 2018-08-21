<template>
    <el-form-item  
        :label="config.label" 
        @click.native="click" 
        :label-width="config.labelWidth"
        :class="{'label-position-top':config.labelPositionTop}"
        style="line-height:30px;"
        class="elementAddDelete"
    >
        <div style="line-height:30px;"><label 
                v-for="(label,key,index) in config.labels" 
                :key="key"
                :style="(config.itemDefault&&config.itemDefault.style?config.itemDefault.style:'')+';text-align:center;font-size:13px;color: #606266;'"
            >{{label.label}}</label>
            <elementButton
                :config="Object.assign({},config.itemDefault,addButton)"
                style="width:auto;"
                @buttonClick="add"
            ></elementButton>            
        </div>
        <div
            v-for="(option,key,index) in config.options"
            :key="key"
        >
            <component 
                v-for="(item,k,i) in option" 
                :key="k"
                :config="Object.assign({},config.itemDefault,item)"
                :is="item.type"
                :form="form[config.name][key]"
            ></component>
            <elementButton
                :config="Object.assign({},config.itemDefault,deleteButton)"
                style="width:auto;"
                @buttonClick="del($event,deleteButton,key)"
            ></elementButton>
        </div>      
    </el-form-item>
</template>
<script>
    export default {
        props: ['config','form'],
        data(){
            return {
                addButton:{
                    type:"elementButton",
                    icon:"el-icon-plus",
                    buttonType:"primary",
                    plain:true,
                    circle:true
                },
                deleteButton:{
                    type:"elementButton",
                    icon:"el-icon-minus",
                    buttonType:"danger",
                    plain:true,
                    circle:true
                }
            }
        },
        mounted(){
            this.labelPositionTop();
        },
        updated(){
           this.labelPositionTop();
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
            },            
            add(event,config){
                this.config.options.push(this.config.options[0]);
                let form=Object.assign({},this.form[this.config.name][0]);
                if('label' in form && 'value' in form){
                    form.value='';
                    form.label='';
                }
                this.form[this.config.name].push(form);
            },
            del(event,config,index){
                if(this.config.options.length>1){
                    this.$delete(this.config.options,index);
                    this.$delete(this.form[this.config.name],index);
                }else{
                    this.$message({
                        message: '最少须有一项',
                        type: 'warning'
                    })
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
    /*elementAddDelete组件下的button变形处理*/
    .elementAddDelete [class*=el-icon-]+span{
        margin-left:0px;
    }
    /*elementAddDelete组件下标签行高*/
    .elementAddDelete .el-form-item__content label,.elementAddDelete .el-form-item__content label+.el-form-item .el-form-item__content{
        line-height:30px;  
    }
</style>
