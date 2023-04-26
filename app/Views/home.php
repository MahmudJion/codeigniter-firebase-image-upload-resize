<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>codeigniter-firebase-image-upload-resize</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    .container {
      max-width: 800px;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
php
Copy code
<form method="post" action="<?= base_url('FileUpload/upload');?>" enctype="multipart/form-data">
  <div class="form-group">
    <input type="file" name="file" class="form-control">
  </div>

  <div class="form-group">
    <button type="submit" class="btn btn-danger">Upload</button>
  </div>
</form>
  </div>
</body>
</html>



