<?php

session_start();
if ( !( isset($_SESSION["token"]) || empty($_SESSION["token"]) ) ) {
    header("Location: /login/index.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/home/style.css">
</head>

<body>
    <h1 style="margin: 0px; margin: 18px 0 0 25px;">To-Do</h1>
    <div class="toggleUIModeContainer container" onclick="toggleUIMode()">
        <img width="25px" class="toggleUIMode" src="/asset/half-moon.svg" alt="UI toggle">
    </div>

    <div id="content">
        <form id="newToDo" onsubmit="addNewToDo(); return false">
            <input type="text" id="newTodoInput" class="user-input" placeholder="Create a new item"
                oninput="onChangeNewToDoInput()">

            <div id="tools" style="display:inline-block;"></form>
        </div>

        <h2 style="margin-top: 0;">Upcoming Deadlines</h2>
        <div id="upcomingContainer" style="display: flex;"></div>

        <div style="display: flex; align-items: center">
            <h2 style="display: inline; margin: 0;">List</h2>
            <button class="container" style="padding: 5px; margin: 10px;" onclick="openNewListModal()"><span
                    style="font-size: 18px;">+</strong>
            </button>
        </div>

        <div id="todoListContainer"></div>
    </div>

    <!-- Modal -->
    <div id="newListModal" class="modal" onclick="closeNewListModel(event)">
        <div class="modal-content">
            <div id="newListModalContent" class="container" onclick="event.stopPropagation()">
                <input type="text" name="" id="newListName" class="textfield" placeholder="List Title">
                <button class="btn" onclick="addNewToDoList()">Submit</button>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal -->
    <div id="newTodoModal" class="modal" onclick="closeNewToDoModel(event)">
        <div class="modal-content">
            <div id="newToDoModalContent" class="container" onclick="event.stopPropagation()">
                <textarea id="newTodoDescription" class="textfield" placeholder="Description"></textarea>
                <button class="btn" onclick="addNewToDo()">Submit</button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="editListModal" class="modal" onclick="closeEditToDoModel(event)">
        <div class="modal-content">
            <div id="editListModalContent" class="container" onclick="event.stopPropagation()">
                <input type="text" name="" id="editListName" class="textfield" placeholder="List Title">
                <button class="btn" onclick="editToDoList()">Submit</button>
            </div>
        </div>
    </div>

    <script src="/script.js"></script>
    <script src="./script.js"></script>
</body>

</html>