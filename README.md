# hordectl

Deploy scenarios for end to end tests from yaml files
Patch desired configurations into horde backends without touching unrelated content

## Usage examples

### Export resources to a yaml file

Exported resources are as backend independent as possible. Some backends may limit Horde's ability to expose every user's resources globally.
Data format is similar to the internal representation but may deviate where it's appropriate. For example, groups do not export backend keys. When a permission query has per-group permissions, the group will be referenced by display name.

    hordectl query user > user.yml
    hordectl query group > group.yml
    hordectl query permission > permission.yml

These will create individual files for resources. When exporting, order is not important.
A syntax for filtering queries or combining resource types into one file is still missing.

### Import definitions from a yaml file

    hordectl import -f user.yml
    hordectl import -f group.yml
    hordectl import -f permission.yml

Order might be significant. Permissions won't accept group permissions for groups which are not present in the system yet.
Some backends may be readonly and will not allow adding/changing some resources.

See doc dir for detailed explanations of possible input formats and their semantics

### Inject a user or change his password

    hordectl patch user fritz mysecretpassword

This only works if the auth backend supports it.

## Intended uses

If you need a verbatim backup, you might be better off with a snapshot of the database and vfs.

- Deploy CI/CD scenario content without dependency on DB type or format
- Reproduce edge cases to demonstrate bugs
- Inject users/groups/perms into an existing or new installation
- Demo scenarios
- Migrate between backend types

## Inspirations

- yaml and similar formats from config management tools, infrastructure as code, kubectl, helm

## Will it dump existing content to yaml?

Yes, it does. For any objects defined. Maybe within some limitations. I don't know. It will evolve as I need it.

## Will it be a complete backup/restore solution?

Likely not. See horde/backup for a different take on dumping/restoring application content.

## Development notes

### Builtin commands

help    TODO give help on commands in general or on a specific command and its switches and sub commands
query   output yaml format representations of backend data for which an exporter is either builtin or provided by the app
import  generate backend data from [potentially incomplete ] yaml repesentations and builder defaults if builtin or provided by the app
patch   manipulate resources with one-shot commands.
app     TODO run app specific apps implemented in your app.

