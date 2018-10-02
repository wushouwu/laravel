<template>
    <wrapper
        :is="config.wrapper||'el-form-item'"
        :prop="config.name"
        :label="config.label" 
        :label-width="config.labelWidth"
        :class="{'size-small-font':config.size==='small'||config.size==='mini'?true:false,'label-position-top':config.labelPositionTop}"
        :style="config.style"
    >
        <component 
            v-if="option.type.indexOf('element')>=0"
            v-for="(option,key) in config.options"
            :key="key"
            :is="option.type"
            :config="Object.assign({},config.itemDefault,option)"
            :form="form[config.name]"
            @e="e"
        >{{option.text}}</component>
        <componen
            v-if="option.type.indexOf('element')<0"
            v-for="(option,key) in config.options"
            :key="key"
            :is="option.type"
            v-model="form[config.name][option.name]"
            :style="config.itemDefault&&config.itemDefault.style?config.itemDefault.style:''"            
        >{{option.text}}</componen>
    </wrapper>
</template>
<script>
    export default {
        props: ['config','form'],
        mounted(){
            this.labelPositionTop();
        },
        updated(){
           this.labelPositionTop();
        },        
        watch:{
            configForm:{
        　　　　handler(newValue, oldValue) {
        　　　　　　eval(this.config.formWatch);
        　　　　},
        　　　　deep: true
            }
        },
        computed:{
            configForm(){
                return this.form[this.config.name];
            }
        },       
        methods:{
            e(event,config){
                switch(config.name){
                    default:
                        eval(config.script);
                }
                this.$emit('componentEvent',event,config);
            },
            //类型转换
            transfer(option){
                if(option.type.indexOf('element')<0){
                    this.$set(this.form[this.config.name],option.name,Boolean(this.form[this.config.name][option.name]));
                }
            },
            labelPositionTop(){
                //labelPositionTop样式调整
                if(this.config.labelPositionTop){
                    let item__content=this.$el.querySelector('.el-form-item__content');
                    if(item__content){
                        item__content.style.cssText="marginLeft:0px;clear:both;";
                        
                    }
                }
            }
        }  
    }
</script>
