<h1>Manajemen Staff</h1>

<table border="1" id="staff-table">
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIP</th>
            <th>Unit Kerja</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<br>

<button onclick="showAddForm('Staff')">Tambah Staff</button>

<div id="form-container" style="display:none;">
    <h3 id="form-title"></h3>
    <form id="add-edit-form">
        <input type="hidden" id="edit-id" name="id">
        <input type="hidden" id="table-name" name="table" value="staff">
        <div id="form-fields"></div>
        <button type="submit">Simpan</button>
        <button type="button" onclick="hideForm()">Batal</button>
    </form>
</div>
