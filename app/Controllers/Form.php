<?php namespace App\Controllers;

use CodeIgniter\Controller;

class Form extends Controller
{

    public function multipleImage()
    {
         return view('multiple-image');
    }

    public function storeMultipleFile()
    {
        echo "storeImage!";

        helper(['form', 'url']);

        $db      = \Config\Database::connect();
        $builder = $db->table('file');

        $msg = 'Please select a valid files';

        if ($this->request->getFileMultiple('file')) {

             foreach($this->request->getFileMultiple('file') as $file)
             {

                $file->move(WRITEPATH . 'uploads');

              $data = [
                'name' =>  $file->getClientName(),
                'type'  => $file->getClientMimeType()
              ];

              $save = $builder->insert($data);
              $msg = 'Files has been uploaded';
             }
        }

       return redirect()->to( base_url('form/multipleImage') )->with('msg', $msg);

     }

}