$rootDir = "c:\xampp\htdocs\CampusCanteen"

# Update files in root directory
$rootFiles = Get-ChildItem -Path $rootDir -File -Include *.php
foreach ($file in $rootFiles) {
    $content = Get-Content $file.FullName -Raw
    $content = $content -replace 'href="menu\.php"', 'href="menu/index.php"'
    $content = $content -replace 'href="pesanan\.php"', 'href="pesanan/index.php"'
    $content = $content -replace 'action="tambah_pesanan\.php"', 'action="pesanan/tambah.php"'
    Set-Content $file.FullName $content
}

# Update files in subdirectories (menu, pesanan)
$subDirs = @("$rootDir\menu", "$rootDir\pesanan")
foreach ($dir in $subDirs) {
    $subFiles = Get-ChildItem -Path $dir -File -Include *.php
    foreach ($file in $subFiles) {
        $content = Get-Content $file.FullName -Raw
        
        # Assets & Config
        $content = $content -replace 'assets/css/style\.css', '../assets/css/style.css'
        $content = $content -replace 'assets/js/script\.js', '../assets/js/script.js'
        $content = $content -replace 'include ''config/koneksi\.php'';', 'include ''../config/koneksi.php'';'
        
        # Navigation
        $content = $content -replace 'href="index\.php"', 'href="../index.php"'
        $content = $content -replace 'href="menu\.php"', 'href="../menu/index.php"'
        $content = $content -replace 'href="pesanan\.php"', 'href="../pesanan/index.php"'
        $content = $content -replace 'href="about\.php"', 'href="../about.php"'
        
        # Specific intra-folder links
        $content = $content -replace 'href="tambah_menu\.php"', 'href="tambah.php"'
        $content = $content -replace 'href="edit_menu\.php', 'href="edit.php'
        $content = $content -replace 'href="hapus_menu\.php', 'href="hapus.php'
        
        $content = $content -replace 'href="tambah_pesanan\.php"', 'href="tambah.php"'
        $content = $content -replace 'href="edit_pesanan\.php', 'href="edit.php'
        $content = $content -replace 'href="hapus_pesanan\.php', 'href="hapus.php'
        
        # Redirects
        $content = $content -replace 'Location: menu\.php', 'Location: index.php'
        $content = $content -replace 'Location: pesanan\.php', 'Location: index.php'
        
        # Cross folder links (e.g., menu -> pesanan/tambah)
        if ($dir -like '*\menu') {
            $content = $content -replace 'action="tambah_pesanan\.php"', 'action="../pesanan/tambah.php"'
        }
        
        Set-Content $file.FullName $content
    }
}
