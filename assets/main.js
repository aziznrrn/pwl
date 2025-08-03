const tableMappings = {
    'Dosen': 'lecturers',
    'Staff': 'staff',
    'Departemen': 'departments'
};

document.addEventListener('DOMContentLoaded', function() {
    const path = window.location.pathname.split('/').pop();
    let tableName = '';

    if (path === 'lecturers.php') {
        tableName = 'lecturers';
    } else if (path === 'staff.php') {
        tableName = 'staff';
    } else if (path === 'departments.php') {
        tableName = 'departments';
    }

    if (tableName) {
        loadTable(tableName);
    }

    const addEditForm = document.getElementById('add-edit-form');
    if (addEditForm) {
        addEditForm.addEventListener('submit', handleFormSubmit);
    }
});

async function handleFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const tableName = document.getElementById('table-name').value;
    const formData = new FormData(form);
    const data = { table: tableName };
    formData.forEach((value, key) => {
        data[key] = value;
    });

    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            hideForm();
            loadTable(tableName);
        } else {
            const errorData = await response.json();
            alert(`Error: ${errorData.message || 'An unknown error occurred.'}`);
        }
    } catch (error) {
        console.error('An error occurred:', error);
        alert('An error occurred while submitting the form.');
    }
}

async function loadTable(tableName) {
    try {
        const response = await fetch(`api.php?table=${tableName}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        const tableBody = document.querySelector(`#${tableName}-table tbody`);
        if (!tableBody) return;
        tableBody.innerHTML = '';

        for (const row of data) {
            const tr = document.createElement('tr');
            let rowHtml = '';
            const columns = (tableName === 'lecturers') ? ['name', 'nidn', 'department_name'] :
                            (tableName === 'staff') ? ['name', 'nip', 'work_unit'] :
                            ['name'];
            
            columns.forEach(col => {
                rowHtml += `<td>${row[col] || ''}</td>`;
            });

            const entityName = Object.keys(tableMappings).find(key => tableMappings[key] === tableName);
            rowHtml += `<td><button onclick="showEditForm('${entityName}', ${row.id})">Edit</button> <button onclick="deleteItem('${tableName}', ${row.id})">Hapus</button></td>`;
            tr.innerHTML = rowHtml;
            tableBody.appendChild(tr);
        }
    } catch (error) {
        console.error('Could not load table:', error);
    }
}

async function showAddForm(entityName) {
    const tableName = tableMappings[entityName];
    if (!tableName) return;

    document.getElementById('form-title').innerText = `Tambah ${entityName}`;
    document.getElementById('table-name').value = tableName;
    document.getElementById('add-edit-form').reset();
    document.getElementById('edit-id').value = '';
    const formFields = document.getElementById('form-fields');
    formFields.innerHTML = await getFormFields(tableName);

    if (tableName === 'lecturers') {
        await populateDepartmentsDropdown();
    }
    document.getElementById('form-container').style.display = 'block';
}

async function showEditForm(entityName, id) {
    const tableName = tableMappings[entityName];
    if (!tableName) return;

    document.getElementById('form-title').innerText = `Edit ${entityName}`;
    document.getElementById('table-name').value = tableName;
    document.getElementById('add-edit-form').reset();
    document.getElementById('edit-id').value = id;
    const formFields = document.getElementById('form-fields');
    formFields.innerHTML = await getFormFields(tableName);

    if (tableName === 'lecturers') {
        await populateDepartmentsDropdown();
    }

    try {
        const response = await fetch(`api.php?table=${tableName}&id=${id}`);
        const item = await response.json();

        for (const key in item) {
            const element = document.getElementById(key);
            if (element) {
                element.value = item[key];
            }
        }

        document.getElementById('form-container').style.display = 'block';
    } catch (error) {
        console.error('Could not fetch item data:', error);
    }
}

function hideForm() {
    document.getElementById('form-container').style.display = 'none';
}

async function populateDepartmentsDropdown() {
    try {
        const response = await fetch('api.php?table=departments');
        const data = await response.json();
        const select = document.getElementById('department_id');
        if (!select) return;
        select.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = "";
        placeholder.text = "Pilih Departemen";
        placeholder.disabled = true;
        placeholder.selected = true;
        select.appendChild(placeholder);

        for (const dept of data) {
            const option = document.createElement('option');
            option.value = dept.id;
            option.text = dept.name;
            select.appendChild(option);
        }
    } catch (error) {
        console.error('Could not populate departments dropdown:', error);
    }
}

async function getFormFields(tableName) {
    let fields = '<table>';
    switch (tableName) {
        case 'lecturers':
            fields += `
                <tr><td><label for="name">Nama:</label></td><td><input type="text" id="name" name="name" required></td></tr>
                <tr><td><label for="nidn">NIDN:</label></td><td><input type="text" id="nidn" name="nidn" required></td></tr>
                <tr><td><label for="department_id">Departemen:</label></td><td><select id="department_id" name="department_id" required></select></td></tr>
            `;
            break;
        case 'staff':
            fields += `
                <tr><td><label for="name">Nama:</label></td><td><input type="text" id="name" name="name" required></td></tr>
                <tr><td><label for="nip">NIP:</label></td><td><input type="text" id="nip" name="nip" required></td></tr>
                <tr><td><label for="work_unit">Unit Kerja:</label></td><td><input type="text" id="work_unit" name="work_unit"></td></tr>
            `;
            break;
        case 'departments':
            fields += `
                <tr><td><label for="name">Nama:</label></td><td><input type="text" id="name" name="name" required></td></tr>
            `;
            break;
    }
    fields += '</table>';
    return fields;
}

async function deleteItem(tableName, id) {
    if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
        try {
            const response = await fetch('api.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ table: tableName, id: id })
            });

            if (response.ok) {
                loadTable(tableName);
            } else {
                const errorData = await response.json();
                alert(`Error: ${errorData.message || 'An unknown error occurred.'}`);
            }
        } catch (error) {
            console.error('An error occurred during deletion:', error);
            alert('An error occurred during deletion.');
        }
    }
}