<?php
namespace OCA\Schulcloud\Controller;

use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCSController;
use OCP\IRequest;

use OCA\GroupFolders\Folder\FolderManager;

class GroupFoldersController extends OCSController {

    /** @var FolderManager */
    private $folderManager;

    public function __construct(string $appName, IRequest $request, FolderManager $folderManager) {
        parent::__construct($appName, $request);
        $this->folderManager = $folderManager;
    }

    /**
     * @param $gid
     * @return DataResponse
     * @throws \OCP\DB\Exception
     * @AuthorizedAdminSetting(settings=OCA\GroupFolders\Settings\Admin)
     */
    public function getFolderByGroupId($gid): DataResponse {
        return new DataResponse($this->folderManager->getFoldersForGroup($gid));
    }
}
