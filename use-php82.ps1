# PowerShell wrapper for PHP 8.2.28
# Usage: .\use-php82.ps1 then use 'php' commands normally

$env:PATH = "C:\laragon\bin\php\php-8.2.28-nts-Win32-vs16-x64;$env:PATH"
php -v
Write-Host "✓ PHP 8.2.28 is now active in this terminal session" -ForegroundColor Green
