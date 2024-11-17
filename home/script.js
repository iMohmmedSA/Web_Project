


function onChangeNewToDoInput() {
    const todoInputValue = document.querySelector('#newTodoInput').value;
    const tools = document.querySelector('#tools');
    if (todoInputValue) {
        tools.innerHTML = `
                    <input type="date" class="container" min="${new Date().toISOString().split('T')[0]}">
                    <input type="time" class="container">
                    <select class="container">
                        <option value="none" selected disabled hidden>Select Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <select class="container">
                        <option value="none" selected disabled hidden>Select Category</option>
                        <option value="personal">Personal</option>
                        <option value="work">Work</option>
                        <option value="school">School</option>
                    </select>
                    <button class="container">Add</button>
                `;
    } else if (todoInputValue === '') {
        tools.innerHTML = '';
    }
}