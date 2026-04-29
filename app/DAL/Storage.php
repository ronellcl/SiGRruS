<?php
namespace App\DAL;

/**
 * Clase para abstraer el almacenamiento de archivos (Local / CDN)
 */
class Storage {
    private $driver;
    private $basePath;

    public function __construct() {
        $this->driver = defined('STORAGE_DRIVER') ? STORAGE_DRIVER : 'local';
        $this->basePath = BASE_PATH . '/public/uploads';
        
        if ($this->driver === 'local' && !is_dir($this->basePath)) {
            mkdir($this->basePath, 0755, true);
        }
    }

    /**
     * Guarda un archivo subido
     * @param array $fileInfo Arreglo de $_FILES['input_name']
     * @param string $folder Carpeta destino (ej: 'actas', 'fichas')
     * @return string|false Ruta o URL del archivo guardado
     */
    public function save($fileInfo, $folder = '') {
        if ($this->driver === 'local') {
            $fileName = uniqid() . '_' . basename($fileInfo['name']);
            $targetDir = $this->basePath . '/' . $folder;
            
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $targetPath = $targetDir . '/' . $fileName;
            if (move_uploaded_file($fileInfo['tmp_name'], $targetPath)) {
                // Devolver ruta relativa para acceso web
                return '/uploads/' . $folder . '/' . $fileName;
            }
        }
        // TODO: Implementar lógica de CDN en el futuro si $driver == 'cdn'
        return false;
    }
}
