
var cateIds = [];
function getCateId(cateId) {
    
    $("tbody tr[fid="+cateId+"]").each(function(index, el) {
        id = $(el).attr('cate-id');
        cateIds.push(id);
        getCateId(id);
    });
}


/***
 * 弹出层
 * 参数解释：
 * url     请求的url
 * title   标题
 * closebtn 关闭按钮
 * maxmin  最大最小按钮
 * w  弹出层宽度（缺省调默认值）
 * h  弹出层高度（缺省调默认值）
 * border   边框
 * bgclose  背景点击关闭
 */
  function openShow(u,title,w,h,border,bgclose){
    if(u=="" || u==null){
      error("参数错误");
      return false;
    }
      $.get(u,'',function(res){
        if(res.code > 0) {
              error(res.msg);
            }else{
              if(title=="" || title==null ){
                title=false;
              }

              if(border=="" || border==null){
                border=false;}else{border=false;
              }

              if (w == null || w == '') {
                w=($(window).width() * 0.9);
              };
              if (h == null || h == '') {
                h=($(window).height() - 50);
              };
              if(bgclose=="" || bgclose==null){
                bgclose=false;
              }
              if(border==false){
                skin="";
              }else{
                skin='layui-layer-rim';
              }
              layer.open({
                type: 2,
                title:title,
                shadeClose: bgclose,
                shade: 0.4,
                area: [w+'px', h +'px'],
                skin: skin, //加上边框
                content: u
              });
        }
    });
    }
//加载效果
function loads(n){
    if(n==null || n==""){n=1;}
    if(n==1){
      $(".mbg").removeClass("none");
    }else if(n==2){
      $(".mbg").addClass("none")
    }
  }

//自定义异步 请求地址、传递参数、回调函数
function sajax(u,d,callback,dtype,showloding){
    if(dtype==""||dtype==null){dtype="json";}
    if(showloding=="no"){showloding=false;}else{showloding=true;}
      $.ajax({
          type: "POST",
          cache: false,
          dataType:dtype,
          url:u,
          data:d,
          dataType:"json",
          success: function(redata){
              callback(redata);
          },
          beforeSend:function(XMLHttpRequest){
            if(showloding){
              loads(1);
            }
          },
          complete:function(XMLHttpRequest,textStatus){
            if(showloding){
              loads(2);
            }
          },
          error:function(data){
            console.log(data)
              error('请求失败，请稍后再试');
          }
      });
  }

//确认操作提示
function conf(info,callback,callback2){
    if(info==null || info=="") info='确定这样操作？';
    layer.confirm(info, {
    btn: ['确定','取消'],title:["请确认操作"]
  }, function(index){
    layer.close(index);
      if(callback){
        callback();
      }
  }, function(index){
    layer.close(index);
      if(callback2){
        callback2();
      }
  });
  }

/*关闭弹出框口*/
function admin_close(){
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}


//错误提示，无确认按钮
function error(content,callback){
  if(content==""||content==null){content="出错啦～";}
  show_error_success_no(content,callback,2);
}
//成功提示,无确认按钮
function success(content,callback){
  if(content==""||content==null){content="操作成功";}
  show_error_success_no(content,callback,1);
}
//成功-错误信息无按钮
function show_error_success_no(content,callback,icon){
    layer.msg(content,{
        shade:.5,
        icon: icon,
        time:1500
    },function(index){
        layer.close(index);
        if(callback){
            callback();
        }
    });
}

//成功-错误信息
function show_error_success_yes(content,callback,icon){
  layer.alert(content,{
      shade:.5,
      icon: icon,
      shift: 2
  },function(index){
      layer.close(index);
      if(callback){
          callback();
      }
  });
}

//错误提示，带确认按钮
function error_yes(content,callback){
  if(content==""||content==null){content="出错啦～";}
  show_error_success_yes(content,callback,2);
}
//成功提示,带确认
function success_yes(content,callback){
  if(content==""||content==null){content="操作成功";}
  show_error_success_yes(content,callback,1);
}


 function getJump(url) {
   if (url == "" || url == null) {
     error("参数错误");
     return false;
   }
   $.get(url,'',function(res){
        if(res.code >0){
          error(res.msg);
        }else{
           location.href = url;
        }
       
   })
 }

