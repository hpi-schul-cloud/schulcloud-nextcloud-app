<?php
namespace OCA\Schulcloud\Helper;

class GroupFolder {

    /**
     * Creates a formatted string for the group folder names from the gid and display name
     * @param $groupId 5e1dc275322ce040a850b14b
     * @param $groupName e.g. A-Team (teamowner)
     * @return string
     */
    public static function make($groupId, $groupName) {
        $id = $groupId;

        if (preg_match("/(?<=-)(.*)$/", $groupId, $matches)) {
            $id = GroupFolder::extractFolderId($matches[0]);
        }
        preg_match("/.+?(?=\s\()/", $groupName, $groupNameWithoutRole);
        return "$groupNameWithoutRole[0] ($id)";
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
