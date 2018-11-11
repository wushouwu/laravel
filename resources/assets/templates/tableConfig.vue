<template>
	<div style="display:flex;flex-flow:column;height:100%;"  ref="tableParent">
		<el-dialog :title="(query.title?query.title:'表格')+'-高级搜索'" 
			:visible.sync="configs.multiSearch" 
			:modal-append-to-body="false" 
			:modal="true"
			:fullscreen="false" 
			width="55%"
			:close-on-click-modal="true"
		>
			<el-form>
				<div 
					class="multisearch-condition"
					v-for="(condition,key) in configs.multiSearchTools"
					:key="key"
				>	
					<tool
						v-for="(tool,k) in condition"
						:is="tool.type"
						v-if="tool.length===undefined"
						:style="{width:tool.width}"
						:key="k"
						:config="Object.assign({disabled:key==0&&(k=='delete'||k=='connective')},tool)"
						:form="configs.form.multiSearch[key]"
						@buttonClick="addOrDelete($event,tool,key)"
					>
					</tool>
					<div 
						class="multisearch-field-condition"
						v-else
					>
						<div 
							class="multisearch-condition"
							v-for="(fieldCondition,fieldCondition_k) in tool"
							:key="fieldCondition_k"
						>
							<tool
								v-for="(t,t_k) in fieldCondition"
								:is="t.type"
								:style="{width:t.width}"
								:key="t_k"
								:config="Object.assign({disabled:fieldCondition_k==0&&(t_k=='delete'||t_k=='connective')},t)"
								:form="configs.form.multiSearch[key].fieldCondition[fieldCondition_k]"
								@buttonClick="fieldConditionDelete($event,t,key,fieldCondition_k)"
							>
							</tool>						
						</div>						
					</div>															
				</div>
			</el-form>
			<div slot="footer" class="dialog-footer">
					<elementButton
						v-for="(button,key) in this.multiSearchButton"
						:key="key"
						:config="button"
						style="margin-right:5px;"
						@buttonClick="buttonClick"
					></elementButton>
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
					@enter="search('event',{name:'search'})"
				></searchTool>		
				<draggable
					v-model="configs.tools" 
					class="draggable"
                    style="display: inline-block;height: 100%;border:1px dashed #c0c4cc;min-width: 60px;min-height:66.8px;cursor:pointer;"
					:options="{group:{ name:'view',  pull:false, put: true},preventOnFilter: true,animation: 250}"
                    @change="buttonChange"
					@click.native="toolsClick"
				>				
					<tool
						v-for="(tool, key) in configs.tools" 
						:key="key" 
						:is="tool.type" 
						:config="tool" 
						:form="tool"
                    	:class="{active:activeField===key}"
                    	@click.prevent.stop.native.right="activeField=key;activeButton='';"
						@config	="toConfig"
                   		@close="del(key,'tools')"
					></tool>
				</draggable>
			</el-form>
		</div>
		<div style="flex:1;">
			<el-table  
				ref="table"
				:max-height="tableHeight"
				:data="data.data"
				:border="Boolean(configs.table.border)"
				:stripe="Boolean(configs.table.stripe)"
				:default-sort="configs.table.defaultSort"
				:fit="true"
				@click.prevent.stop.native.right="rightClick"
			>
				<el-table-column 
					v-for="(head,headkey) in configs.table.header.column"
					v-if="configs.table.header.show&&!head.hide"
					:key="headkey" 
					:label="head.label"
					:prop="head.value"
					:sortable="Boolean(head.sortable)"
					:fixed="Boolean(head.fixed)"
					:resizable="Boolean(head.resizable)"
					:width="head.width"
					:show-overflow-tooltip="Boolean(head.showOverflowTooltip)"
				>
					<el-table-column 
						v-for="(field,key) in head.children"
						v-if="configs.table.header.show&&!head.hide"
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
			:page-sizes="[5,10, 20, 30, 50,100]"
			:page-size="configs.pagination.pageSize"
			layout="total, sizes, prev, pager, next, jumper"
			:total="data.total"
		>
		</el-pagination>		
	</div>
</template>
<script>
    import draggable from 'vuedraggable';
	export default {
        components: {
            draggable
        },        
		props:['query'],
		data() {
			return {
				activeField:"",//激活的组件index
				tableHeight:400,//table 的默认高度
				multiSearchTools:[{
					"connective":{
						"name":"connective",
						"type": "elementSelect",
						"placeholder":" ",
						"width":"90px",
						"options": [{
							value:"and",
							label:"并且"
						},{
							value:"or",
							label:"或者"
						}]			
					},
					"field":{
						"name":"field",
						"type": "elementSelect",
						"options": []			
					},
					"add":{
						type:"elementButton",
						icon:"el-icon-plus",
						name:"add",
						buttonType:"primary",
						plain:true,
						circle:true,
						noText:true	,
						size:"small"					
					},
					"fieldCondition":[{
						"connective":{
							"placeholder":" ",
							"name":"connective",
							"type": "elementSelect",
							"size":"mini",
							"width":"90px",
							"options": [{
								value:"and",
								label:"并且"
							},{
								value:"or",
								label:"或者"
							}]			
						},
						"operator":{
							"name":"operator",
							"type": "elementSelect",
							"size":"mini",
							"width": "95px",	
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
							"size":"mini",
						},
						"delete":{
							name:"delete",
							type:"elementButton",
							icon:"el-icon-minus",
							buttonType:"primary",
							plain:true,
							circle:true,
							noText:true	,
							size:"mini"					
						}
					}],
					"delete":{
						type:"elementButton",
						name:"delete",
						icon:"el-icon-minus",
						buttonType:"primary",
						plain:true,
						circle:true,
						noText:true	,
						size:"small"					
					}
				}],
				multiSearchButton:{
					add:{
						style:"position: absolute;left: 0;",
						type:"elementButton",
						icon:"el-icon-plus",
						buttonType:"primary",
						wrapper:"span",
						"title":"添加",
						circle:true,
						noText:true,
						"script":"this.multiSearchAdd()"					
					},					
					cancel:{
						"type":"elementButton",
						wrapper:"span",
						circle:true,
						noText:true	,
						"title":"取消",
						"icon":"el-icon-close",
						"script":"this.configs.multiSearch = false"				
					},
					reset:{
						"type":"elementButton",
						wrapper:"span",
						"buttonType":"primary",
						plain:true,
						circle:true,
						noText:true	,
						"title":"重置",
						"icon":"el-icon-refresh",
						"script":"this.reset(event,config);"				
					},
					multiSearch:{
						name:"multiSearch",
						"type":"elementButton",
						wrapper:"span",
						"buttonType":"primary",
						circle:true,
						noText:true	,
						"title":"搜索",
						"icon":"el-icon-search",
						"script":"this.multiSearch(event,config);"				
					}
				},				
				configs:{	//table 配置
					multiSearch:false,
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
						"header":{
							"fields":[],							
							"column":[{
								"value": "default",
								"label":" ",
								"type":"text",
								"sortable": true,
								"fixed": true,
								"resizable": true
							}],
							"show":true
						},
						"operator":[]
					},     
					"pagination":{
						"currentPage":1,
						"pageSize":10
					},
				},
				data:{	//table数据
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
					data: Object.assign({},this.query.row,{priv:false})
				},
				success:function(response,option){
					let data=response.data.data;
					//其他配置
					option.vue.configs=Object.assign({},option.vue.configs,data);
					//表头
					if(!option.vue.configs.table.header.column||(option.vue.configs.table.header.column&&!option.vue.configs.table.header.column.length)){
						option.vue.$set(option.vue.configs.table.header,'column',Object.assign([],option.vue.configs.table.header.fields));
					}
					//处理动态字段渲染问题
					option.vue.$set(option.vue.configs.table.header,'show',true);
					//搜索字段
					option.vue.$set(option.vue.configs.searchTools.field,'options',option.vue.configs.table.header.fields);
					option.vue.$set(option.vue.multiSearchTools[0].field,'options',option.vue.configs.table.header.fields);
					//高级搜索
					if(!option.vue.configs.form.multiSearch){										
						option.vue.$set(option.vue.configs.form,'multiSearch',[{
							connective:"",
							field:"",
							fieldCondition:[{
								connective:"",
								operator:"like",
								value:""
							}]
						}]);
					}
					if(!option.vue.configs.multiSearchTools){
						option.vue.$set(option.vue.configs,'multiSearchTools',JSON.parse(JSON.stringify(option.vue.multiSearchTools)));
					}					
					//搜索值类型为搜索字段的类型
					let defaultSearch=option.vue.configs.searchTools.field.options.find((item,index,arr)=>item.value==data.form.field);
					option.vue.$set(option.vue.configs.searchTools.value,'type',defaultSearch?option.vue.camelCase('element-'+defaultSearch.type):'elementText');					
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
				let option = this.configs.searchTools.field.options.find((item)=>item.value === this.configs.form.field);
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
            },
            configs:{
                handler(newValue,oldValue){
                    this.$emit('configChange',newValue);
                },
                deep:true
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
				let data=Object.assign({TABLE_NAME:this.query.row.TABLE_NAME},this.configs.pagination);
				if(this.configs.form.search=='search'||this.configs.form.search=='multiSearch'){
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
			search(event,config,page=1){
				if(page){
					this.$set(this.configs.pagination,'currentPage',page);
				}
				this.$set(this.configs.form,'search',config.name);			
				this.request();
			},
			//搜索
			multiSearch(event,config,page=1){
				if(page){
					this.$set(this.configs.pagination,'currentPage',page);
				}
				this.$set(this.configs.form,'search',config.name);		
				this.request();
			},			
			//高级搜索增加条件
            multiSearchAdd(event,config){
				this.configs.multiSearchTools.push(JSON.parse(JSON.stringify(this.multiSearchTools[0])));
				this.configs.form.multiSearch.push({
					connective:"and",
					field:"",
					fieldCondition:[{
						connective:"",
						operator:"like",
						value:""
					}]
				});
			},
			//高级搜索增减条件
			addOrDelete($event,config,index){
				if(config.name=='delete'){
					this.$delete(this.configs.multiSearchTools,index);
					this.$delete(this.configs.form.multiSearch,index);
				}else{
					this.configs.multiSearchTools[index].fieldCondition.push(JSON.parse(JSON.stringify(this.multiSearchTools[0].fieldCondition[0])));
					this.configs.form.multiSearch[index].fieldCondition.push({
						connective:"and",
						operator:"like",
						value:""
					});
				}
			},
			//高级搜索减字段条件
			fieldConditionDelete($event,config,index,i){
				this.$delete(this.configs.multiSearchTools[index].fieldCondition,i);
				this.$delete(this.configs.form.multiSearch[index].fieldCondition,i);
			},				
			//重置
			reset(event,config){
				this.search(event,config,false);
			},
			//添加
			add(event,config){
				let title=(this.query.title?this.query.title:'表格')+'-'+config.text;
				let query=Object.assign({},this.query);
				//delete query.where;
				query=Object.assign({},query,config.query);
				console.log(query,config);
				this.addTab({
					value: title,
					content: query.content||'formVue',
					query: query
				})
			},			
			//高级搜索弹窗
			buttonItemClick(event,command){
				if(command==0){
					this.configs.multiSearch=true;
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
				console.log(this.query.view_name,operator.query.view_name)
				this.addTab({
					value: this.query.title+'-'+row.id+'-'+operator.text,
					content: 'formVue',
					query:Object.assign({},this.query,{row:row},operator.query)
				});
			},
			//编辑数据
			edit(row,operator){
				console.log(this.query.view_name,operator.query.view_name)
				let title=this.query.title+'-'+row.id+'-'+operator.text;
				this.addTab({
					value: title,
					content: operator.query.content||'formVue',
					query:Object.assign({},this.query,{row:row},operator.query)
				})
			},
			//删除数据
			delete(row,operator){
				let vue=this;
				this.$confirm(`确定删除id为${row.id}的数据?`, '操作确认', {
					//confirmButtonText: '确定',
					//cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					vue.my.axios({
						vue: vue,
						axiosOption:{
							url:operator.query.url||'admin/table/delete',
							data:Object.assign({},this.query,{row:row},operator.query)
						},
						success:function(response,option){
							option.vue.search('event',option.vue.configs.form.search?{name:"search"}:{},false);
						},
						successMsg:"删除成功"
					});
				}).catch(() => {
					          
				});				
			},
            //按钮拖放后去掉标签
            buttonChange(event){
                if('added' in event){
                    this.$set(event.added.element,'labelWidth','0px');
                    this.$set(event.added.element,'label',' ');
                }
			},
            //配置组件
            toConfig:function(event,config,attr){ 
                this.$emit('toConfig',event,config,attr)
			},
			//配置表格列
			headerClick(column, event){
				if(column.label=="操作" && !column.property){
					let operator=JSON.parse(JSON.stringify(this.configs.table.operator));
					this.$emit('toConfig',event,this.configs.table,{
						operator:{
							type:"elementAddDelete",
							name:"operator",
							label:"操作项",	
							labelPositionTop:true,					
							labels:[{label:'按钮'}],
							itemDefault:{
								style:"width:60%;display:inline-block;margin-right:2%;text-align:center;",
								size:"mini",
								delScript:`this.$delete(this.$parent.$children[1].config.options[0],'options');`						
							},
							options:operator.map((item,index,arr)=>{
								item.script=`
									this.$set(this.attrs['button'].options[0],'options',option.attr);
									this.$set(this.attrs['button'].options[0],'name',String(option.index));
								`;
								return item;
							})
						},
						button:{
							type:"elementComponents",
							name:"operator",
							label:"按钮设置",
							labelPositionTop:true,
							itemDefault:{
								size:"small"
							},
							options:[{
								type:"elementComponents",
								name:"0",
								itemDefault:{
									size:"small"
								},							
								options:[]
							}]
						}
					});
				}else{
					let vue=this,
						defaultCheckedKeys=[];
					this.configs.table.header.column.map((item,index,arr)=>{ 
						item.fixed=Boolean(item.fixed);
						item.resizable=Boolean(item.resizable);
						item.sortable=Boolean(item.sortable);
						item.showOverflowTooltip=Boolean(item.showOverflowTooltip);
						defaultCheckedKeys.push(item.value);
						return item;
					});
					//表头配置
					this.$emit('toConfig',event,this.configs.table.header,{
						column:{
							type:"elementTree",
							name:"column",
							labelWidth:'0px',	
							expandOnClickNode:false,
							itemDefault:{
								size:"small"
							},
							delShow:(node,data)=>(!data.children || data.children && !data.children.length) && !data.value,
							add:function(event,config,form){
								config.data.push({
									label:"点击设置",
									align:"center",
									resizable:true	
								});
								form.column.push({
									label:"点击设置",
									align:"center",
									resizable:true	
								})
							},
							defaultCheckedKeys:defaultCheckedKeys,
							checkChange:function(vue,data, checked, indeterminate){
								vue.$set(data,'hide',!checked);
							},
							allowDrop:(draggingNode, dropNode, type)=>dropNode.data.value&&type=='inner'?false:true,
							draggable:true,
							showCheckbox:true,
							data:Object.assign([],this.configs.table.header.column),
							labelDefault:{
								style:"padding: 4px;"
							},
							labels:[{
								label:"拖动"
							},{
								label:"排序"
							},{
								label:"固定"
							}],
							item:{
								type:"elementComponents",
								name:"data",
								wrapper:"span",
								itemDefault:{
									style:"margin-left:10px;"
								},
								options:[{
									type:"el-checkbox",
									name:"resizable"
								},{
									type:"el-checkbox",
									name:"sortable"
								},{
									type:"el-checkbox",
									name:"fixed"
								}]
							}
						}
					});

				}
			},
			//配置表格列
			rightClick(event){
				if(event.target.innerText=='操作'){
					let operator=JSON.parse(JSON.stringify(this.configs.table.operator));
					this.$emit('toConfig',event,this.configs.table,{
						operator:{
							type:"elementAddDelete",
							name:"operator",
							label:"操作项",	
							labelPositionTop:true,					
							labels:[{label:'按钮'}],
							itemDefault:{
								style:"width:60%;display:inline-block;margin-right:2%;text-align:center;",
								size:"mini",
								delScript:`this.$delete(this.$parent.$children[1].config.options[0],'options');`						
							},
							options:operator.map((item,index,arr)=>{
								item.script=`
									this.$set(this.attrs['button'].options[0],'options',option.attr);
									this.$set(this.attrs['button'].options[0],'name',String(option.index));
								`;
								return item;
							})
						},
						button:{
							type:"elementComponents",
							name:"operator",
							label:"按钮设置",
							labelPositionTop:true,
							itemDefault:{
								size:"small"
							},
							options:[{
								type:"elementComponents",
								name:"0",
								itemDefault:{
									size:"small"
								},							
								options:[]
							}]
						}
					});
				}else{
					let vue=this,
						defaultCheckedKeys=[];
					this.configs.table.header.column.map((item,index,arr)=>{ 
						item.fixed=Boolean(item.fixed);
						item.resizable=Boolean(item.resizable);
						item.sortable=Boolean(item.sortable);
						item.showOverflowTooltip=Boolean(item.showOverflowTooltip);
						defaultCheckedKeys.push(item.value);
						return item;
					});
					//表头配置
					this.$emit('toConfig',event,this.configs.table.header,{
						column:{
							type:"elementTree",
							name:"column",
							labelWidth:'0px',	
							expandOnClickNode:false,
							itemDefault:{
								size:"small"
							},
							delShow:(node,data)=>(!data.children || data.children && !data.children.length) && !data.value,
							add:function(event,config,form){
								config.data.push({
									label:"点击设置",
									align:"center",
									resizable:true	
								});
								form.column.push({
									label:"点击设置",
									align:"center",
									resizable:true	
								})
							},
							defaultCheckedKeys:defaultCheckedKeys,
							checkChange:function(vue,data, checked, indeterminate){
								vue.$set(data,'hide',!checked);
							},
							allowDrop:(draggingNode, dropNode, type)=>dropNode.data.value&&type=='inner'?false:true,
							draggable:true,
							showCheckbox:true,
							data:Object.assign([],this.configs.table.header.column),
							labelDefault:{
								style:"padding: 4px;"
							},
							labels:[{
								label:"拖动"
							},{
								label:"排序"
							},{
								label:"固定"
							}],
							item:{
								type:"elementComponents",
								name:"data",
								wrapper:"span",
								itemDefault:{
									style:"margin-left:10px;"
								},
								options:[{
									type:"el-checkbox",
									name:"resizable"
								},{
									type:"el-checkbox",
									name:"sortable"
								},{
									type:"el-checkbox",
									name:"fixed"
								}]
							}
						}
					});

				}
			},					
			/*	删除组件
				@key index
				@type 组件类型
			*/
            del(key,type){
                this.$delete(this.configs[type],key);
                this.$emit('del')   
			},
			//工具框点击
			toolsClick(event){
				this.$emit('fieldsClick',event);
			},
	
		}
	}
</script>
<style>
	.el-table td, .el-table th{
		padding:6.5px 0px;
	}
	.multisearch-condition{
		display: flex;
		justify-content: flex-start;
		align-items: center;
	}
	.multisearch-field-condition{
		display: flex;
		justify-content: flex-start;
		align-items: center;
		margin-right:10px;
		flex-flow:column;
	}
	.multisearch-condition>.el-form-item{
		margin-right:10px;
		margin-bottom:5px;
	}
	.el-form>.multisearch-condition{
		margin:15px 0px;	
	}
	.el-dialog{
		min-width:700px;	
	}	
	.dialog-footer{
		position: relative;
	}	
</style>