<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FileUpload extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function upload()
    {
        helper(['form', 'url']);

        $input = $this->validate([
            'file' => [
                'uploaded[file]',
                'mime_in[file,image/jpg,image/jpeg,image/png,image/webp]',
                'max_size[file,1024]',
            ]
        ]);

        if (!$input) {
            return $this->response->setJSON(['error' => 'Choose a valid file.']);
        }

        try {
            $img = $this->request->getFile('file');

            // Initialize Firebase Storage
            $serviceAccount = ServiceAccount::fromJsonFile(APPPATH . 'FirebaseCredentials.json');
            $firebase = (new Factory)
                ->withServiceAccount($serviceAccount)
                ->create();
            $storage = $firebase->getStorage();

            // Upload the original image to Firebase Storage
            $originalImagePath = WRITEPATH . 'uploads/' . $img->getName();
            $img->move(WRITEPATH . 'uploads');
            $imageData = file_get_contents($originalImagePath);
            $storage->getBucket()->upload($imageData, [
                'name' => 'images/' . $img->getName()
            ]);

            // Resize the image using CodeIgniter's Image Manipulation library
            $image = \Config\Services::image()
                ->withFile($originalImagePath)
                ->resize(800, 800, true, 'auto')
                ->save(WRITEPATH . 'uploads/thumb_' . $img->getName());

            // Upload the resized image to Firebase Storage
            $resizedImagePath = WRITEPATH . 'uploads/thumb_' . $img->getName();
            $resizedImageData = file_get_contents($resizedImagePath);
            $storage->getBucket()->upload($resizedImageData, [
                'name' => 'images/thumb_' . $img->getName()
            ]);

            // Delete the original and resized images from local server
            if (file_exists($originalImagePath)) {
                unlink($originalImagePath);
            }
            if (file_exists($resizedImagePath)) {
                unlink($resizedImagePath);
            }

            return $this->response->setJSON(['success' => 'File has been successfully uploaded and resized.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}
