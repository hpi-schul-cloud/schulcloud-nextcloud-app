# Schulcloud

Place this app in **\<nextcloud\>/custom_apps/**

This App is used to implement requirements of the dBildungscloud for Nextcloud.

## Requirements

- GroupFolders App
- Social Login App

## API

- `GET apps/schulcloud/logout`: front-channel logout
- `GET apps/schulcloud/groupfolders/folders/group/{gid}`: find a groupfolder by a given group id