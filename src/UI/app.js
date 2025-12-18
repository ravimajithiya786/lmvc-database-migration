async function loadMigrations() {
    const res = await fetch('api/migrations.php');
    const data = await res.json();

    const select = document.getElementById('migration');
    data.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m;
        opt.text = m;
        select.appendChild(opt);
    });
}

async function save() {
    const name = document.getElementById('migration').value;
    const schema = JSON.parse(document.getElementById('editor').value);

    await fetch('api/migration-save.php', {
        method: 'POST',
        body: JSON.stringify({ name, schema })
    });

    alert('Saved');
}

loadMigrations();
