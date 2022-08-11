<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\Schulcloud\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
        ['name' => 'Logout#logout', 'url' => '/logout', 'verb' => 'GET'],
        ['name' => 'Logout#getAppPassword', 'url' => '/apppassword', 'verb' => 'GET'],
        ['name' => 'GroupFolders#getFolderByGroupId', 'url' => '/groupfolders/folders/group/{gid}', 'verb' => 'GET']
    ]
];
