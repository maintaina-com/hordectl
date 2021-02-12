# Developer notes


## Global scopes

For backward compat with h5ish code, hordectl itself must not rely on globals.
When interacting with horde code, hordectl or code run by hordectl may globalize / reset registry, conf, injector.


## Injectors
Hordectl keeps track of two separate injector scopes
- The hordectl "Dependencies" injector

This 


- The HordeInjector injector

The HordeInjector injector will be consumed by the accessed horde instance.
It will be globalized when needed into $GLOBALS['injector'] to ensure it's present.
