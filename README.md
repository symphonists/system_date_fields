# System Date

- Version: 1.0
- Author: Alistair Kearney (alistair@symphony-cms.com)
- Build Date: 5th August 2009
- Requirements: Symphony 2.0.6+

**Deprecated** this field is no longer used.

Exposes the internal creation date of an entry (read-only) in the format of a normal date field.

NOTE: This field is not complete. The only thing it currently does is added the system date to the publish table for the section.


## INSTALLATION

1. Upload `system_date_field` to your Symphony `/extensions` folder.

2. Enable it by selecting the "Field: System Date", choose Enable/Install from the with-selected menu, then click Apply.

3. You can now add the "System Date" field to your sections.


## USAGE

Functions similar to the normal Date field in Symphony, however it does not allow editing of the value it contains, instead exposing the entry Creation Date as stored internally by Symphony.
