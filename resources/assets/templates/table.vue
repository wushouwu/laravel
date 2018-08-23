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
				:model="form"
				ref="form"
			> 
				<searchTool :style="{width:searchTool.width}"
					v-for="(searchTool, key,index) in searchTools" 
					:key="key" 
					:is="searchTool.type" 
					:config="searchTool" 
					:form="form"
					@buttonClick="buttonClick"
					@buttonItemClick="buttonItemClick"
					@enter="currentChange(1)"
				></searchTool>		
				<tool
					v-for="(tool, key,index) in tools" 
					:key="index" 
					:is="tool.type" 
					:config="tool" 
					:form="form"
					@buttonClick="buttonClick"
				></tool>
			</el-form>
		</div>
		<div style="flex:1;">
			<el-table  
				:max-height="tableHeight"
				:data="data"
				:border="Boolean(table.border)"
				:stripe="Boolean(table.stripe)"
				:default-sort = "table.defaultSort"
				:fit="true"
				ref="table"
			>
				<el-table-column 
					v-for="(field,key,index) in table.fields" 
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
					:width="this.table.operator?this.table.operator.length*50:100"
				>
					<template slot-scope="scope">
						<el-button 
							v-for="(operator,key,index) in table.operator" 
							:key="key"
							@click="operate(scope.row,operator)" 
							type="text" 
							size="small"
						>{{operator.text}}</el-button>
					</template>
				</el-table-column>
			</el-table>
		</div>
		<el-pagination ref="pagination" style="text-align:center;margin-top:10px;"
			@current-change="currentChange"
			@size-change="sizeChange"
			background
			:current-page="pagination.currentPage"
			:page-sizes="[1,5,10, 20, 30, 50,100]"
			:page-size="pagination.pageSize"
			layout="total, sizes, prev, pager, next, jumper"
			:total="total"
		>
		</el-pagination>		
	</div>
</template>

<script>
	import camelCase from 'lodash/camelCase';
	export default {
		props:['query'],
		data() {
			return {
				multiSearch:false,
				tableHeight:400,
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
						"shape":"plain",
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
				data: [],
				total:0
			}
		},
		created: function(){
			this.my.axios({
				vue: this,
				axiosOption:{
					url:'admin/table/view',
					data: Object.assign({type:'table'},this.query)
				},
				success:function(response,option){
					let data=response.data.data;
					//搜索配置
					option.vue.searchTools.field.options=data.table.fields;
					option.vue.searchTools.value.type=option.vue.camelCase('element-'+data.table.defaultSearch.type);
					//其他配置
					option.vue=Object.assign(option.vue,data);
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
				let option = this.searchTools.field.options.find((item)=>{
					return item.value === this.form.field;
				});
				this.$set(this.searchTools.value,'type',this.camelCase('element-'+option.type))
				this.$set(this.form,'value','');
				switch(option.type){
					case 'datetime':
						if(this.form.operator=='between'||this.form.operator=='not between'){
							this.$set(this.searchTools.value,'pickerType','datetimerange');
						}else{
							this.$delete(this.searchTools.value,'pickerType');
						}
						break;
					case 'inputNumber':
						if(this.form.operator=='between'||this.form.operator=='not between'){
							this.$set(this.searchTools.value,'type',this.camelCase('element-'+'inputRange'));
							this.$set(this.form,'value',[0,100]);
						}else{
							this.$set(this.searchTools.value,'type',this.camelCase('element-'+'inputNumber'));
							this.$set(this.form,'value','');
						}	
						break;
				}			
			}
		},
		computed:{
			formField(){
				return this.form.field+'-'+this.form.operator;
			}			
		},
		updated(){
			//处理后台加载配置默认排序不正常的bug
			if(this.table.defaultSort){
				this.$refs.table.sort(this.table.defaultSort.prop);
			}
		},
		methods: {
			//请求数据
			request(){
				let pagination=Object.assign({},this.pagination);
				//pagination.table=this.query.table;
				pagination=Object.assign({},pagination,this.query);
				if(this.form.search){
					pagination.query=this.form;
				}
				this.my.axios({
					vue: this,
					axiosOption:{
						url: this.table.url||'admin/table/table',
						data:pagination
					},
					success:function(response,option){
						option.vue=Object.assign(option.vue,response.data);
					}
				});
			},
			//换页
			currentChange(page){
				this.$set(this.pagination,'currentPage',page);
				this.request();
			},
			//一页条数更改
			sizeChange(size){
				this.$set(this.pagination,'pageSize',size);	
				this.request();
			},	
			//工具栏事件	
			buttonClick(event,config){
				eval(config.script);
			},
			//搜索
			search(event,config){
				this.$set(this.pagination,'currentPage',1);
				this.$set(this.form,'search',config.name=='search'?true:false);			
				this.request(this.pagination);
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