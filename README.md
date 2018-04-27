The Darling Content Management System (Darling CMS) was created by Sevi Donnelly Foreman.
Official development began on 01/22/2017. The Darling Cms is a work of love.

The Darling CMS aims to provide a flexible web development architecture built on a solid set of
core components that serve to aide in development process without assuming to much about implementation.

Through custom apps and themes developers will be able to extend and style the Darling CMS however they like.

The core API defines interfaces, abstract classes, and concrete classes designed to aide in the development of
apps and themes. 

The Darling CMS aims to be standards compliant, and to provide a core whose code is simple and S.O.L.I.D.
in design.

At this moment there is not a stable release. The following development versions are available:

darlingCms_0.0_dev: This version is the first draft of the Darling Cms. The core is stable, though in need of 
                    some major refactoring. The overall logic is still clunky, and in some places overly complex,
                    and the doc comments are either incomplete, missing, or in need of major revision.
                    
darlingCms_0.1_dev: This is the current development branch of the Darling Cms. It is a major refactor of version 
                    darlingCms_0.0_dev. The goal is to reduce the overall complexity and clunkiness, and to add
                    clear documentation throughout core. When complete, this version will be more in line with
                    S.O.L.I.D design principals, and therefore easier to maintain.

# Codacy Badge
[![Codacy Badge](https://api.codacy.com/project/badge/grade/5b4c6fabcebe47d2bd7648823c073156)](https://www.codacy.com/app/sdmwebsdm/DarlingCms)

---------------------------------------------------
--------- darlingCms_0.1_dev Architecture ---------
---------------------------------------------------

-----------------------
--- Base Interfaces ---
-----------------------

IStartup: Defines the basic contract of an object that handles startup, shutdown, and restart logic.
- startup():bool - True if startup was successful, false otherwise.
- shutdown():bool - True if shutdown was successful, false otherwise.
- restart():bool - True if restart was successful, false otherwise.

ICrud: Defines the contract of an object that can create, read, update, and delete data associated with an id.
- create(string $dataId, $data):bool - True if data was created under the specified id, false otherwise.
- read(string $dataId):mixed - The data associated with the specified id.
- update(string $dataId, $newData):bool - True if data associated with the specified id was updated, false otherwise.
- delete(string $dataId):bool - True if data associated with the specified id was deleted, false otherwise.

IPathMap: Defines the contract of an object that defines an array of paths.
- getPaths():array - Array of paths, or an empty array if there are no paths.

IAccessController: Defines the basic contract of an object that validates access.
- validateAccess():bool - True if access is valid, false otherwise.​

------------------------
--- Niche Interfaces ---
------------------------

IAppConfig extends IAccessController: Defines the basic contract of an object that can be used to get the configuration settings of a Darling Cms App.
- getName():string - The name of the app.
- getThemeNames():array - Array of the names of the Darling Cms themes assigned to the app.
- getJsLibraryNames(): array - Array of the names of the javascript libraries assigned to the app.
- IAccessController::validateAccess():bool - True if access is valid, false otherwise.​

​IAppStartup extends IStartup, IPathMap: Defines the  basic contract of an object that is responsible for handling the startup, shutdown, and restart logic of a Darling Cms app.
- getCssPaths():array - Array of paths to css files assigned to the app.
- getJsPaths():array - Array of paths to javascript files assigned to the app.
- getAppOutput(): string - The app's output as a string.
- IStarup::startup(): bool - True if startup was successful, false otherwise.
- IStarup::shutdown: boo - True if shutdown was successful, false otherwise.
- IStarup::restart: bool : True if restart was successful, false otherwise.
- IPathMap::getPaths():array - Array of paths, or an empty array if there are no paths.
