<?php
namespace App\Controllers;

use App\Models\Media;

class MediaFileController extends BaseController
{
    protected $mediaModel;

    public function __construct()
    {
        $this->startSession(); 
        $this->mediaModel = new Media();
    }

    public function showMediaFiles() {
        $mediaFiles = $this->mediaModel->getMediaFiles();

        $data = [
            'message' => $_SESSION['msg'] ?? null,
            'msg_type' => $_SESSION['msg_type'] ?? null,
            'media_files' => $mediaFiles, 
        ];

        // Clear session messages
        unset($_SESSION['msg'], $_SESSION['msg_type']);

        // Render the Mustache template
        echo $this->renderPage('media-files', $data);
    }

    public function addMediaFile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
            $file = $_FILES['photo'];
    
            if ($file['error'] == UPLOAD_ERR_OK) {
                $targetDir = __DIR__ . "/../../views/uploads/products/";
                $targetFile = $targetDir . basename($file["name"]);
                $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
                if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                        $this->mediaModel->addMedia($file["name"], $fileType);
                        $_SESSION['msg'] = "File uploaded successfully!";
                        $_SESSION['msg_type'] = "success";

                    } else {
                        $_SESSION['msg'] = "Failed to upload file.";
                        $_SESSION['msg_type'] = "danger";
                    }
                } else {
                    $_SESSION['msg'] = "Only image files are allowed.";
                    $_SESSION['msg_type'] = "danger";
                }
            } else {
                $_SESSION['msg'] = "No file uploaded";
                $_SESSION['msg_type'] = "danger";
            }
        }
        $this->redirect('/media-files');
    }

    public function deleteMediaFile() {
        if (isset($_GET['id'])) {
            $mediaId = $_GET['id'];
            
            $media = $this->mediaModel->getMediaById($mediaId);
            
            if ($media) {
                $fileName = $media['file_name'];
                $filePath = __DIR__ . "/../../views/uploads/products/" . $fileName;
                if (file_exists($filePath)) {
                    unlink($filePath);
                    $this->mediaModel->deleteMedia($mediaId);
                    $_SESSION['msg'] = "File deleted successfully!";
                    $_SESSION['msg_type'] = "success";

                } else {
                    $_SESSION['msg'] = "File does not exist.";
                    $_SESSION['msg_type'] = "danger";
                }
            } else {
                $_SESSION['msg'] = "File not found.";
                $_SESSION['msg_type'] = "danger";
            }
        }
        $this->redirect('/media-files');
    }

}
