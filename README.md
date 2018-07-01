CSS Editor module for Omeka S
=============================

[CSS Editor] is a amodule for [Omeka S] that allows to put specific CSS on all
pages and/or put CSS on pages of a specific site.

This is a full rewrite of [CSS Editor plugin for Omeka].


Installation
------------

The module uses an external library [`css-tidy`], so use the release zip to
install it, or use and init the source.

* From the zip

Download the last release [`CSSEditor.zip`] from the list of releases (the
master does not contain the dependency), and uncompress it in the `modules`
directory.

* From the source and for development:

If the module was installed from the source, rename the name of the folder of
the module to `CSSEditor`, and go to the root module, install [composer] and
run:

```
    composer install --no-dev
```

See general end user documentation for [Installing a module].


TODO
----

- Move the config in the site settings.


Warning
-------

Use it at your own risk.

It’s always recommended to backup your files and your databases and to check
your archives regularly so you can roll back if needed.


Troubleshooting
---------------

See online issues on the [module issues] page on GitHub.


License
-------

This plugin is published under [GNU/GPL].

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
details.

You should have received a copy of the GNU General Public License along with
this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

The library [`css-tidy`] is published under the [GNU/GPL] version 2.1 or later.


Contact
-------

Current maintainers of the module:
* BibLibre (see [BibLibre])
* Daniel Berthereau (see [Daniel-KM])


Copyright
---------

Library [`css-tidy`]:

* Copyright 2005, 2006, 2007 Florian Schmitz
* Copyright 2018 cerdic ([css-tidy updates])

Module CSSEditor for Omeka S:

* Copyright  Roy Rosenzweig Center for History and New Media
* Copyright BibLibre, 2016-2017
* Copyright Daniel Berthereau, 2018


[CSS Editor]: https://github.com/Daniel-KM/Omeka-S-module-CSSEditor
[CSS Editor plugin for Omeka]: https://omeka.org/add-ons/plugins/css-editor/
[Omeka S]: https://github.com/omeka/omeka-s
[`css-tidy`]: http://csstidy.sourceforge.net/usage.php
[`CSSEditor.zip`]: https://github.com/Daniel-KM/Omeka-S-module-CSSEditor/releases
[composer]: https://getcomposer.org/
[Installing a module]: http://dev.omeka.org/docs/s/user-manual/modules/#installing-modules
[css-tidy updates]: https://github.com/Cerdic/CSSTidy
[module issues]: https://github.com/Daniel-KM/Omeka-S-module-CSSEditor/issues
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html
[BibLibre]: https://github.com/BibLibre
[Daniel-KM]: https://github.com/Daniel-KM "Daniel Berthereau"
