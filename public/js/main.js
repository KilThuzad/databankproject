document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.sidebar .nav-link');
    const toggle = document.querySelector('.navbar-toggler');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const userDropdown = document.querySelector('.user-dropdown');
    
    const backdrop = document.createElement('div');
    backdrop.className = 'sidebar-backdrop';
    document.body.appendChild(backdrop);

    function setActiveLink() {
        const currentPath = window.location.pathname;
        navItems.forEach(item => {
            if (item.getAttribute('href') === currentPath) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    function setupNavHover() {
        navItems.forEach(item => {
            item.addEventListener('mouseover', function() {
                if (window.innerWidth >= 992) {
                    navItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                }
            });
            
            item.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    navItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
    }

    function toggleSidebar() {
        const isMobile = window.innerWidth < 992;
        sidebar.classList.toggle('show');
        mainContent.classList.toggle('shifted');
        
        if (isMobile) {
            backdrop.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        }
        
        if (window.innerWidth >= 992) {
            const isOpen = sidebar.classList.contains('show');
            localStorage.setItem('sidebarOpen', isOpen);
        }
    }

    backdrop.addEventListener('click', function() {
        sidebar.classList.remove('show');
        mainContent.classList.remove('shifted');
        backdrop.classList.remove('show');
        document.body.style.overflow = '';
        
        if (window.innerWidth >= 992) {
            localStorage.setItem('sidebarOpen', false);
        }
    });

    function initSidebarState() {
        if (localStorage.getItem('sidebarOpen') === 'true' && window.innerWidth >= 992) {
            sidebar.classList.add('show');
            mainContent.classList.add('shifted');
        }
    }

    function setupUserDropdown() {
        if (userDropdown) { 
            const dropdownToggle = userDropdown.querySelector('.dropdown-toggle');
            const dropdownMenu = userDropdown.querySelector('.dropdown-menu');

            if (dropdownToggle && dropdownMenu) {
                const bootstrapDropdown = new bootstrap.Dropdown(dropdownToggle);
                
                dropdownToggle.addEventListener('click', function(e) {
                    if (window.innerWidth < 768) {
                        e.preventDefault();
                        const isShown = dropdownMenu.classList.contains('show');
                        
                        if (!isShown) {
                            bootstrapDropdown.show();
                        } else {
                            bootstrapDropdown.hide();
                        }
                    }
                });
                
                document.addEventListener('click', function(e) {
                    if (!userDropdown.contains(e.target) && dropdownMenu.classList.contains('show')) {
                        bootstrapDropdown.hide();
                    }
                });
                
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && dropdownMenu.classList.contains('show')) {
                        bootstrapDropdown.hide();
                    }
                });
            }
        }
    }

    function handleResize() {
        if (window.innerWidth >= 992) {
            backdrop.classList.remove('show');
            document.body.style.overflow = '';
            
            if (window.innerWidth >= 992 && !localStorage.getItem('sidebarOpen') === 'true') {
                sidebar.classList.remove('show');
                mainContent.classList.remove('shifted');
            }
        }
    }

    toggle.addEventListener('click', toggleSidebar);
    window.addEventListener('resize', handleResize);

    setActiveLink();
    setupNavHover();
    initSidebarState();
    setupUserDropdown();
});