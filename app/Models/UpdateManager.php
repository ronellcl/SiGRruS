<?php
namespace App\Models;

class UpdateManager extends Model {
    
    private $repo = 'onell86/SiGRruS'; // Repositorio por defecto
    private $githubApiUrl = 'https://api.github.com/repos/';

    public function checkUpdates() {
        $url = $this->githubApiUrl . $this->repo . '/releases/latest';
        $response = $this->httpRequest($url);
        
        if (!$response) return null;
        
        $data = \json_decode($response, true);
        if (!isset($data['tag_name'])) return null;
        
        $latest = \ltrim($data['tag_name'], 'v');
        $current = \SIGRRUS_VERSION;
        
        return [
            'current' => $current,
            'latest' => $latest,
            'has_update' => \version_compare($latest, $current, '>'),
            'zip_url' => $data['zipball_url'],
            'body' => $data['body'] ?? ''
        ];
    }

    public function downloadUpdate($url) {
        $tempDir = \dirname(__DIR__, 2) . '/public/backups/tmp';
        if (!\is_dir($tempDir)) \mkdir($tempDir, 0777, true);
        
        $tempFile = $tempDir . '/update.zip';
        $content = $this->httpRequest($url);
        
        if (!$content) return false;
        
        \file_put_contents($tempFile, $content);
        return $tempFile;
    }

    private function httpRequest($url) {
        // Opción 1: cURL
        if (\function_exists('curl_init')) {
            $ch = \curl_init();
            \curl_setopt($ch, CURLOPT_URL, $url);
            \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            \curl_setopt($ch, CURLOPT_USERAGENT, 'SiGRruS-Updater');
            \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = \curl_exec($ch);
            return $response;
        } 
        
        // Opción 2: file_get_contents (si allow_url_fopen está activo)
        if (\ini_get('allow_url_fopen')) {
            $options = [
                'http' => [
                    'method' => "GET",
                    'header' => "User-Agent: SiGRruS-Updater\r\n"
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ];
            $context = \stream_context_create($options);
            return @\file_get_contents($url, false, $context);
        }

        return null;
    }

    public function installUpdate($zipPath) {
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) === TRUE) {
            $extractPath = \dirname(__DIR__, 2) . '/public/backups/tmp/extracted';
            if (!\is_dir($extractPath)) \mkdir($extractPath, 0777, true);
            
            $zip->extractTo($extractPath);
            $zip->close();
            
            // GitHub zips have a root folder like repo-name-hash/
            $folders = glob($extractPath . '/*', GLOB_ONLYDIR);
            if (empty($folders)) return false;
            
            $sourceDir = $folders[0];
            $targetDir = dirname(__DIR__, 2);
            
            $this->copyRecursive($sourceDir, $targetDir);
            
            // Limpiar temporales
            $this->deleteRecursive($extractPath);
            unlink($zipPath);
            
            return true;
        }
        return false;
    }

    private function copyRecursive($source, $target) {
        $dir = opendir($source);
        @mkdir($target);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($source . '/' . $file)) {
                    // No sobreescribir carpetas de datos
                    if ($file === 'uploads' || $file === 'backups') continue;
                    
                    $this->copyRecursive($source . '/' . $file, $target . '/' . $file);
                } else {
                    copy($source . '/' . $file, $target . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function deleteRecursive($dir) {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteRecursive("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}
