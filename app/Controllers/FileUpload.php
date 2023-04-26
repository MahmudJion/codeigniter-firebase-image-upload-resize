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
            print_r('Choose a valid file');
        } else {
            $img = $this->request->getFile('file');

            // Initialize Firebase Storage
            $serviceAccount = ServiceAccount::fromJsonFile(APPPATH . 'FirebaseCredentials.json');
            $firebase = (new Factory)
                ->withServiceAccount($serviceAccount)
                ->create();
            $storage = $firebase->getStorage();

            // Upload the original image to Firebase Storage
            $originalImagePath = WRITEPATH . 'uploads/' . $img->getName();
            $imageData = file_get_contents($originalImagePath);
            $storage->getBucket()->upload($imageData, [
                'name' => 'images/' . $img->getName()
            ]);

            // Resize the image using CodeIgniter's Image Manipulation library
            $config = [
                'image_library' => 'gd2',
                'source_image' => $originalImagePath,
                'create_thumb' => false,
                'maintain_ratio' => true,
                'width' => 800,
                'height' => 800
            ];
            $this->load->library('image_lib', $config);
            $this->image_lib->resize();

            // Upload the resized image to Firebase Storage
            $resizedImagePath = WRITEPATH . 'uploads/' . 'thumb_' . $img->getName();
            $resizedImageData = file_get_contents($resizedImagePath);
            $storage->getBucket()->upload($resizedImageData, [
                'name' => 'images/thumb_' . $img->getName()
            ]);

            // Delete the original and resized images from local server
            unlink($originalImagePath);
            unlink($resizedImagePath);

            print_r('File has been successfully uploaded and resized');
        }
    }
}
