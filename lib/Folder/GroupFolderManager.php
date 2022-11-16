<?php
namespace OCA\Schulcloud\Folder;

use OCP\IGroup;
use OCP\IGroupManager;
use OCP\ILogger;

use OCA\GroupFolders\Folder\FolderManager;

use OCA\Schulcloud\Helper\GroupFolder;

class GroupFolderManager {

    /** @var FolderManager from Group Folders*/
    private FolderManager $folderManager;

    /** @var ILogger */
    private ILogger $logger;

    /** @var IGroupManager */
    private IGroupManager $groupManager;

    public function __construct(FolderManager $folderManager, ILogger $logger, IGroupManager $groupManager = null) {
        $this->folderManager = $folderManager;
        $this->logger = $logger;
        if (!$groupManager) {
            $groupManager = \OC::$server->get(IGroupManager::class);
        }
        $this->groupManager = $groupManager;
    }

    /**
     * Creates a group folder and associates a group with it.
     *
     * @param $group { "gid": "SchulcloudNextcloud-5e1dba1eaa30ab4df47e11d2_teamowner", "displayName": "Test Team" }
     * @return void
     */
    public function createFolderForGroup($group) {
        $groupId = $group->getGID();
        $folderName = GroupFolder::make($groupId, $group->getDisplayName());

        $foundGroups = $this->groupManager->search(GroupFolder::extractFolderId($groupId));
        try {
            if (count($foundGroups) == 1) {
                $folderId = $this->folderManager->createFolder($folderName);
                $this->logger->info("Successfully created new group folder: [id: $folderId, name: $folderName]");
            } else {
                foreach ($foundGroups as $foundGroup) {
                    $foundFolders = $this->folderManager->getFoldersForGroup($foundGroup->getGID());
                    if (!isset($foundFolders[0])) {
                        continue;
                    }
                    $folder = $this->folderManager->getFoldersForGroup($foundGroup->getGID())[0];
                    $folderId = $folder['folder_id'];
                    break;
                }
            }
            $this->folderManager->addApplicableGroup($folderId, $groupId);
            $groupRole = explode('_', $groupId, 2)[1];
            if ($groupRole == 'teamowner' || $groupRole == 'teamadministrator'){
                $this->folderManager->setFolderACL($folderId, true);
                $this->folderManager->setManageACL($folderId, 'group', $groupId, true);
                $this->logger->info("Successfully set ACL to: [groupId: $groupId, folderId: $folderId]");
            }
            $this->logger->info("Successfully added group $groupId to: [id: $folderId, name: $folderName]");

        } catch(\Exception $e) {
            $this->logger->error("Failed to create groupfolder for group [id: $groupId, folderName: $folderName]:\n" . $e->getMessage());
        }
    }

    /**
     * Fetches all group folders of a user and renames them according to the nextcloud group that is connected to that folder
     * @param $user
     * @return void
     */
    public function renameFoldersOfUser($user) {
        try {
            $folderList = $this->folderManager->getFoldersForUser($user);

            foreach ($folderList as $folder) {
                $folderId = $folder['folder_id'];

                $groups = $this->folderManager->searchGroups($folderId);
                // Assumes that the automatically generated group folders are named after the first (and only) connected group
                $group = $groups[0];

                $newFolderName = GroupFolder::make($group['gid'], $group['displayname']);

                $this->folderManager->renameFolder($folderId, $newFolderName);
            }

            if(count($folderList) > 0) {
                $this->logger->info("Successfully renamed group folders for user: [id: " . $user->getUID() . ", name: " . $user->getDisplayName() . "]");
            }
        } catch(\Exception $e) {
            $this->logger->error("Failed to rename group folder:\n" . $e->getMessage());
        }
    }
}
