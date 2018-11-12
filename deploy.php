<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'idp-api');

// Project repository
set('repository', 'https://bitbucket.org/zanichelli/idp-api.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

host('dc-php7-prod')
    ->stage('prod')
    ->set('deploy_path', '/srv/www/htdocs/{{application}}');
    
host('devphp7-local')
    ->stage('dev')
    ->set('deploy_path', '/srv/www/htdocs/{{application}}');
    
// Tasks

desc('Execute artisan create-env');
task('create-env', function (){
   upload('.env.production', '{{deploy_path}}/shared/.env') ;
})->onStage('prod');

desc('Execute artisan config:clear');
task('artisan:config:clear', function () {
    run('{{bin/php}} {{release_path}}/artisan config:clear');
});

before('artisan:storage:link', 'create-env');
after('cleanup', 'artisan:config:clear');

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');

