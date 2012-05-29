	// 前台管理的添加分类
function addCategory(name) {
	if(name=="") {
		ui.error("请输入分类名");
		return ;
	}

    var a = $( "li[id]:first" ).clone();
    $.post( U('task/Index/addCategory'),{name:name},function( txt ){
    	if(-1 == txt) {
    		ui.error( '添加失败' );
        }else if(-2 == txt) {
            ui.error( "分类名已存在" );
        }else if(-3 == txt) {
        	ui.error( '添加失败' );
        }else{
            txt = $.trim(txt);
            var html = "";
            html += '<li class="lineD_btm" style="margin:0px;" id ="cate'+txt+'">';
            html += '<div class="left" style="width: 40%;">';
            html += '<input name="name['+txt+']" style="width:200px;" type="text" class="text" onBlur="this.className=\'text\'" onFocus="this.className=\'text2\'" value="'+name+'"/>';
            html += '</div>';
            html += '<div id="c'+txt+'" class="left" style="width: 50%;">0</div>';
            html += '<div class="left" style="width: 9%;"><a href="javascript:deleteCategory('+txt+')">[移除]</a></div>';
            html += '</li>';

            $( "li[id]:last" ).after( html );
            $( '#insertCategory' ).val( "" );
        }
    });
}


function photo_size(name){
    $(name +" img").each(function(){
        var width = 500;
        var height = 500;
        var image = $(this);
        image.addClass('hand');
        image.bind('click',function(){
            window.open(image.attr('src'),"图片显示",'width='+image.width()+',height='+image.height());
        });
        if (image.width() > image.height()){
            if(image.width()>width){
                image.width(width);
                image.height(width/image.width()*image.height());
            }
        }else{
            if(image.height()>height){
                image.height(height);
                image.width(height/image.height()*image.width());
            }
        }
    });
}

$(function(){
    photo_size('#task_con');
});

function deleteCategoryTask( toCate,formCate ){
    $.post( U('task/Index/deleteCategory'),{
        id:formCate,
        toCate:toCate
    },function( txt ){
        if( -1 != txt ){
            if( toCate != null ){
                var c1 = $( '#c'+toCate ).text();
                var c2 = $( '#c'+formCate ).text();
                $( '#c'+toCate ).html(parseInt(c1)+parseInt(c2) );
            }
            $( '#cate'+formCate ).remove();
        }else{
            ui.error( "删除分类失败" );
        }
        ui.box.close();
    })
}


function deleteCategory( id ){
    var count = $( '#c'+id ).text();
    if( count > 0 ){
    	 ui.box.load(U("task/iIndex/deleteCateFrame",["id="+id,"count="+count] ),{title:'转移分类',closeable:true});
       return;
    }
    if( confirm("是否确定删除" ))
	{
		$.post(U('task/Index/deleteCategory'),{id:id},function( txt ){
			if( -1 != txt ){
				$( '#cate'+id ).remove();
			}else{
				ui.error( "删除分类失败" );
			}
		});
	}
}

function deleteTask( url ){
	if(confirm("是否确定删除这条任务")) {
		location.href=url;
	}
	return ;
}

function deleteNotice( url ){
	if(confirm("是否确定删除这条任务通告")) {
		location.href=url;
	}
	return ;
}

function deleteNote( url ){
	if(confirm("是否确定删除这条任务便签")) {
		location.href=url;
	}
	return ;
}

function deleteCommentCount( appid ){
    //计数
    $.post(U('task/Index/deleteSuccess'),{
        id:appid
    },function(result){
        $('#commentCount').text(result);
    });
}
function commentSuccess(json){
    //计数
    $.post(U('task/Index/commentSuccess'),{
        data:json
    },function(result){
        $('#commentCount').text(result);
    });
}

function toggleDropdown(el) {
	div = $(el).next(".dropdown-menu");
	div.toggle();
}

function toggleMode(showMode,hideMode) {
	$("#"+showMode).show();
	$("#"+hideMode).hide();
	return false;
}

function toggleTab(el,showId) {
	$(el).parent().siblings().removeClass("active");
	$(el).parent().addClass("active");
	$("#"+showId).siblings().hide();
	$("#"+showId).show();
	return false;
}

function toggleFieldset(el) {
	fieldset = $(el).parents("fieldset");
	fieldset.toggleClass("collapsed");
	div= fieldset.children("div");
	div.toggle();
}

function scrollTo(el) {
	$("html,body").animate({scrollTop:$(el).offset().top-parseInt($(".header_holder").css("height"))},200);
}

function addDetailRow(el,masterId,containerId) {
	container = $("#"+containerId);
	rowEditor = "<tr>";
	tds = "<td><a href='javascript:void();' title='确认' onclick='submitAddDetailRow(this);'><i class='icon-ok'></i></a>&nbsp;<a href='javascript:void();' title='取消' onclick=\"removeDetailRow(this,\'detail_table\');\"><i class='icon-remove'></i></a>";
	tds = tds + "<input type='hidden' id='task_id_detail_input' name='task_id' value='";
	tds = tds +masterId+"'>";
	tds = tds + "</td>";
	tds = tds + "<td><select name='behavior_type' id='behavior_type_detail_input' class='detail_select'>";
	tds = tds + task_behaviorType_list;
	tds = tds + "</select></td>";
	tds = tds + "<td><input type='text' name='start_date' value='' id='start_date_detail_input' class='date-pick detail_input'></td>";
	tds = tds + "<td><input type='text' name='start_time' id='start_time_detail_input' value='' class='detail_input'></td>";
	tds = tds + "<td><input type='text' name='stop_date' value='' id='stop_date_detail_input' class='date-pick detail_input'></td>";
	tds = tds + "<td><input type='text' name='stop_time' id='stop_time_detail_input' value='' class='detail_input'></td>";
	tds = tds + "<td><input type='text' name='spent_time' id='spent_time_detail_input' value='' class='detail_input'></td>";
	tds = tds + "<td><input type='text' name='done_ratio' id='done_ratio_detail_input' value='' class='progress-input input-append edit'><span class='add-on'>%</span></td>";
	tds = tds + "<td><input type='text' name='description' id='description_detail_input' value='' class='detail_input'></td>";
	rowEditor = rowEditor + tds + "</tr>";
	container.append(rowEditor);
}

function submitAddDetailRow(el) {
	data = "";
	data = data + "task_id="+$('#task_id_detail_input').val() + "&";
	data = data + "behavior_type="+$('#behavior_type_detail_input').val() + "&";
	data = data + "start_date="+$('#start_date_detail_input').val() + "&";
	data = data + "start_time="+$('#start_time_detail_input').val() + "&";
	data = data + "stop_date="+$('#stop_date_detail_input').val() + "&";
	data = data + "stop_time="+$('#stop_time_detail_input').val() + "&";
	data = data + "spent_time="+$('#spent_time_detail_input').val() + "&";
	data = data + "done_ratio="+$('#done_ratio_detail_input').val() + "&";
	data = data + "description="+$('#description_detail_input').val() + "&";
	data = data + "task_id="+$('#task_id_detail_input').val() + "&";
	data = data+ "";
	$.post(U('task/Index/doAddDetail'),data,function(data){
		ui.success("添加成功");
		location.reload();
	});
	return false;
}

function submitRemoveDetailRow(detailId){
	if (confirm("确认删除吗？")) {
		data = "id="+detailId;
		$.post(U('task/Index/doRemoveDetail'),data,function(data){
			ui.success("删除成功");
			location.reload();
		});
	};
	return false;
}

function submitUpdateDetailRow(el) {
	data = "";
	data = data + "task_id="+$('#task_id_detail_input').val() + "&";
	data = data + "behavior_type="+$('#behavior_type_detail_input').val() + "&";
	data = data + "start_date="+$('#start_date_detail_input').val() + "&";
	data = data + "start_time="+$('#start_time_detail_input').val() + "&";
	data = data + "stop_date="+$('#stop_date_detail_input').val() + "&";
	data = data + "stop_time="+$('#stop_time_detail_input').val() + "&";
	data = data + "spent_time="+$('#spent_time_detail_input').val() + "&";
	data = data + "done_ratio="+$('#done_ratio_detail_input').val() + "&";
	data = data + "description="+$('#description_detail_input').val() + "&";
	data = data + "task_id="+$('#task_id_detail_input').val() + "&";
	data = data+ "";
	$.post("{:U('task/Index/doUpdateDetail')",data,function(data){
		ui.success("修改成功");
		location.reload();
	});
	return false;
}


function updateDetailRow(el,containerId) {
	container = $("#"+containerId);
	rowEditor = "<tr>";
	tds = "";
	tds = tds + "<td><input type='text' name='task_id' value='' class='child_input'></td>";
	tds = tds + "<td><input type='text' name='behavior_type' value='' class='child_input'></td>";
	tds = tds + "<td><input type='text' name='start_date' value='' id='start_date_detail_input' class='child_input'><i class='icon-calendar' id='start_date_detail_trigger'></i></td>";
	tds = tds + "<td><input type='text' name='start_time' value='' class='child_input'></td>";
	tds = tds + "<td><input type='text' name='stop_date' value='' id='stop_date_detail_input' class='child_input'><i class='icon-calendar' id='stop_date_detail_trigger'></i></td>";
	tds = tds + "<td><input type='text' name='stop_time' value='' class='child_input'></td>";
	tds = tds + "<td><input type='text' name='spent_time' value='' class='child_input'></td>";
	tds = tds + "<td><input type='text' name='done_ratio' value='' class='child_input'></td>";
	tds = tds + "<td><input type='text' name='description' value='' class='child_input'></td>";
	rowEditor = rowEditor + tds + "</tr>";
	container.append(rowEditor);
}

function removeDetailRow(el,containerId) {
	container = $("#"+containerId);
	container.find("tbody tr").last().remove();
}

function addChildRow(id) {
	location.href = U('task/Index/addTask');
}
function removeChildRow(id) {
	$.post(U('task/Index/doRemoveChild'),{id:id},function(data){
		alert(data);
		ui.success("删除成功");
		location.reload();
	});
}

function addUser() {

}
function removeUser() {

}
function addChildTask(id) {
	location.href= U('task/Index/addTask');
}
function doAddChildTask(id) {

}
function doRemoveChildTask(id) {
	$(id).hide();
}

function loadKEditor(id,config) {
	var editor;
	items = [
		'source','|','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
		'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
		'insertunorderedlist', '|', 'emoticons', 'image', 'link'];
	KindEditor.ready(function(K) {
		editor = K.create('#'+id, {
			resizeType : 1,
			allowPreviewEmoticons : false,
			allowImageUpload : false,
			fullscreenShortcut : false,
			items : items
		});
	});
}

function loadSimpleKEditor(id,config) {
	var editor;
	items = ['selectall'];
	KindEditor.ready(function(K) {
		editor = K.create('#'+id, {
			resizeType : 1,
			allowPreviewEmoticons : false,
			allowImageUpload : false,
			fullscreenShortcut : false,
			items : items
		});
	});
}
