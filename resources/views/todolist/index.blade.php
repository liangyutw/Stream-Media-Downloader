<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Todo List</title>

</head>
<body onload="startTime()">

    <div id="todo_area">

        <div style="width=50%; float:left;padding:0 10px 0 10px;">
            <h2>Todo List</h2>
            <input type="text" v-model="newTodoText" v-on:keyup.enter="addThis"><button @click="addThis">Add</button>

            <ul style="list-style-type:none;padding:0px;">
                <li
                  v-for="(todo, index) in todos"
                  v-bind:title="todo.title"
                >
                <input type=checkbox @click="getCheck($event, index)">
                <span id="list_item" >@{{todo.title}} - <span style="font-size: 10pt;color: gray; font-family: verdana;">@{{todo.date}}</span></span>
                <button id="edit_btn" @click="editThis(index)">Edit</button>
                <button id="del_btn" v-on:click="goDel(index)" title="delete">X</button>

                </li>
            </ul>

        </div>


        <div id="done_area" style="width=50%; float:left;padding:0 10px 0 10px;">
            <h2>Done List</h2>
            <ul style="list-style-type:none;padding:0px;">
                <li v-for="done_row in done_list">
                  @{{done_row.title}} - <span style="font-size: 10pt;color: gray; font-family: verdana;">@{{done_row.date}}</span>
                </li>
            </ul>
        </div>


    </div>

</body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.min.js"></script>
<script src="/js/todolist_script.js"></script>
