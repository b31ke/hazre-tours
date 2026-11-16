# Pravi paket za server od zadnjeg commita (samo ono što je sačuvano u gitu, bez vendor/ i bez slika).
#   powershell -ExecutionPolicy Bypass -File deploy\napravi-paket.ps1
# Rezultat: deploy\paket\hazre-paket.zip

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root
New-Item -ItemType Directory -Force -Path 'deploy\paket' | Out-Null
$out = 'deploy\paket\hazre-paket.zip'
git archive --format=zip -o $out HEAD app public deploy composer.json composer.lock .htaccess
$commit = git log -1 --format='%h %s'
Write-Host "Paket: $out"
Write-Host "Verzija: $commit"
