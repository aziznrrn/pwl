<?php
declare(strict_types=1);

$html = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Kepegawaian</title>
</head>
<body>
    <nav>
        <a href="#home">Home</a>
	<span> | </span>
        <a href="#lecturers">Dosen</a>
	<span> | </span>
        <a href="#staff">Staff</a>
	<span> | </span>
        <a href="#departments">Departemen</a>
    </nav>
    <hr>
    <div id="content"></div>

    <script src="assets/main.js"></script>
    <script>
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

        async function loadContent(page) {
            try {
                const response = await fetch(page);
                if (!response.ok) {
                    document.getElementById('content').innerHTML = '<h1>Page Not Found</h1>';
                    return;
                }
                const content = await response.text();
                document.getElementById('content').innerHTML = content;

                const tableName = page.replace('.php', '');
                if (tableName !== 'home' && typeof loadTable === 'function') {
                    loadTable(tableName);
                }

                const addEditForm = document.getElementById('add-edit-form');
                if (addEditForm) {
                    addEditForm.addEventListener('submit', handleFormSubmit);
                }
            } catch (error) {
                console.error('Error loading content:', error);
                document.getElementById('content').innerHTML = '<h1>Error loading page</h1>';
            }
        }

        function handleRoutes() {
            const hash = window.location.hash.substring(1) || 'home';
            const validRoutes = ['home', 'lecturers', 'staff', 'departments'];
            const page = validRoutes.includes(hash) ? `${hash}.php` : 'home.php';
            loadContent(page);
        }

        window.addEventListener('DOMContentLoaded', handleRoutes);
        window.addEventListener('hashchange', handleRoutes);
    </script>
</body>
</html>
HTML;

echo $html;