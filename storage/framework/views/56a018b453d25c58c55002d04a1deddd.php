<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title><?php echo e(config('app.name', 'Sistem Keuangan')); ?></title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans antialiased bg-surface-50 min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-sm">
            <div class="text-center mb-8">
                <div class="w-14 h-14 rounded-2xl bg-brand-600 flex items-center justify-center text-white text-xl font-bold mx-auto shadow-lg mb-4">
                    F
                </div>
                <h1 class="page-title"><?php echo e(config('app.name')); ?></h1>
                <p class="text-sm text-surface-400 mt-1"><?php echo e(\App\Models\AppSetting::get('sidebar_tagline', 'Coffee Shop Manager')); ?></p>
            </div>
            <div class="bg-white rounded-2xl border border-surface-200 p-6">
                <?php echo e($slot); ?>

            </div>
            <p class="text-center text-xs text-surface-400 mt-6">&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All rights reserved.</p>
        </div>
    </body>
</html>
<?php /**PATH /Users/deniubaidillah/Documents/Project/Sistem_Keuangan_clean/resources/views/layouts/guest.blade.php ENDPATH**/ ?>