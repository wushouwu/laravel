<template>
    <wrapper  
        :is="config.wrapper||'el-form-item'"    
        :label="config.label"
        :label-width="config.labelWidth"
        :prop="config.name"
        @click.native="click" 
        :class="{'size-small-font':config.size==='small'||config.size==='mini'?true:false}"
        :style="config.style+';text-align: center;'"
    >
        <el-transfer 
            v-model="transferForm" 
            :data="config.data"
            :props="config.props||{key:'value'}" 
            :filterable="config.filterable"
            :filter-placeholder="config.filterPlaceholder"
            :target-order="config.targetOrder"
            :titles="config.titles"
            :left-default-checked="config.leftDefaultChecked"
            :right-default-checked="config.rightDefaultChecked"
            style="text-align: left;display: inline-block"
        ></el-transfer>
    </wrapper>
</template>
<script>
    export default {
        props: ['config','form'], 
        created(){
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
        },
        computed:{
            transferForm:{
                get(){
                    return typeof(this.form[this.config.name])=='string'?
                    this.form[this.config.name]?this.form[this.config.name].split(','):[]
                    :
                    this.form[this.config.name];
                },
                set(newValue){
                    this.$set(this.form,this.config.name,typeof(this.form[this.config.name])=='string'?newValue.join():newValue);
                }
            }
        },
        methods:{            
            click: function(event){
                /* this.$emit('config',event,this.config,{
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
                    }            
                }); */
            },
            enter(event){
                this.$emit('enter',event,this.config);
            }
        }
    }
</script>
