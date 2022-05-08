<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title>Big Store</title>
        <link href=" <?php echo e(mix('css/app.css')); ?>" rel="stylesheet">
    </head>
    <body>
        <div id="app">
            <app></app>
        </div>
        <script src="<?php echo e(mix('js/bootstrap.js')); ?>"></script>
        <script src="<?php echo e(mix('js/app.js')); ?>"></script>
    </body>
    </html>
<?php /**PATH /var/www/html/resources/views/landing.blade.php ENDPATH**/ ?>