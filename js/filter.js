function submitFilterForm() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams();

    for (const [key, value] of formData.entries()) {
        if (value !== '' && value !== null) {
            params.append(key, value);
        }
    }

    window.location.href = 'search.php?' + params.toString();
}

document.getElementById('categorySelect').addEventListener('change', function() {
    submitFilterForm();
});