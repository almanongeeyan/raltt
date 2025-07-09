document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active from all
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(tc => tc.style.display = 'none');
            // Activate current
            btn.classList.add('active');
            document.getElementById(btn.getAttribute('data-tab')).style.display = 'flex';
        });
    });
});