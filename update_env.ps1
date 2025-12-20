$path = ".env"
$content = Get-Content $path -Raw
$content = $content -replace "DB_CONNECTION=sqlite", "DB_CONNECTION=mysql"
$content = $content -replace "# DB_HOST=127.0.0.1", "DB_HOST=127.0.0.1"
$content = $content -replace "# DB_PORT=3306", "DB_PORT=3306"
$content = $content -replace "# DB_DATABASE=laravel", "DB_DATABASE=tarumt_fbs"
$content = $content -replace "# DB_USERNAME=root", "DB_USERNAME=root"
$content = $content -replace "# DB_PASSWORD=", "DB_PASSWORD="
Set-Content $path $content
