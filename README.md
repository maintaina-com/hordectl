# hordectl
Deploy scenarios for end to end tests from yaml files
Patch desired configurations into horde backends without touching unrelated content

## Intended uses

- Deploy CI/CD scenario content without dependency on DB type or format
- Reproduce edge cases to demonstrate bugs
- Inject users/groups/perms into an existing or new installation
- Demo scenarios

## Inspirations

- yaml and similar formats from config management tools, infrastructure as code, kubectl, helm

## Will it dump existing content to yaml?

Yes, it does. For any objects defined. Maybe within some limitations. I don't know. It will evolve as I need it.

## Will it be a complete backup/restore solution?

Likely not. See horde/backup for a different take on dumping/restoring application content.

## Development notes

### Builtin commands

help    give help on commands in general or on a specific command and its switches and sub commands
query   output yaml format representations of backend data for which an exporter is either builtin or provided by the app
import  generate backend data from [potentially incomplete ] yaml repesentations and builder defaults if builtin or provided by the app
app     run app specific apps implemented in your app.

