<?php
namespace OCA\Schulcloud\Helper;

class GroupFolder {

    /**
     * Creates a formatted string for the group folder names from the gid and display name
     * @param $groupId
     * @param $groupName
     * @return string
     */
    public static function make($groupId, $groupName) {
        $id = $groupId;

        if (preg_match("/(?<=-)(.*)$/", $groupId, $matches)) {
            $id = GroupFolder::extractFolderId($matches[0]);
        }

        return "$groupName ($id)";
    }

    /**
     * Extracts the folderId out of the gid by splitting on underscore to remove the role of the user.
     * @param $gid "637204050b71c976b8f6425e_teamowner"
     * @return string
     */
    public static function extractFolderId($gid) {
        return explode('_', $gid, 2)[0];
    }
}
