<template>
    <el-form-item  
        :label="config.label"
        :label-width="config.labelWidth"
        :prop="config.name"
        :rules="rules"
        :class="{'size-small-font':config.size==='small'||config.size==='mini'?true:false}"
        :style="config.style"
        @click.prevent.stop.native.right="rightClick"
    >
        <textarea 
            :name="config.name" 
            v-model="form[config.name]"
            :placeholder="config.placeholder||'请输入'"
            style="width:100%;height:100%;"
            :readonly="config.readonly"
            :editorType="config.editorType"
        >
        </textarea>     
    </el-form-item>
</template>
<script>
    import 'kindeditor/kindeditor-all-min.js';
    import CodeMirror from 'codemirror/lib/codemirror.js';
    import  'codemirror/mode/htmlmixed/htmlmixed.js';
    import  'codemirror/mode/javascript/javascript.js';
    import 'codemirror/mode/php/php.js';
    import 'codemirror/lib/codemirror.css';
    export default {
        props: ['config','form'], 
        data(){
            return {
                KindEditor:'',
                CodeMirror:''
            }
        },
        created(){
            //默认验证
            this.$set(this.config,'rules',Object.assign([{
                required:false,
                trigger:[],
                message:"字段不能为空",
            },{
                type:'',
                trigger:[],
                message:'',
            },{
                custom:'pattern',
                trigger:[],
                message:'',
            }],this.config.rules));            
        },
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
            this.editorUpdate();
        },
        updated(){
            let vue=this;
            if(this.KindEditor){
                this.KindEditor.remove();
                this.KindEditor='';
            }
            console.log(this.CodeMirror)
            if(this.CodeMirror){
                this.CodeMirror.toTextArea();
                this.CodeMirror='';
            }
            this.editorUpdate();
        },
        computed:{
            //验证函数validator与正则验证pattern解析            
            rules(){
                let rules=this.config.rules;
                if(rules){
                    let rule=['validator','pattern'];
                    for(var k in rule){
                        let r=rule[k];
                        if(rules[r]){
                            try{
                                eval('rules=Object.assign({},rules,{'+r+':'+rules[r]+'});');
                            }catch(e){
                                console.log(e);
                            }
                        }
                        if(rules[2] && rules[2][r]){
                            try{                           
                                eval('rules=Object.assign([],rules,{'+2+':{'+r+':'+rules[2][r]+'}});')
                            }catch(e){
                                console.log(e);
                            }
                        }
                    }
                }
                return rules;
            }
        },
        methods:{
            editorUpdate(){
                let vue=this;
                if(this.config.editorType=='multy'){
                    let options=Object.assign({
                        readonly:false,
                        minWidth:200,
                        "themeType":"simple",
                        basePath:"kindeditor/",
                        afterBlur:function(){
                            vue.$set(vue.form,vue.config.name,this.html());
                        }                
                    },this.config.options)            
                    this.KindEditor=KindEditor.create(
                        this.$el.querySelector('textarea[name="'+this.config.name+'"]'),
                        options
                    );
                    this.$set(this.form,this.config.name,this.KindEditor.html());
                    this.KindEditor.readonly(this.config.readonly);
                }else{
                    let options=Object.assign({
                        value : this.form[this.config.name],  // 文本域默认显示的文本
                        mode : "javascript",  // 模式
                        // theme : "",  // CSS样式选择
                        indentUnit : 2,  // 缩进单位，默认2
                        smartIndent : true,  // 是否智能缩进
                        tabSize : 4,  // Tab缩进，默认4
                        readOnly : false, 
                        showCursorWhenSelecting : true,
                        lineNumbers : true  // 是否显示行号              
                    },vue.config.options)                
                    this.CodeMirror = CodeMirror.fromTextArea(vue.$el.querySelector('textarea[name="'+vue.config.name+'"]'),options);
                    console.log(this.CodeMirror)
                }
            },    
            rightClick: function(event){
                this.$emit('config',event,this.config,{
                    name:{
                        type:"elementSelect",
                        label:"字段",
                        name:"name",
                        allowCreate:true,
                        options:[]
                    },
                    label: {
                        type:"elementText",
                        label:"字段名",
                        name:"label"
                    },
                    editorType:{
                        type:"elementSelect",
                        label:"类型",
                        name:"editorType",                        
                        options:[{
                            label:"富文本",
                            value:"multy"
                        },{
                            label:"代码",
                            value:"code"
                        }]
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
                    },
                    rules:{
                        type:"elementComponents",
                        label:"验证规则",
                        name:"rules",
                        itemDefault:{
                            size:"small",
                            style:"border-bottom:1px solid #eee;margin-bottom:10px;padding-bottom:10px;"
                        },
                        labelPositionTop:true,
                        options:[{
                            type:"elementComponents",
                            labelWidth:"0px",
                            name:"0",
                            itemDefault:{
                                size:"small"
                            },
                            options:[{
                                type:"elementSwitch",
                                label:"必填",
                                name:"required",
                            },{
                                type:"elementSelect",
                                label:"触发",
                                name:"trigger",
                                multiple:true,
                                options:[{
                                    label:"失去焦点",
                                    value:"blur"
                                },{
                                    label:"改变",
                                    value:"change"
                                }]
                            },{
                                type:"elementText",
                                label:"提示信息",
                                name:"message",
                            }]
                        },{
                            type:"elementComponents",
                            labelWidth:"0px",
                            name:"1",
                            itemDefault:{
                                size:"small"
                            },
                            options:[{
                                type:"elementSelect",
                                label:"类型",
                                name:"type",
                                options:[{
                                    label:"字符",
                                    value:"string"
                                },{
                                    label:"邮箱",
                                    value:"email"
                                },{
                                    label:"url地址",
                                    value:"url"
                                },{
                                    label:"正则表达式",
                                    value:"regexp"
                                },{
                                    label:"函数",
                                    value:"method"
                                }]
                            },{
                                type:"elementSelect",
                                label:"触发",
                                name:"trigger",
                                multiple:true,
                                options:[{
                                    label:"失去焦点",
                                    value:"blur"
                                },{
                                    label:"改变",
                                    value:"change"
                                }]
                            },{
                                type:"elementText",
                                label:"提示信息",
                                name:"message",
                            }]
                        },{
                            type:"elementComponents",
                            labelWidth:"0px",
                            name:"2",
                            itemDefault:{
                                size:"small"
                            },
                            options:[{
                                type:"elementRadioChange",
                                label:"自定义",
                                name:"custom",
                                itemDefault:{
                                    size:"small",
                                    label:" "
                                },
                                options:[{
                                    type:'el-radio',
                                    label:"正则",
                                    value:"pattern",
                                    config:{
                                        type:"elementText",
                                        name:"pattern",
                                        rules:[{
                                            type:"regexp",
                                            trigger:["change"],
                                            message:"正则表达式格式错误"
                                        }]
                                    }
                                },{
                                    type:'el-radio',
                                    label:"函数",
                                    value:"validator",
                                    config:{
                                        type:"elementTextarea",
                                        name:"validator",
                                        rules:[{
                                            type:"method",
                                            trigger:["change"],
                                            message:"函数格式错误"
                                        }]                                        
                                    }
                                }]
                            },{
                                type:"elementSelect",
                                label:"触发",
                                name:"trigger",
                                multiple:true,
                                options:[{
                                    label:"失去焦点",
                                    value:"blur"
                                },{
                                    label:"改变",
                                    value:"change"
                                }]
                            },{
                                type:"elementText",
                                label:"提示信息",
                                name:"message",
                            }]
                        }]
                    }                                   
                });
            }
        }
    }
</script>
<style>
    .CodeMirror { 
        height: auto;
        border: 1px solid #ddd; 
        min-height:38px;
    }
    .CodeMirror-scroll { max-height: 200px; }
    .CodeMirror pre { padding-left: 7px; line-height: 1.25; }
    .CodeMirror-linenumber.CodeMirror-gutter-elt{
        line-height: 1.25;
    }
</style>
