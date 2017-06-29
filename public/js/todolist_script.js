// Vue.component('todo-item', {
//   template: `
//     <li>
//       {{ title }}
//       <button v-on:click="$emit('remove')">X</button>
//     </li>
//   `,
//   props: ['title']
// })


var dformat = '';

function checkTime(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}

function startTime() {
    var today = new Date();
    var y = today.getFullYear();
    var month = (today.getMonth()+1);
    var d = today.getDate();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    // add a zero in front of numbers<10
    month = checkTime(month);
    m = checkTime(m);
    s = checkTime(s);
    dformat = y + "-" + month + "-" + d + " " + h + ":" + m + ":" + s;
    var t = setTimeout(function(){ startTime() }, 500);
}


var list_data = {
    newTodoText:'',
    checkedNames:[],
    done_list:[],
    todos: [{
        title:'Do the dishes',
        date:''
    }]
}

var item_content = '';

new Vue({
  el:'#todo_area',
  data:list_data,
  methods:{
    addThis:function(){
        list_data.todos.push({
            title:list_data.newTodoText,
            date:dformat
        });
        list_data.newTodoText = ''
    },
    goDel:function(item){
        list_data.todos.splice(item, 1);
    },
    getCheck: function (event, item) {
        if (event.target.checked === true) {
            list_data.done_list.push({
                title:list_data.todos[item].title,
                date:dformat
            });
        list_data.todos.splice(item, 1);
        event.target.checked = false;
        }else{
            list_data.done_list.pop(list_data.todos[item]);
        }
    },
    editThis:function(item){

        item_content = list_data.todos[item].title + " - <span style='font-size: 10pt;color: gray; font-family: verdana;'>" + list_data.todos[item].date + "</span>";

        $.each($("li"), function(k, v){
            if (v.title == list_data.todos[item].title) {
                // console.log($(v).attr('title'));
                $(v).find("#list_item").html("<input type='text' id='upd_todo' value='"+list_data.todos[item].title+"'><input type='button' onclick='goUpd("+item+")' value='Update'><button onclick='goCancel("+item+")' title='Cancel'>Cancel</button>");
                $(v).find("#edit_btn, #del_btn").hide();
            }

        });
    }
  }
});

function goUpd(item) {
    var old_val = list_data.todos[item].title;
    list_data.todos[item].title = $("#upd_todo").val();
    list_data.todos[item].date = dformat;

    $.each($("li"), function(k, v){
        if (v.title == old_val) {
            $(v).find("#list_item").html(list_data.todos[item].title + " - <span style='font-size: 10pt;color: gray; font-family: verdana;'>" + list_data.todos[item].date + "</span>");
            $(v).find("#edit_btn, #del_btn").show();
        }
    });
}

function goCancel(item) {

    var old_val = list_data.todos[item].title;
    var old_content = list_data.todos[item].title + " - <span style='font-size: 10pt;color: gray; font-family: verdana;'>" + list_data.todos[item].date + "</span>";

    $.each($("li"), function(k, v){
        if (v.title == old_val) {
            $(v).find("#list_item").html(old_content);
            $(v).find("#edit_btn, #del_btn").show();
        }
    });
}
