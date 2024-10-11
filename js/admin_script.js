document.addEventListener('DOMContentLoaded', function() {
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a[data-section]');
    const contentSections = document.querySelectorAll('.content-section');

    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetSection = this.getAttribute('data-section');
            
            contentSections.forEach(section => {
                section.classList.remove('active');
            });
            
            document.getElementById(targetSection).classList.add('active');
            
            sidebarLinks.forEach(link => {
                link.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    // Toggle dropdown menus
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            this.nextElementSibling.classList.toggle('show');
        });
    });

    // AJAX form submission
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const url = this.id === 'department-form' ? 'add_department.php' :
                        this.id === 'leave-type-form' ? 'add_leave_type.php' :
                        this.id === 'employee-form' ? 'add_employee.php' : '';

            if (!url) return;

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message); // You might want to replace this with a more user-friendly notification
                if (data.status === 'success') {
                    this.reset(); // Reset form on success
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
});