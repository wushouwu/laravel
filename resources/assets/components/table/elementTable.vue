<template>
  <el-table
    :data="table"
    stripe
    @click.native="click"
    style="width: 100%">
        <el-table-column 
        v-for="(field,key,index) in config.fields"
        :key="index"
        :prop="field.name"
        :label="field.label"
        :width="field.width">
        </el-table-column>
  </el-table>
</template>

<script>
  export default {
    props: ['config'],
    data() {
        return {        
            table: [{
                date: '2016-05-03',
                name: '王小虎',
                province: '上海',
                city: '普陀区',
                address: '上海市普陀区金沙江路 1518 弄',
                zip: 200333
            }, {
                date: '2016-05-02',
                name: '王小虎',
                province: '上海',
                city: '普陀区',
                address: '上海市普陀区金沙江路 1518 弄',
                zip: 200333
            }, {
                date: '2016-05-04',
                name: '王小虎',
                province: '上海',
                city: '普陀区',
                address: '上海市普陀区金沙江路 1518 弄',
                zip: 200333
            }, {
                date: '2016-05-04',
                name: '王小虎',
                province: '山东',
                city: '普陀区',
                address: '上海市普陀区金沙江路 1518 弄',
                zip: 200333
            }]
        }
    },
    methods:{
        click: function(event){
            this.$emit('config',event,this.config,{
                name:{
                    type:"elementText",
                    label:"字段",
                    name:"name"
                },
                label: {
                    type:"elementText",
                    label:"字段名",
                    name:"label"
                }
            });
        }
    }
  }
</script>