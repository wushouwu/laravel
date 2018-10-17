export var my={
    //请求数据
    axios: option=>{
        option.axiosOption=Object.assign(option.axiosOption,{
            method: 'post'
        })
        option = Object.assign(option,{ 
            errorMsg: '请求失败!'
        });  
        let load=option.vue.$loading({
            target:option.vue.$el,
            background: 'rgba(0,0,0,0)'

        });
        axios(option.axiosOption).then((response)=>{
            load.close();
            if('msg' in response.data){
                option.vue.$message({
                    showClose: true,
                    message: response.data.msg
                });
                if(typeof(option.error)=='function'){
                    option.error(response,option);
                }
            }else{
                if(typeof(option.success)=='function'){
                    option.success(response,option);
                }
                if('successMsg' in option){
                    option.vue.$message({
                        showClose: true,
                        message: option.successMsg,
                        type: 'success'
                    });
                }
                     
            }
        }).catch((error)=>{
            load.close();
            option.vue.$message({
                showClose: true,
                message: option.errorMsg+error,
                type: 'error',
                duration: 0
            });
            if(error.message.indexOf(401)>=0 || error.message.indexOf(419)>=0 || error.message.indexOf(302)>=0){
                location.href='login';
            }
        });
    },
    //请求数据
    axiosAll: option=>{
        option = Object.assign(option,{ 
            errorMsg: '请求失败!'
        });  
        let load=option.vue.$loading({
            target:option.vue.$el,
            background: 'rgba(0,0,0,0)'

        });
        let req=[];
        for(var i in option.axiosOption){
            req.push(axios(Object.assign(option.axiosOption[i],{
                method: 'post'
            })));
        }
        axios.all(req).then(axios.spread(function(...res){
            load.close();
            option.success(res,option);
        })).catch((error)=>{
            load.close();
            option.vue.$message({
                showClose: true,
                message: option.errorMsg+error,
                type: 'error',
                duration: 0
            });
            if(error.message.indexOf(401)>=0 || error.message.indexOf(419)>=0 || error.message.indexOf(302)>=0){
                location.href='login';
            }
        });
    }    
}