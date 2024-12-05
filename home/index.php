<?php
include("../db/db.php");

session_start();
if (!(isset($_SESSION["token"]) || empty($_SESSION["token"]))) {
    header("Location: /login/index.php");
    exit();
}


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION["token"]) || empty($_SESSION["token"])) {
    header("Location: /login/index.php");
    exit();
}
$token = $_SESSION["token"];

$userSql = "SELECT id FROM users WHERE token = '$token'";
$userResult = mysqli_query($conn, $userSql);

if ($userResult->num_rows !== 1) {
    header("Location: /login/index.php");
    exit();
}

$userRow = mysqli_fetch_assoc($userResult);
$userId = $userRow['id'];
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
        <form id="newToDo" method="POST" action="newItem.php">
            <input type="text" name="title" id="newTodoInput" class="user-input" maxlength="32"
                placeholder="Create a new item" oninput="onChangeNewToDoInput()">

            <div id="tools" style="display:none;">
                <input type="date" name="date" class="container" min="<?php echo date('Y-m-d'); ?>">
                <input type="time" name="time" class="container">
                <select class="container" name="priority">
                    <option value="none" selected disabled hidden>Select Priority</option>
                    <option value="none">None</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
                <select class="container" name="list_id">
                    <option value="none" selected disabled hidden>Select List</option>
                    <option value="none">None</option>
                    <?php
                    $listSql = "SELECT id, title FROM todo_lists WHERE user_id = '$userId'";
                    $listResult = mysqli_query($conn, $listSql);

                    if ($listResult->num_rows > 0) {
                        while ($list = mysqli_fetch_assoc($listResult)) {
                            echo '<option value="' . $list['id'] . '">' . $list['title'] . '</option>';
                        }
                    } else {
                        echo "<option disabled>No lists available.</option>";
                    }
                    ?>
                </select>
                <button class="container">Add</button>
        </form>
    </div>

    <h2 style="margin-top: 0;">Upcoming Deadlines</h2>
    <div id="upcomingContainer" style="display: flex; overflow-x: auto; scrollbar-width: none; ">
        <?php
        $todoListsSql = "SELECT todo_lists.id AS list_id, 
                                todo_lists.title AS list_title, 
                                todo_lists.icon_uri AS icon, 
                                items.title AS item_title, 
                                items.description, 
                                DATEDIFF(items.date, CURDATE()) AS remaining_days
                                FROM todo_lists
                                LEFT JOIN items ON todo_lists.id = items.list_id
                                WHERE todo_lists.user_id = '$userId' 
                                AND items.date IS NOT NULL 
                                AND items.checked = FALSE
                                HAVING remaining_days <= 7 AND remaining_days >= 0";


        $todoListsResult = mysqli_query($conn, $todoListsSql);

        if ($todoListsResult->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($todoListsResult)) {
                $icon = $row['icon'];
                $listTitle = $row['list_title'];
                $title = $row['item_title'];
                $description = $row['description'];
                $remainingDays = $row['remaining_days'];

                echo '
                <div class="listContainer container">
                    <div class="listBar">
                        <img src="' . $icon . '" width="30px">
                        <div>
                            <h4>' . $title . '</h4>
                            <h5 style="color:gray">' . $listTitle . '</h5>
                        </div>
                    </div>
                    <p style="margin:0px; margin-top: 10px; color:gray">' . $description . '</p>
                    <p style="margin:0px; margin-top: 10px; color:red; font-size: 12px;">' . round($remainingDays) . ' days remaining</p>
                </div>';
            }
        } else {
            echo '<p>Nothing in the next 7 days.</p>';
        }
        ?>
    </div>

    <div style="display: flex; align-items: center">
        <h2 style="display: inline; margin: 0;">List</h2>
        <button class="container" style="padding: 5px; margin: 10px;" onclick="openNewListModal()"><span
                style="font-size: 18px;">+</strong>
        </button>
    </div>

    <div id="todoListContainer">
        <?php
        $assignedSql = "SELECT 
                            i.*,
                            tl.title AS list_title,
                            u.username AS assigned_to
                        FROM items AS i
                        JOIN todo_lists AS tl ON i.list_id = tl.id
                        LEFT JOIN users AS u ON i.assign_id = u.id
                        WHERE i.assign_id = '$userId'";

        $assignedResult = mysqli_query($conn, $assignedSql);

        if ($assignedResult->num_rows > 0) {
            $assignedItem = "";
            while ($row = mysqli_fetch_assoc($assignedResult)) {
                $metadataHtml = '
                <div style="display: ' . (!$row['checked'] ? "flex" : "none") . '">
                ';

                if (!empty($row['date']) || !empty($row['time'])) {
                    $metadataHtml .= '
                        <div class="container" style="display: flex; align-items: center;">
                            <img src="/asset/icons/calendar.svg" width="15px">
                            <span style="font-size:13px">' . ($row['date'] ?? '') . ' ' . ($row['time']) . '</span>
                        </div>';
                }

                if (!empty($row['priority']) && $row['priority'] !== 'none') {
                    $metadataHtml .= '
                        <div class="container" style="display: flex; align-items: center;">
                            <img src="/asset/icons/high-priority.svg" width="15px">
                            <span style="font-size:13px">' . $row['priority'] . '</span>
                        </div>';
                }

                $metadataHtml .= '
                </div>
                ';

                $metadataHtml .= '
                <div class="todoItemCheck" style="opacity: ' . ($row['checked'] ? '0.3' : '1') . ';">
                    <div style="display: flex; justify-content: space-between;">
                        <div class="todoContainerItem">
                            <h4 class="listItemTitle" 
                            onclick="editItem(' . $row['id'] . ')">' . $row['title'] . '</h4>
                            <p>' . $row['description'] . '</p>
                        </div> 
                            <input type="checkbox" 
                               id="checkbox-' . $row['id'] . '" ' .
                                'onchange="toggleItem(\'checkbox-' . $row['id'] . '\')" ' .
                                ($row['checked'] ? 'checked' : "") . '>
    
                        
                    </div>
                    ' . $metadataHtml . '
                </div>
                <div style="border-top: 1px dotted gray; margin-top: 12px"><hr style="visibility: hidden;"></div>';
            }

            echo '
            <div class="listContainer container">
                <div class="listBar">
                    <img src="/asset/icons/high-priority.svg" width="30px">
                    <h4>Assign to you</h4>
                </div>
                <hr>
                ' . $metadataHtml . '
            </div>';
        }
        ?>




        <?php
        $todoSql = "SELECT * FROM todo_lists WHERE user_id = '$userId'";
        $todoResult = mysqli_query($conn, $todoSql);

        if ($todoResult->num_rows !== 0) {
            while ($todoRow = mysqli_fetch_assoc($todoResult)) {
                $listId = $todoRow['id'];

                $itemsSql = "SELECT 
                                    i.*,
                                    (SELECT COUNT(*) FROM items AS subitems WHERE subitems.parent_id = i.id) > 0 AS is_parent
                                    FROM items AS i WHERE i.list_id = '$listId' AND i.parent_id IS NULL";
                $itemsResult = mysqli_query($conn, $itemsSql);

                $todoItemsHtml = '';

                while ($todoRowItem = mysqli_fetch_assoc($itemsResult)) {
                    $metadataHtml = '';
                    $parent = $todoRowItem["is_parent"] > 0;

                    if ($parent) {
                        $totalWeightsResult = mysqli_query($conn, 
                        "SELECT SUM(weight) as total_weight 
                         FROM items 
                         WHERE parent_id = '{$todoRowItem['id']}'"
                        );

                        $totalWeights = mysqli_fetch_assoc($totalWeightsResult)['total_weight'];
                        $completedWeightsResult = mysqli_query($conn, 
                        "SELECT SUM(weight) as completed_weight 
                         FROM items 
                         WHERE parent_id = '{$todoRowItem['id']}' AND checked = TRUE"
                        );
                        $completedWeights = mysqli_fetch_assoc($completedWeightsResult)['completed_weight'];
                        $completionPercentage = ($totalWeights > 0) ? ($completedWeights / $totalWeights) * 100 : 0;
                    }


                    $metadataHtml = '
                    <div style="display: ' . (!$todoRowItem['checked'] ? "flex" : "none") . '">
                    ';

                    if (!empty($todoRowItem['date']) || !empty($todoRowItem['time'])) {
                        $metadataHtml .= '
                            <div class="container" style="display: flex; align-items: center;">
                                <img src="/asset/icons/calendar.svg" width="15px">
                                <span style="font-size:13px">' . ($todoRowItem['date'] ?? '') . ' ' . ($todoRowItem['time']) . '</span>
                            </div>';
                    }

                    if (!empty($todoRowItem['priority']) && $todoRowItem['priority'] !== 'none') {
                        $metadataHtml .= '
                            <div class="container" style="display: flex; align-items: center;">
                                <img src="/asset/icons/high-priority.svg" width="15px">
                                <span style="font-size:13px">' . $todoRowItem['priority'] . '</span>
                            </div>';
                    }

                    $metadataHtml .= '
                        </div>
                    ';

                    $todoItemsHtml .= '
                        <div class="todoItemCheck" style="opacity: ' . ($todoRowItem['checked'] ? '0.3' : '1') . ';">
                            <div style="display: flex; justify-content: space-between;">
                                <div class="todoContainerItem">
                                    <h4 class="listItemTitle" onclick="editItem(' . $todoRowItem['id'] . ', ' . $listId . ', \'' . $todoRowItem['title'] . '\', \'' . $todoRowItem['description'] . '\', ' . ($todoRowItem['checked'] ? 'true' : 'false') . ', \'' . $todoRowItem['date'] . '\', \'' . $todoRowItem['time'] . '\', \'' . $todoRowItem['priority'] . '\', ' . ($parent ? "true" : "false") . ' )">' . $todoRowItem['title'] . '</h4>
                                    <p>' . $todoRowItem['description'] . '</p>
                                </div>' . ($parent? 
                                    $completionPercentage . '%'
                                :'
                                <input type="checkbox" 
                                       id="checkbox-' . $todoRowItem['id'] . '" ' .
                                        'onchange="toggleItem(\'checkbox-' . $todoRowItem['id'] . '\')" ' .
                                        ($todoRowItem['checked'] ? 'checked' : "") . '>
                                ') . '

                                
                            </div>
                            ' . $metadataHtml . '
                        </div>
                        <div style="border-top: 1px dotted gray; margin-top: 12px"><hr style="visibility: hidden;"></div>';
                }

                $totalItems = $itemsResult->num_rows;
                $completedItems = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as completed FROM items WHERE list_id = '$listId' AND checked = TRUE AND parent_id IS NULL"))['completed'];
                $completedRatio = ($totalItems > 0) ? ($completedItems / $totalItems) : 0;

                echo '
                    <div class="listContainer container">
                        <div class="listBar">
                            <img src="' . $todoRow['icon_uri'] . '" width="30px">
                            <h4 class="listItemTitle" onclick="editList(' . $todoRow['id'] . ',\'' . $todoRow['title'] . '\',\'' . $todoRow['icon_uri'] . '\')">' . $todoRow['title'] . '</h4>
                        </div>
                        <hr>
                        ' . $todoItemsHtml . '
                        <div class="progressBar">
                            <div class="progress" style="width: ' . ($completedRatio * 100) . '%;"></div>
                        </div>
                    </div>';
            }
        }
        ?>
    </div>
    </div>

    <!-- Modal -->
    <div id="newListModal" class="modal" onclick="closeNewListModel(event)">
        <div class="modal-content">
            <div class="container" id="iconContainer" style="width: 30px; display: none"
                onclick="event.stopPropagation()">
                <img style="padding:0px; margin:0px" id="listiconModel" src="/" width="30px">
            </div>
            <form id="newListModalContent" class="container" onclick="event.stopPropagation()" method="POST"
                action="createList.php">
                <input type="text" name="title" id="newListName" class="textfield" placeholder="List Title"
                    maxlength="32" required>
                <select class="textfield" name="icon_uri" id="newListIcon" onchange="selectIcon()" required>
                    <option value="none" disabled selected hidden>Select icon</option>
                    <option value="/asset/icons/default.svg">Default</option>
                    <option value="/asset/icons/calendar.svg">Calendar</option>
                    <option value="/asset/icons/high-priority.svg">Important</option>
                    <option value="/asset/icons/personalcard.svg">Personal</option>
                    <option value="/asset/icons/work.svg">Work</option>
                </select>
                <button class="btn">Submit</button>
            </form>
        </div>
    </div>
    </div>

    <!-- Modal -->
    <div id="editListModal" class="modal" onclick="closeModal('editListModal')">
        <div class="modal-content">
            <div class="container" id="iconContainer" style="width: 30px; display: none"
                onclick="event.stopPropagation()">
                <img style="padding:0px; margin:0px" id="listiconModel" src="/" width="30px">
            </div>
            <form id="newListModalContent" class="container" onclick="event.stopPropagation()" method="POST" action="updatelist.php">
                <input type="text" name="title" id="editListTitle" class="textfield" placeholder="List Title"
                    maxlength="32" required>
                <select class="textfield" name="icon_uri" id="editListIcon" onchange="selectIcon()" required>
                    <option value="none" disabled selected hidden>Select icon</option>
                    <option value="/asset/icons/default.svg">Default</option>
                    <option value="/asset/icons/calendar.svg">Calendar</option>
                    <option value="/asset/icons/high-priority.svg">Important</option>
                    <option value="/asset/icons/personalcard.svg">Personal</option>
                    <option value="/asset/icons/work.svg">Work</option>
                </select>
                <input type="hidden" name="list_id" id="editListID">
                <div style="display:flex">
                <a id="deleteList" class="btn" style="width: 20%; margin-right: 5px; text-align: center;">Delete</a>
                <button class="btn">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
     <div id="editItemModal" class="modal" onclick="closeModal('editItemModal')">
        <div class="modal-content">
            <form id="newListModalContent" class="container" onclick="event.stopPropagation()" method="POST" action="updateItem.php">
                <input type="text" name="title" id="editItemTitle" class="textfield" placeholder="Item Title" maxlength="32" required>
                <input type="text" name="description" id="editItemDescription" class="textfield" placeholder="Item Description" maxlength="128">
                <input type="date" name="date" id="editItemDueDate" class="textfield">
                <input type="time" name="time" id="editItemDueTime" class="textfield">
                <select class="textfield" name="priority" id="editItemPriority">
                    <option value="none" selected disabled hidden>Select Priority</option>
                    <option value="none">None</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
                <input type="hidden" name="item_id" id="editItemID">
                <input type="hidden" name="list_id" id="editItemListID">
                <div style="display:flex">
                <a id="deleteItem" class="btn" style="width: 20%; margin-right: 5px; text-align: center;">Delete</a>
                <button id="splitTask" class="btn" style="width: 35%; margin-right: 5px; text-align: center;">
                    Split Task</button>
                <button class="btn">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Split Task -->

    <div id="splitTaskModal" class="modal" onclick="closeModal('splitTaskModal')">
        <div class="modal-content">
            <div id="newListModalContent" class="container" onclick="event.stopPropagation()" method="POST" action="splitItem.php">
                <div>
                    <h3 id="splitTitle"></h3>
                    <h5 id="splitDescription" style="color:gray; margin-top:0px"></h5>
                </div>
                <hr>

                <div id="itemsContainer"></div>

                <form style="display: flex;" method="POST" action="addSubTask.php">
                    <input type="text" name="title" id="splitItemTitle" class="textfield" placeholder="Item Title" maxlength="32" required>
                    <input type="number" value="1" min="1" name="weight" id="splitItemTitle" class="textfield" style="width: 20%; margin-left: 10px;" placeholder="Weight" maxlength="32" required>
                    <input type="hidden" name="item_id" id="splitItemID">
                    <input type="hidden" name="list_id" id="splitListID">
                    <button class="textfield" style="width: 40px; margin-left: 10px;"><strong id="splitAdd">+</strong></button>
                </form>
            </div>
        </div>
    </div>
    <script src="/script.js"></script>
    <script src="./script.js"></script>
</body>

</html>