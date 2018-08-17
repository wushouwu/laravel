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
                    message: response.data.msg,
                    type: 'error',
                    duration: 0 
                });
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
        });
    }
}