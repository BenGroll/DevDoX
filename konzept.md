# DB

## Top Level : Section (Core, Plugins, Custom-Plugins, Port)

id
name
slug (url)

## Version: e.g Core@v13, TestPlugin@v1, TestCustomPlugin@v1, Port@v1

id
section_id (parent)
version_number
release_date

## Node : Directory (e.g General, Installation, Changelogs)
id
version_id (parent)
type (folder or document)
title
slug (url)
order (sorting)
path
is_root

## Document: Entry

id
node_id (FK to Node, one-to-one)
content (markdown)
last_edited_by (FK to User)
updated_at