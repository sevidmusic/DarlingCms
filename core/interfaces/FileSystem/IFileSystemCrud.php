<?php


namespace DarlingCms\interfaces\FileSystem;

/**
 * Interface IFileSystemCrud. Defines the basic contract of an object that can perform
 * Crud operations on files and directories in a specified directory.
 * @package DarlingCms\interfaces\FileSystem
 */
interface IFileSystemCrud extends IFileCrud, IDirectoryCrud
{

}
