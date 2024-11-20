var todoList = [];

const params = new URLSearchParams(window.location.search);
if (params.get('fake') === 'true') {
    fakeList();
}

function onChangeNewToDoInput() {
    const todoInputValue = document.querySelector('#newTodoInput').value;
    const tools = document.querySelector('#tools');
    if (todoInputValue) {

        let options = ``;

        todoList.forEach((list) => {
            options += `<option value="${list.title}">${list.title}</option>`;
        });

        tools.innerHTML = `
                    <input type="date" class="container" min="${new Date().toISOString().split('T')[0]}">
                    <input type="time" class="container">
                    <select class="container">
                        <option value="none" selected disabled hidden>Select Priority</option>
                        <option value="none">None</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <select class="container">
                        <option value="none" selected disabled hidden>Select Category</option>
                        <option value="none">None</option>
                        ${options}
                    </select>
                    <button class="container" onclick="openNewTodoModal(); return false;">Add With Decription</button>
                    <button class="container">Add</button>
                `;
        tools.style = 'display:inline-block; margin-bottom: 10px;';
    } else if (todoInputValue === '') {
        tools.innerHTML = '';
        tools.style = 'display:inline-block;';
    }
}

function addNewToDo () {
    const todoInput = document.querySelector('#newTodoInput');
    const todoInputValue = todoInput.value;
    const dateInput = document.querySelector('input[type="date"]');
    const timeInput = document.querySelector('input[type="time"]');
    const priorityInput = document.querySelectorAll('select')[0];
    const categoryInput = document.querySelectorAll('select')[1];
    const descriptionInput = document.querySelector('#newTodoDescription');

    let category = categoryInput.value === 'none' ? 'Default' : categoryInput.value;
    if (!todoList.find((list) => list.title === category)) {
        todoList.push({
            title: category,
            icon: `/asset/icons/high-priority.svg`,
            todos: []
        });
    }

    if (todoInputValue) {
        const listIndex = todoList.findIndex((list) => list.title === category);
        todoList[listIndex].todos.push({
            title: todoInputValue,
            description: descriptionInput.value || '',
            dueDate: dateInput.value,
            dueTime: timeInput.value,
            priority: priorityInput.value,
            category: category,
            checked: false
        });
        updateToDoList();
        updateUpcoming();
        todoInput.value = '';
        descriptionInput.value = '';
        onChangeNewToDoInput();
        closeNewToDoModel();
    }    

    return false;
}

function openNewTodoModal() {
    const modal = document.querySelector('#newTodoModal');
    modal.style.display = 'block';
}

function closeNewToDoModel() {
    const modal = document.querySelector('#newTodoModal');
    modal.style.display = 'none';
}

function openNewListModal() {
    const modal = document.querySelector('#newListModal');
    modal.style.display = 'block';
}

function closeNewListModel() {
    const modal = document.querySelector('#newListModal');
    modal.style.display = 'none';
}

function addNewToDoList() {
    let listInput = document.querySelector('#newListName'); 
    if (listInput.value) {
        todoList.push({
            title: listInput.value,
            icon: `/asset/icons/high-priority.svg`,
            todos: []
        });
        closeNewListModel();
        updateToDoList();
        updateUpcoming();
        onChangeNewToDoInput();
        listInput.value = '';    
    }
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