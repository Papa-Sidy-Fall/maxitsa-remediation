<?php

class FileUpload
{
    private string $uploadPath;
    private array $allowedTypes;
    private int $maxSize;

    public function __construct()
    {
        $this->uploadPath = env('UPLOAD_PATH', 'public/images/uploads/');
        $this->allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        $this->maxSize = 5 * 1024 * 1024; // 5MB
    }

    public function upload(array $file, string $directory = ''): array
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'error' => 'Paramètres de fichier invalides.'];
        }

        // Vérifier les erreurs d'upload
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return ['success' => false, 'error' => 'Aucun fichier envoyé.'];
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['success' => false, 'error' => 'Le fichier dépasse la taille maximale autorisée.'];
            default:
                return ['success' => false, 'error' => 'Erreur inconnue lors de l\'upload.'];
        }

        // Vérifier la taille
        if ($file['size'] > $this->maxSize) {
            return ['success' => false, 'error' => 'Le fichier est trop volumineux.'];
        }

        // Vérifier le type MIME
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $this->allowedTypes)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé.'];
        }

        // Générer un nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        
        // Créer le répertoire de destination
        $destinationDir = $this->uploadPath . $directory;
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $destinationPath = $destinationDir . '/' . $filename;

        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            return ['success' => false, 'error' => 'Erreur lors du déplacement du fichier.'];
        }

        return [
            'success' => true,
            'filename' => $filename,
            'path' => $destinationPath,
            'url' => '/' . $destinationPath
        ];
    }

    public function uploadMultiple(array $files, string $directory = ''): array
    {
        $results = [];
        
        foreach ($files as $key => $file) {
            if (is_array($file['name'])) {
                // Multiple files from same input
                for ($i = 0; $i < count($file['name']); $i++) {
                    $singleFile = [
                        'name' => $file['name'][$i],
                        'type' => $file['type'][$i],
                        'tmp_name' => $file['tmp_name'][$i],
                        'error' => $file['error'][$i],
                        'size' => $file['size'][$i]
                    ];
                    $results[] = $this->upload($singleFile, $directory);
                }
            } else {
                $results[] = $this->upload($file, $directory);
            }
        }
        
        return $results;
    }

    public function delete(string $filepath): bool
    {
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
}
