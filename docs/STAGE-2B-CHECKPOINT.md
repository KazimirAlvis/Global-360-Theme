# Stage 2B Development Checkpoint

Stage 2B is functionally validated as a development checkpoint. It is not
approved for merge or production deployment because representative database
query counts regressed during verification.

| Route | Before | Stage 2B |
| --- | ---: | ---: |
| Homepage | 95 | 102 |
| Clinic | 82 | 121 |
| Doctor | 81 | 120 |
| Find a Doctor | 173 | 177 |
| Populated state | 88 | 140 |

Stage 2B must not be described as a database-performance improvement. Stage 2C
must profile and address the Clinic, Doctor, and state-route regressions before
these branches are considered for merge or deployment.

The Stage 2B asset result remains valid independently: the full Global Blocks
CSS bundle is no longer loaded globally, and block/context styles are scoped to
the pages that use them.
