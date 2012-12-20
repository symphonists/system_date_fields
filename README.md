# System Dates

- Version: 1.0
- Author: Symphonists Community
- Build Date: 18th December 2012
- Requirements: Symphony 2.3.1+

Exposes the internal creation and modification dates of an entry (read-only) in the format of a normal date field.

NOTE: This field is not complete. The only thing it currently does is added the system dates to the publish table for the section.

TODO: Add Grouping for Datasources.


## INSTALLATION

1. Upload `system_dates_field` to your Symphony `/extensions` folder.

2. Enable it by selecting the "Fields: System Dates", choose Enable/Install from the with-selected menu, then click Apply.

3. You can now add the "Date: System Created" and "Date: System Modified" fields to your sections.


## USAGE

Functions similar to the normal Date field in Symphony, however it does not allow editing of the value it contains, instead exposing the entry Creation Date and entry Modification Date as stored internally by Symphony.
