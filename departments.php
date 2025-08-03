<h1>Manajemen Departemen</h1>

<table border="1" id="departments-table">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<br>

<button onclick="showAddForm('Departemen')">Tambah Departemen</button>

<div id="form-container" style="display:none;">
    <h3 id="form-title"></h3>
    <form id="add-edit-form">
        <input type="hidden" id="edit-id" name="id">
        <input type="hidden" id="table-name" name="table" value="departments">
        <div id="form-fields"></div>
        <button type="submit">Simpan</button>
        <button type="button" onclick="hideForm()">Batal</button>
    </form>
</div>
