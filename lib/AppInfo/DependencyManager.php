<?php
namespace OCA\Schulcloud\AppInfo;

use DomainException;

class DependencyManager {

    /**
     * helper function to check all required dependencies
     * @return void
     * @throws DomainException
     */
   public static function checkDependencies(): void {
      self::checkClassDependency('OCA\GroupFolders\Folder\FolderManager');
   }

    /**
     * Checks if a class exists
     * @param string $class Class name with namespace e.g. OCA\Schulcloud\SomeClass
     * @return void
     * @throws DomainException if class does not exist
     */
   public static function checkClassDependency(string $class): void {
      if(!class_exists($class)) {
         throw new DomainException("Could not find class '$class', but it is a required dependency.");
      }
   }
}
