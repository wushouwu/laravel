<template>
    <el-form-item  
        :label="config.label" 
        @click.native="click" 
        :label-width="config.labelWidth"
        :class="{'size-small-font':config.size==='small'||config.size==='mini'?true:false,'label-position-top':config.labelPositionTop}"
        style="line-height:30px;"
        class="elementAddDelete"
    >
        <div style="line-height:30px;font-size:0px"><label 
                v-for="(label,key) in config.labels" 
                :key="key"
                :style="(config.itemDefault&&config.itemDefault.style?config.itemDefault.style:'')+';text-align:center;font-size:13px;color: #606266;'"
            >{{label.label}}</label>
            <elementButton
                :config="Object.assign({},config.itemDefault,addButton)"
                style="width:auto;"
                @buttonClick="add"
            ></elementButton>            
        </div> 
        <!-- options 格式[[{},{}]] --> 
        <div style="font-size:0px"
            v-if="config.options.length && config.options[0].length"
            v-for="(option,key) in config.options"
            :key="key"
        >
            <component 
                v-for="(item,k) in option" 
                :key="k"
                :config="Object.assign({},config.itemDefault,item)"
                :is="item.type"
                :form="form[config.name][key]"
                :index="key"
                @config="itemConfig"
            ></component>
            <elementButton
                :config="Object.assign({},config.itemDefault,deleteButton)"
                style="width:auto;"
                @buttonClick="del($event,deleteButton,key)"
            ></elementButton>
        </div>
        <!-- options 格式[{},{}] -->
        <div style="font-size:0px"
            v-if="config.options.length && config.options[0].length===undefined"
            v-for="(option,key) in config.options"
            :key="key"
        >
            <component 
                :config="Object.assign({},config.itemDefault,option)"
                :is="option.type"
                :form="form[config.name][key]"
                :index="key"
                @config="itemConfig"
            ></component>
            <elementButton
                :config="Object.assign({},config.itemDefault,deleteButton)"
                style="width:auto;"
                @buttonClick="del($event,deleteButton,key)"
            ></elementButton>
        </div> 
        <!-- options 格式{} -->  
        <div style="font-size:0px"
            v-if="config.options.length===undefined"
            v-for="(option,key,index) in config.options"
            :key="key"
        >
            <elementSelect 
                :config="configHandle(optionKey,String(index))"
                :form="formKey"
                @selectChange="selectChange($event,key,index)"
            ></elementSelect>
            <elementText 
                :config="configHandle(optionValue,key)"
                :form="form[config.name]"
                v-if="typeof(option)!='object'"
            ></elementText>
            <div 
                v-else
                v-for="(value,k,i) in option"
                :key="k"
            >
                <elementText 
                    :config="configHandle(optionValue,String(i))"
                    :form="Object.keys(form[config.name][key])"
                ></elementText> 
                <elementText 
                    :config="configHandle(optionValue,k)"
                    :form="form[config.name][key]"
                ></elementText>                                
            </div>            
            <elementButton
                :config="Object.assign({},config.itemDefault,deleteButton)"
                style="width:auto;"
                @buttonClick="del($event,deleteButton,key)"
            ></elementButton>
        </div>
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
                index:'',
                addButton:{
                    type:"elementButton",
                    icon:"el-icon-plus",
                    buttonType:"primary",
                    plain:true,
                    circle:true,
                    noText:true
                },
                deleteButton:{
                    type:"elementButton",
                    icon:"el-icon-minus",
                    buttonType:"danger",
                    plain:true,
                    circle:true,
                    noText:true
                },
                formKey:[],
                formK:[],
                optionKey:{
                    type:'elementSelect',
                    name:'',
                    allowCreate:true,
                    options:[{
                        label:'url',
                        value:'url'
                    },{
                        label:'表名',
                        value:'TABLE_NAME'
                    },{
                        label:'模板',
                        value:'content'
                    },{
                        label:'视图',
                        value:'view_name'
                    }]
                },
                optionValue:{
                    type:'elementText',
                    name:''
                }
            }
        },
        created(){
            if(this.config.options.length===undefined){
                this.formKey=Object.keys(this.form[this.config.name]);

                /* let formValue=Object.values(this.form[this.config.name]);
                for(var key in formValue){
                    if(typeof(formValue[key])=='object'){
                        this.formK=this.formK.concat(Object.keys(formValue[key]));
                    }
                }  */               
            }
        },
        mounted(){
            this.labelPositionTop();
        },
        updated(){           
           this.labelPositionTop();
        },          
        watch:{
            option:{
                handler(newValue,oldValue){
                    if(newValue &&　this.config.options.length && this.config.options[0].length===undefined&&this.index!==''){
                        this.$set(this.config.options,this.index,Object.assign({},newValue,{script:this.config.options[this.index].script}));
                    }
                    if(this.config.options.length===undefined){
                        this.formKey=Object.keys(newValue);
                    }
                },
                deep:true
            }          
        },
        computed:{
            option(){
                return this.index===''?this.form[this.config.name]:this.form[this.config.name][this.index];
            }
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
                if(this.config.options.length){
                    this.config.options.push(Object.assign([],this.config.options[0]));
                    let form=Object.assign({},this.form[this.config.name][0]);
                    if('label' in form && 'value' in form){
                        form.value='';
                        form.label='';
                    }
                    this.form[this.config.name].push(form);
                }else{
                    let index=String(Object.values(this.config.options).length);
                    let key='参数'+index;
                    this.$set(this.config.options,key,'');
                    this.$set(this.form[this.config.name],key,'');
                    //this.formKey=Object.keys(this.form[this.config.name]);
                }
            },
            del(event,config,index){
                if(this.config.itemDefault.delScript){
                    eval(this.config.itemDefault.delScript);
                }                
                if(this.config.options.length>1||this.config.options.length===undefined){
                    this.$delete(this.config.options,index);
                    this.$delete(this.form[this.config.name],index);
                    //this.$delete(this.formKey,this.formKey.findIndex((item)=>item===index));
                }else{
                    this.$message({
                        message: '最少须有一项',
                        type: 'warning'
                    })
                }
            },
            //处理config
            configHandle(config,name){
                return Object.assign({},config,{name:name},this.config.itemDefault);
            },
            //更改key
            selectChange(event,key,index){
                let oldValue=this.form[this.config.name][key];
                this.$delete(this.form[this.config.name],key);
                this.$delete(this.config.options,key);
                this.$set(this.form[this.config.name],event,oldValue);
                this.$set(this.config.options,event,oldValue);
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
            },
            //子组件配置
            itemConfig(event,config,attr){
                let target=event.currentTarget || event.myTarget;
                this.index=target.getAttribute('index');
                let option={config:config,attr:attr,index:this.index};
                this.$emit('e',event,option);
                
            }             
        }
    }
</script>
<style>
    /*elementAddDelete组件下标签行高*/
    .elementAddDelete .el-form-item__content label,.elementAddDelete .el-form-item__content label+.el-form-item .el-form-item__content{
        line-height:30px;  
    }
</style>
