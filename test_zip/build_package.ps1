$sourceDir = ".\"
$zipFile = "..\SiGRruS_v1.0.zip"
$tempDir = "..\sigrrus_temp_build"

Write-Host "Iniciando empaquetamiento de SiGRruS v1.0..."

if (Test-Path $zipFile) { Remove-Item $zipFile -Force }
if (Test-Path $tempDir) { Remove-Item $tempDir -Recurse -Force }

New-Item -ItemType Directory -Path $tempDir | Out-Null

$exclude = @(".git", ".gemini", "database_schema.sql", "request_debug.log")

Write-Host "Copiando archivos..."
Get-ChildItem -Path $sourceDir -Force | ForEach-Object {
    $skip = $false
    foreach ($ex in $exclude) {
        if ($_.Name -eq $ex) {
            $skip = $true
            break
        }
    }
    if ($_.Name -like "scratch_*" -or $_.Name -like "*.zip") {
        $skip = $true
    }
    
    if (-not $skip) {
        Copy-Item -Path $_.FullName -Destination $tempDir -Recurse -Force
    }
}

# Borrar app\config.php si se copió
if (Test-Path "$tempDir\app\config.php") {
    Remove-Item "$tempDir\app\config.php" -Force
}

Write-Host "Comprimiendo paquete ZIP..."
Get-ChildItem -Path $tempDir -Force | Compress-Archive -DestinationPath $zipFile -Force

Remove-Item $tempDir -Recurse -Force

Write-Host "Empaquetamiento completado con éxito: $zipFile"
