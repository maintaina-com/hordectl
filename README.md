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

Maybe some day. Maybe within some limitations. I don't know. It will evolve as I need it.

## Will it be a complete backup/restore solution?

Likely not. See horde/backup for a different take on dumping/restoring application content.