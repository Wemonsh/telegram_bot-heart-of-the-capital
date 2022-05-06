<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'https://github.com/Wemonsh/telegram_bot-heart-of-the-capital');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('188.225.56.139')
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '~/application');

// Tasks

task('build', function () {
    cd('{{release_path}}');
    run('npm run build');
});

after('deploy:failed', 'deploy:unlock');
