const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const content = document.getElementById('content');
const navItems = document.querySelectorAll('.nav-item');

// Toggle sidebar
sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    sidebarToggle.classList.toggle('collapsed');
    content.classList.toggle('expanded');
});

// Handle navigation item clicks
navItems.forEach(item => {
    item.addEventListener('click', () => {
        // Remove active class from all items
        navItems.forEach(navItem => {
            navItem.classList.remove('active');
        });
        
        // Add active class to clicked item
        item.classList.add('active');
        
        // For mobile view, close the sidebar after selection
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
            sidebarToggle.classList.add('collapsed');
            content.classList.add('expanded');
        }
    });
}
);
// Handle window resize to adjust sidebar state
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        sidebar.classList.remove('collapsed');
        sidebarToggle.classList.remove('collapsed');
        content.classList.remove('expanded');
    } else {
        sidebar.classList.add('collapsed');
        sidebarToggle.classList.add('collapsed');
        content.classList.add('expanded');
    }
}
);      
// Initial state check
if (window.innerWidth <= 768) {
    sidebar.classList.add('collapsed');
    sidebarToggle.classList.add('collapsed');
    content.classList.add('expanded');
} else {
    sidebar.classList.remove('collapsed');
    sidebarToggle.classList.remove('collapsed');
    content.classList.remove('expanded');
}   
