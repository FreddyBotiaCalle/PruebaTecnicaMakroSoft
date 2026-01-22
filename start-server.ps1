#!/usr/bin/env pwsh
# Script para iniciar el servidor de desarrollo de Symfony

$projectPath = "c:\Users\Daniel Calle\Documents\PruebaTecnicaMakrosoft\PruebaTecnicaMakroSoft"
Set-Location $projectPath

Write-Host "Iniciando servidor de desarrollo..." -ForegroundColor Green
php -S localhost:8000 -t public
