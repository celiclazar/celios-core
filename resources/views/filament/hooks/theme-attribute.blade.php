<script>
    const adminTheme = '{{ setting('admin_theme', 'ocean') }}';
    document.documentElement.setAttribute('data-admin-theme', adminTheme);
    if (adminTheme === 'midnight' || adminTheme === 'obsidian') {
        document.documentElement.classList.add('dark');
        document.documentElement.style.colorScheme = 'dark';
        try { localStorage.setItem('theme', 'dark'); } catch (e) {}
    } else if (adminTheme === 'modern-builder') {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        } else {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
            try { localStorage.setItem('theme', 'dark'); } catch (e) {}
        }
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.style.colorScheme = 'light';
        try { localStorage.setItem('theme', 'light'); } catch (e) {}
    }
</script>
