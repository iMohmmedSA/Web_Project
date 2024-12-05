var todoList = [];

const params = new URLSearchParams(window.location.search);
if (params.get('fake') === 'true') {
    fakeList();
}

function onChangeNewToDoInput() {
    const todoInputValue = document.querySelector('#newTodoInput').value;
    const tools = document.querySelector('#tools');
    if (todoInputValue) {
        tools.style.display = 'inline-block';
        tools.style.marginBottom = '10px';
    } else if (todoInputValue === '') {
        tools.style.display = 'none';
    }
}

function openModal(id) {
    const modal = document.querySelector(`#${id}`);
    modal.style.display = 'block';
}

function closeModal(id) {
    const modal = document.querySelector(`#${id}`);
    modal.style.display = 'none';
}

function editList(id, title, icon) {
    console.log(id, title, icon);
    openModal('editListModal');
    document.querySelector('#editListTitle').value = title;
    document.querySelector('#editListIcon').value = icon;
    document.querySelector('#editListID').value = id;
    document.querySelector('#deleteList').href = `/home/deleteList.php?id=${id}`;
}

function editItem(id, listId, title, description, check, dueDate, dueTime, priority, isParent) {
    if (title) document.querySelector('#editItemTitle').value = title;
    if (description) document.querySelector('#editItemDescription').value = description;
    // if (check !== undefined) document.querySelector('#editItemCheck').checked = check; // didn't implement this yet
    if (dueDate) document.querySelector('#editItemDueDate').value = dueDate;
    if (dueTime) document.querySelector('#editItemDueTime').value = dueTime;
    if (priority) document.querySelector('#editItemPriority').value = priority;
    if (id) document.querySelector('#editItemID').value = id;
    if (listId) document.querySelector('#editItemListID').value = listId;
    document.querySelector('#deleteItem').href = `/home/deleteItem.php?id=${id}`;

    if (isParent) {
        splitTask();

        fetch(`/home/getItems.php?parent=${id}`, {
            method: 'GET'
        })
        .then((response) => {
            if (response.ok) {
                response.text().then((content) => {
                    document.querySelector('#itemsContainer').innerHTML = content;
                    document.querySelector('#itemsContainer').innerHTML += `<hr>`;
                });
            }
            throw new Error('Network response was not ok');
        })
        .catch(() => {
            document.querySelector('#itemsContainer').innerHTML = 'Failed to load items';
        })
        return;
    } else {
        document.querySelector('#itemsContainer').innerHTML = '';
    }
    openModal('editItemModal');
}

document.querySelector('#splitTask').addEventListener('click', splitTask);

function splitTask(e) {
    if (e) e.preventDefault();
    closeModal('editItemModal');
    let id = document.querySelector('#editItemID').value;
    let listId = document.querySelector('#editItemListID').value;
    let title = document.querySelector('#editItemTitle').value;
    let description = document.querySelector('#editItemDescription').value;
    
    document.querySelector('#splitTitle').textContent = title;
    document.querySelector('#splitDescription').textContent = description;
    document.querySelector('#splitItemID').value = id;
    document.querySelector('#splitListID').value = listId;

    openModal('splitTaskModal');
}

function openNewListModal() {
    const modal = document.querySelector('#newListModal');
    modal.style.display = 'block';
}

function closeNewListModel() {
    const modal = document.querySelector('#newListModal');
    modal.style.display = 'none';
}

function updateToDoList() {
    const todoListContainer = document.querySelector('#todoListContainer');
    todoListContainer.innerHTML = '';

    todoList.forEach((list, listIndex) => {
        const listContainer = document.createElement('div');
        const todoContainer = document.createElement('div');

        const completedRatio = list.todos.filter((todo) => todo.checked).length / list.todos.length;

        list.todos.forEach((todo, todoIndex) => {
            const todoElement = document.createElement('div');
            let check = '';
            if (!todo.checked) {
                let dateExist = todo.dueDate ? true : false;
                let timeExist = todo.dueTime ? true : false;
                let priorityExist = todo.priority ? true : false;
                check = `
                <div style="display: flex;">
                `;
                if (dateExist || timeExist) {
                    check += `
                        <div class="container" style="display: flex; align-items: center;">
                            <img src="/asset/icons/calendar.svg" width="15px">
                            <span style="font-size:13px">${todo.dueDate} ${todo.dueTime}</span>
                        </div>
                    `
                }

                if (priorityExist && todo.priority !== 'none') {
                    check += `
                        <div class="container" style="display: flex; align-items: center;">
                            <img src="/asset/icons/high-priority.svg" width="15px">
                            <span style="font-size:13px">${todo.priority}</span>
                        </div>
                    `
                }

                check += `
                </div>
                `
            }

            todoElement.innerHTML = `
                <div style="opacity: ${todo.checked ? 0.3: 1};">
                    <div style="display: flex; justify-content: space-between;" onclick="checkItem(${listIndex}, ${todoIndex})">
                        <div class="todoContainerItem">
                            <h4>${todo.title}</h4>
                            <p>${todo.description}</p>
                        </div>
                        <input type="checkbox" ${todo.checked ? 'checked' : ''}>
                    </div>

                    ${check}

                </div>
                <div style="border-top: 1px dotted gray; margin-top: 12px"><hr style="visibility: hidden;"></div>
            `;
            todoContainer.appendChild(todoElement);
        });

        listContainer.innerHTML = `
            <div class="listContainer container">
                <div class="listBar">
                    <img src="${list.icon}" width="30px">
                    <h4>${list.title}</h4>
                </div>
                <hr>
                ${todoContainer.innerHTML}

                <div class="progressBar">
                    <div class="progress" style="width: ${completedRatio * 100}%;"></div>
                </div>
                
            </div>
        `;
        todoListContainer.appendChild(listContainer);
    });
}

function checkItem(listIndex, todoIndex) {
    todoList[listIndex]['todos'][todoIndex]['checked'] = !todoList[listIndex]['todos'][todoIndex]['checked'];
    updateToDoList();
    updateUpcoming();
}

function toggleItem(itemID) {
    const item = document.querySelector(`#${itemID}`);
    const parent = item.parentElement.parentElement;
    parent.style.opacity = item.checked ? 0.3 : 1;
    const id = itemID.split('-')[1];

    if (item.checked) {
        parent.children[1].style.display = 'none';
    } else {
        parent.children[1].style.display = 'flex';
    }

    updateTheProgress(parent.parentElement);

    fetch(`/home/toggleItem.php?id=${id}&checked=${item.checked}`, {
        method: 'GET'
    })
    .then((response) => {
        if (response.ok) {
            return;
        }
        throw new Error('Network response was not ok');
    })
    .catch(() => {
        item.checked = !item.checked;
        parent.style.opacity = item.checked ? 0.3 : 1;
        if (item.checked) {
            parent.children[1].style.display = 'none';
        } else {
            parent.children[1].style.display = 'flex';
        }
    });
}

function toggleSubItem(itemID) { 
    const item = document.querySelector(`#${itemID}`);
    let parent = item.parentElement;
    parent.style.opacity = item.checked ? 0.3 : 1;

    fetch(`/home/toggleItem.php?id=${itemID.split('-')[1]}&checked=${item.checked}`, {
        method: 'GET'
    })
    .then((response) => {
        if (response.ok) {
            return;
        }
        throw new Error('Network response was not ok');
    })
    .catch(() => {
        item.checked = !item.checked;
        parent.style.opacity = item.checked ? 0.3 : 1;
    });
}

function assignTask(taskID) {
    const assign = document.querySelector(`#assign-${taskID}`).value;
    fetch(`/home/assignTask.php?id=${taskID}&assign=${assign}`, {
        method: 'GET'
    })
    .then((response) => {
        if (response.ok) {
            return;
        }
        throw new Error('Network response was not ok');
    })
    .catch(() => {
        document.querySelector(`#assign-${taskID}`).value = '';
    });
}

function updateTheProgress(parentObject) {
    const progress = parentObject.querySelector('.progress');
    const children = Array.from(parentObject.children).filter((child) => child.className === 'todoItemCheck');
    const checkedCount = children.filter((child) => {
        return child.querySelector('input[type="checkbox"]').checked;
    }).length;

    const completedRatio = checkedCount / children.length;
    progress.style.width = `${completedRatio * 100}%`;
}

function updateUpcoming() {
    const upcomingContainer = document.querySelector('#upcomingContainer');
    upcomingContainer.innerHTML = '';

    // Upcoming todos have less than 7 days to due date
    let upcomingTodos = [];
    todoList.forEach((list) => {
        list.todos.forEach((todo) => {
            if (todo.checked) return;
            let remainingDays = (new Date(todo.dueDate) - new Date()) / (1000 * 60 * 60 * 24);
            if (remainingDays <= 7) {
                upcomingTodos.push({
                    icon: list.icon,
                    listTitle: list.title,
                    remainingDays: remainingDays,
                    ...todo,
                });
            }
        });
    });

    upcomingTodos.sort((a, b) => {
        return new Date(a.dueDate) - new Date(b.dueDate);
    });

    upcomingTodos.forEach((todo) => {
        const todoElement = document.createElement('div');
        todoElement.innerHTML = `
            <div class="listContainer container">
                <div class="listBar">
                    <img src="${todo.icon}" width="30px">
                    <div>
                        <h4>${todo.title}</h4>
                        <h5 style="color:gray">${todo.listTitle}</h5>
                    </div>
                </div>
                <p style="margin:0px; margin-top: 10px; color:gray">${todo.description}</p>
                <p style="margin:0px; margin-top: 10px; color:red; font-size: 12px;">${Math.round(todo.remainingDays)} days remaining</p>
            </div>
        `;
        upcomingContainer.appendChild(todoElement);
    });
}

function fakeList() {
    todoList = [
        {
            title: 'Personal',
            icon: `/asset/icons/personalcard.svg`,
            todos: [
                {
                    title: 'Todo 1',
                    description: 'Description for Todo 1',
                    dueDate: '2024-12-01',
                    dueTime: '12:00',
                    priority: 'High',
                    category: 'personal',
                    checked: false
                },
                {
                    title: 'Todo 2',
                    description: 'Description for Todo 2',
                    dueDate: '2024-12-10',
                    dueTime: '18:00',
                    priority: 'Medium',
                    category: 'personal',
                    checked: true
                },
                {
                    title: 'Todo 3',
                    description: 'Description for Todo 3',
                    dueDate: '2024-11-21',
                    dueTime: '18:00',
                    priority: 'Low',
                    category: 'personal',
                    checked: false
                }
            ]
        },
        {
            title: 'Work',
            icon: `/asset/icons/work.svg`,
            todos: [
                {
                    title: 'Todo 1',
                    description: 'Description for Todo 1',
                    dueDate: '2024-12-01',
                    dueTime: '12:00',
                    priority: 'High',
                    category: 'work',
                    checked: false
                },
                {
                    title: 'Todo 2',
                    description: 'Description for Todo 2',
                    dueDate: '2024-12-10',
                    dueTime: '18:00',
                    priority: 'Medium',
                    category: 'work',
                    checked: false
                }
            ]   
        }
    ];
    updateToDoList();
    updateUpcoming();
}

function selectIcon() {
    const selectedicon = document.querySelector('#newListIcon');
    const iconContainer = document.querySelector('#iconContainer');
    const icon = document.querySelector('#listiconModel');
    iconContainer.style.display = 'block';
    icon.src = selectedicon.value;
}

document.querySelector('#newListModalContent').addEventListener('submit', function (e) {
    const selectedicon = document.querySelector('#newListIcon');
    
    if (selectedicon.value === 'none') {
        alert('Please select an icon');
        e.preventDefault();
    }
});