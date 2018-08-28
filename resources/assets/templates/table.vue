<template>
	<div style="display:flex;flex-flow:column;height:100%;"  ref="tableParent">
		<el-dialog title="高级搜索" 
			:visible.sync="multiSearch" 
			:modal-append-to-body="false" 
			:modal="true"
			:fullscreen="false" 
			:close-on-click-modal="false"
		>
			<el-form >
				<el-form-item label="活动名称" >
				<el-input  auto-complete="off"></el-input>
				</el-form-item>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button @click="multiSearch = false">取 消</el-button>
				<el-button type="primary" @click="multiSearch = false">确 定</el-button>
			</div>
		</el-dialog>		
		<div style="height:auto;">
			<el-form 
				inline 
				label-width="0px" 
				style="width:100%;height:100%"
				:model="configs.form"
				ref="form"
			> 
				<searchTool :style="{width:searchTool.width}"
					v-for="(searchTool, key) in configs.searchTools" 
					:key="key" 
					:is="searchTool.type" 
					:config="searchTool" 
					:form="configs.form"
					@buttonClick="buttonClick"
					@buttonItemClick="buttonItemClick"
					@enter="currentChange(1)"
				></searchTool>
				<tool
					v-for="(tool, key,index) in configs.tools" 
					:key="index" 
					:is="tool.type" 
					:config="tool" 
					:form="configs.form"
					@buttonClick="buttonClick"
				></tool>
			</el-form>
		</div>
		<div style="flex:1;">
			<el-table  
				:max-height="tableHeight"
				:data="data.data"
				:border="Boolean(configs.table.border)"
				:stripe="Boolean(configs.table.stripe)"
				:default-sort="configs.table.defaultSort"
				:fit="true"
				ref="table"
			>
				<el-table-column 
					v-for="(field,key) in configs.table.fields" 
					:key="key" 
					:label="field.label"
					:prop="field.value"
					:sortable="Boolean(field.sortable)"
					:fixed="Boolean(field.fixed)"
					:resizable="Boolean(field.resizable)"
					:width="field.width"
					:show-overflow-tooltip="Boolean(field.showOverflowTooltip)"
				>
				</el-table-column>
				<el-table-column
					fixed="right"
					label="操作"
					:resizable="false"
					:width="configs.table.operator?configs.table.operator.length*50:100"
				>
					<template slot-scope="scope">
						<operator
							:is="operator.type"
							v-for="(operator,key) in configs.table.operator" 
							:key="key"
							:config="operator"
							@click.native="operate(scope.row,operator)" 
							:style="configs.table.operator.length-1!=key?'margin-right:10px;':''"
						>{{operator.text}}</operator>
					</template>
				</el-table-column>
			</el-table>
		</div>
		<el-pagination ref="pagination" style="text-align:center;margin-top:10px;"
			@current-change="currentChange"
			@size-change="sizeChange"
			background
			:current-page="configs.pagination.currentPage"
			:page-sizes="[1,5,10, 20, 30, 50,100]"
			:page-size="configs.pagination.pageSize"
			layout="total, sizes, prev, pager, next, jumper"
			:total="data.total"
		>
		</el-pagination>		
	</div>
</template>

<script>
	export default {
		props:['query'],
		data() {
			return {
				multiSearch:false,
				tableHeight:400,
				configs:{
					searchTools:{
						"field":{
							"name":"field",
							"type": "elementSelect",
							"options": []			
						},
						"operator":{
							"name":"operator",
							"type": "elementSelect",
							"width": "90px",	
							"options": [{
								"value": "like",
								"label": "包含"
							}, {
								"value": "=",
								"label": "等于"
							}, {
								"value": "not like",
								"label": "不包含"
							}, {
								"value": "!=",
								"label": "不等于"
							}, {
								"value": ">",
								"label": "大于"
							}, {
								"value": "<",
								"label": "小于"
							},{
								"value": "between",
								"label": "间于"
							},{
								"value": "not between",
								"label": "非间于"
							}]			
						},
						"value":{
							"name":"value",
							"type": "elementText",
						},
						"search":{
							"name":"search",
							"type":"elementDropdown",
							"buttonType":"primary",
							"icon":"el-icon-search",
							"script":"this.search(event,config);",
							"options":[{
								"text":"高级搜索"
							}]
						},
						"reset":{
							"name":"reset",
							"buttonType":"primary",
							"shape":"",
                			"saturation": "plain",
							"title":"重置",
							"type":"elementButton",
							"icon":"el-icon-refresh",
							"script":"this.reset(event,config);",
						}
					},
					tools:[],
					form:{
						operator: 'like'
					},
					//默认表格配置，避免首次加载表格变形
					"table":{
						"border":true,
						"stripe":true,
						"defaultSort":{
							"prop":"",
							"order":"ascending"
						},
						"defaultSearch":{
							"value": "default",
							"label":" ",
							"type":"text",
							"sortable": true,
							"fixed": true,
							"resizable": true
						}, 
						"fields":[{
							"value": "default",
							"label":" ",
							"type":"text",
							"sortable": true,
							"fixed": true,
							"resizable": true
						}]
					},     
					"pagination":{
						"currentPage":1,
						"pageSize":10
					},
				},
				data:{
					data: [],
					total:0
				}
			}
		},
		created: function(){
			this.my.axios({
				vue: this,
				axiosOption:{
					url:'admin/table/view',
					data: Object.assign({},this.query,{type:'table'})
				},
				success:function(response,option){
					let data=response.data.data;
					//搜索字段为表格的字段
					option.vue.$set(option.vue.configs.searchTools.field,'options',data.table.fields);
					//搜索值类型为搜索字段的类型
					let defaultSearch=option.vue.configs.searchTools.field.options.find((item,index,arr)=>item.value==data.form.field);
					option.vue.$set(option.vue.configs.searchTools.value,'type',defaultSearch?option.vue.camelCase('element-'+defaultSearch.type):'elementText');
					//其他配置
					option.vue.configs=Object.assign({},option.vue.configs,data);
					//数据
					option.vue.request();
				}
			});
		},  
		mounted(){	
			//表格高度自适应
			let self=this;
			this.$nextTick(()=>{
				let string="self.tableHeight = self.$refs.tableParent.offsetHeight-self.$refs.form.$el.offsetHeight-self.$refs.pagination.$el.offsetHeight-10;";
				eval(string);
				window.onresize = function() {
					eval(string);
				}
			});
		},
		watch:{
			//监测搜索字段变化
			formField(newValue,oldValue){
				let option = this.configs.searchTools.field.options.find((item)=>{
					return item.value === this.configs.form.field;
				});
				this.$set(this.configs.searchTools.value,'type',this.camelCase('element-'+option.type))
				this.$set(this.configs.form,'value','');
				switch(option.type){
					case 'datetime':
						if(this.configs.form.operator=='between'||this.configs.form.operator=='not between'){
							this.$set(this.configs.searchTools.value,'pickerType','datetimerange');
						}else{
							this.$delete(this.configs.searchTools.value,'pickerType');
						}
						break;
					case 'inputNumber':
						if(this.configs.form.operator=='between'||this.configs.form.operator=='not between'){
							this.$set(this.configs.searchTools.value,'type',this.camelCase('element-'+'inputRange'));
							this.$set(this.configs.form,'value',[0,100]);
						}else{
							this.$set(this.configs.searchTools.value,'type',this.camelCase('element-'+'inputNumber'));
							this.$set(this.configs.form,'value','');
						}	
						break;
				}			
			}
		},
		computed:{
			formField(){
				return this.configs.form.field+'-'+this.configs.form.operator;
			}			
		},
		updated(){
			//处理后台加载配置默认排序不正常的bug
			if(this.configs.table.defaultSort){
				this.$refs.table.sort(this.configs.table.defaultSort.prop);
			}
		},
		methods: {
			//请求数据
			request(){
				let data=Object.assign({},this.query,this.configs.pagination,);
				if(this.configs.form.search){
					data.query=this.configs.form;
				}
				this.my.axios({
					vue: this,
					axiosOption:{
						url: this.configs.table.url||'admin/table/table',
						data:data
					},
					success:function(response,option){
						option.vue.data=response.data;
					}
				});
			},
			//换页
			currentChange(page){
				this.$set(this.configs.pagination,'currentPage',page);
				this.request();
			},
			//一页条数更改
			sizeChange(size){
				this.$set(this.configs.pagination,'pageSize',size);	
				this.request();
			},	
			//工具栏事件	
			buttonClick(event,config){
				eval(config.script);
			},
			//搜索
			search(event,config){
				this.$set(this.configs.pagination,'currentPage',1);
				this.$set(this.configs.form,'search',config.name=='search'?true:false);			
				this.request(this.configs.pagination);
			},
			//重置
			reset(event,config){
				this.search(event,config);
			},
			//添加
			add(event,config){
				let title=this.query.TABLE_COMMENT+'-'+this.query.view_name+'-添加';
				this.addTab({
					name: title,
					content: config.query.content||'formVue',
					query:Object.assign({},this.query,config.query)
				})
			},			
			//高级搜索弹窗
			buttonItemClick(event,command){
				if(command==0){
					this.multiSearch=true;
				}
			},
			//表格操作
			operate(row,operator) {
				eval(operator.script);
			},
			//打开tab
			addTab(option){
				this.$root.$children[0].addTab(option);
			},
			//查看数据
			view(row,operator){
				this.addTab({
					name: this.query.TABLE_NAME+'-'+row.id+'-view',
					content: 'formVue',
					query:Object.assign({},this.query,{row:row},operator.query)
				});
			},
			//编辑数据
			edit(row,operator){
				let title=this.query.TABLE_COMMENT+'-'+this.query.view_name+'-'+row.id+'-编辑';
				this.addTab({
					name: title,
					content: operator.query.content||'formVue',
					query:Object.assign({},this.query,{row:row},operator.query)
				})
			},
			//删除数据
			delete(row,operator){
				console.log(row,operator)
			}
		}
	}
</script>
<style>
</style>